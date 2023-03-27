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
namespace app\logic\common;

use app\common\library\helper\LogHelper;
use app\model\Column;
use app\model\Question;
use app\model\Topic;
use app\model\Users;

class FocusLogic
{
    public static $error;

    /**
     * 更新全局关注
     * @param $item_id
     * @param $item_type
     * @param $uid
     * @return array|false
     */
    public static function updateFocusAction($item_id,$item_type,$uid)
    {
        if(!$item_id || !$item_type || !$uid)
        {
            self::setError('请求参数不正确');
            return false;
        }

        if(!in_array($item_type,['question','topic','user','column','favorite']))
        {
            self::setError('请求参数不正确');
            return false;
        }

        $dbName = 'question_focus';
        $where = $insertData = [];
        $anonymous = 0;
        switch ($item_type)
        {
            case 'question':
                $where['uid'] = (int)$uid;
                $where['question_id']=(int)$item_id;
                $insertData = [
                    'question_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                $anonymous = db('question')->where(['id'=>$item_id,'status'=>1])->value('is_anonymous');
                break;
            case 'topic':
                $dbName = 'topic_focus';

                $where['uid'] = (int)$uid;
                $where['topic_id']=(int)$item_id;
                $insertData = [
                    'topic_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'user':
                $dbName = 'users_follow';

                $where['fans_uid'] = (int)$uid;
                $where['friend_uid'] = (int)$item_id;
                $insertData = [
                    'friend_uid'=>(int)$item_id,
                    'fans_uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'column':
                $dbName = 'column_focus';
                $where['uid'] = (int)$uid;
                $where['column_id'] = (int)$item_id;
                $insertData = [
                    'column_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
            case 'favorite':
                $dbName = 'favorite_focus';
                $where['uid'] = (int)$uid;
                $where['tag_id'] = (int)$item_id;
                $insertData = [
                    'tag_id'=>(int)$item_id,
                    'uid'=>(int)$uid,
                    'create_time'=>time()
                ];
                break;
        }

        if(db($dbName)->where($where)->value('id'))
        {
            if(!db($dbName)->where($where)->delete())
            {
                self::setError('取消关注失败');
                return false;
            }
            //删除行为日志
            LogHelper::removeActionLog($item_type,$item_id,'focus_'.$item_type,true);

            $count = self::updateFocusCount($item_id,$item_type);
            return ['count'=>$count,'type'=>'un_focus'];
        }

        if(!db($dbName)->insertGetId($insertData))
        {
            self::setError('关注失败');
            return false;
        }
        LogHelper::addActionLog('focus_'.$item_type,$item_type,$item_id,$uid,$anonymous);
        if ($item_type=='user'){
            send_notify($uid,$item_id,'TYPE_PEOPLE_FOCUS_ME','',$item_id);
        }

        $count = self::updateFocusCount($item_id,$item_type);
        return ['count'=>$count,'type'=>'focus'];
    }

    /**
     * 检查用户是否关注过
     * @param mixed $uid
     * @param mixed $item_type
     * @param int $item_id
     * @return mixed
     */
    public static function checkUserIsFocus($uid=null,$item_type=null, int $item_id=0)
    {
        if(!$item_id || !$item_type || !$uid)
        {
            self::setError('请求参数不正确');
            return false;
        }
        $where = [];
        $dbName = '';

        switch ($item_type)
        {
            case 'question':
                $dbName = 'question_focus';
                $where['uid'] = (int)$uid;
                $where['question_id']=$item_id;
                break;
            case 'topic':
                $dbName = 'topic_focus';
                $where['uid'] = (int)$uid;
                $where['topic_id']=(int)$item_id;
                break;

            case 'fans':
                $dbName = 'users_follow';
                $where['friend_uid'] = (int)$uid;
                $where['fans_uid'] = (int)$item_id;
                break;

            case 'user':
            case 'friend':
                $dbName = 'users_follow';
                $where['fans_uid'] = (int)$uid;
                $where['friend_uid'] = (int)$item_id;
                break;
            case 'column':
                $dbName = 'column_focus';
                $where['uid'] = (int)$uid;
                $where['column_id'] = (int)$item_id;
                break;

            case 'favorite':
                $dbName = 'users_favorite_focus';
                $where['uid'] = (int)$uid;
                $where['tag_id'] = (int)$item_id;
                break;
        }
        return db($dbName)->where($where)->value('id');
    }

    /**
     * 更新关注数量
     * @param $item_id
     * @param $item_type
     * @return int
     */
    public static function updateFocusCount($item_id,$item_type): int
    {
        $count = 0;
        switch ($item_type)
        {
            case 'question':
                $dbName = 'question_focus';
                $countWhere = ['question_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                Question::update(['focus_count'=>$count],['id'=>$item_id]);
                break;
            case 'topic':
                $dbName = 'topic_focus';
                $countWhere =['topic_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                Topic::update(['focus'=>$count],['id'=>$item_id]);
                break;
            case 'user':
                $dbName = 'users_follow';
                $countWhere =['fans_uid'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                $fans_count = db($dbName)->where(['friend_uid'=>(int)$item_id])->count();
                Users::updateUserFiled($item_id,['friend_count'=>$count,'fans_count'=>$fans_count]);
                break;
            case 'column':
                $dbName = 'column_focus';
                $countWhere =['column_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                Column::update(['focus_count'=>$count],['id'=>(int)$item_id]);
                break;
            case 'favorite':
                $dbName = 'users_favorite_focus';
                $countWhere =['tag_id'=>(int)$item_id];
                $count = db($dbName)->where($countWhere)->count();
                db('favorite_tag')->where(['id'=>$item_id])->update(['focus_count'=>$count]);
                break;
        }

        return $count;
    }

    /**
     * 获取用户关注内容
     * @param $uid
     * @param array $type
     * @param int $page
     * @param int $per_page
     * @param string $pjax_page
     * @return array
     */
    public static function getUserFocus($uid,$type=[],$page=1,$per_page=10,$pjax_page='')
    {
        $where['status'] = 1;
        $where['fans_uid'] = intval($uid);
        $friend_uid = db('users_follow')->where($where)->column('friend_uid');
        $friend_uid[] = $uid;
        return LogHelper::getActionLogList($type,$friend_uid,$uid,$page,$per_page,$pjax_page);
    }

    /**
     * 设置错误信息
     * @param $error
     * @return mixed
     */
    public static function setError($error)
    {
        return self::$error = $error;
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public static function getError() {
        return self::$error;
    }
}