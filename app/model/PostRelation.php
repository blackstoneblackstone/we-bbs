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
use app\common\library\helper\ImageHelper;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use think\Model;

class PostRelation extends Model
{
	//添加数据到聚合表
	public static function savePostRelation($item_id, $item_type, $data = [])
	{
		$result = array();
		if (!$data)
		{
            $relation = config('aws.relation');
            if(in_array($item_type,$relation))
            {
                $result = db($relation[$item_type])->where(['id'=>$item_id])->find();
            }else{
                $result = db($item_type)->where(['id'=>$item_id])->find();
            }
            $data = array(
                'item_id' => intval($item_id),
                'item_type' => $item_type,
                'create_time' => $result['create_time']??0,
                'update_time' => $result['update_time']??0,
                'category_id' => $result['category_id']??0,
                'is_recommend' => $result['is_recommend']??0,
                'view_count' => $result['view_count']??0,
                'is_anonymous' => $result['is_anonymous']??0,
                'popular_value' => $result['popular_value']??0,
                'uid' => $result['uid']??0,
                'agree_count' => $result['agree_count']??0,
                'answer_count' => $result['answer_count']??0,
                'status' =>$result['status']??0
            );
		}
		db('post_relation')->where(['item_id'=>$item_id, 'item_type'=>$item_type])->delete();
		return db('post_relation')->insertGetId($data);
	}

    /**
     * 获取聚合数据列表
     * @param null $uid
     * @param null $item_type
     * @param null $sort
     * @param null $topic_ids
     * @param null $category_id
     * @param int $page
     * @param int $per_page
     * @param int $relation_uid
     */
	public static function getPostRelationList($uid=null,$item_type=null, $sort = null, $topic_ids = null, $category_id = null,$page=1, $per_page=0,$relation_uid=0,$pjax='tabMain')
    {
        $sort = is_array($sort)?end($sort) : $sort;
        $item_type = is_array($item_type)?end($item_type) : $item_type;
        $uid = is_array($uid)?end($uid) : $uid;
        $per_page = $per_page ?:intval(get_setting('contents_per_page'));
        $result_list = [];
        $key = $sort.'-'.$category_id.'-'.$item_type.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page.'-'.$relation_uid;
        $cache_key = 'cache_explore_data_'.$key;
        if($cache_explore_time = get_setting('cache_explore_time'))
        {
            $result_list = cache($cache_key);
        }
        if($result_list) return $result_list;

		$order = $where = array();
        $order['set_top_time'] = 'DESC';
		$where[] = ['status','=',1];
		if($relation_uid)
		{
			$where[] = ['uid','=',$relation_uid];
		}

        $relation = config('aws.relation');
        $item_types = $relation ? array_merge(['question','article'],$relation) : ['question','article'];

        if($item_type && !in_array($item_type,$item_types))
        {
            return false;
        }

		//关注单独处理
		if($sort == 'focus') {
            return LogHelper::getUserFocusLogList($uid, 'all', $page, $per_page, $pjax);
        }

        //推荐内容
        if($sort=='recommend')
        {
            return self::getRecommendPost($uid,$item_type, $topic_ids, $category_id,intval($page), intval($per_page),intval($relation_uid),$pjax);
        }

        if($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
            $item_type = 'question';
        }

        if($sort=='hot')
        {
            $order['popular_value'] = 'desc';
            $where[] = ['popular_value','>',get_setting('content_popular_value_show',0)];
        }

        if($sort=='new'){
            $order['set_top_time'] = 'DESC';
            $order['update_time'] = 'DESC';
        }

        $order['create_time'] = 'DESC';

        if(!$item_type)
        {
            $where[] = ['item_type','IN', $item_types];
        }

		if ($item_type)
		{
			$where[] = ['item_type','=', $item_type];
		}

        if ($category_id)
        {
            $category_ids = Category::getCategoryWithChildIds($category_id,true);
            if($category_ids)
            {
                $where[] = ['category_id','in', implode(',',$category_ids )];
            }else{
                $where[] = ['category_id','=', $category_id];
            }
        }

		if ($topic_ids)
		{
			$topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
			$topic_where = $item_type ? 'item_type="'.$item_type.'" AND status=1' : 'status=1';
			$topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
			$relationInfo = db('topic_relation')
				->whereRaw($topic_where.$topicIdsWhere)
				->column('item_id,item_type');

            if(!$relationInfo)
            {
                $result_list['list'] = [];
                $result_list['page'] = '';
                $result_list['total'] = 1;
                return $result_list;
            }

            $post_ids = $post_id_where = [];
            $whereRaw = '';

            foreach ($relationInfo as $val)
            {
                $post_ids[$val['item_type']][$val['item_id']] = $val['item_id'];
            }

            foreach ($post_ids AS $key => $val)
            {
                $post_id_where[] = "(item_id IN (" . implode(',', $val) . ") AND item_type = '" . $key . "')";
            }

            if ($post_id_where)
            {
                $whereRaw = '(' . implode(' OR ', $post_id_where) . ')';
            }

            if($whereRaw)
            {
                $list = db('post_relation')
                    ->where($where)
                    ->whereRaw($whereRaw)
                    ->order($order)
                    ->field('id,item_id,item_type,uid')
                    ->paginate([
                        'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                        'page' => $page,
                        'query'=>request()->param(),
                        'pjax'=>$pjax
                    ]);
                $pageVar = $list->render();
                $allList = $list->toArray();
                $list = self::processPostList($allList['data'],$uid,$sort);
                $result_list['list'] = $list;
                $result_list['page'] = $pageVar;
                $result_list['total'] = $allList['last_page'];
                if($cache_explore_time)
                {
                    cache($cache_key,$result_list,['expire'=>$cache_explore_time*60]);
                }

                return $result_list;
            }
        }

        $list = db('post_relation')
            ->where($where)
            ->order($order)
            ->field('id,item_id,item_type,uid')
            ->paginate([
                'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                'page' => $page,
                'query'=>request()->param(),
                'pjax'=>$pjax
            ]);

		$pageVar = $list->render();
		$allList = $list->toArray();
		$list = self::processPostList($allList['data'],$uid,$sort);
        $result_list['list'] = $list;
        $result_list['page'] = $pageVar;
        $result_list['total'] = $allList['last_page'];
        if($cache_explore_time)
        {
            cache($cache_key,$result_list,['expire'=>$cache_explore_time*60]);
        }

        return $result_list;
	}

