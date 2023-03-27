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
use app\logic\common\FocusLogic;
use app\model\BrowseRecords;
use app\model\Topic as TopicModel;
use app\model\TopicMerge;
use WordAnalysis\Analysis;

/**
 * 话题模块
 * Class Topic
 * @package app\ask\controller
 */
class Topic extends Frontend
{
    protected $needLogin = [
        'manager',
        'remove_topic'
    ];

	public function initialize() {
		parent::initialize();
		$this->model = new TopicModel;
	}

    /**
     * 话题列表
     * @return mixed
     */
	public function index()
	{
        $type = $this->request->param('type','','trim');
        $pid = $this->request->param('pid',0,'intval');
        if($type=='discuss')
        {
            $order['discuss'] ='desc';
        }else if($type=='focus'){
            $order['focus'] ='desc';
        }else{
            $order['discuss_update'] ='desc';
        }
        $where[] = ['status','=',1];

        $parent_list = $this->model->where(['status'=>1,'is_parent'=>1,'pid'=>0])->column('id,title');

        if($pid){
            $pid = is_array($pid)?end($pid):$pid;
            $child_ids = TopicModel::getTopicWithChildIds($pid);
            $where[] =  $child_ids ? ['id','IN',$child_ids] : ['id','IN',$pid];
        }

		$list = $this->model->where($where)->order($order)->paginate(
            [
                'list_rows'=> 12,
                'page' => $this->request->param('page',1),
                'pjax'=>'tabMain',
                'query'=>request()->param(),
            ]
        );

		$page = $list->render();

		foreach ($list->all() as $key => $value)
		{
            $list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'topic', $value['id']) ? 1 : 0;
			$list[$key]['description'] = $value['description'] ? str_cut(strip_tags(htmlspecialchars_decode($value['description'])), 0, 45) : '';
		}

