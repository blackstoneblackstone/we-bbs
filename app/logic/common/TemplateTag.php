<?php
namespace app\logic\common;

use app\common\library\helper\ImageHelper;
use app\common\library\helper\LogHelper;
use app\model\Answer;
use app\model\Article;
use app\model\Category;
use app\model\Column;
use app\model\Question;
use app\model\Topic;
use app\model\Users;
use app\model\Vote;

class TemplateTag
{
    /**
     * 获取问题列表
     */
    public static function getQuestionList($uid=null, $sort = null, $topic_ids = null, $category_id = null, $limit=0,  $per_page=0,$cache=0, $pjax='tabMain',$question_type='normal'): array
    {
        $page = request()->param('page',1,'intval');
        $key = $sort.'-'.$category_id.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page;
        $cache_key = 'cache_tag_template_question_data_'.$key;
        $result_list = [];
        if($cache_explore_time = get_setting('cache_explore_time'))
        {
            $result_list = cache($cache_key);
        }
        if($result_list && $cache) return $result_list;

        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        if($question_type) $where[] = ['question_type','=',$question_type];
        if($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
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

        //推荐内容
        if($sort=='recommend')
        {
            $order['is_recommend'] = 'DESC';
            $where[] = ['is_recommend','=',1];
        }

        $order['create_time'] = 'DESC';

        if ($topic_ids)
        {
            $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
            $topic_where =  'item_type="question" AND status=1' ;
            $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
            $relationInfo = db('topic_relation')
                ->whereRaw($topic_where.$topicIdsWhere)
                ->column('item_id,item_type');
            $item_ids = array_column($relationInfo,'item_id');
            $where[] = ['id','in',implode(',', $item_ids)];
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

        $result_list = [];

        if(!$limit)
        {
            $list = db('question')->where($where)->order($order)->paginate(
                [
                    'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                    'query' => request()->param(),
                    'pjax'=>$pjax
                ]
            );

            $pageVar = $list->render();
            $allList = $list->toArray();

            $data_list['page'] = $pageVar;
            $data_list['total'] = $allList['last_page'];
            $allList = $allList['data'];
        }else{
            $allList = db('question')->where($where)->order($order)->limit($limit)->select()->toArray();
        }

        $uid_lists = array_column($allList,'uid','uid');
        $last_answers = $sort=='hot'?Answer::getHotAnswerByIds(array_column($allList,'id')):Answer::getLastAnswerByIds(array_column($allList,'id'));
        if ($last_answers)
        {
            foreach ($last_answers as $key => $val)
            {
                $uid_lists[$val['uid']] = $val['uid'];
            }
        }

        $topic_infos = Topic::getTopicByItemIds(array_column($allList,'id'), 'question');
        $answerUidList = $last_answers ? array_column($last_answers,'uid') : [];
        $uid_lists = $answerUidList ? array_merge($uid_lists,$answerUidList) : $uid_lists;

        $users_info = Users::getUserInfoByIds($uid_lists,'user_name,avatar,nick_name,uid',99);

        foreach ($allList as $key=>$data)
        {
            $result_list[$key] = $data;
            $result_list[$key]['title'] = htmlspecialchars_decode($result_list[$key]['title']);

            //是否已回答
            $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$data['id'],'status'=>1])->value('id') : 0;

            //回答用户
            $answerUidLists = db('answer')->where(['question_id'=>$data['id'],'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
            $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];

            //最后回答
            $result_list[$key]['answer_info'] = $last_answers[$data['id']] ?? false;

            if($result_list[$key]['answer_info']){
                $result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$data['id']]['uid']]??['url'=>'javascript:;','uid'=>0,'name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
                $result_list[$key]['answer_info']['vote_value'] = isset($last_answers[$data['id']])?Vote::getVoteByType($last_answers[$data['id']]['id'],'answer',$uid):0;
                $result_list[$key]['answer_info']['content'] = str_cut(strip_tags($result_list[$key]['answer_info']['content']),0,150);
                $result_list[$key]['answer_info']['img_list'] = ImageHelper::srcList($result_list[$key]['answer_info']['content']);
            }

            $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'question',$data['id']);

            $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);

