<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------

namespace app\frontend;
use app\common\library\helper\FormatHelper;
use app\common\library\helper\PopularHelper;
use app\model\Approval;
use app\model\Attach;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\BrowseRecords;
use app\model\Redirect;
use app\model\Users;
use app\model\Common;
use app\model\Topic;
use app\model\Vote;
use app\model\Report;
use app\model\Question as QuestionModel;
use app\model\Answer;
use think\exception\ValidateException;
use WordAnalysis\Analysis;

/**
 * 问答模块
 * Class Question
 * @package app\ask\controller
 */
class Question extends Frontend
{
	protected $needLogin = [
        'publish',
        'delete_answer',
        'delete_answer',
        'comment_vote',
    ];

	/**
	 * 问题首页
	 */
	public function index()
	{
        $sort = $this->request->param('sort','new','sqlFilter');
        $category = $this->request->param('category_id',0,'intval');
        $this->assign([
            'sort'=> $sort,
            'category'=>$category,
        ]);
        $this->TDK('问题列表');
		return $this->fetch();
	}

	/**
	 * 发起问题/编辑问题
	 */
	public function publish()
	{
		if ($this->request->isPost())
		{
            $postData = $this->request->post();
            $access_key = $postData['access_key'];

            /*问题提交前顶部钩子*/
            hook('question_publish_post_top',$postData);

            if(isset($postData['id']) && intval($postData['id']))
            {
                $question_info = QuestionModel::getQuestionInfo(intval($postData['id']),'uid,id');
                if(!$question_info || ($question_info['uid']!=$this->user_id && get_user_permission('modify_question')!='Y'))
                {
                    $this->error('您没有修改问题的权限');
                }
            }

            try {
                validate(\app\validate\Question::class)->check($postData);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if(!intval($postData['id']))
            {
                if($this->user_info['permission']['publish_question_enable']!='Y')
                {
                    $this->error('您没有发布问题的权限');
                }

                // 用户今天是否还可以发送问题
                if ($this->user_info['permission']['publish_question_num'] AND ($question_num = Users::getUserPublishNum($this->user_id,'question')) >= $this->user_info['permission']['publish_question_num']){
                    $this->error('你所在的用户组当天只允许发布' . $this->user_info['permission']['publish_question_num'] . '篇问题帖,您已发布' . $question_num . '篇问题帖');
                }

                //验证用户积分是否满足积分操作条件
                if(!LogHelper::checkUserIntegral('NEW_QUESTION',$this->user_id))
                {
                    $this->error('您的积分不足,无法发起问题');
                }
            }

            /*问题提交前钩子*/
            hook('question_publish_post_before',$postData);

            unset($postData['__token__']);

			$postData['uid'] = $postData['uid']??$this->user_id;
            $postData['question_type'] = $postData['question_type'] ?? 'normal';

            if(get_setting('topic_enable')=='Y' && !isset($postData['topics']))
            {
                $this->error('请至少选择一个话题');
            }

            if(get_setting('topic_enable')=='Y' && get_setting('max_topic_select') && get_setting('max_topic_select')<count(explode(',',$postData['topics'])))
            {
                $this->error('您最多只可设置'.get_setting('max_topic_select').'个话题');
            }

			$postData['is_anonymous'] = $postData['is_anonymous'] ?? 0;

            if(htmlspecialchars_decode($postData['title'])=='' || removeEmpty($postData['title'])=='')
            {
                $this->error('请填写问题标题');
            }

            if($this->settings['enable_category'] && isset($postData['category_id']) && !intval($postData['category_id']))
            {
                $this->error('请选择问题分类');
            }

            if ($this->user_info['permission']['publish_url']=='N' && FormatHelper::outsideUrlExists($postData['detail']))
            {
                $this->error('你所在的用户组不允许发布站外链接');
            }

			//发起需要审核
			if($postData['question_type']=='normal' && !$postData['id'] && $this->publish_approval_valid(htmlspecialchars_decode($postData['detail'])))
			{
				Approval::saveApproval('question',$postData,$postData['uid'],$access_key);
				$this->error('发表成功,请等待管理员审核', get_user_url($postData['uid'],['type'=>'question']));
			}

            //修改需要审核
            if($postData['question_type']=='normal' && $this->publish_approval_valid(htmlspecialchars_decode($postData['detail']),'modify_question_approval') && $postData['id'])
            {
                $question_uid = db('question')->where('id',$postData['id'])->value('uid');
                Approval::saveApproval('modify_question',$postData,$question_uid,$access_key);
                $this->error('修改成功,请等待管理员审核', get_user_url($question_uid,['type'=>'question']));
            }

			if ($id = QuestionModel::saveQuestion($postData['uid'], $postData,$access_key))
			{
                /*问题提交后钩子*/
                hook('question_publish_post_after',['id'=>$id,'postData'=>$postData]);
				$this->success('发表成功', (string)url('question/detail?id=' . $id));
			}else{
                $this->error('提交失败'.QuestionModel::getError());
            }
		}

        /**
         * 问题发起页面前置钩子
         */
        hook('question_publish_get_before',$this->request->param());

		$question_id = $this->request->param('id',0,'intval');
        $captcha_enable = 0;
        $draft_info = \app\model\Draft::getDraftByItemID($this->user_id,'question',$question_id);
        $access_key = md5($this->user_id.time());
		if($question_id)
		{
			$question_info = QuestionModel::getQuestionInfo($question_id);
            if($question_info)
            {
                if($this->user_id!=$question_info['uid'] && get_user_permission('modify_question')!='Y')
                {
                    $this->error('您没有修改问题的权限',url('index'));
                }
                $question_info['detail'] = htmlspecialchars($question_info['detail']);
                $question_info['topics'] = Topic::getTopicByItemType('question',$question_info['id']);
            }

            if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $question_info = $draft_info['data'];

                if (isset($draft_info['data']['topics'])) {
                    // 兼容uniApp话题数据
                    if (is_array($draft_info['data']['topics']) && !empty($draft_info['data']['topics'])) {
                        if (is_array($draft_info['data']['topics'][0])) {
                            $draft_info['data']['topics'] = array_unique(array_column($draft_info['data']['topics'], 'id'));
                        }
                    }
                    $question_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
                }
                $access_key = $draft_info['data']['access_key'];
            }
		}else{
            if($this->user_info['permission']['publish_question_enable']!='Y')
            {
                $this->error('您没有发布问题的权限',url('index'));
            }

            // 用户今天是否还可以发送问题
            if ($this->user_info['permission']['publish_question_num'] AND ($question_num = Users::getUserPublishNum($this->user_id,'question')) >= $this->user_info['permission']['publish_question_num']){
                $this->error('你所在的用户组当天只允许发布' . $this->user_info['permission']['publish_question_num'] . '篇问题帖,您已发布' . $question_num . '篇问题帖',url('index'));
            }

            if(get_setting('publish_content_verify_time') && !Users::checkUserPublishTimeAndCount($this->user_id,'question'))
            {
                $captcha_enable = 1;
            }
			$question_info = array();
			if($topic_id = $this->request->param('topic_id'))
            {
                $question_info['topics'] = db('topic')->where('id', $topic_id)->column('id,title');
            }
			if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $question_info = $draft_info['data'];
                if (isset($draft_info['data']['topics'])) {
                    $question_info['topics'] = $draft_info['data']['topics'] ? Topic::getTopicByIds($draft_info['data']['topics']) : [];
                }
                $access_key = $draft_info['data']['access_key'];
            }
            unset($question_info['id']);
		}
		$this->assign([
            'captcha_enable'=>$captcha_enable,
            'question_info'=>$question_info,
            'category_list'=>\app\model\Category::getCategoryListByType('question'),
            'access_key'=>$access_key,
            'attach_list'=>Attach::getAttach('question_attach',$question_id)
        ]);

