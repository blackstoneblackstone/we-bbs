<?php
namespace app\model;

class TopicMerge extends BaseModel
{
    public static function getTopicMerge($item_id)
    {
        if(!$item_id) return false;
        return db('topic_merge')->where(['source_id'=>$item_id])->order('id','desc')->find();
    }
}