            $result_list[$key]['img_list'] = ImageHelper::srcList($result_list[$key]['detail']);

            $result_list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);


            if($result_list[$key]['answer_info'] && !isset($users_info[$last_answers[$data['id']]['uid']]))
            {
                $result_list[$key]['answer_info'] = [];
            }

            //问题话题
            $result_list[$key]['topics'] = $topic_infos[$data['id']] ?? [];

            if(!isset($users_info[$data['uid']]))
            {
                unset($result_list[$key]);
                continue;
            }
            $result_list[$key]['user_info'] = $users_info[$data['uid']];
        }

        $data_list['list'] = $result_list;

        if($cache_explore_time && $cache)
        {
            cache($cache_key,$data_list,['expire'=>$cache_explore_time*60]);
        }
        return $data_list;
    }

    /**
     * 获取文章列表
     */
    public static function getArticleList($uid=null,$sort = null, $topic_ids = null, $category_id = null, $limit=0, $per_page=0,$cache=0, $pjax='tabMain',$article_type='normal'): array
    {
        $page = request()->param('page',1,'intval');
        $key = $sort.'-'.$category_id.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page;
        $cache_key = 'cache_tag_template_article_data_'.$key;
        $data_list = [];
        if($cache_explore_time = get_setting('cache_explore_time'))
        {
            $data_list = cache($cache_key);
        }
        if($data_list && $cache) return $data_list;

        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        if($article_type) $where[] = ['article_type','=',$article_type];
        $orderRaw = '';
        if($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
        }elseif($sort=='hot')
        {
            $order['popular_value'] = 'desc';
            $where[] = ['popular_value','>',get_setting('content_popular_value_show',0)];
        }elseif($sort=='new'){
            $order['set_top_time'] = 'DESC';
            $order['update_time'] = 'DESC';
        }elseif($sort=='recommend')
        {
            $order['is_recommend'] = 'DESC';
            $where[] = ['is_recommend','=',1];
        }else{
            $orderRaw = $sort;
        }

        $order['create_time'] = 'DESC';

        if ($topic_ids)
        {
            $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
            $topic_where =  'item_type="article" AND status=1' ;
            $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
            $relationInfo = db('topic_relation')
                ->whereRaw($topic_where.$topicIdsWhere)
                ->column('item_id,item_type');
            $item_ids = array_column($relationInfo,'item_id');
            $where[] = ['id','in',implode(',', $item_ids)];
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

        if(!$limit)
        {
            $list = db('article')
                ->where($where)
                ->order($orderRaw?:$order)
                ->paginate(
                    [
                        'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                        'query'=>request()->param(),
                        'pjax'=>$pjax
                    ]
                );

            $pageVar = $list->render();
            $allList = $list->toArray();
            $data_list['page'] = $pageVar;
            $data_list['total'] = $allList['last_page'];
            $allList = $allList['data'];
        }else{
            $allList = db('article')
                ->where($where)
                ->order($orderRaw?:$order)
                ->limit($limit)
                ->select()
                ->toArray();
        }

        $users_info = Users::getUserInfoByIds(array_column($allList,'uid'));
        $topic_infos = Topic::getTopicByItemIds(array_column($allList,'id'), 'article');
        $result_list = [];
        foreach ($allList as $key=>$data)
        {
            $result_list[$key] = $data;
            $result_list[$key]['title'] = htmlspecialchars_decode($data['title']);
            $result_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($data['message'])),0,120);
            $cover  = ImageHelper::srcList(htmlspecialchars_decode($data['message']));
            $result_list[$key]['img_list'] = $cover?:[];
            $result_list[$key]['topics'] = $topic_infos[$data['id']] ?? [];
            $result_list[$key]['user_info'] = $users_info[$data['uid']] ?? ['url'=>'javascript:;','uid'=>0,'name'=>'未知用户'];
            $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'article',$uid);
        }
        $data_list['list'] = $result_list;
        if($cache_explore_time && $cache)
        {
            cache($cache_key,$data_list,['expire'=>$cache_explore_time*60]);
        }
        return $data_list;
    }

    /**
     * 万能渲染
     * @param string $table
     * @param string $where
     * @param string $order
     * @param string $sql
     * @param int $per_page
     * @param int $limit
     * @param string $field
     * @return array|string
     */
    public static function sqlFetch(string $table='', string $where='', string $order='', string $sql='', int $per_page=1, int $limit=0, string $field='*')
    {
        if(!$table && !$sql) return ['list'=>[]];
        if($sql)
        {
            $data = db()->query($sql);
            $data_list['list'] = $data;
            return $data_list;
        }

        if(!$table) return '';
        $db = db($table);
        if(is_string($where))
        {
            $db->whereRaw($where);
        }else{
            $db->where($where);
        }

        if(is_string($order))
        {
            $db->orderRaw($order);
        }else{
            $db->order($order);
        }

        if(!$limit)
        {
            $data = $db->field($field)->paginate(
                [
                    'list_rows'=> $per_page ?: get_setting('contents_per_page'),
                    'query'=>request()->param(),
                ]
            );
            $pageVar = $data->render();
            $allList = $data->toArray();
            $data_list['total'] = $allList['last_page'];
            $data_list['list'] = $allList['data'];
            $data_list['page'] = $pageVar;
        }else{
            $data = $db->field($field)->select()->toArray();
            $data_list['list'] = $data;
        }

        return $data_list;
    }

    /**
     * 获取聚合数据列表
     * @param null $uid
     * @param null $item_type
     * @param null $sort
     * @param null $topic_ids
     * @param null $category_id
     * @param int $limit
     * @param int $per_page
     * @param string $pjax
     * @return array|false|null
     */
    public static function getPostRelationList($uid=null,$item_type=null, $sort = null, $topic_ids = null, $category_id = null,$limit=0, $per_page=0,$cache=0, $pjax='tabMain')
    {
        $page = request()->param('page',1,'intval');
        $key = $sort.'-'.$category_id.'-'.$item_type.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page;
        $cache_key = 'cache_tag_template_relation_data_'.$key;
        $result_list = [];
        if($cache_explore_time = get_setting('cache_explore_time'))
        {
            $result_list = cache($cache_key);
        }
        if($result_list && $cache) return $result_list;

        $sort = is_array($sort)?end($sort) : $sort;
        $item_type = is_array($item_type)?end($item_type) : $item_type;
        $uid = is_array($uid)?end($uid) : $uid;
        $per_page = $per_page ?:intval(get_setting('contents_per_page'));
        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        $orderRaw = '';
        $relation = config('aws.relation');
        $item_types = $relation ? ['question','article',implode(',',$relation)] : ['question','article'];

        if($item_type && !in_array($item_type,$item_types))
        {
            return false;
        }
        $order['create_time'] = 'DESC';
        //关注单独处理
        if($sort == 'focus') {
            return LogHelper::getUserFocusLogList($uid, 'all', request()->param('page',1), $per_page, $pjax);
        }elseif($sort=='recommend')
        {
            return self::getRecommendPost($uid,$item_type, $topic_ids, $category_id,intval(request()->param('page',1)), intval($per_page),0,$cache,$pjax);
        }elseif($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
            $item_type = 'question';
        }elseif($sort=='hot')
        {
            $order['popular_value'] = 'desc';
            $where[] = ['popular_value','>',get_setting('content_popular_value_show',0)];
        }elseif($sort=='new'){
            $order['set_top_time'] = 'DESC';
            $order['update_time'] = 'DESC';
        }else{
            $orderRaw = $sort;
        }

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

        if ($topic_ids) {
            $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',', $topic_ids);
            $topic_where = $item_type ? 'item_type="' . $item_type . '" AND status=1' : 'status=1';
            $topicIdsWhere = ' AND topic_id IN(' . implode(',', array_unique($topic_ids)) . ')';
            $relationInfo = db('topic_relation')
                ->whereRaw($topic_where . $topicIdsWhere)
                ->column('item_id,item_type');

            if (!$relationInfo) {
                $result_list['list'] = [];
                $result_list['page'] = '';
                $result_list['total'] = 1;
                return $result_list;
            }

            $post_ids = $post_id_where = [];
            $whereRaw = '';

            foreach ($relationInfo as $val) {
                $post_ids[$val['item_type']][$val['item_id']] = $val['item_id'];
            }

            foreach ($post_ids as $key => $val) {
                $post_id_where[] = "(item_id IN (" . implode(',', $val) . ") AND item_type = '" . $key . "')";
            }

            if ($post_id_where) {
                $whereRaw = '(' . implode(' OR ', $post_id_where) . ')';
            }

            if ($whereRaw) {
                if (!$limit) {
                    $list = db('post_relation')
                        ->where($where)
                        ->whereRaw($whereRaw)
                        ->order($orderRaw?:$order)
                        ->field('id,item_id,item_type,uid')
                        ->paginate([
                            'list_rows' => $per_page ?: get_setting('contents_per_page'),
                            'query' => request()->param(),
                            'pjax' => $pjax
                        ]);
                    $pageVar = $list->render();
                    $allList = $list->toArray();
                    $list = self::processPostList($allList['data'], $uid,$sort);
                    $result_list['list'] = $list;
                    $result_list['page'] = $pageVar;
                    $result_list['total'] = $allList['last_page'];
                } else {
                    $list = db('post_relation')
                        ->where($where)
                        ->whereRaw($whereRaw)
                        ->order($orderRaw?:$order)
                        ->field('id,item_id,item_type,uid')
                        ->limit($limit)
                        ->select()
                        ->toArray();

                    $list = self::processPostList($list, $uid,$sort);
                    $result_list['list'] = $list;
                }
                return $result_list;
            }
        }

        if(!$limit)
        {
            $list = db('post_relation')
                ->where($where)
                ->order($orderRaw?:$order)
                ->field('id,item_id,item_type,uid')
                ->paginate([
                    'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                    'query'=>request()->param(),
                    'pjax'=>$pjax
                ]);

            $pageVar = $list->render();
            $allList = $list->toArray();
            $list = self::processPostList($allList['data'],$uid,$sort);
            $result_list['list'] = $list;
            $result_list['page'] = $pageVar;
            $result_list['total'] = $allList['last_page'];
        }else{
            $list = db('post_relation')
                ->where($where)
                ->order($orderRaw?:$order)
                ->field('id,item_id,item_type,uid')
                ->limit($limit)
                ->select()
                ->toArray();

            $list = self::processPostList($list,$uid,$sort);
            $result_list['list'] = $list;
        }

        if($cache_explore_time)
        {
            cache($cache_key,$result_list,['expire'=>$cache_explore_time*60]);
        }
        return $result_list;
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
                    $result_list[$key]['img_list'] = ImageHelper::srcList($result_list[$key]['detail']);

                    $result_list[$key]['detail'] = str_cut(strip_tags($result_list[$key]['detail']),0,150);

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
    public static function getRecommendPost($uid=null,$item_type=null,$topic_ids = null, $category_id = null,$page=1, $per_page=0,$relation_uid=0,$cache=0, $pjax='tabMain')
    {
        $per_page = $per_page ?:intval(get_setting('contents_per_page'));
        $result_list = [];
        $key = $category_id.'-'.$item_type.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page.'-'.$relation_uid;
        $cache_key = 'cache_tag_template_recommend_data_'.$key;
        if($cache_explore_time = get_setting('cache_explore_time'))
        {
            $result_list = cache($cache_key);
        }

        if($result_list && $cache) return $result_list;

        $where = [];

        $relation = config('aws.relation');

        if(!$item_type)
        {
            $where[] = ['item_type','IN', ['question','article',implode(',',$relation)]];
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
                        'query'=>request()->param(),
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
        if($cache_explore_time && $cache)
        {
            cache($cache_key,$result_list,['expire'=>$cache_explore_time*60]);
        }
        return $result_list;
    }

    /**
     * 获取话题列表
     * @param int $uid
     * @param string $where
     * @param string $order
     * @param int $limit
     * @param int $per_page
     * @param string $pjax
     * @return array
     */
    public static function getTopicList($uid=0,$where='',$order='discuss desc',$limit=0,$per_page=10,$pjax = 'tabMain'): array
    {
        $pageVar = '';
        $total = 0;
        if(!$limit)
        {
            $list = db('topic')
                ->where($where)
                ->order($order)
                ->paginate([
                    'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                    'query'=>request()->param(),
                    'pjax'=>$pjax
                ]);
            $pageVar = $list->render();
            $total = $list['last_page'];
            $list = $list->all();
        }else{
            $list = db('topic')
                ->where($where)
                ->order($order)
                ->limit($limit)
                ->select()
                ->toArray();
        }

        foreach ($list as $key=>$val)
        {
            $list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'topic',$val['id']);
        }

        return ['list'=>$list,'page'=>$pageVar,'total'=>$total];
    }

    /**
     * 我关注的话题
     * @param $uid
     * @param string $sort
     * @param int $limit
     * @param int $per_page
     * @return array|false
     */
    public static function getFocusTopics($uid=0,$sort='RAND()', $limit = 0,$per_page=10)
    {
        if (!$uid) {
            return false;
        }
        $focus_topics = db('topic_focus')->where(['uid'=>intval($uid)])->select()->toArray();
        if (!$focus_topics) return false;

        $topic_ids = array_column($focus_topics,'topic_id');

        if(empty($topic_ids)) return false;

        if($limit)
        {
            $list = db('topic')
                ->whereRaw("id IN(".implode(',',$topic_ids).")")
                ->orderRaw($sort)
                ->limit($limit)
                ->select()
                ->toArray();
            $pageVar = '';
            $total = count($list);
        }else{
            $list = db('topic')
                ->whereRaw("id IN(".implode(',',$topic_ids).")")
                ->orderRaw($sort)
                ->paginate([
                    'list_rows'=> $per_page,
                    'query'=>request()->param(),
                ]);
            $pageVar = $list->render();
            $total = $list['last_page'];
            $list = $list->all();
        }

        foreach ($list as $k=> $v)
        {
            $list[$k]['question_count'] = db('topic_relation')->where(['topic_id'=>intval($v['id']),'item_type'=>'question'])->count();
            $list[$k]['article_count'] = db('topic_relation')->where(['topic_id'=>intval($v['id']),'item_type'=>'article'])->count();
        }

        return ['list'=>$list,'page'=>$pageVar,'total'=>$total];
    }

    /**
     * 获取热门话题
     * @param int $uid
     * @param string $where1
     * @param string $order
     * @param int $per_page
     * @param int $limit
     * @return array
     */
    public static function getHotTopics(int $uid=0, $where1='', $order='',  $per_page=5, $limit=0)
    {
        $where[] = ['status','=',1];
        $where[] = ['discuss_month','>',0];
        if(!$where)
        {
            $where[] = ['discuss_update','>',time()-30*24*60*60];
        }

        $order = $order ?: ['top'=>'DESC','focus'=>'DESC','discuss_month'=>'DESC'];

        if(!$limit)
        {
            $list = db('topic')
                ->where($where1)
                ->where($where)
                ->order($order)
                ->paginate(
                    [
                        'list_rows'=> $per_page,
                        'query'=>request()->param()
                    ]
                )->toArray();
            $pageVar = $list->render();
            $total = $list['last_page'];
            $list = $list->all();
        }else{
            $list = db('topic')
                ->where($where1)
                ->where($where)
                ->order($order)
                ->select()
                ->toArray();
            $pageVar = '';
            $total = count($list);
        }
        foreach ($list as $key=>$val)
        {
            $list[$key]['is_focus'] = db('topic_focus')->where(['uid'=>intval($uid),'topic_id'=>$val['id']])->value('id');
        }
        return ['list'=>$list,'page'=>$pageVar,'total'=>$total];
    }

    //获取用户关注
    public static function getUserFocus($uid=0,$type='',$limit=0,$per_page=10)
    {
        if(!$uid || !$type) return false;
        $types = ['fans', 'friend', 'column', 'topic', 'question'];
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
            'query'=>request()->param()
        ];
        $table = db($dbName)
            ->where($where);

        $result = [];

        if($limit)
        {
            $list = $table->limit($limit)->select()->toArray();
            $result['page'] = 0;
        }else{
            $list = $table->paginate($paginate);
            $page_render = $list->render();
            $list = $list->all();
            $result['page'] = $page_render;
        }

        if($list)
        {
            foreach ($list as $key=>$val)
            {
                switch ($type)
                {
                    case 'question':
                        $question_ids = array_column($list,'question_id');
                        $question_infos = Question::getQuestionByIds($question_ids);
                        if(!empty($question_infos) && isset($question_infos[$val['question_id']]))
                        {
                            $list[$key] = $question_infos[$val['question_id']];
                            $list[$key]['user_info'] = Users::getUserInfo($question_infos[$val['question_id']]['uid']);
                            $list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($question_infos[$val['question_id']]['detail'])),0,150);
                            $list[$key]['topics'] = Topic::getTopicByItemType('question',$val['question_id']);
                            $list[$key]['vote_value'] =  Vote::getVoteByType($val['question_id'],'question',$uid);
                            $list[$key]['item_id'] =  $val['question_id'];
                        }else{
                            unset($list[$key]);
                        }
                        break;

                    case 'friend':
                        $uid_s = array_column($list,'friend_uid');
                        $user_infos = Users::getUserInfoByIds($uid_s);
                        if(!empty($user_infos))
                        {
                            $list[$key]['user_info'] = $user_infos[$val['friend_uid']];
                            $list[$key]['item_id'] =  $val['friend_uid'];
                        }
                        break;

                    case 'fans':
                        $uid_s = array_column($list,'fans_uid');
                        $user_infos = Users::getUserInfoByIds($uid_s);
                        $list[$key]['user_info'] = $user_infos[$val['fans_uid']] ?? [];
                        $list[$key]['item_id'] =  $val['fans_uid'];
                        break;

                    case 'column':
                        $column_ids = array_column($list,'column_id');
                        $column_infos = Column::getColumnByIds($column_ids);
                        $list[$key] = $column_infos[$val['column_id']];
                        $list[$key]['item_id'] =  $val['column_id'];
                        break;

                    case 'topic':
                        $topic_ids = array_column($list,'topic_id');
                        $topic_infos = Topic::getTopicByIds($topic_ids);
                        $list[$key] = $topic_infos[$val['topic_id']];
                        $list[$key]['uid'] = $val['uid'];
                        $list[$key]['item_id'] = $val['topic_id'];
                        break;
                }

                if(in_array($type, $types)) {
                    if ($type == 'fans') $val['uid'] = $val['fans_uid'];
                    if ($type == 'friend') $val['uid'] = $val['friend_uid'];
                    $list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, $type == 'fans' ? 'friend' : $type, $val['item_id']);
                }
            }
        }
        $result['list'] = $list;
        return $result;
    }

}