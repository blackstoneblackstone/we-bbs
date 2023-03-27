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

use app\common\controller\Frontend;
use app\common\library\helper\FormatHelper;
use app\common\library\helper\LogHelper;
use app\common\library\helper\PopularHelper;
use app\model\Approval;
use app\model\Attach;
use app\model\BrowseRecords;
use app\model\UsersFavorite;
use app\model\PostRelation;
use app\model\Users;
use app\model\Article as ArticleModel;
use app\model\Report;
use app\model\Topic;
use app\model\Vote;
use think\facade\Cache;
use tools\Tree;
use WordAnalysis\Analysis;

class Article extends Frontend
{
	protected $needLogin = [
        'publish',
    ];

	/**
	 * 文章列表
	 * @return mixed
	 */
	public function index()
    {
        $sort = $this->request->param('sort','new','sqlFilter');
        $category = $this->request->param('category_id',0,'intval');
        $this->assign(
            [
                'sort'=> $sort,
                'category'=>$category,
            ]
        );
        $this->TDK('文章列表');
		return $this->fetch();
	}

	/**
	 * 发表文章
	 */
	public function publish()
    {
		if ($this->request->isPost()) {
			$postData = $this->request->post();

            /*文章提交前钩子*/
            hook('article_publish_post_top',$postData);

            if(isset($postData['id']) && intval($postData['id']))
            {
                $article_info = ArticleModel::getArticleInfo(intval($postData['id']));
                if(!$article_info || ($article_info['uid']!=$this->user_id && get_user_permission('modify_article')!='Y'))
                {
                    $this->error('您没有修改文章的权限');
                }
            }

            if(!intval($postData['id']))
            {
                if ($this->user_info['permission']['publish_article_num'] && ($question_num = Users::getUserPublishNum($this->user_id,'article')) >= intval($this->user_info['permission']['publish_article_num'])){
                    $this->error('你所在的用户组当天只允许发布' . $this->user_info['permission']['publish_article_num'] . '篇文章,您已发布' . $question_num . '篇文章');
                }
                //验证用户积分是否满足积分操作条件
                if(!LogHelper::checkUserIntegral('NEW_ARTICLE',$this->user_id))
                {
                    $this->error('您的积分不足,无法发表文章');
                }
            }

            /*文章提交前钩子*/
            hook('article_publish_post_before',$postData);

            $access_key = $postData['access_key'];
            unset($postData['__token__']);

            if(htmlspecialchars_decode($postData['title'])=='' || removeEmpty($postData['title'])=='')
            {
                $this->error('请填写文章标题');
            }

            if(htmlspecialchars_decode($postData['message'])=='' || removeEmpty($postData['message'])=='')
            {
                $this->error('请填写文章正文');
            }

            if($this->settings['enable_category'] && isset($postData['category_id']) && !$postData['category_id'])
            {
                $this->error('请选择文章分类');
            }

            if(get_setting('topic_enable')=='Y' && !isset($postData['topics']))
            {
                $this->error('请至少选择一个话题');
            }

            if(get_setting('topic_enable')=='Y' && get_setting('max_topic_select') && get_setting('max_topic_select')<count(explode(',',$postData['topics'])))
            {
                $this->error('您最多只可设置'.get_setting('max_topic_select').'个话题');
            }

            if ($this->user_info['permission']['publish_url']=='N' AND FormatHelper::outsideUrlExists($postData['message'])) {
                $this->error('你所在的用户组不允许发布站外链接');
            }

            $uid = $postData['uid'] ?? $this->user_id;

			//需要审核
			if ($this->publish_approval_valid(htmlspecialchars_decode($postData['message']),'publish_article_approval') && !$postData['id'])
			{
				unset($postData['__token__']);
				Approval::saveApproval('article', $postData, $uid,$access_key);
				$this->error('发表成功,请等待管理员审核', get_user_url($uid,['type'=>'article']));
			}

            //修改需要审核
            if($this->publish_approval_valid(htmlspecialchars_decode($postData['message']),'modify_article_approval') && $postData['id'])
            {
                $article_uid = db('article')->where('id',$postData['id'])->value('uid');
                Approval::saveApproval('modify_article',$postData,$article_uid,$access_key);
                $this->error('修改成功,请等待管理员审核', get_user_url($article_uid,['type'=>'article']));
            }

            $id = $postData['id'] ?  ArticleModel::updateArticle($uid, $postData,$access_key) : ArticleModel::saveArticle($uid, $postData,$access_key);

            /*文章提交后钩子*/
            hook('article_publish_post_after',['id'=>$id,'postData'=>$postData]);

            if($id)
            {
                $this->success('提交成功',url('article/detail',['id'=>$id]));
            }
            $this->error('提交失败'.':'.ArticleModel::getError());
		}

        /**
         * 文章发起页面前置钩子
         */
        hook('article_publish_get_before',$this->request->param());
		$article_id = $this->request->param('id',0,'intval');
        $draft_info = \app\model\Draft::getDraftByItemID($this->user_id,'article',$article_id);
        $captcha_enable = 0;
		if ($article_id)
        {
			$article_info = ArticleModel::getArticleInfo($article_id);
            if(!$article_info)
            {
                $this->error('文章不存在',url('index'));
            }

            $article_info['message'] = htmlspecialchars($article_info['message']);
            if($this->user_id!=$article_info['uid'] && get_user_permission('modify_article')!='Y')
            {
                $this->error('您没有修改文章的权限',url('index'));
            }

            $article_info['topics'] = Topic::getTopicByItemType('article', $article_info['id']);
            if($draft_info)
            {
                if(isset($draft_info['data']['detail']))
                {
                    $draft_info['data']['message'] = htmlspecialchars_decode($draft_info['data']['detail']);
                }
                $article_info = $draft_info['data'];
                if (isset($draft_info['data']['topics'])) {
                    // 兼容uniApp话题数据
                    if (is_array($draft_info['data']['topics']) && !empty($draft_info['data']['topics'])) {
                        if (is_array($draft_info['data']['topics'][0])) {
                            $draft_info['data']['topics'] = array_unique(array_column($draft_info['data']['topics'], 'id'));
                        }
                    }
                    $article_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
                }
            }
		} else {
            if($this->user_info['permission']['publish_article_enable']!='Y')
            {
                $this->error('您没有发布文章的权限',url('index'));
            }

            // 用户今天是否还可以发文章
            if ($this->user_info['permission']['publish_article_num'] && ($question_num = Users::getUserPublishNum($this->user_id,'article')) >= $this->user_info['permission']['publish_article_num']){
                $this->error('你所在的用户组当天只允许发布' . $this->user_info['permission']['publish_article_num'] . '篇文章,您已发布' . $question_num . '篇文章',url('index'));
            }

            if(get_setting('publish_content_verify_time') && !Users::checkUserPublishTimeAndCount($this->user_id,'article'))
            {
                $captcha_enable = 1;
            }

            $article_info = array();
            if($topic_id = $this->request->param('topic_id'))
            {
                $article_info['topics'] = db('topic')->where('id', $topic_id)->column('id,title');
            }

            if($draft_info)
            {
                if (isset($draft_info['data']['detail'])) {
                    $draft_info['data']['message'] = htmlspecialchars_decode($draft_info['data']['detail']);
                }
                $article_info = $draft_info['data'];
                if (isset($draft_info['data']['topics'])) {
                    $article_info['topics'] = Topic::getTopicByIds($draft_info['data']['topics']);
                }
            }
            unset($article_info['id']);
		}

		//从专栏进入发起文章
		$column_id = $this->request->param('column_id', 0);
		$article_info['column_id'] = $column_id;
		$this->assign(
            [
                'captcha_enable'=>$captcha_enable,
                'article_info'=>$article_info,
                'column_list'=>\app\model\Column::getColumnByUid($this->user_id),
                'article_category_list'=>\app\model\Category::getCategoryListByType('article'),
                'access_key'=>md5($this->user_id.time()),
                'attach_list'=>Attach::getAttach('article_attach',$article_id)
            ]);

        /**
         * 文章发起页面后置钩子
         */
        hook('article_publish_get_after',$this->request->param());
		return $this->fetch();
	}

