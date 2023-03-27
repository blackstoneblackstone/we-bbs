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
use app\common\library\helper\TreeHelper;
use app\logic\common\FocusLogic;
use app\logic\search\libs\ElasticSearch;
use Overtrue\Pinyin\Pinyin;
use think\facade\Db;
use tools\Tree;
use WordAnalysis\Analysis;

/**
 * 话题模型类
 * Class Topic
 * @package app\model
 */
class Topic extends BaseModel
{
    //模糊搜索话题列表
    public static function getTopic($where,$page=1,$per_page=5)
    {
        $where = is_array($where) ? implode(' AND ',$where) : $where;
        return db('topic')->whereRaw($where)->order(['discuss'=>'desc'])->page($page,$per_page)->select()->toarray();
    }

    //ajax话题列表
    public static function getAjaxTopicList($where,$uid,$page,$per_page=10): array
    {
        $list = db('topic')->where($where)->order('discuss desc')->paginate([
            'list_rows'=> $per_page,
            'page' => $page,
            'query'=>request()->param()
        ]);
        $total = db('topic')->where($where)->count();
        $pageVar = $list->render();
        $list = $list->all();
        foreach ($list as $key=>$val)
        {
            $list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'topic',$val['id']);
        }
        return ['list'=>$list,'page'=>$pageVar,'total'=>ceil($total/$per_page)];
    }

    /**
     * 获取话题列表
     * @param $item_type
     * @param $item_id
     * @return false|array
     */
    public static function getTopicByItemType($item_type,$item_id)
    {
        $ids = db('topic_relation')->where(['item_type' => $item_type, 'item_id' => $item_id])->column('topic_id');
        if (empty($ids)) {
            return false;
        }
        return db('topic')->whereIn('id', $ids)->column('id,title,url_token');
    }

    /**
     * 添加话题
     * @param $topic_title
     * @param null $uid
     * @param bool $auto_create
     * @param null $topic_description
     * @return int|string
     */
    public static function saveTopic($topic_title, $uid = null, bool $auto_create = true, $topic_description = null)
    {
        if(is_numeric($topic_title))
        {
            $topic_id = $topic_title;
        }else{
            $topic_title = str_replace(array('-', '/'), '_', $topic_title);
            $topic_id = db('topic')->where(['title' => $topic_title])->value('id');
        }

        if (!$topic_id AND $auto_create)
        {
            $pinyin = new Pinyin();
            $url_token = $pinyin->permalink($topic_title,'');
            if(db('topic')->where(['url_token'=>$url_token])->value('id'))
            {
                $url_token = $url_token.time();
            }

            $topic_id = db('topic')->insertGetId([
                'uid'=>intval($uid),
                'title' => htmlspecialchars($topic_title),
                'description' => $topic_description ? htmlspecialchars($topic_description) : '',
                'seo_title'=>htmlspecialchars($topic_title),
                'seo_keywords'=> $topic_description ? Analysis::getKeywords(htmlspecialchars($topic_description), 5) : '',
                'seo_description'=>$topic_description ? str_cut(strip_tags($topic_description),0,150) : '',
                'url_token'=>$url_token,
                'lock' => 0,
                'create_time' => time(),
            ]);

            ElasticSearch::instance()->create('topic',db('topic')->find($topic_id));

            if ($uid) {
                self::addFocusTopic($uid, $topic_id);
                //新建话题记录
                LogHelper::addActionLog('create_topic','topic',$topic_id,$uid);
            }
        } else {
            self::updateDiscuss($topic_id);
        }
        return $topic_id;
    }

    //更新话题
    public static function updateTopic($data,$topic_id,$uid=0): bool
    {
        $insertData = [
            'pic'=>$data['pic']??'/static/common/image/topic.svg',
        ];
        $insertData['seo_title'] = $data['seo_title'] ?: $data['title'];
        $insertData['seo_keywords'] = $data['seo_keywords'] ?: Analysis::getKeywords(strip_tags(htmlspecialchars_decode($data['description'])), 5);
        $insertData['seo_description'] = $data['seo_description'] ?: str_cut(strip_tags(htmlspecialchars_decode($data['description'])), 0, 150);
        $insertData['description'] = remove_xss($data['description']);
        $pinyin = new Pinyin();
        $insertData['url_token'] = $pinyin->permalink($data['title'],'');
        if(db('topic')->where('id','<>',$topic_id)->where(['url_token'=>$data['url_token']])->value('id'))
        {
            $insertData['url_token'] = $data['url_token'].time();
        }

        // 启动事务
        Db::startTrans();
        try {
            self::update($insertData,['id'=>$topic_id]);
            LogHelper::addActionLog('update_topic','topic',$topic_id,$uid);
            ElasticSearch::instance()->update('topic',db('topic')->find($topic_id));
            if(isset($postData['topics']) && $postData['topics'])
            {
                $topics = is_array($postData['topics']) ? array_filter($postData['topics']) : explode(',',trim($postData['topics']));
                if (!empty($topics))
                {
                    foreach ($topics as $title)
                    {
                        if($title)
                        {
                            $target_id = Topic::saveTopic($title, $uid);
                            Topic::saveTopicMerge($uid, $topic_id, $target_id);
                        }
                    }
                }
            }
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * 获取话题列表
     * @param $source_id
     * @return false|array
     */
    public static function getRelatedTopicBySourceId($source_id)
    {
        $ids = db('topic_related')->where(['source_id' => $source_id])->column('target_id');
        if (empty($ids)) {
            return false;
        }
        return db('topic')->whereIn('id', $ids)->column('id,title,url_token');
    }

    //添加话题关注
    public static function addFocusTopic($uid, $topic_id)
    {
        $focus_id = db('topic_focus')->where([['uid','=',intval($uid)], ['topic_id','=',intval($topic_id)]])->find();
        //TODO 事务处理方式

        if (!$focus_id) {
            $res = db('topic_focus')->insert(array(
                "topic_id" => (int)$topic_id,
                "uid" => (int)$uid,
                "create_time" => time(),
            ));
            if ($res) {
                db('topic')->where(['id' => $topic_id])->inc('focus')->update();
            }
        } else if (db('topic_focus')->where(['id' => $focus_id])->delete()) {
            db('topic')->where(['id' => $topic_id])->dec('focus')->update();
        }

        // 更新个人计数
        $focus_count = db('topic_focus')->where(['uid' => (int)$uid])->count();
        Users::updateUserFiled($uid, (array(
            'topic_focus_count' => $focus_count,
        )));
        return $focus_id;
    }

    /**
     * 更新话题讨论数
     * @param $topic_id
     * @return Topic|bool
     */
    public static function updateDiscuss($topic_id) {
        if (!$topic_id) {
            return false;
        }
        $discuss_count = db('topic_relation')->where([
            'status' =>1,
            'topic_id' => (int)$topic_id,
        ])->count();

        $discuss_week_count = db('topic_relation')->where([
            ['create_time', '>', time() - 604800],
            ['topic_id','=', (int)$topic_id],
            ['status','=',1],
        ])->count();

        $discuss_month_count = db('topic_relation')->where([
            ['create_time', '>', time() - 2592000],
            ['topic_id','=', (int)$topic_id],
            ['status','=',1],
        ])->count();

        $discuss_update = db('topic_relation')->where(['status' =>1, 'topic_id' => (int)$topic_id])->order('create_time', 'desc')->value('create_time');
        return self::update(array(
            'discuss' => $discuss_count,
            'discuss_week' => $discuss_week_count,
            'discuss_month' => $discuss_month_count,
            'discuss_update' => $discuss_update,
        ), ['id' => (int)$topic_id]);
    }

    //获取话题关注用户列表
    public static function getTopicFocusUser($topic_id,$limit = 10)
    {
        $focus_uid =  db('topic_focus')->orderRaw('RAND()')->where(['topic_id'=> (int)$topic_id])->page(1,$limit)->column('uid');
        return Users::getUserInfoByIds($focus_uid);
    }

    //获取话题内容的数量及浏览量
    public static function getTopicPostCountResult($topic_id): array
    {
        $where[] = ['topic_id', '=', (int)$topic_id];
        $where[] =['status','=',1];
        $topic_relation = db('topic_relation')->where($where)->select();

        $article_count = $question_count = $question_view_count = $article_view_count = $answer_count = 0;
        $article_ids = $question_ids = array();
        foreach ($topic_relation as $key=>$val)
        {
            switch ($val['item_type'])
            {
                case 'question':
                    $question_ids[] = $val['item_id'];
                    ++$question_count;
                    break;
                case 'article':
                    $article_ids[] = $val['item_id'];
                    ++$article_count;
                    break;
            }
        }

        $answer_count = Answer::where(['status'=>1])->whereIn('question_id',array_unique($question_ids))->count();
        $question_view_count = Question::where(['status'=>1])->whereIn('id',array_unique($question_ids))->sum('view_count');
        $article_view_count = Article::where(['status'=>1])->whereIn('id',array_unique($article_ids))->sum('view_count');
        return ['article_count'=>$article_count,'question_count'=>$question_count,'answer_count'=>$answer_count,'question_view_count'=>$question_view_count,'article_view_count'=>$article_view_count];
    }

    /**
     * 添加关联话题
     * @param $uid
     * @param $topic_id
     * @param $item_id
     * @param $item_type
     * @param int $status
     * @return bool|int|string|Db
     */
    public static function saveTopicRelation($uid, $topic_id, $item_id, $item_type,int $status=1) {
        if (!$topic_id || !$item_id || !$item_type) {
            return false;
        }

        if (!$topic_info = self::getById($topic_id)) {
            return false;
        }

        if ($id = self::checkTopicRelation($topic_id, $item_id, $item_type)) {
            return $id;
        }

        $insert_id = db('topic_relation')->insertGetId(array(
            'topic_id' => (int)$topic_id,
            'item_id' => (int)$item_id,
            'create_time' => time(),
            'uid' => (int)$uid,
            'item_type' => $item_type,
            'status'=>$status
        ));

        //记录话题添加内容
        LogHelper::addActionLog('modify_'.$item_type.'_topic','topic',$topic_id,$uid,0,time(),$item_type,$item_id);

        self::updateDiscuss($topic_id);
        return $insert_id;
    }

    /**
     * 添加关联话题
     * @param $uid
     * @param $topic_id
     * @param $target_id
     * @return bool|int|string|Db
     */
    public static function saveTopicMerge($uid, $topic_id, $target_id) {
        if (!$topic_id || !$target_id ) {
            return false;
        }
        if (!self::getById($topic_id)) {
            return false;
        }

        $where[] = ['source_id', '=', (int)$topic_id];
        $where[] = ['target_id', '=', (int)$target_id];

        if(!$id= db('topic_related')->where($where)->value('id'))
        {
            return db('topic_related')->insertGetId(array(
                'source_id' => (int)$topic_id,
                'target_id' => (int)$target_id,
            ));
        }
        return  $id;
    }

    /**
     * 检查是否已有关联记录
     * @param $topic_id
     * @param $item_id
     * @param $item_type
     * @return mixed
     */
    public static function checkTopicRelation($topic_id, $item_id, $item_type)
    {
        $where[] = ['topic_id', '=', (int)$topic_id];
        $where[] = ['item_type', '=', $item_type];
        $where[] = ['item_id', '=', (int)$item_id];
        return db('topic_relation')->where($where)->value('id');
    }

    /**
     * 更新关联话题
     * @param $type
     * @param $item_id
     * @param $topics
     * @param $uid
     * @return bool
     */
    public static function updateRelation($type,$item_id,$topics=[], $uid=0)
    {
        if(!$item_id || !$type) return false;
        db('topic_relation')->where(['item_type' => $type, 'item_id' => $item_id])->delete();
        if($topics)
        {
            $_data = [];
            $topics = is_array($topics) ? $topics : explode(',', $topics);
            $topics = array_unique($topics);
            foreach ($topics as $key => $value) {
                $tmp['topic_id'] = (int)$value;
                $tmp['uid'] = $uid;
                $tmp['create_time'] = time();
                $tmp['item_id'] = (int)$item_id;
                $tmp['item_type'] = $type;
                $_data[] = $tmp;
            }
            if(db('topic_relation')->insertAll($_data))
            {
                self::updateTopicDiscussCount($topics);
                return true;
            }
        }
        return false;
    }

    /**
     * 更新相关话题
     * @param $item_id
     * @param $topics
     * @return bool
     */
    public static function updateRelated($item_id,$topics=[]): bool
    {
        if(!$item_id) return false;
        db('topic_related')->where(['source_id' => $item_id])->delete();
        if($topics)
        {
            $_data = [];
            $topics = is_array($topics) ? $topics : explode(',', $topics);
            $topics = array_unique($topics);
            foreach ($topics as $key => $value) {
                $tmp['target_id'] = (int)$value;
                $tmp['source_id'] = (int)$item_id;
                $_data[] = $tmp;
            }
            if(db('topic_related')->insertAll($_data))
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 更新话题讨论数
     * @param $topic_ids
     * @return bool
     */
    public static function updateTopicDiscussCount($topic_ids): bool
    {
        $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',', $topic_ids);
        foreach ($topic_ids as $topic_id)
        {
            $discuss = db('topic_relation')->where(['topic_id'=>$topic_id,'status'=>1])->count();
            $discuss_week = db('topic_relation')->where([['topic_id','=',$topic_id],['status','=',1],['create_time','>',time()-(7*ONE_DAY)]])->count();
            $discuss_month = db('topic_relation')->where([['topic_id','=',$topic_id],['status','=',1],['create_time','>',time()-(30*ONE_DAY)]])->count();
            db('topic')->where('id',$topic_id)->update([
                'discuss'=>$discuss,
                'discuss_week'=>$discuss_week,
                'discuss_month'=>$discuss_month,
                'discuss_update'=>time()
            ]);
        }
        return true;
    }

    //根据话题ids获取话题列表
    public static function getTopicByIds($topic_ids,$item_type=null)
    {
        if (!$topic_ids) {
            return false;
        }

        $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
        // 兼容 uniApp 话题数据
        if (!empty($topic_ids) && is_array($topic_ids[0])) {
            $topic_ids = array_unique(array_column($topic_ids, 'id'));
        }
        if($item_type)
        {
            $topics = db('topic')->whereIn('id',implode(',', $topic_ids))->where(['item_type'=>$item_type])->column('id,title,description,pic,pid,discuss,url_token');
        }else{
            $topics = db('topic')->whereIn('id',implode(',', $topic_ids))->select()->toArray();
        }

        $result = array();

        $pinyin = new Pinyin();
        foreach ($topics AS $key => $val)
        {
            $val['url'] = (string)url('topic/detail',['id'=>$val['id']]);
            if (!$val['url_token'])
            {
                $val['url_token'] = $pinyin->permalink($val['title'],'');
            }
            $val['description'] = str_cut(strip_tags(htmlspecialchars_decode($val['description'])),0,150);
            $result[$val['id']] = $val;
        }
        return $result;
    }

    // 获取单个话题详情
    public static function getById($id)
    {
        if (!$topic = db('topic')->where('id', $id)->find()) return false;
        $topic['url_token'] = (new Pinyin())->permalink($topic['title'],'');
        $topic['description'] = str_cut(strip_tags(htmlspecialchars_decode($topic['description'])),0,150);
        return $topic;
    }

    //根据关联ids和类型获取话题列表
    public static function getTopicByItemIds($item_ids, $item_type)
    {
        if (!is_array($item_ids) || count($item_ids) == 0) {
            return false;
        }
        $item_topics = db('topic_relation')
            ->whereIn('item_id',$item_ids)
            ->where(['item_type'=>$item_type])
            ->column('topic_id,item_type,item_id');

        $result = array();
        if (!$item_topics)
        {
            return false;
        }

        $topic_ids = array_column($item_topics,'topic_id');
        $topics_info = self::getTopicByIds(array_unique($topic_ids));
        foreach ($item_topics AS $key => $val)
        {
            if(!isset($topics_info[$val['topic_id']])) continue;

            $result[$val['item_id']][] = $topics_info[$val['topic_id']];
        }
        return $result;
    }

    //获取话题内容列表
    public static function getTopicRelationList($uid,$topic_id,$item_type=null,$page = 1, $per_page = 10)
    {
        $where = array();
        $where[]=['topic_id','=',$topic_id];
        $where[]=['status','=',1];
        if($item_type)
        {
            $where[] = ['item_type','=',$item_type];
        }
        $contents = db('topic_relation')->where($where)->page($page,$per_page)->select();
        return PostRelation::processPostList($contents,$uid);
    }

    /**
     * 我关注的话题
     * @param $uid
     * @param int $limit
     * @return false
     */
    public static function getFocusTopicByRand($uid, int $limit = 5)
    {
        if (!$uid) {
            return false;
        }

        $focus_topics = db('topic_focus')->where(['uid'=>intval($uid)])->select()->toArray();
        if (!$focus_topics) return false;

        $topic_ids = array_column($focus_topics,'topic_id');

        if(empty($topic_ids)) return false;

        $topic_list = db('topic')->whereRaw("id IN(".implode(',',$topic_ids).")")->orderRaw('RAND()')->limit($limit)->select()->toArray();

        foreach ($topic_list as $k=> $v)
        {
            $topic_list[$k]['question_count'] = db('topic_relation')->where(['topic_id'=>intval($v['id']),'item_type'=>'question'])->count();
            $topic_list[$k]['article_count'] = db('topic_relation')->where(['topic_id'=>intval($v['id']),'item_type'=>'article'])->count();
        }
        return $topic_list;
    }

    /**
     * 获取话题列表
     * @param null $item_type
     * @param int $item_id
     * @return array|false
     */
    public static function getTopics($item_type=null, int $item_id=0)
    {
        $topic_list = db('topic')->select()->toArray();
        if(!$topic_list)
        {
            return false;
        }

        foreach ($topic_list as $key=>$val)
        {
            $topic_list[$key]['is_checked'] = 0;
            if(self::checkTopicRelation($val['id'], $item_id, $item_type))
            {
                $topic_list[$key]['is_checked'] = 1;
            }
        }
        return  $topic_list;
    }

    /**
     * 获取热门话题
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $per_page
     * @param int $page
     * @param bool $is_api
     * @return mixed
     */
    public static function getHotTopics(int $uid=0, array $where=[], array $order=[], int $per_page=5, int $page=1,$is_api=false)
    {
        $where[] = ['status','=',1];
        $where[] = ['discuss_month','>',0];
        if(!$where)
        {
            $where[] = ['discuss_update','>',time()-30*24*60*60];
        }
        $order = !empty($order) ? $order : ['top'=>'DESC','focus'=>'DESC','discuss_month'=>'DESC'];
        $list = db('topic')
            ->where($where)
            ->orderRaw('RAND()')
            ->order($order)
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'query'=>request()->param()
                ]
            )->toArray();
        $pic = request()->domain().'/static/common/image/topic.svg';
        foreach ($list['data'] as $key=>$val)
        {
            if($is_api)
            {
                $list['data'][$key]['pic'] = $val['pic'] ? ImageHelper::replaceImageUrl($val['pic']) : $pic;
            }
            $list['data'][$key]['is_focus'] = db('topic_focus')->where(['uid'=>intval($uid),'topic_id'=>$val['id']])->value('id');
        }
        return $list;
    }

    /**
     * 获取最近使用话题
     */
    public static function getRecentTopic($uid,$item_type='', $item_id=0)
    {
        if(!$uid) return false;
        $recentTopic = db('topic_relation')->page(1,10)->order('create_time','DESC')->where(['uid'=>intval($uid)])->column('topic_id');
        if(!$recentTopic)
        {
            return false;
        }
        $topic_ids = array_unique($recentTopic);
        $list = self::getTopicByIds($topic_ids);
        foreach ($list as $key=>$val)
        {
            $list[$key]['is_checked'] = 0;
            if(self::checkTopicRelation($val['id'], $item_id, $item_type))
            {
                $list[$key]['is_checked'] = 1;
            }
        }
        return $list;
    }

    /**
     * 删除话题
     * @param $topic_id
     * @return bool|null
     */
    public static function removeTopic($topic_id): ?bool
    {
        if(!$topic_id)
        {
            self::setError('参数错误');
            return false;
        }

        if(db('topic')->where(['id'=>$topic_id])->delete())
        {
            //删除话题关联
            db('topic_relation')->where(['topic_id'=>$topic_id])->delete();
            //删除话题关注
            db('topic_focus')->where(['topic_id'=>$topic_id])->delete();
            return true;
        }
        return false;
    }

    /**
     * 锁定话题
     * @param $id
     * @param int $uid
     * @return bool
     */
    public static function lockTopic($id,$uid=0): bool
    {
        $lock= db('topic')->where(['id'=>$id])->value('lock') ? 0 : 1;

        if(!self::update(['lock'=>$lock],['id'=>$id])){
            return false;
        }
        $lock ? LogHelper::addActionLog('lock_topic','topic',$id,$uid) :LogHelper::addActionLog('unlock_topic','topic',$id,$uid);
        return true;
    }

    /**
     * 根据话题获取相关内容
     * @param $item_id
     * @param $item_type
     * @param mixed $topic_ids
     * @param int $uid
     * @param int $limit
     * @return array|bool
     */
    public static function getRecommendPost($item_id,$item_type=null,$topic_ids=0,int $uid=0,int $limit=10)
    {
        $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
        if(!$topic_ids) return [];

        $cache_key = 'cache_relation_recommend_post_list_time_'.md5($item_id).md5($item_type);

        $contents = cache($cache_key)?:[];
        $cache_relation_list_time = get_setting('cache_relation_list_time');
        if($cache_relation_list_time && $contents)
        {
            return $contents;
        }

        $topic_where = $item_type ? 'item_type="'.$item_type.'" AND status=1' : 'status=1';
        if($item_type)
        {
            $topic_where .= ' AND item_id!='.$item_id;
        }
        $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
        $relationInfo = db('topic_relation')
            ->whereRaw($topic_where.$topicIdsWhere)
            ->column('item_id,item_type');
        if(!$relationInfo) return [];
        $item_ids = array_column($relationInfo,'item_id');
        $item_types = array_column($relationInfo,'item_type');

        if($item_ids) $where[] = ['item_id','in',implode(',', $item_ids)];
        if($item_types) $where[] = ['item_type','in',implode(',', $item_types)];
        $order['popular_value'] = 'desc';
        $where[] = ['popular_value','>',0];

        $list = db('post_relation')
            ->where($where)
            ->orderRaw('RAND()')
            ->order($order)
            ->paginate(
                [
                    'list_rows'=> $limit,
                    'page' => 1,
                    'query'=>request()->param(),
                ]
            )->toArray();

        $contents = PostRelation::processPostList($list['data'],$uid);
        cache($cache_key,$contents,$cache_relation_list_time*60);
        return $contents;
    }

    /**
     * 获取话题记录
     * @param $topic_id
     * @param int $uid
     * @param int $page
     * @param int $per_page
     * @param string $pjax
     * @return array
     */
    public static function getLogs($topic_id,int $uid=0,int $page=1,int $per_page=15,string $pjax='tabMain'): array
    {
        $topic_id = intval($topic_id);
        $action = [
            'modify_question_topic',
            'modify_article_topic',
            'create_topic',
            'update_topic',
            'lock_topic',
            'unlock_topic',
        ];
        $action_ids = [];
        if ($action) {
            $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
        }
        if (!empty($action_ids)) {
            $where3[] = ['action_id', 'IN', $action_ids];
        }
        $where3[] = ['status', '=', 1];
        $where3[] = ['record_id', '=', $topic_id];
        $where3[] = ['record_type', '=', 'topic'];
        $action_log_list = db('action_log')
            ->where($where3)
            ->order(['create_time' => 'desc'])
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'pjax'=>$pjax
                ]
            );

        $pageVar = $action_log_list->render();
        $action_log_list = $action_log_list->toArray();
        $data = $action_log_list['data'];
        $result_list = LogHelper::parseActionLog($uid,$data);
        $action_log_list['data'] = $result_list?:[];
        return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
    }

    /**
     * 获取话题的所有子集
     * @param $topic_id
     * @param bool $self
     * @return array|bool
     */
    public static function getTopicWithChildIds($topic_id,bool $self=false)
    {
        if(!$topic_id) return false;
        $where = ['status'=>1];
        $child_data = db('topic')->where($where)->column('id,pid');
        $tree = TreeHelper::instance()->init($child_data,'pid');
        return $tree->getChildrenIds($topic_id,$self);
    }

    /**
     * 获取话题树结构
     * @param int $parent_id
     * @return array
     */
    public static function getTreeTopicList(int $parent_id=0): array
    {
        $topic_list = db('topic')->where(['status'=>1])->select()->toArray();
        $result_list = [];
        $tree = TreeHelper::instance()->init($topic_list,'pid');
        if($parent_id)
        {
            $result_list = $tree->getChildren($parent_id,true);
        }else{
            foreach ($topic_list as $k=>$v)
            {
                if($v['is_parent'])
                {
                    $result_list = array_merge($tree->getChildren($v['id'],true),$result_list);
                }
            }
        }
        return Tree::toTree($result_list);
    }

    /**
     * 获取话题树结构
     * @param int $parent_id
     * @return array
     */
    public static function getTreeTopicListHtml(int $parent_id=0): array
    {
        $topic_list = db('topic')->where(['status'=>1])->select()->toArray();
        $result_list = [];
        $tree = TreeHelper::instance()->init($topic_list,'pid');
        if($parent_id)
        {
            $result_list = $tree->getChildren($parent_id,true);
        }else{
            foreach ($topic_list as $k=>$v)
            {
                if($v['is_parent'])
                {
                    $result_list = array_merge($tree->getChildren($v['id'],true),$result_list);
                }
            }
        }
        return TreeHelper::tree($result_list);
    }

    /**
     * 获取当前话题的根话题
     * @param $topic_id
     * @return array
     */
    public static function getParentTopic($topic_id): array
    {
        $child_data = db('topic')->where(['status'=>1])->column('id,pid,is_parent,title');
        $tree = TreeHelper::instance()->init($child_data,'pid');
        $parentsIds = $tree->getParentsIds($topic_id);
        $parentInfo = [];
        foreach ($child_data as $k=>$v)
        {
            if($v['is_parent'] && in_array($v['id'],$parentsIds))
            {
                $parentInfo = $v;
            }
        }
        return $parentInfo;
    }
}