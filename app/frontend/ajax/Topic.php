<?php
namespace app\frontend\ajax;
use app\common\controller\Frontend;
use app\model\Topic as TopicModel;

class Topic extends Frontend
{
    protected $needLogin=[
        'merge_topic',
        'remove_topic'
    ];

    public function merge_topic()
    {
        if($this->request->isPost()){
            $item_id = $this->request->param('item_id',0,'intval');
            $target_id = $this->request->param('target_id',0,'intval');
            if(!$item_id || !$target_id) $this->error('请求参数不正确');
            if($item_id==$target_id) $this->error('不可合并到话题本身');

            if(db('topic_merge')->where([
                'source_id'=>$item_id,
                'target_id'=>$target_id,
            ])->value('id')) $this->error('该话题已被合并啦...');
            db('topic_merge')->insert([
                'source_id'=>$item_id,
                'target_id'=>$target_id,
                'uid'=>$this->user_id,
                'create_time'=>time()
            ]);
            $this->success('重定向成功',url('topic/detail',['id'=>$target_id,'rf'=>$item_id]));
        }

        $item_id = $this->request->param('item_id',0,'intval');
        $this->assign([
            'item_id'=>$item_id,
        ]);
        return $this->fetch();
    }

    //取消合并
    public function cancel_merge()
    {
        $item_id = $this->request->param('item_id',0,'intval');
        if(db('topic_merge')->where([
            'source_id'=>$item_id,
        ])->delete())
        {
            $this->success('话题合并撤销成功');
        }
        $this->error('话题合并撤销失败');
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

}