    /**
     * 获取置顶数据
     * @param null $uid
     * @param null $item_type
     * @param null $category_id
     * @return array[]|bool[]
     */
    public static function getPostTopList($uid=null,$item_type=null, $category_id = null): array
    {
        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        $where[] = ['set_top','=', 1];

        if ($item_type)
        {
            $where[] = ['item_type','=', $item_type];
        }

        if ($category_id)
        {
            $category_ids = Category::getCategoryWithChildIds($category_id,true);
            if($category_ids)
            {
                $where[] = ['category_id','in', implode(',',$category_ids )];
            }else{
                $where[] = ['category_id','=', $category_id];
            }
        }

        $list = db('post_relation')->where($where)->order($order)->select()->toArray();
        return self::processPostList($list,$uid,$sort);
    }

    /**
     * 通用解析聚合数据列表
     */
	public static function processPostList($contents,$uid,$sort='')
	{
		if (!$contents) {
            return [];
        }
        $result_list = [];
        $last_answers = $topic_infos = $question_ids = $article_ids = $data_list_uid = $question_infos = $article_infos =  array();
		foreach ($contents as $data)
		{
            if($data['item_type']=='question')
            {
                $question_ids[] = $data['item_id'];
            }elseif($data['item_type']=='article')
            {
                $article_ids[] = $data['item_id'];
            }

			$data_list_uid[$data['uid']] = $data['uid'];
		}

		if ($question_ids)
		{
            $last_answers = $sort=='hot'?Answer::getHotAnswerByIds($question_ids):Answer::getLastAnswerByIds($question_ids);

			if ($last_answers)
			{
				foreach ($last_answers as $key => $val)
				{
					$data_list_uid[$val['uid']] = $val['uid'];
				}
			}
			$topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
			$question_infos = Question::getQuestionByIds($question_ids);
		}

		if ($article_ids)
		{
			$topic_infos['article'] = Topic::getTopicByItemIds($article_ids, 'article');
			$article_infos = Article::getArticleByIds($article_ids);
		}
		$users_info = Users::getUserInfoByIds($data_list_uid,'user_name,avatar,nick_name,uid',99);

		foreach ($contents as $key => $data)
		{
            if($data['item_type']=='question')
            {
                if($question_infos && isset($question_infos[$data['item_id']]))
                {
                    $result_list[$key] = $question_infos[$data['item_id']];
                    $result_list[$key]['answer_info'] = $last_answers[$data['item_id']] ?? false;

                    //是否已回答
                    $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$data['item_id'],'status'=>1])->value('id') : 0;

                    //回答用户
                    $answerUidLists = db('answer')->where(['question_id'=>$data['id'],'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
                    $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];

                    if($result_list[$key]['answer_info']){
                        $result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$data['item_id']]['uid']]??['url'=>'javascript:;','uid'=>0,'name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
                        $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($last_answers[$data['item_id']]['id'],'answer',$uid);
                        $result_list[$key]['answer_info']['content'] = str_cut(strip_tags($result_list[$key]['answer_info']['content']),0,150);
                        $result_list[$key]['answer_info']['img_list'] = ImageHelper::srcList($result_list[$key]['answer_info']['content']);
                    }

                    $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'question',$data['item_id']);

                    //$detail = $result_list[$key]['answer_info'] ? $result_list[$key]['answer_info']['content'] : $result_list[$key]['detail'];
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($data['item_id'],'question',$uid);

                    $result_list[$key]['detail'] = str_cut(strip_tags($result_list[$key]['detail']),0,150);
                    $cover = ImageHelper::srcList($result_list[$key]['detail']);

                    $result_list[$key]['img_list'] = $cover;
                    $result_list[$key]['item_type'] = 'question';
                    $result_list[$key]['topics'] = $topic_infos['question'][$data['item_id']] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$data['uid']] ?? ['url'=>'javascript:;','uid'=>0,'name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
                    $result_list[$key]['item_id'] = intval($data['item_id']);
                    $result_list[$key]['item_type'] = $data['item_type'];
                    $result_list[$key]['post_id'] = intval($data['id']);
                }
            }

            if($data['item_type']=='article')
            {
                if($article_infos && isset($article_infos[$data['item_id']]))
                {
                    $result_list[$key] = $article_infos[$data['item_id']];
                    if(isset($article_infos[$data['item_id']]['paid_read']) && $article_infos[$data['item_id']]['paid_read'])
                    {
                        $result_list[$key]['message'] = str_cut(strip_tags($result_list[$key]['message']),0,$article_infos[$data['item_id']]['free_words']);
                    }else{
                        $result_list[$key]['message'] = str_cut(strip_tags($result_list[$key]['message']),0,120);
                    }

                    $result_list[$key]['item_type'] = 'article';
                    $cover  = ImageHelper::srcList($article_infos[$data['item_id']]['message']);
                    $result_list[$key]['img_list'] = $cover;
                    $result_list[$key]['topics'] = $topic_infos['article'][$data['item_id']] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$data['uid']] ?? ['url'=>'javascript:;','uid'=>0,'name'=>'未知用户'];
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($data['item_id'],'article',$uid);
                    $result_list[$key]['item_id'] = intval($data['item_id']);
                    $result_list[$key]['item_type'] = $data['item_type'];
                    $result_list[$key]['post_id'] = intval($data['id']);
                }
            }

            $data['key'] = $key;
            $result = hook('post_relation_'.$data['item_type'],$data);
            if($result)
            {
                $result = json_decode($result,true);
                $result_list = array_merge($result_list,$result);
            }
		}
		return $result_list;
	}

	//更新关联表
	public static function updatePostRelation($item_id,$item_type,$data)
    {
		return db('post_relation')->where(['item_id'=>$item_id,'item_type'=>$item_type])->update($data);
	}

    /**
     * 获取用户推荐内容,管理员推荐的内容会优先显示
     * @param null $uid
     * @param null $item_type
     * @param null $topic_ids
     * @param null $category_id
     * @param int $page
     * @param int $per_page
     * @param int $relation_uid
     * @param string $pjax
     * @return void
     */
    public static function getRecommendPost($uid=null,$item_type=null,$topic_ids = null, $category_id = null,$page=1, $per_page=0,$relation_uid=0,$pjax='tabMain')
    {
        $per_page = $per_page ?:intval(get_setting('contents_per_page'));
        $result_list = [];
        $key = $category_id.'-'.$item_type.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page.'-'.$relation_uid;
        $cache_key = 'cache_explore_data_'.$key;
        if($cache_explore_time = get_setting('cache_explore_time'))
        {
            $result_list = cache($cache_key);
        }

        if($result_list) return $result_list;

        $where = [];

        $relation = config('aws.relation');
        $item_types = $relation ? array_merge(['question','article'],$relation) : ['question','article'];

        if(!$item_type)
        {
            $where[] = ['item_type','IN', $item_types];
        }

        if ($item_type)
        {
            $where[] = ['item_type','=', $item_type];
        }

        if ($category_id)
        {
            $category_ids = Category::getCategoryWithChildIds($category_id,true);
            if($category_ids)
            {
                $where[] = ['category_id','in', implode(',',$category_ids )];
            }else{
                $where[] = ['category_id','=', $category_id];
            }
        }

        $relationInfo = [];

        if(!$uid)
        {
            if ($topic_ids)
            {
                $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
                $topic_where = $item_type ? 'item_type="'.$item_type.'" AND status=1' : 'status=1';
                $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
                $relationInfo = db('topic_relation')
                    ->whereRaw($topic_where.$topicIdsWhere)
                    ->column('item_id,item_type');
            }
        }else{
            if ($topic_ids)
            {
                $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
            }else{
                $topic_ids = db('topic_focus')->where(['uid'=>intval($uid)])->orderRaw('RAND()')->limit(10)->column('topic_id');
                $relationTopic = db('topic_relation')->where(['uid'=>$uid,'status'=>1])->column('topic_id');
                $topic_ids = $relationTopic ? array_merge($topic_ids,$relationTopic) : $topic_ids;
            }
            //根据关注的话题和自己发布内容所在的话题排除自己发布的推荐内容
            $topic_where = $item_type ? 'item_type="'.$item_type.'" AND status=1 AND uid<>'.$uid : 'status=1 AND uid<>'.$uid;
            $topicIdsWhere = $topic_ids ? ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')' : '';
            $relationInfo = db('topic_relation')
                ->whereRaw($topic_where.$topicIdsWhere)
                ->column('item_id,item_type');
        }

        if($relationInfo)
        {
            $post_ids = $post_id_where = [];
            $whereRaw = '';

            foreach ($relationInfo as $val)
            {
                $post_ids[$val['item_type']][$val['item_id']] = $val['item_id'];
            }

            foreach ($post_ids AS $key => $val)
            {
                $post_id_where[] = "(item_id IN (" . implode(',', $val) . ") AND item_type = '" . $key . "')";
            }

            if ($post_id_where)
            {
                $whereRaw = '(' . implode(' OR ', $post_id_where) . ')';
            }

            if($whereRaw)
            {
                $list = db('post_relation')
                    ->where($where)
                    ->whereRaw($whereRaw)
                    ->order(['is_recommend'=>'DESC'])
                    ->field('id,item_id,item_type,uid')
                    ->paginate([
                        'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                        'page' => $page,
                        //'query'=>request()->param(),
                        'pjax'=>$pjax
                    ]);
                $pageVar = $list->render();
                $allList = $list->toArray();
                $list = self::processPostList($allList['data'],$uid);
                $result_list['list'] = $list;
                $result_list['page'] = $pageVar;
                $result_list['total'] = $allList['last_page'];
                if($cache_explore_time)
                {
                    cache($cache_key,$result_list,['expire'=>$cache_explore_time*60]);
                }

                return $result_list;
            }
        }

        $list = db('post_relation')
            ->where($where)
            ->orderRaw('RAND()')
            ->order(['is_recommend'=>'DESC'])
            ->field('id,item_id,item_type,uid')
            ->paginate(
            [
                'list_rows'=> $per_page,
                'page' => $page,
                'pjax'=>$pjax
            ]
        );

        $pageVar = $list->render();
        $allList = $list->toArray();
        $list = self::processPostList($allList['data'],$uid);
        $result_list['list'] = $list;
        $result_list['page'] = $pageVar;
        $result_list['total'] = $allList['last_page'];
        if($cache_explore_time)
        {
            cache($cache_key,$result_list,['expire'=>$cache_explore_time*60]);
        }
        return $result_list;
    }
}