        /**
         * 问题发起页面后置钩子
         */
        hook('question_publish_get_after',$this->request->param());
		return $this->fetch();
	}

    /**
     * 问题详情页
     */
	public function detail()
	{
        hook('question_detail_get_before',$this->request->param());

		$question_id = $this->request->param('id',0,'intval');
		$answer_id = $this->request->param('answer',0,'intval');
		$question_info = QuestionModel::getQuestionInfo($question_id);
        hook('question_detail_get_middle',['question_info'=>$question_info]);

		if (!$question_info || $question_info['status']===0) {
			$this->error('问题不存在或已被删除',url('question/index'));
		}
        $target_question = $redirect_message = [];
        if($log= $this->request->param('log',0,'intval'))
        {
            $logs = LogHelper::parseActionLogList(['modify_question_title','modify_question_detail','modify_question_topic'],'question',$question_id);
            $this->assign('logs',$logs);
        }else{
            //获取重定向
            $question_info['redirect'] = Redirect::getRedirect($question_info['id']);
            if (isset($question_info['redirect']['target_id']))
            {
                $target_question = QuestionModel::getQuestionInfo($question_info['redirect']['target_id']);
            }

            $rf = $this->request->get('rf','');

            if (is_numeric($rf))
            {
                if ($from_question = QuestionModel::getQuestionInfo($rf))
                {
                    $redirect_message[] = L('从问题 %s 跳转而来', '<a href="' . (string)url('question/detail',['id'=>$rf,'rf'=>'false']) . '">' . $from_question['title'] . '</a>');
                }
            }

            if ($question_info['redirect'] && !$rf)
            {
                if ($target_question)
                {
                    $this->redirect(url('question/detail',['id'=>$question_info['redirect']['target_id'],'rf'=>$question_info['id']]));
                }
                else
                {
                    $redirect_message[] = L('重定向目标问题已被删除, 将不再重定向问题');
                }
            }

            if ($question_info['redirect'])
            {
                if ($target_question)
                {
                    $message = L('此问题将跳转至') . ' <a href="' . (string)url('question/detail',['id'=>$question_info['redirect']['target_id'],'rf'=> $question_info['id']]) . '">' . $target_question['title'] . '</a>';
                    if ($this->user_id && (isSuperAdmin() || isNormalAdmin() || (!$question_info['is_lock'] && get_user_permission('redirect_question'))))
                    {
                        $message .= '&nbsp; (<a href="javascript:;" class="aw-ajax-get" data-url="'.(string)url('ajax.Question/cancel_redirect', ['item_id'=> $question_info['id']]) . '">' . L('撤消重定向') . '</a>)';
                    }

                    $redirect_message[] = $message;
                }
                else
                {
                    $redirect_message[] = L('重定向目标问题已被删除, 将不再重定向问题');
                }
            }
        }

        //更新问题浏览
        QuestionModel::updateQuestionViews($question_id,$this->user_id);

        //记录用户浏览记录
        BrowseRecords::recordViewLog($this->user_id,$question_info['id'],'question');

        //更新问题热度值
        PopularHelper::calcQuestionPopularValue($question_id);

		//问题用户信息
		$question_info['user_info'] = Users::getUserInfo($question_info['uid']);
		$question_info['user_focus'] = (bool)Users::checkFocus($this->user_id, $question_info['uid']);

		// 获取话题
		$question_info['topics'] = Topic::getTopicByItemType('question',$question_info['id']);
        $question_info['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'question', $question_info['id']) ? 1 : 0;

        //获取相关问题
        $relation_question = QuestionModel::getRelationQuestion($question_id);

        //是否举报
		$checkReport=Report::getReportInfo($question_id,'question',$this->user_id)?1:0;

        //是否点赞
        $question_info['vote_value'] = Vote::getVoteByType($question_id,'question',$this->user_id);

        //是否收藏
		$checkFavorite=Common::checkFavorite(['uid'=>$this->user_id,'item_id'=>$question_id,'item_type'=>'question'])?1:0;

        //获取推荐内容
        $recommend_post=[];

		if($question_info['topics'])
        {
            $recommend_post = Topic::getRecommendPost($question_info['id'],'question',array_column($question_info['topics'], 'id'),$this->user_id);
        }

		$this->assign([
			'question_info' => $question_info,
			'answer_id' => $answer_id,
			'checkReport' => $checkReport,
			'checkFavorite' => $checkFavorite,
            'relation_question'=>$relation_question,
            'recommend_post'=>$recommend_post,
            'best_answer_count'=>db('answer')->where(['question_id'=>$question_id,'is_best'=>1])->count() ? 1 : 0,
            'attach_list'=>Attach::getAttach('question_attach',$question_info['id']),
            'question_focus_users'=>QuestionModel::getQuestionFocusUsers($question_id),
            'redirect_message'=> $redirect_message,
            'log'=>$log
		]);

        $this->assign('publish_question_count',LogHelper::getActionLogCount('publish_question',$question_info['uid'],$this->user_id));
        $this->assign('publish_answer_count',LogHelper::getActionLogCount('publish_answer',$question_info['uid'],$this->user_id));
        $this->assign('publish_article_count',LogHelper::getActionLogCount('publish_article',$question_info['uid'],$this->user_id));

        if(!$log)
        {
            //回答
            $page = $this->request->param('page', 1);
            $sort = $this->request->param('sort',get_setting('answer_sort_type','new'));

            //TODO 不感兴趣的回答排除操作
            $answer = Answer::getAnswerByQuestionId($question_id,$answer_id,$page,10,$sort,$this->user_id,'aw-answer-list',1);

            foreach ($answer['data'] as $key=>$val)
            {
                $answer['data'][$key]['user_focus'] = (bool)Users::checkFocus($this->user_id, $val['uid']);
                $answer['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$this->user_id);
                $answer['data'][$key]['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$val['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
                $answer['data'][$key]['has_uninterested'] = db('uninterested')->where(['item_id'=>$val['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
            }
            $force_fold = db('answer')->where(['question_id'=>$question_info['id'],'force_fold'=>1])->count();
            $this->assign($answer);
            $this->assign([
                'force_fold'=>$force_fold,
                'sort'=>$sort,
                'sort_texts'=>['new'=>L('最新排序'),'hot'=>L('热门排序'),'publish'=>L('只看楼主'),'focus'=>L('关注的人')]
            ]);
        }

        $seo_title = $question_info['seo_title'] ? : $question_info['title'];

        $seo_keywords = $question_info['seo_keywords'] ? : Analysis::getKeywords($question_info['detail'], 4);
        $seo_description = $question_info['seo_description'] ? : str_cut(strip_tags($question_info['detail']),0,200);
		$this->TDK($seo_title, $seo_keywords, $seo_description);

        hook('question_detail_get_after',$this->request->param());

        return $this->fetch();
	}


	//问题回答评论
	public function answer_comments()
	{
		$this->view->engine()->layout(false);
		$data = $this->request->param();
		$list = Answer::getAnswerComments($data);
		$this->assign('list', $list);
		return $this->fetch();
	}

	//获取问题回答列表
	public function answers() 
	{
        // hook
        hook('answersBefore');

		$data['page'] = $this->request->param('page', 1);
		$data['question_id'] = $this->request->param('question_id');
		$data['answer_id'] = $this->request->param('answer_id', 0);
		$data['limit'] = 10;
		$sort = $this->request->param('sort','new');
		if($sort=='new')
        {
            $order = ['is_best'=>'DESC','create_time'=>'DESC'];
        }else{
            $order = ['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'];
        }

		$answer = Answer::getAnswerByQid($data,$order);
		foreach ($answer['data'] as $key=>$val)
        {
            $answer['data'][$key]['has_uninterested'] = db('uninterested')->where(['item_id'=>$val['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
            $answer['data'][$key]['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$val['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
            $answer['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$this->user_id);
            $answer['data'][$key]['checkFavorite']=Common::checkFavorite(['uid'=>$this->user_id,'item_id'=>$val['id'],'item_type'=>'answer'])?1:0;
            $answer['data'][$key]['checkReport']=Report::getReportInfo($val['id'],'answer',$this->user_id)?1:0;
        }

        hook('answersAfter',$answer);

		$this->result([
		    'total'=>$answer['total'],
            'last_page'=>$answer['last_page'],
            'html'=>$this->fetch('',[
                'list'=>$answer['data'],
                'best_answer_count'=>db('answer')->where(['question_id'=>$data['question_id'],'is_best'=>1])->count() ? 1 : 0,
            ]),
            'page_render'=>$answer['page_render']
        ],1);
	}
}