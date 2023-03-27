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
use app\common\library\helper\HtmlHelper;
use app\common\library\helper\ImageHelper;
use app\common\library\helper\IpHelper;
use app\common\library\helper\LogHelper;
use app\common\library\helper\TextDiffHelper;
use app\logic\common\FocusLogic;
use app\logic\search\libs\ElasticSearch;
use Overtrue\Pinyin\Pinyin;
use Pay\Exceptions\Exception;
use think\facade\Db;
use WordAnalysis\Analysis;

/**
 * 文章模型
 * Class Article
 * @package app\model
 */
class Article extends BaseModel
{
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;

	/**
	 * 文章详情获取器
	 * @param $value
	 * @return string
	 */
	public function getMessageAttr($value)
    {
		return htmlspecialchars_decode($value);
	}

    /**
     * 获取文章详情
     * @param $id
     * @param string $field
     * @param int $status
     * @return mixed
     */
	public static function getArticleInfo($id,string $field='*',$status=1)
	{
        $where['status'] = $status;
        $where['id'] = intval($id);
        $article_info = db('article')->where($where)->field($field)->find();
        if(isset($article_info['message']))
        {
            $article_info['message'] = htmlspecialchars_decode($article_info['message']);
        }
        return $article_info;
	}

    /**
     * 根据文章id获取文章列表
     * @param $article_ids
     * @return array|false
     */
	public static function getArticleByIds($article_ids)
	{
		if (!is_array($article_ids) OR sizeof($article_ids) == 0) return false;
		array_walk_recursive($article_ids, 'intval');
		$articles_list = db('article')->where(['status'=>1])->whereIn('id',implode(',', $article_ids))->select()->toArray();
		$result = array();
		if ($articles_list)
		{
			foreach ($articles_list AS $key => $val)
			{
			    $val['message'] = htmlspecialchars_decode($val['message']);
                $val['title'] = htmlspecialchars_decode($val['title']);
				$result[$val['id']] = $val;
			}
		}
		return $result;
	}

    /**
     * 保存文章
     * @param $uid
     * @param $postData
     * @param string $access_key
     * @return false|int|string
     */
	public static function saveArticle($uid,$postData,string $access_key='')
	{
        $pinyin = new Pinyin();
        $token = $pinyin->permalink($postData['title'],'');
        $url_token = md5($token);
        $url_token = db('article')
            ->where([
                ['url_token','=',$url_token],
                ['id','<>',intval($postData['id'])]
            ])->value('id') ? md5($token.uniqueDate(2)) : $url_token;

		$column_id = $postData['column_id'] ?? 0;
		$data = array(
			'uid' => (int)$uid,
			'title' => sqlFilter(strip_tags($postData['title'])),
			'message' => HtmlHelper::fetchContentImagesToLocal($postData['message'],'article',$uid,true),
            'search_text'=>sqlFilter(strip_tags(htmlspecialchars_decode($postData['message']))),
			'category_id' => $postData['category_id'] ?? 0,
            'user_ip'=>IpHelper::getRealIp(),
			'column_id' => (int)$column_id,
			'cover' => $postData['cover'],
			'create_time'=>time(),
			'update_time'=>time(),
            'url_token'=>$url_token,
			'status' =>(isset($postData['wait_time']) && $postData['wait_time']) ? 3 : 1
		);

        Db::startTrans();
        try {
            $article_id = db('article')->insertGetId($data);
            if(!$article_id) {
                self::setError('文章插入失败');
                return false;
            }
            //添加话题关联
            if(isset($postData['topics']) && $postData['topics'])
            {
                $topics = is_array($postData['topics']) ? array_filter($postData['topics']) : explode(',',trim($postData['topics']));
                if (!empty($topics))
                {
                    foreach ($topics as $title)
                    {
                        if($title)
                        {
                            $topic_id = Topic::saveTopic($title, $uid, true);
                            Topic::saveTopicRelation($uid, $topic_id, $article_id, 'article');
                        }
                    }
                }
            }

            ElasticSearch::instance()->create('article',self::getArticleInfo($article_id));

            //更新用户文章数
            $article_count = self::where(['uid'=>$uid,'status'=>1])->count();
            Users::updateUserFiled($uid,['article_count'=>$article_count]);

            //更新专栏文章数量
            if($column_id)
            {
                db('column')->where(['id'=>$column_id])->inc('post_count',1)->update();
                //添加行为日志
                LogHelper::addActionLog('create_column_article','column',$column_id,$uid,'0',0,'article',$article_id);
            }

            //添加行为日志
            LogHelper::addActionLog('publish_article','article',$article_id,$uid);

            //添加积分记录
            LogHelper::addIntegralLog('NEW_ARTICLE',$article_id,'article',$uid);

            //更新附件
            Attach::updateAttach('article',$article_id,$access_key);
            Attach::updateAttach('article_attach',$article_id,$access_key);

            /**
             * 内容存储后操作
             */
            hook('article_model_save',['id'=>$article_id,'data'=>$postData]);

            //加入内容聚合表
            PostRelation::savePostRelation($article_id,'article');

            //删除草稿
            Draft::deleteDraftByItemID($uid,'article',0);
            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::setError($e->getMessage());
            return  false;
        }

		return $article_id;
	}