		$this->assign([
            'list'=> $list,
            'page'=>$page,
            'type'=>$type,
            'parent_list'=>$parent_list,
            'pid'=>$pid
        ]);
		return $this->fetch();
	}

	/**
	 * 话题详情
	 */
	public function detail()
	{
        hook('topic_detail_get_before',$this->request->param());

		$topic_id = $this->request->param('id', 0,'intval');
		$type = $this->request->param('type','','trim');
		$sort = $this->request->param('sort','new','trim');

        $topic_id = intval($topic_id);
        $topic_info = db('topic')->where(['id' => $topic_id])->find();

        hook('topic_detail_get_middle',['topic_info'=>$topic_info]);

        if (!$topic_info || $topic_info['status']!=1)
        {
            $this->error('话题不存在',url('index'));
        }

        //记录用户浏览记录
        BrowseRecords::recordViewLog($this->user_id,$topic_info['id'],'topic');

        //获取重定向
        $topic_info['redirect'] = TopicMerge::getTopicMerge($topic_info['id']);
        $target_topic = $redirect_message = [];
        if (isset($topic_info['redirect']['target_id']))
        {
            $target_topic = $this->model->where(['id' => $topic_info['redirect']['target_id']])->find();
        }

        $rf = $this->request->get('rf','');

        if (is_numeric($rf))
        {
            if ($from_topic = $this->model->where(['id' => $rf])->find())
            {
                $redirect_message[] = L('从话题 %s 合并而来', '<a href="' . (string)url('topic/detail',['id'=>$rf,'rf'=>'false']) . '">' . $from_topic['title'] . '</a>');
            }
        }

        if ($topic_info['redirect'] && !$rf)
        {
            if ($target_topic)
            {
                $this->redirect(url('topic/detail',['id'=>$topic_info['redirect']['target_id'],'rf'=>$topic_info['id']]));
            }
            else
            {
                $redirect_message[] = L('重定向目标话题已被删除, 将不再合并话题');
            }
        }

        if ($topic_info['redirect'])
        {
            if ($target_topic)
            {
                $message = L('此话题将合并至') . ' <a href="' . (string)url('topic/detail',['id'=>$topic_info['redirect']['target_id'],'rf'=> $topic_info['id']]) . '">' . $topic_info['title'] . '</a>';
                if ($this->user_id && (isSuperAdmin() || isNormalAdmin() || get_user_permission('merge_topic')))
                {
                    $message .= '&nbsp; (<a href="javascript:;" class="aw-ajax-get" data-url="'.(string)url('ajax.Topic/cancel_merge', ['item_id'=> $topic_info['id']]) . '">' . L('撤消话题合并') . '</a>)';
                }

                $redirect_message[] = $message;
            }
            else
            {
                $redirect_message[] = L('重定向目标话题已被删除, 将不再合并话题');
            }
        }

        $focus_user = TopicModel::getTopicFocusUser($topic_id);
		$topic_info['description'] = $topic_info['description'] ? htmlspecialchars_decode($topic_info['description']) : '';
		$topic_info['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'topic', $topic_info['id']) ? 1 : 0;
        $topic_info['relation_topics'] = TopicModel::getRelatedTopicBySourceId($topic_info['id']);

        $sort_texts = [
            'new'=>'最新排序',
            'hot'=>'热门排序',
            'recommend'=>'推荐内容',
            'unresponsive'=>'等待回答'
        ];
        $this->assign([
            'topic_info'=>$topic_info,
            'sort'=>$sort,
            'type'=>$type,
            'sort_texts'=>$sort_texts,
            'focus_user'=>$focus_user,
            'redirect_message'=>$redirect_message,
            'top_parent_topic'=>TopicModel::getParentTopic($topic_id),//根话题
            'parent_topic'=>$topic_info['pid'] ? db('topic')->where(['status'=>1,'id'=>$topic_info['pid']])->find() : [],//父话题
            'child_topics'=>TopicModel::getTopicByIds(TopicModel::getTopicWithChildIds($topic_info['id']))
        ]);

        $seo_title = $topic_info['seo_title'] ? : $topic_info['title'];
        $seo_keywords = $topic_info['seo_keywords'] ? : Analysis::getKeywords($topic_info['description'], 5);
        $seo_description = $topic_info['seo_description'] ? : str_cut(strip_tags($topic_info['description']),0,200);
        $this->TDK($seo_title, $seo_keywords, $seo_description);

        hook('topic_detail_get_after',$this->request->param());
		return $this->fetch();
	}

	//编辑话题
	public function manager()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			$postData['uid']=$this->user_id;
            if(!$postData['topic_id'])
            {
                $this->error('话题不存在');
            }

            if (!$info = $this->model->where(['id' => intval($postData['topic_id'])])->find()) {
                $this->error('话题不存在');
            }

            if($this->user_info['permission']['topic_manager']=='N')
            {
                $this->error('您没有编辑话题的权限');
            }

            if($info['lock'])
            {
                $this->error('话题已锁定，无法编辑该话题');
            }

			if(!TopicModel::updateTopic($postData,intval($postData['topic_id']),$this->user_id))
			{
				$this->error('更新失败');
			}
			$this->success('更新成功',url('topic/detail',['id'=>intval($postData['topic_id'])]));
		}

		$topic_id = $this->request->param('id', 0);
		if (!$info = $this->model->where(['id' => $topic_id])->find()) {
			$this->error('话题不存在');
		}

        if($this->user_info['permission']['topic_manager']=='N')
        {
            $this->error('您没有编辑话题的权限');
        }

        if($info['lock'])
        {
            $this->error('话题已锁定，无法编辑该话题');
        }

        $info['relation_topics'] = TopicModel::getRelatedTopicBySourceId($info['id']);
        $info['description'] = htmlspecialchars_decode($info['description']);
		$this->assign([
            'info'=>$info,
            'access_key'=>md5($this->user_id.'_'.time()),
        ]);
		return $this->fetch();
	}

    /**
     * 话题选择
     * @param $item_type
     * @param int $item_id
     * @return mixed
     */
    public function select($item_type, int $item_id = 0)
    {
        if ($this->request->isPost()) {
            $topics = $this->request->post('tags');
            if (get_setting('topic_enable')=='Y' && empty($topics)) {
                $this->error(L('请至少设置一个话题'));
            }

            if(get_setting('topic_enable')=='Y' && get_setting('max_topic_select')<count($topics))
            {
                $this->error('您最多只可设置'.get_setting('max_topic_select').'个话题');
            }

            TopicModel::updateRelation($item_type, $item_id, $topics, $this->user_id);
            if(!empty($topics))
            {
                $list = TopicModel::getTopicByIds($topics);
                $this->result(['list' => $list, 'total' => count($list)], 1, '保存成功');
            }
            $this->result(['list' => [], 'total' => 0], 1, '保存成功');
        }
        //最近使用话题
        $recent_topic_list = TopicModel::getRecentTopic($this->user_id,$item_type,$item_id);
        $topics = TopicModel::getTopicByItemType($item_type, $item_id);
        //推荐话题
        $this->assign([
            'recent_topic_list'=>$recent_topic_list,
            'item_type'=>$item_type,
            'item_id'=>$item_id,
            'topics'=>$topics?:[]
        ]);
        return $this->fetch();
    }
}