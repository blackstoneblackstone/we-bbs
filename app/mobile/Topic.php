<?php
namespace app\mobile;
use app\common\controller\Frontend;
use app\logic\common\FocusLogic;
use app\model\Topic as TopicModel;
use WordAnalysis\Analysis;

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
     */
    public function index()
    {
        $type = $this->request->param('type','new','trim');
        $pid = $this->request->param('pid',0,'intval');
        $parent_list = $this->model->where(['status'=>1,'is_parent'=>1,'pid'=>0])->column('id,title');

        $this->assign([
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
        $topic_id = $this->request->param('id', 0,'intval');
        $type = $this->request->param('type','','trim');
        $sort = $this->request->param('sort','all','trim');
        $topic_info = db('topic')->where(['id' => $topic_id])->find();

        if (!$topic_info || $topic_info['status']!=1)
        {
            $this->error('话题不存在',url('index'));
        }

        $focus_user = TopicModel::getTopicFocusUser($topic_id);
        $topic_info['description'] = $topic_info['description'] ? htmlspecialchars_decode($topic_info['description']) : '';
        $topic_info['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'topic', $topic_info['id']) ? 1 : 0;
        $this->assign('focus_user',$focus_user);
        $this->assign('type',$type);
        $this->assign('sort',$sort);
        $this->assign('topic_info', $topic_info);

        $seo_title = $topic_info['seo_title'] ? : $topic_info['title'];
        $seo_keywords = $topic_info['seo_keywords'] ? : Analysis::getKeywords($topic_info['description'], 5);
        $seo_description = $topic_info['seo_description'] ? : str_cut(strip_tags($topic_info['description']),0,200);
        $this->TDK($seo_title, $seo_keywords, $seo_description);
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
        //推荐话题
        $this->assign([
            'recent_topic_list'=>$recent_topic_list,
            'item_type'=>$item_type,
            'item_id'=>$item_id
        ]);
        return $this->fetch();
    }

    /**
     * 删除话题
     */
    public function remove_topic()
    {
        $id = $this->request->param('id');
        if($this->user_id && ($this->user_info['group_id']===1 || $this->user_info['group_id']===2))
        {
            TopicModel::removeTopic((int)$id);
            $this->success('删除成功');
        }
        $this->error('您没有删除话题的权限');
    }

    /**
     * 获取话题日志
     * @return mixed
     */
    public function logs()
    {
        $id = $this->request->param('id',0,'intval');
        $data=TopicModel::getLogs($id,$this->user_id,$this->request->get('page',1,'intval'),10);
        $this->assign($data);
        return $this->fetch();
    }

    //编辑话题
    public function manager()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $postData['uid']=$this->user_id;

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

        $info['description'] = htmlspecialchars_decode($info['description']);
        $this->assign([
            'info'=>$info,
            'access_key'=>md5($this->user_id.'_'.time()),
        ]);
        return $this->fetch();
    }

}