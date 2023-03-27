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

namespace app\mobile;
use app\common\controller\Frontend;
use app\model\Topic;

class Extend extends Frontend
{
    public function index()
    {
        $topic_list = Topic::getHotTopics($this->user_id,[],[],get_setting('contents_per_page',15));
        foreach ($topic_list['data'] as $key=>$val)
        {
            $topic_list['data'][$key]['relation_list'] = Topic::getTopicRelationList($this->user_id,$val['id'],null,1,3);
        }
        $this->assign('topic_list',$topic_list['data']);
        return $this->fetch();
    }
}