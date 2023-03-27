<?php
namespace app\mobile;
use app\common\controller\Frontend;
use app\common\library\helper\FormatHelper;
use app\common\library\helper\IpHelper;
use app\common\library\helper\IpLocation;
use app\common\library\helper\LogHelper;
use app\common\library\helper\PopularHelper;
use app\logic\common\FocusLogic;
use app\model\Answer;
use app\model\Approval;
use app\model\Attach;
use app\model\Common;
use app\model\PostRelation;
use app\model\Question as QuestionModel;
use app\model\Report;
use app\model\Topic;
use app\model\Users;
use app\model\Vote;
use think\exception\ValidateException;
use WordAnalysis\Analysis;

class Question extends Frontend
{
    protected $needLogin = [
        'publish',
    ];

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

        //更新问题浏览
        QuestionModel::updateQuestionViews($question_id,$this->user_id);

        //更新问题热度值
        PopularHelper::calcQuestionPopularValue($question_id);

        //问题用户信息
        $question_info['user_info'] = Users::getUserInfo($question_info['uid']);
        $question_info['user_focus'] = (bool)Users::checkFocus($this->user_id, $question_info['uid']);

        // 获取话题
        $question_info['topics'] = Topic::getTopicByItemType('question',$question_info['id']);
        $question_info['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'question', $question_info['id']) ? 1 : 0;

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
            'recommend_post'=>$recommend_post,
            'best_answer_count'=>db('answer')->where(['question_id'=>$question_id,'is_best'=>1])->count() ? 1 : 0,
            'attach_list'=>Attach::getAttach('question_attach',$question_info['id'])
        ]);
        $this->assign('publish_question_count',LogHelper::getActionLogCount('publish_question',$question_info['uid'],$this->user_id));
        $this->assign('publish_answer_count',LogHelper::getActionLogCount('publish_answer',$question_info['uid'],$this->user_id));
        $this->assign('publish_article_count',LogHelper::getActionLogCount('publish_article',$question_info['uid'],$this->user_id));

        //回答
        $page = $this->request->param('page', 1);
        $sort = $this->request->param('sort',get_setting('answer_sort_type','new'));

        //TODO 不感兴趣的回答排除操作
        $answer = Answer::getAnswerByQuestionId($question_id,$answer_id,$page,10,$sort,$this->user_id);

        foreach ($answer['data'] as $key=>$val)
        {
            $answer['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$this->user_id);
            $answer['data'][$key]['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$val['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
            $answer['data'][$key]['has_uninterested'] = db('uninterested')->where(['item_id'=>$val['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
        }

        $this->assign($answer);
        $this->assign([
            'sort'=>$sort,
            'sort_texts'=>['new'=>L('最新排序'),'hot'=>L('热门排序'),'publish'=>L('只看楼主'),'focus'=>L('关注的人')]
        ]);

        $seo_title = $question_info['seo_title'] ? : $question_info['title'];

        $seo_keywords = $question_info['seo_keywords'] ? : Analysis::getKeywords($question_info['detail'], 4);
        $seo_description = $question_info['seo_description'] ? : str_cut(strip_tags($question_info['detail']),0,200);
        $this->TDK($seo_title, $seo_keywords, $seo_description);

        hook('question_detail_get_after',$this->request->param());

        if($question_info['question_type']=='reward')
        {
            return $this->fetch('reward');
        }else{
            return $this->fetch();
        }
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

            unset($postData['__token__'],$postData['access_key']);
            $postData['uid'] = $postData['uid']??$this->user_id;
            $postData['question_type'] = $postData['question_type'] ?? 'normal';

            if(get_setting('topic_enable')=='Y' && (!isset($postData['topics'])) || empty(explode(',',$postData['topics'])))
            {
                $this->error('请至少选择一个话题');
            }

            if(get_setting('topic_enable')=='Y' && get_setting('max_topic_select')<count(explode(',',$postData['topics'])))
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
                Approval::saveApproval('modify_question',$postData,$postData['uid'],$access_key);
                $this->error('修改成功,请等待管理员审核', get_user_url($postData['uid'],['type'=>'question']));
            }

            if ($id = QuestionModel::saveQuestion($postData['uid'], $postData,$access_key))
            {
                /*问题提交后钩子*/
                hook('question_publish_post_after',$id);
                $this->success('发表成功', (string)url('question/detail?id=' . $id));
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
            if($this->user_id!=$question_info['uid'] && get_user_permission('modify_question')!='Y')
            {
                $this->error('您没有修改问题的权限');
            }

            $question_info['topics'] = Topic::getTopicByItemType('question',$question_info['id']);
            if($draft_info)
            {
                $draft_info['data']['detail'] = htmlspecialchars_decode($draft_info['data']['detail']);
                $question_info = $draft_info['data'];
                if(isset($draft_info['data']['topics']))
                {
                    $question_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
                }
                $access_key = $draft_info['data']['access_key'];
            }
        }else{
            if($this->user_info['permission']['publish_question_enable']!='Y')
            {
                $this->error('您没有发布问题的权限');
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
                if(isset($draft_info['data']['topics']))
                {
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

    //获取问题回答列表
    public function answers()
    {
        $data['page'] = $this->request->param('page', 1);
        $data['question_id'] = $this->request->param('question_id');
        $data['answer_id'] = $this->request->param('answer_id', 0);
        $data['limit'] = get_setting('contents_per_page',15);
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

        $question_info = db('question')->where(['id'=>$data['question_id'],'status'=>1])->field('title,detail,id')->find();
        $question_info['detail'] = str_cut(strip_tags(htmlspecialchars_decode($question_info['detail'])),0,200);
        $this->result([
            'total'=>$answer['total'],
            'html'=>$this->fetch('',[
                'question_info'=>$question_info,
                'list'=>$answer['data'],
                'best_answer_count'=>db('answer')->where(['question_id'=>$data['question_id'],'is_best'=>1])->count() ? 1 : 0,
            ]),
            'list'=>$answer['data']
        ],1);
    }

}