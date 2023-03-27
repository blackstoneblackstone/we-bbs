<?php
namespace app\model;

use app\common\library\helper\ImageHelper;
use app\logic\common\FocusLogic;

class Feature extends BaseModel
{
    /**
     * 获取专题列表
     * @param $where
     * @param $order
     * @param $page
     * @param $per_page
     * @param $pjax
     * @return array
     */
    public static function getFeatureList($where=[],$order=[],$page=1,$per_page=10,$pjax='tabMain')
    {
        $list = db('feature')
            ->where($where)
            ->order($order)
            ->paginate(
                [
                    'list_rows' => $per_page,
                    'page' => $page,
                    'query' => request()->param(),
                    'pjax' => $pjax
                ]
            );
        $pageVar = $list->render();
        $data = $list->toArray();
        foreach ($data['data'] as $k=>$v)
        {
            $data['data'][$k]['topics'] = self::getRelationTopicByFeatureId($v['id'],4);
        }
        return ['list' => $data['data'], 'page' => $pageVar, 'total' => $data['last_page']];
    }

    private static function getRelationTopicByFeatureId($feature_id,$limit)
    {
        $topic_ids = db('feature_topic')
            ->where(['feature_id'=>$feature_id])
            ->limit($limit)
            ->column('topic_id');

        return Topic::getTopicByIds($topic_ids);
    }

    /**
     * 获取关联话题内容
     * @param $uid
     * @param $feature_id
     * @param $sort
     * @param $page
     * @param $per_page
     * @param $pjax
     * @return array|false|mixed|object|null
     */
    public static function getRelationFeatureList($uid,$feature_id,$sort='hot',$page=1,$per_page=10,$pjax='tabMain')
    {
        $topic_ids = db('feature_topic')
            ->where(['feature_id'=>$feature_id])
            ->column('topic_id');
        if(!$topic_ids) return [];

        if($sort=='best')
        {
            $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
            $topic_where ='item_type="question" AND status=1';
            $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
            $questionIds = db('topic_relation')
                ->whereRaw($topic_where.$topicIdsWhere)
                ->column('item_id');
            $questions_list = db('question')
                ->where(['status'=>1,'question_type'=>'normal'])
                ->where('best_answer','>',0)
                ->whereIn('id',$questionIds)
                ->paginate(
                    [
                        'list_rows' => $per_page,
                        'page' => $page,
                        'query' => request()->param(),
                        'pjax' => $pjax
                    ]
                );
            $pageVar = $questions_list->render();
            $allList = $questions_list->toArray();
            $result = $question_ids = $data_list_uid = $topic_infos = array();

            if ($allList['data'])
            {
                foreach ($allList['data'] AS $key => $val)
                {
                    $val['detail'] = htmlspecialchars_decode($val['detail']);
                    $val['title'] = htmlspecialchars_decode($val['title']);
                    $result[$val['id']] = $val;
                    $question_ids[]=$val['id'];
                    $data_list_uid[$val['uid']] = $val['uid'];
                }
            }

            $last_answers = Answer::getBestAnswerByQuestionIds($question_ids)?:[];
            if ($last_answers)
            {
                foreach ($last_answers as $key => $val)
                {
                    $data_list_uid[$val['uid']] = $val['uid'];
                }
            }

            $topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
            $users_info = Users::getUserInfoByIds($data_list_uid,'user_name,avatar,nick_name,uid',99);

            $result_list = [];
            foreach ($result as $key=>$data) {
                $result_list[$key] = $data;
                $result_list[$key]['answer_info'] = $last_answers[$data['id']] ?? false;
                //是否已回答
                $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$data['id'],'status'=>1])->value('id') : 0;

                //回答用户
                $answerUidLists = db('answer')->where(['question_id'=>$data['id'],'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
                $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];

                if($result_list[$key]['answer_info']){
                    $result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$data['id']]['uid']]??['url'=>'javascript:;','uid'=>0,'name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
                    $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($last_answers[$data['id']]['id'],'answer',$uid);
                }
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'question',$data['id']);
                $detail = $result_list[$key]['answer_info'] ? $result_list[$key]['answer_info']['content'] : $result_list[$key]['detail'];
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
                $result_list[$key]['detail'] = $result_list[$key]['answer_info'] ?  ($result_list[$key]['answer_info']['is_anonymous'] ? '<a href="javascript:;" class="aw-username" >匿名用户</a> :' : '<a href="'.$result_list[$key]['answer_info']['user_info']['url'].'" class="aw-username" >'.$result_list[$key]['answer_info']['user_info']['name'].'</a> :').str_cut(strip_tags($result_list[$key]['answer_info']['content']),0,150) : str_cut(strip_tags($result_list[$key]['detail']),0,150);
                $cover = ImageHelper::srcList($detail);
                $result_list[$key]['img_list'] = $cover;
                $result_list[$key]['item_type'] = 'question';
                $result_list[$key]['topics'] = $topic_infos['question'][$data['id']] ?? [];
                $result_list[$key]['user_info'] = $users_info[$data['uid']] ?? ['url'=>'javascript:;','uid'=>0,'name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
                $result_list[$key]['item_id'] = intval($data['id']);
            }

            return ['list'=>$result_list,'page'=>$pageVar,'total'=>$allList['last_page']];
        }

        return PostRelation::getPostRelationList($uid,null,$sort,$topic_ids,null,$page,$per_page,0,$pjax);
    }

}