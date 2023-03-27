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

namespace app\model;

use think\facade\Db;
use think\Model;

class Common extends BaseModel
{
    /**
     * 检查收藏
     * @param $where
     * @return mixed
     */
	public static function checkFavorite($where){
		return db('users_favorite')->where($where)->find();
	}

    /**
     * 检查赞踩
     * @param $where
     * @param $type
     * @return array|Model|null
     */
	public static function checkVote($where,$type){
		switch ($type) {
			case 'question_comment':
				$info=db('question_vote')->where($where)->find();
				break;
			
			default:

				break;
		}
		return $info;
	}

    /**
     * 获取用户关注
     * @param $uid
     * @param $type
     * @param $page
     * @param $per_page
     * @param $pjax
     * @return false
     */
	public static function getUserFocus($uid, $type, $page = 1, $per_page = 10, $pjax = '')
    {
        if(!$uid || !$type) return false;
        $dbName = 'question_focus';
        $where = [];
        switch ($type)
        {
            case 'question':
                $where['uid'] = $uid;
                break;

            case 'friend':
                $dbName = 'users_follow';
                $where = ['fans_uid'=>$uid];
                break;

            case 'fans':
                $dbName = 'users_follow';
                $where = ['friend_uid'=>$uid];
                break;

            case 'column':
                $dbName = 'column_focus';
                $where = ['uid'=>$uid];
                break;

            case 'topic':
                $dbName = 'topic_focus';
                $where = ['uid'=>$uid];
                break;

            case 'favorite':
                $dbName = 'favorite_focus';
                $where = ['uid'=>$uid];
                break;
        }
        $paginate = [
            'list_rows'=> $per_page,
            'page' => $page,
            'query'=>request()->param()
        ];
        if ($pjax) $paginate['pjax'] = $pjax;
        $result = db($dbName)
            ->where($where)
            ->paginate($paginate);
        $page_render = $result->render();
        $result = $result->toArray();
        $result['page'] = $page_render;

        if($result['data'])
        {
            foreach ($result['data'] as $key=>$val)
            {
                switch ($type)
                {
                    case 'question':
                        $question_ids = array_column($result['data'],'question_id');
                        $question_infos = Question::getQuestionByIds($question_ids);
                        if(!empty($question_infos) && isset($question_infos[$val['question_id']]))
                        {
                            $result['data'][$key] = $question_infos[$val['question_id']];
                            $result['data'][$key]['user_info'] = Users::getUserInfo($question_infos[$val['question_id']]['uid']);
                            $result['data'][$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($question_infos[$val['question_id']]['detail'])),0,150);
                            $result['data'][$key]['topics'] = Topic::getTopicByItemType('question',$val['question_id']);
                            $result['data'][$key]['vote_value'] =  Vote::getVoteByType($val['question_id'],'question',$uid);
                            $result['data'][$key]['item_id'] =  $val['question_id'];
                        }else{
                            unset($result['data'][$key]);
                        }
                        break;

                    case 'friend':
                        $uid_s = array_column($result['data'],'friend_uid');
                        $user_infos = Users::getUserInfoByIds($uid_s);
                        if(!empty($user_infos))
                        {
                            $result['data'][$key]['user_info'] = $user_infos[$val['friend_uid']];
                            $result['data'][$key]['item_id'] =  $val['friend_uid'];
                        }
                        break;

                    case 'fans':
                        $uid_s = array_column($result['data'],'fans_uid');
                        $user_infos = Users::getUserInfoByIds($uid_s);
                        $result['data'][$key]['user_info'] = $user_infos[$val['fans_uid']] ?? [];
                        $result['data'][$key]['item_id'] =  $val['fans_uid'];
                        break;

                    case 'column':
                        $column_ids = array_column($result['data'],'column_id');
                        $column_infos = Column::getColumnByIds($column_ids);
                        $result['data'][$key] = $column_infos[$val['column_id']];
                        $result['data'][$key]['item_id'] =  $val['column_id'];
                        break;

                    case 'topic':
                        $topic_ids = array_column($result['data'],'topic_id');
                        $topic_infos = Topic::getTopicByIds($topic_ids);
                        $result['data'][$key] = $topic_infos[$val['topic_id']];
                        $result['data'][$key]['uid'] = $val['uid'];
                        $result['data'][$key]['item_id'] = $val['topic_id'];
                        break;
                }
            }
        }

        return $result;
    }

    /**
     * 获取热搜记录
     * @param $page
     * @param $per_page
     * @return mixed
     */
    public static function getHotSearchList($page = 1, $per_page = 10)
    {
        $sql = 'select keyword,count(*) from '.get_table('search_log').' group by keyword order by count(*) DESC limit '.$page.','.$per_page;
        return db()->query($sql);
    }
}