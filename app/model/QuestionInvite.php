<?php
namespace app\model;

class QuestionInvite extends BaseModel
{
    /**
     * 获取推荐邀请用户列表
     * @param $uid
     * @param int $question_id
     * @param int $page
     * @return array
     */
    public static function getRecommendInviteUsers($uid, int $question_id=0, int $page=1): array
    {
        $data = [];
        //获取问题关联的话题
        $question_topic = db('topic_relation')
            ->where(['item_id'=>$question_id,'item_type'=>'question','status'=>1])
            ->column('topic_id');
        if($question_topic)
        {
            $question_topic = array_column($question_topic,'topic_id');

            //获取在该问题话题中的相关问题
            $relation_question_ids = db('topic_relation')
                ->whereIn('topic_id',$question_topic)
                ->where([['item_id','<>',$question_id],['item_type','=','question'],['status','=',1]])
                ->column('item_id,uid');

            $relation_question_ids = array_unique(array_column($relation_question_ids,'item_id'));
            $relation_question_uid = array_unique(array_column($relation_question_ids,'uid'));
            if($relation_question_ids)
            {
                //获取在这些问题回答中的用户
                $answer_user_ids = db('answer')
                    ->whereIn('question_id',$relation_question_ids)
                    ->where('status',1)
                    ->page($page,10)
                    ->column('uid');
                if($answer_user_ids)
                {
                    $answer_user_ids = array_unique(array_column($answer_user_ids,'uid'));
                    $data = db('users')
                        ->whereIn('uid',$answer_user_ids)
                        ->where(['status'=>1])
                        ->order(['power'=>'DESC'])
                        ->field('uid,nick_name,user_name,avatar')
                        ->paginate([
                            'list_rows'=> 10,
                            'page' => $page,
                        ])->toArray();
                    foreach ($data['data'] as $key=>$val)
                    {
                        $data['data'][$key]['has_invite'] = 0;
                        $data['data'][$key]['remark'] = '最近回答过该领域问题';
                        if( db('question_invite')->where(['sender_uid'=>$uid,'recipient_uid'=>$val['uid'],'question_id'=>intval($question_id)])->value('id'))
                        {
                            $data['data'][$key]['has_invite'] = 1;
                        }
                    }
                }else{
                    $data = db('users')
                        ->whereIn('uid',$relation_question_uid)
                        ->where(['status'=>1])
                        ->order(['power'=>'DESC'])
                        ->field('uid,nick_name,user_name,avatar')
                        ->paginate([
                            'list_rows'=> 10,
                            'page' => $page,
                        ])->toArray();
                    foreach ($data['data'] as $key=>$val)
                    {
                        $data['data'][$key]['has_invite'] = 0;
                        $data['data'][$key]['remark'] = '可能对该问题感兴趣';
                        if( db('question_invite')->where(['sender_uid'=>$uid,'recipient_uid'=>$val['uid'],'question_id'=>intval($question_id)])->value('id'))
                        {
                            $data['data'][$key]['has_invite'] = 1;
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 获取已邀请用户
     */
    public static function getInvitedUsers($question_id=0)
    {
        $uid_list = db('question_invite')->where(['question_id'=>intval($question_id)])->column('recipient_uid');
        if(!$uid_list) return false;
        $users = Users::getUserInfoByIds($uid_list);
        foreach ($users as $key=>$val)
        {
            $users[$key]['has_invite'] = 1;
        }
        return  $users;
    }
}