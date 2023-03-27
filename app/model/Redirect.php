<?php
namespace app\model;

class Redirect extends BaseModel
{
    public static function getRedirect($item_id)
    {
        if(!$item_id) return false;
        return db('question_redirect')->where(['item_id'=>$item_id])->order('id','desc')->find();
    }
}