	/**
	 * 文章详情
	 */
	public function detail()
    {
        hook('article_detail_get_before',$this->request->param());
		$id = $this->request->param('id',0,'intval');
		$article_info = ArticleModel::getArticleInfo($id);
        hook('article_detail_get_middle',['article_info'=>$article_info]);

        if (!$article_info || $article_info['status']==0) {
            $this->error('文章不存在或已被删除',url('article/index'));
        }

        //更新文章浏览量
        ArticleModel::updateArticleViews($id, $this->user_id);

        //记录用户浏览记录
        BrowseRecords::recordViewLog($this->user_id,$article_info['id'],'article');

        //更新文章热度值
        PopularHelper::calcArticlePopularValue($id);

        $article_info['title'] = htmlspecialchars_decode($article_info['title']);

		//举报状态
		$article_info['is_report'] = Report::getReportInfo($article_info['id'], 'article', $this->user_id);

        //投票状态
		$article_info['vote_value'] = Vote::getVoteByType($article_info['id'], 'article', $this->user_id);

        //收藏状态
		$article_info['is_favorite'] = UsersFavorite::checkFavorite($this->user_id, 'article', $article_info['id']);

        //用户信息
		$article_info['user_info'] = Users::getUserInfo($article_info['uid'])?: ['url'=>'javascript:;','uid'=>0,'nick_name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];

		$article_info['topics'] = Topic::getTopicByItemType('article', $article_info['id']);

        if($article_info['column_id'])
        {
            $article_info['column_info'] = db('column')->where(['id'=>$article_info['column_id'],'verify'=>1])->field('name,description,cover,focus_count,view_count,post_count')->find();
        }

        //获取相关文章
        $relation_article = ArticleModel::getRelationArticleList($article_info['id']);
        $this->assign('relation_article', $relation_article);

        //获取推荐内容
        $recommend_post=[];
        if($article_info['topics'])
        {
            $recommend_post = Topic::getRecommendPost($article_info['id'],'article',array_column($article_info['topics'], 'id'),$this->user_id);
        }

        $this->assign('recommend_post', $recommend_post?:[]);

        $page = $this->request->param('page', 1);

        $sort = $this->request->param('sort', 'new');

        $comment_list = ArticleModel::getArticleCommentList($article_info['id'], $sort, intval($page));

        foreach ($comment_list['data'] as $key => $val)
        {
            $comment_list['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'], 'article_comment', $this->user_id);
        }

        $this->assign([
            'article_info'=> $article_info,
            'comment_list'=> Tree::toTree($comment_list['data']),
            'page_render'=> $comment_list['page_render'],
            'sort' =>$sort,
            'attach_list'=>Attach::getAttach('article_attach',$article_info['id'])
        ]);
        $seo_title = $article_info['seo_title'] ? : $article_info['title'];
        $seo_keywords = $article_info['seo_keywords'] ? : Analysis::getKeywords($article_info['title'], 4);
        $seo_description = $article_info['seo_description'] ? : str_cut(strip_tags($article_info['message']),0,200);
        $this->TDK($seo_title, $seo_keywords, $seo_description);

        hook('article_detail_get_after',$this->request->param());
		return $this->fetch();
	}