    /**
     * 更新文章
     * @param $uid
     * @param $postData
     * @param string $access_key
     * @return mixed
     */
	public static function updateArticle($uid,$postData,string $access_key='')
	{
		$column_id = $postData['column_id'] ?? 0;
        $postData['id'] = intval($postData['id']);
		$data = array(
			'uid' => (int)$uid,
			'title' => strip_tags($postData['title']),
			'message' => HtmlHelper::fetchContentImagesToLocal($postData['message'],'article',$uid,true),
            'search_text'=>strip_tags(htmlspecialchars_decode($postData['message'])),
			'category_id' => $postData['category_id'] ?? 0,
			'column_id' => (int)$column_id,
			'cover' => $postData['cover'],
			'update_time'=>time(),
			'status' =>1
		);
        Db::startTrans();
        try {
            $article_info = db('article')->where('id',intval($postData['id']))->find();

            unset($data['uid']);
            self::update($data,['id'=>intval($postData['id'])]);

            //添加行为日志 标题有变化
            if(!empty(TextDiffHelper::compare($article_info['title'],$data['title'])))
            {
                LogHelper::addActionLog('modify_article_title','article',$article_info['id'],$uid,0,time(),null,0,['content'=>$article_info['title']]);
            }

            $old_message = strip_tags(htmlspecialchars_decode($article_info['message']));
            $new_message = strip_tags(htmlspecialchars_decode($data['message']));

            //添加行为日志 描述有变化
            if(!empty(TextDiffHelper::compare($old_message,$new_message)))
            {
                LogHelper::addActionLog('modify_article_detail','article',$article_info['id'],$uid,0,time(),null,0,['old_content'=>$article_info['message'],'content'=>$data['message']]);
            }

            //内容修改记录
            $old_topics = Topic::getTopicByItemType('article',$article_info['id']);
            $old_info = [
                'message'=>$old_message,
                'title'=>$article_info['title'],
                'category_id'=>$article_info['category_id'],
                'topics'=>$old_topics?array_column($old_topics,'id'):[]
            ];
            $new_info = [
                'message'=>$new_message,
                'title'=>$data['title'],
                'category_id'=>$data['category_id'],
                'topics'=>$postData['topics']
            ];
            $extends = [
                'old_info'=>$old_info,
                'new_info'=>$new_info
            ];

            LogHelper::addActionLog('modify_log','article',$article_info['id'],$uid,0,0,null,0,$extends);

            //ES索引
            ElasticSearch::instance()->update('article',$article_info);

            if(isset($postData['topics']) && $postData['topics']){
                $topics = is_array($postData['topics']) ? $postData['topics'] : explode(',',$postData['topics']);
                //更新话题关联
                if($topics && db('topic_relation')->where(['item_type' => 'article', 'item_id' => $postData['id']])->delete()) {
                    foreach (array_unique($topics) as $key => $title)
                    {
                        $topic_id = Topic::saveTopic($title, $uid);
                        Topic::saveTopicRelation($uid, $topic_id, $postData['id'], 'article');
                    }
                }
            }

            //更新编辑器附件
            Attach::updateAttach('article',$postData['id'],$access_key);

            //更新附件
            Attach::updateAttach('article_attach',$postData['id'],$access_key);

            if($column_id)
            {
                LogHelper::addActionLog('modify_column_article','column',$column_id,$uid,'0',0,'article',$postData['id']);
            }
            //删除草稿
            Draft::deleteDraftByItemID($uid,'article',$postData['id']);

            /**
             * 内容存储后操作
             */
            hook('article_model_update',['id'=>$postData['id'],'data'=>$postData]);

            // 提交事务
            Db::commit();
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
        return $postData['id'];
	}

    /**
     * 删除文章集合
     * @param $ids
     * @param bool $realMove
     * @return bool
     */
    public static function removeArticle($ids,bool $realMove=false): bool
    {
        $ids = is_array($ids) ? $ids : explode(',',$ids);
        $article_infos = db('article')->whereIn('id',$ids)->column('id,status,uid,column_id');
        if(!$article_infos)
        {
            return false;
        }

        db()->startTrans();
        try {
            if($realMove)
            {
                db('article_comment')->whereIn('article_id',$ids)->delete();
                db('article')->whereIn('id',$ids)->delete();
                Attach::removeAttachByItemIds('article',$ids);
                foreach ($article_infos as $article_info)
                {
                    if($article_info['column_id'])
                    {
                        $post_count = db('article')->where(['column_id'=>$article_info['column_id'],'status'=>1])->count();
                        Column::update(['post_count'=>$post_count],['id'=>$article_info['column_id']]);
                    }
                    PostRelation::updatePostRelation($article_info['id'],'article',['status'=>0]);
                    db('browse_records')->whereIn('item_id',$ids)->where(['item_type'=>'article'])->delete();
                }
            }else{
                if(!db('article')->whereIn('id',$ids)->update(['status'=>0]))
                {
                    return false;
                }
                //更新专栏文章数
                foreach ($article_infos as $article_info)
                {
                    if($article_info['column_id'])
                    {
                        $post_count = db('article')->where(['column_id'=>$article_info['column_id'],'status'=>1])->count();
                        Column::update(['post_count'=>$post_count],['id'=>$article_info['column_id']]);
                    }
                    //逻辑删除行为记录
                    LogHelper::removeActionLog('article',$article_info['id']);
                    PostRelation::updatePostRelation($article_info['id'],'article',['status'=>0]);
                    db('browse_records')->whereIn('item_id',$ids)->where(['item_type'=>'article'])->update(['status'=>0]);
                }
            }
            // 提交事务
            db()->commit();
        } catch (\Exception $e) {
            // 回滚事务
            db()->rollback();
            self::setError($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * 恢复文章集合
     * @param $ids
     * @return bool
     */
    public static function recordArticle($ids): bool
    {
        $ids = is_array($ids) ? $ids : explode(',',$ids);
        $article_infos = db('article')->whereIn('id',$ids)->column('id,status,uid,column_id');
        if(!$article_infos)
        {
            return false;
        }

        if(!db('article')->whereIn('id',$ids)->update(['status'=>1]))
        {
            return false;
        }
        db('browse_records')->whereIn('item_id',$ids)->where(['item_type'=>'article'])->update(['status'=>1]);
        db()->startTrans();
        try {
            //更新专栏文章数
            foreach ($article_infos as $article_info)
            {
                if($article_info['column_id'])
                {
                    $post_count = db('article')->where(['column_id'=>$article_info['column_id'],'status'=>1])->count();
                    Column::update(['post_count'=>$post_count],['id'=>$article_info['column_id']]);
                }
                //逻辑删除行为记录
                LogHelper::recordActionLog('article',$article_info['id']);
                PostRelation::updatePostRelation($article_info['id'],'article',['status'=>1]);
            }
            // 提交事务
            db()->commit();
        } catch (\Exception $e) {
            // 回滚事务
            db()->rollback();
            self::setError($e->getMessage());
            return false;
        }
        //更新首页表
        return true;
    }

    /**
	 * 更新文章浏览量
	 * @param $article_id
	 * @param int $uid
	 * @return mixed
	 */
	public static function updateArticleViews($article_id,int $uid=0)
	{
		$cache_key = md5('cache_article_'.$article_id.'_'.$uid);
		$cache_result = cache($cache_key);
		if($cache_result) return true;
		cache($cache_key,$cache_key,['expire'=>60]);
		if($info = self::getArticleInfo($article_id))
        {
            db('article')->where(['id'=>$article_id])->inc('view_count')->update();
            //更新专栏浏览量
            if($info['column_id'] && db('column')->where(['id'=>$info['column_id']])->value('id'))
            {
                db('column')->where(['id'=>$info['column_id']])->inc('view_count')->update();
            }
        }
        return true ;
	}

    /**
     * 获取文章详情
     * @param $id
     * @param string $field
     * @return mixed
     */
    public static function getArticleInfoField($id,string $field="*")
    {
        $article_info=cache('article_'.$id);
        if(!$article_info){
            $article_info=db('article')->field($field)->find($id);
            cache('article_'.$id,$article_info);
        }
        return $article_info;
    }

    /**
     * 保存文章评论
     */
	public static function saveArticleComment($article,$message,$user_info,$at_uid=0,$pid=0)
	{
        $at_uid = intval($at_uid);
        $comment_pid= 0;
        if($pid)
        {
            $comment_pid = db('article_comment')->where('id',$pid)->value('pid');
        }

        $pid = $comment_pid ? : $pid;
		$data = array(
			'uid' => (int)$user_info['uid'],
			'message' => Users::parseAtUser($message)[0],
			'at_uid' => intval($at_uid),
			'article_id' => $article['id'],
			'create_time' => time(),
            'pid' => $at_uid ? $pid : 0
		);
		$comment_id = db('article_comment')->insertGetId($data);
		if(!$comment_id) {
            return false;
        }

        if($at_uid){
            send_notify($user_info['uid'],intval($at_uid),'TYPE_PEOPLE_AT_ME','article',$article['id']);
        }

        if($user_info['uid']!=$article['uid'])
            send_notify($user_info['uid'],$article['uid'],'NEW_ARTICLE_COMMENT','article',$article['id']);

		//更新文章评论数
		$comment_count = db('article_comment')->where(['article_id'=>$article['id'],'status'=>1])->count();
		self::update(['comment_count'=>$comment_count],['id'=>$article['id']]);

		//更新首页数据
		PostRelation::updatePostRelation($article['id'],'article',['answer_count'=>$comment_count]);
		//TODO 记录

		//TODO 给文章发起者发送新评论的通知
		return [
		    'comment_id' => $comment_id,
            'comment_count' => $comment_count,
            'id' => $comment_id,
            'pid' => $data['pid'],
            'create_time' => date_friendly($data['create_time']),
            'message' => strip_tags(htmlspecialchars_decode($data['message']))
        ];
	}

    /**
     * 获取文章评论列表
     * @param $article_id
     * @param null $order
     * @param int $page
     * @param int $per_page
     * @param string $pjax
     * @return array
     */
	public static function getArticleCommentList($article_id,$order=null,int $page=1,int $per_page=10,string $pjax='comment-container'): array
    {
		$map = ['article_id'=>intval($article_id),'status'=>1];
        $sort = [];
		if($order)
        {
            switch ($order)
            {
                case 'hot':
                    $sort['agree_count'] = 'DESC';
                    break;

                default :
                    $sort['create_time'] = 'DESC';
            }
        }

		$comments = db('article_comment')
            ->where($map)
            ->order($sort)
            ->page($page,$per_page)
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'query'=>request()->param(),
                    'pjax'=>$pjax
                ]
            );
		$pageRender = $comments->render();
        $comments = $comments->toArray();
		foreach ($comments['data'] as $key => $val)
		{
            if(!$user_info = Users::getUserInfo($val['uid'],'user_name,nick_name,avatar,uid'))
                $user_info = ['url'=>'javascript:;','uid'=>0,'nick_name'=>'未知用户','name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
			$comments['data'][$key]['user_info'] = $user_info;
		}
        $comments['page_render'] = $pageRender;
		return $comments;
	}

    /**
     * 删除评论
     * @param $id
     * @return false
     */
	public static function deleteComment($id): bool
    {
		$comment = db('article_comment')->find(intval($id));
		Db::startTrans();
		try {
            db('article_comment')->where(['id'=>intval($id)])->delete();
            db('article_comment')->where(['pid' => $id])->delete();

            $comment_count = db('article_comment')->where(['article_id'=>$comment['article_id'],'status'=>1])->count();
            db('article')->where('id', $comment['article_id'])->update(['comment_count'=>$comment_count]);
            //更新首页数据
            PostRelation::updatePostRelation($comment['article_id'],'article',['answer_count'=>$comment_count]);
            Db::commit();
        } catch (\Exception $e) {
		    Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
        return true;
	}

    //获取相关文章
	public static function getRelationArticleList($article_id,$limit=10)
    {
        if(!intval($article_id)) {
            return false;
        }

        $article_id = intval($article_id);
        $cache_key = 'cache_relation_article_list_time_'.md5(intval($article_id));

        $list = cache($cache_key)?:[];
        $cache_relation_list_time = get_setting('cache_relation_list_time');
        if($cache_relation_list_time && $list)
        {
            return $list;
        }

        $article_info = self::getArticleInfo($article_id);
        $keywords = Analysis::getKeywords($article_info['title']);
        $keywords = $keywords ? explode(',', $keywords) : [];

        if($keywords)
        {
            foreach ($keywords as $key=> $keyword)
            {
                $keywords[$key]=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keyword));
            }
        }

        $where =$keywords ? "status=1 and (title regexp'".implode('|',$keywords)."')" : 'status=1';
        $articleIds = db('article')
            ->whereRaw($where)
            ->orderRaw('RAND()')
            ->order('view_count','DESC')
            ->limit($limit)
            ->select()
            ->toArray();
        if($articleIds = array_column($articleIds,'id'))
        {
            unset($articleIds[array_search($article_id, $articleIds, true)]);
            $list = self::getArticleByIds($articleIds);
            cache($cache_key,$list,$cache_relation_list_time*60);
        }
        return $list;
    }

    //获取相关文章
    public static function getRelationArticleListByMobile($article_id,$page=1,$per_page=10)
    {
        if(!intval($article_id)) {
            return false;
        }

        $article_id = intval($article_id);
        $cache_key = 'cache_relation_article_list_time_'.md5(intval($article_id));

        $list = cache($cache_key)?:[];
        $cache_relation_list_time = get_setting('cache_relation_list_time');
        if($cache_relation_list_time && $list)
        {
            return $list;
        }

        $article_info = self::getArticleInfo($article_id);
        $keywords = Analysis::getKeywords($article_info['title']);
        $keywords = $keywords ? explode(',', $keywords) : [];
        $where =$keywords ? "status=1 and (title regexp'".implode('|',$keywords)."')" : 'status=1';
        $list = db('article')
            ->whereRaw($where)
            ->orderRaw('RAND()')
            ->order('view_count','DESC')
            ->page($page,$per_page)
            ->paginate(
                [
                    'list_rows'=> $per_page,
                    'page' => $page,
                    'query'=>request()->param(),
                ]
            )->toArray();
        if ($list)
        {
            cache($cache_key,$list,$cache_relation_list_time*60);
        }
        return $list;
    }

    /**
     * 获取文章列表
     * @param null $uid
     * @param null $sort
     * @param null $topic_ids
     * @param null $category_id
     * @param int $page
     * @param int $per_page
     * @param int $relation_uid
     * @param string $pjax
     * @return array
     */
    public static function getArticleList($uid=null,$sort = null, $topic_ids = null, $category_id = null,int $page=1, int $per_page=0,int $relation_uid=0,string $pjax='tabMain'): array
    {
        $data_list = [];
        $key = md5($sort.'-'.$category_id.'-'.($topic_ids && is_array($topic_ids) ? implode(',',$topic_ids) :$topic_ids).'-'.$page.'-'.$per_page.'-'.$relation_uid);
        $cache_key = 'cache_list_article_data_'.$key;

        if($cache_list_time = get_setting('cache_list_time'))
        {
            $data_list = cache($cache_key);
        }
        if($data_list) return $data_list;

        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        if($relation_uid)
        {
            $where[] = ['uid','=',$relation_uid];
        }

        //推荐内容
        if($sort=='recommend')
        {
            return PostRelation::getRecommendPost($uid,'article', $topic_ids, $category_id,$page, $per_page,$relation_uid,$pjax);
        }

        if($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
        }

        if($sort=='hot')
        {
            $order['popular_value'] = 'desc';
            $where[] = ['popular_value','>',1];
        }

        if($sort=='new'){
            $order['set_top_time'] = 'DESC';
            $order['update_time'] = 'DESC';
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

        $list = db('article')->where($where)->order($order)->paginate(
            [
                'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                'page' => $page,
                'query'=>request()->param(),
                'pjax'=>$pjax
            ]
        );
        $pageVar = $list->render();
        $allList = $list->toArray();
        $users_info = Users::getUserInfoByIds(array_column($allList['data'],'uid'));
        $topic_infos = Topic::getTopicByItemIds(array_column($allList['data'],'id'), 'article');
        $result_list = [];
        foreach ($allList['data'] as $key=>$data)
        {
            $result_list[$key] = $data;
            $result_list[$key]['title'] = htmlspecialchars_decode($data['title']);
            $result_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($data['message'])),0,120);
            $cover  = ImageHelper::srcList(htmlspecialchars_decode($data['message']));
            $result_list[$key]['img_list'] = $cover;
            $result_list[$key]['topics'] = $topic_infos[$data['id']] ?? [];
            $result_list[$key]['user_info'] = $users_info[$data['uid']] ?? ['url'=>'javascript:;','uid'=>0,'name'=>'未知用户'];
            $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'article',$uid);
        }

        $data_list['list'] = $result_list;
        $data_list['page'] = $pageVar;
        $data_list['total'] = $allList['last_page'];

        if($cache_list_time)
        {
            cache($cache_key,$data_list,['expire'=>$cache_list_time*60]);
        }

        return $data_list;
    }
}