	//预览文章
	public function preview()
    {
		if ($this->request->isPost())
		{
			$data = $this->request->post('data');
			unset($data['__token__'], $data['topics_text']);
			$article_info = array();
            $article_info['id'] = 0;
            $article_info['comment_count'] = 0;
            $article_info['uid'] = $this->user_id;
			if (isset($data['id']) && $data['id']) {
				$article_info = ArticleModel::getArticleInfo($data['id']);
			}
			$article_info['message'] = htmlspecialchars_decode($data['message']);
			$article_info['title'] = $data['title'];
			$article_info['agree_count'] = $article_info['agree_count'] ?? 0;
			$article_info['view_count'] = $article_info['view_count'] ?? 0;
			$article_info['create_time'] = $article_info['create_time'] ?? time();
			if (isset($data['topics'])) {
				$article_info['topics'] = Topic::getTopicByIds($data['topics']);
			}
			Cache::set('article_preview_' . $this->user_id, $article_info);
		} else {
			$article_info = Cache::get('article_preview_' . $this->user_id);
		}
		$this->assign('article_info', $article_info);
		$keywords =$article_info['message'] ? Analysis::getKeywords($article_info['message'], 5) : '';
		$this->TDK($article_info['title'], $keywords, str_cut(strip_tags($article_info['message']), 0, 200));
		return $this->fetch();
	}
}