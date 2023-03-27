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
use app\common\library\helper\IpHelper;
use app\common\library\helper\IpLocation;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\logic\search\libs\ElasticSearch;
use think\facade\Db;
use think\facade\Request;

class Answer extends BaseModel
{
	protected $createTime = 'create_time';
	protected $updateTime = 'update_time';
	protected $autoWriteTimestamp = true;

    /**
     * 获取回答信息
     * @param $answer_id
     * @return mixed
     */
	public static function getAnswerInfoById($answer_id)
	{
		$answer_info = db('answer')->where(['id'=>$answer_id,'status'=>1])->find();
        if(isset($answer_info['content']) && $answer_info['content'])
        {
            $answer_info['content']= htmlspecialchars_decode($answer_info['content']);
        }
		return $answer_info;
	}

    /**
     * 获取回答信息
     * @param $answer_ids
     * @return array
     */
    public static function getAnswerInfoByIds($answer_ids): array
    {
        $answer_ids = is_array($answer_ids) ? $answer_ids : explode(',',$answer_ids);
        $answer_list = db('answer')->whereIn('id',$answer_ids)->where(['status'=>1])->select()->toArray();
        $result = [];

        foreach ($answer_list as $key=>$val)
        {
            $result[$val['id']] = $val;
        }
        return $result;
    }

    /**
     * 保存回答
     * @param $data
     * @param string $access_key
     * @return array|false
     */
	public static function saveAnswer($data,string $access_key='')
    {
        db()->startTrans();
        $postData = [
            'question_id'=>intval($data['question_id']),
            'is_anonymous'=>$data['is_anonymous']??0,
            'uid'=>$data['uid']
        ];
        try {
            $postData['answer_user_ip'] = $data['answer_user_ip'] ?? IpHelper::getRealIp();
            //是否开启显示用户IP地址
            $Ip = new IpLocation(); // 实例化类 参数表示IP地址库文件
            $postData['answer_user_local'] = $data['answer_user_local'] ?? $Ip->getLocation(IpHelper::getRealIp())['country'];

            $focus_question = $data['focus_question'] ?? 0;
            unset($data['focus_question']);
            $question_info= Question::getQuestionInfo($data['question_id'],'uid,title');
            $postData['search_text'] = strip_tags(htmlspecialchars_decode($data['content']));
            $postData['content'] = HtmlHelper::fetchContentImagesToLocal($data['content'],'answer',$data['uid'],true);
            if ($data['id']) {
                $postData['update_time'] = time();
                $uid = $data['uid'];
                unset($data['uid']);
                $ret = db('answer')->where(['id' => $data['id']])->update($postData);
                if($ret)
                {
                    Attach::updateAttach('answer',$data['id'],$access_key);
                    //添加行为日志
                    LogHelper::addActionLog('modify_answer','answer',$data['id'],$uid,$data['is_anonymous']??0,0,'question',intval($data['question_id']));

                    ElasticSearch::instance()->update('answer',self::getAnswerInfoById($data['id']));

                    Draft::deleteDraftByItemID($uid,'answer',$data['id']);
                }
            } else {
                $postData['create_time'] = time();
                $ret = db('answer')->insertGetId($postData);
                if($ret)
                {
                    //添加问题关注
                    if($focus_question)
                    {
                        FocusLogic::updateFocusAction(intval($data['question_id']), 'question', $data['uid']);
                    }

                    //排除自己
                    if($data['uid']!=$question_info['uid'])
                    {
                        //添加积分记录
                        LogHelper::addIntegralLog('ANSWER_QUESTION',$ret,'answer',$data['uid']);

                        //给发起人添加积分记录
                        LogHelper::addIntegralLog('QUESTION_ANSWER',$ret,'answer',$question_info['uid']);
                    }

                    //添加行为日志
                    LogHelper::addActionLog('publish_answer','answer',$ret,$data['uid'],$data['is_anonymous']??0,0,'question',intval($data['question_id']));

                    //邀请回答积分记录
                    $invite_info = db('question_invite')->where(['question_id'=> (int)$data['question_id'], 'recipient_uid'=> (int)$data['uid']])->find();
                    $answer_count = db('answer')->where(['question_id'=> (int)$data['question_id'], 'uid'=> (int)$data['uid']])->count();

                    if($invite_info && $answer_count==1)
                    {
                        //被邀请者添加积分记录
                        LogHelper::addIntegralLog('ANSWER_INVITE',$ret,'answer',$data['uid']);

                        //邀请者添加积分记录
                        LogHelper::addIntegralLog('INVITE_ANSWER',$ret,'answer',$invite_info['sender_uid']);
                    }

                    ElasticSearch::instance()->create('answer',self::getAnswerInfoById($ret));
                    Attach::updateAttach('answer',$ret,$access_key);

                    Draft::deleteDraftByItemID($data['uid'],'answer');
                }

                send_notify($data['uid'],$question_info['uid'],'QUESTION_ANSWER','question',$data['question_id'],[],$data['is_anonymous']??0);
            }

            if ($ret) {
                $answer_count = db('answer')->where(['question_id'=>$data['question_id'],'status'=>1])->count();
                $answer_id = $data['id'] ? : $ret;
                Question::update(['answer_count'=>$answer_count,'last_answer'=>$answer_id],['id'=>$data['question_id']]);
                PostRelation::updatePostRelation($data['question_id'],'question',['answer_count'=>$answer_count,'update_time'=>time()]);
                $info = db('answer')->where('id',$answer_id)->find();
                Users::updateUserFiled($info['uid'],['answer_count'=>$answer_count]);
                $info['user_info'] = Users::getUserInfo($info['uid']);
                /**
                 * 内容存储后操作
                 */
                $data['access_key']=$access_key;
                hook('answer_model_save',['id'=>$answer_id,'data'=>$data]);
                // 提交事务
                db()->commit();
                return ['answer_count'=>$answer_count,'info'=>$info];
            }else{
                db()->rollback();
                self::setError('保存失败');
                return false;
            }
        } catch (\Exception $e) {
            // 回滚事务
            db()->rollback();
            self::setError($e->getMessage());
            return false;
        }
	}

    /**
     * 删除回答集合
     * @param $answer_ids
     * @param bool $realMove
     * @return bool|false
     */
    public static function deleteAnswer($answer_ids,bool $realMove=false): bool
    {
        $answer_ids = is_array($answer_ids) ? $answer_ids : explode(',',$answer_ids);
        if($realMove)
        {
            return db('answer')->whereIn('id',$answer_ids)->delete();
        }

        $answer_infos = self::getAnswerInfoByIds($answer_ids);
        if(!db('answer')->whereIn('id',$answer_ids)->update(['status'=>0]))
        {
            self::setError('回答删除失败');
            return false;
        }
        foreach ($answer_infos as $answer_info)
        {
            $answer_count = db('answer')->where(['question_id'=>$answer_info['question_id'],'status'=>1])->count();
            $last_answer = db('question')->where('id',$answer_info['question_id'])->value('last_answer');
            if($last_answer==$answer_info['id'])
            {
                $last_answer = db('answer')->where(['question_id'=>$answer_info['question_id'],'status'=>1])->order('create_time','DESC')->value('id');
            }
            Question::update(['answer_count'=>$answer_count,'last_answer'=>$last_answer],['id'=>$answer_info['question_id']]);
            PostRelation::updatePostRelation($answer_info['question_id'],'question',['answer_count'=>$answer_count]);
        }
        return true;
    }

    /**
     * 根据问题id获取回答
     * @param $data
     * @param array $sort
     * @return mixed
     */
	public static function getAnswerByQid($data,array $sort=[])
	{
		if ($data['answer_id']) {
			$where = ['question_id' => $data['question_id'], 'id' => $data['answer_id'],'status'=>1];
		} else {
			$where = ['question_id' => $data['question_id'],'status'=>1];
		}
		$list =db('answer')
			->where($where)
            ->order($sort)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' => intval($data['limit']),
                'page'=>intval($data['page']),
                'pjax'=>''
            ]);

        $page_render = $list->render();
        $list = $list->toArray();
        foreach ($list['data'] as $key=>$val)
        {
            $list['data'][$key]['user_info'] = Users::getUserInfo($val['uid']);
            $list['data'][$key]['content'] = htmlspecialchars_decode($val['content']);
        }
        $list['page_render']=$page_render;
		return $list;
	}

    /**
     * 获取回答列表
     * @param $question_id
     * @param int $answer_id
     * @param int $page
     * @param int $per_page
     * @param string $sort
     * @param int $uid
     * @param string $pjax
     * @param int $force_fold 是否隐藏折叠
     * @return array
     */
    public static function getAnswerByQuestionId($question_id, int $answer_id=0,int $page=1,int $per_page=10,string $sort='new',int $uid=0,string $pjax='aw-answer-list',int $force_fold=0): array
    {
        $where[]=['status','=',1];
        $where[]=['question_id','=',$question_id];
        $visitor_view_answer_count = get_setting('visitor_view_answer_count',0);

        if ($answer_id)
        {
            $where[]=['id','=',$answer_id];
        }

        if($visitor_view_answer_count && !$uid)
        {
            $order = ['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'];
            $per_page = $visitor_view_answer_count;
            $page = 1;
        }else{

            if(!$answer_id && $force_fold)
            {
                $where[]=['force_fold','=',0];
            }

            if($sort=='hot')
            {
                //热门排序
                $order = ['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'];
            }elseif($sort=='publish'){
                //只看楼主
                $question_uid = db('question')->where(['id'=>$question_id,'status'=>1])->value('uid');
                $where[]=['uid','=',$question_uid];
                $order = ['is_best'=>'DESC','create_time'=>'DESC'];
            }elseif($sort=='focus' && $uid){
                //关注的人
                $friend_uid_list = db('users_follow')->where(['status'=>1,'fans_uid'=>$uid])->column('friend_uid');
                $where[]= $friend_uid_list ? ['uid','IN',implode(',',$friend_uid_list)] : ['uid','=',0];
                $order = ['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'];
            }else{
                //最新排序
                $order = ['is_best'=>'DESC','id'=>'DESC'];
            }
        }
        $list =db('answer')
            ->where($where)
            ->order($order)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' => $per_page,
                'page'=>intval($page),
                'pjax'=>$pjax
            ]);

        $pageRender = $list->render();
        $list = $list->toArray();
        $ip = new IpLocation();
        foreach ($list['data'] as $key=>$val)
        {
            $list['data'][$key]['user_info'] = Users::getUserInfo($val['uid']);
            $list['data'][$key]['content'] = htmlspecialchars_decode($val['content']);
            $list['data'][$key]['checkFavorite']=Common::checkFavorite(['uid'=>$uid,'item_id'=>$val['id'],'item_type'=>'answer'])?1:0;
            $list['data'][$key]['checkReport']=Report::getReportInfo($val['id'],'answer',$uid)?1:0;
            if(get_setting('show_answer_user_ip')=='Y' && $val['answer_user_ip'] && !$val['answer_user_local'])
            {
                $list['data'][$key]['answer_user_local'] = $ip->getLocation($val['answer_user_ip'])['country'];
            }
        }
        $list['page_render']= $pageRender;
        if($visitor_view_answer_count && !$uid)
        {
            $list['page_render']= '';
        }
        return $list;
    }

    /**
     * 获取折叠回复列表
     * @param $question_id
     * @param int $uid
     * @return array
     */
    public static function getForceFoldByQuestionId($question_id,int $uid=0): array
    {
        $where[]=['status','=',1];
        $where[]=['force_fold','=',1];
        $where[]=['question_id','=',$question_id];
        $order = ['is_best'=>'DESC','create_time'=>'DESC'];

        $list =db('answer')
            ->where($where)
            ->order($order)
            ->select()
            ->toArray();
        $ip = new IpLocation();
        foreach ($list as $key=>$val)
        {
            $list[$key]['user_info'] = Users::getUserInfo($val['uid']);
            $list[$key]['content'] = htmlspecialchars_decode($val['content']);
            $list[$key]['checkFavorite']=Common::checkFavorite(['uid'=>$uid,'item_id'=>$val['id'],'item_type'=>'answer'])?1:0;
            $list[$key]['checkReport']=Report::getReportInfo($val['id'],'answer',$uid)?1:0;
            if(get_setting('show_answer_user_ip')=='Y' && $val['answer_user_ip'] && !$val['answer_user_local'])
            {
                $list[$key]['answer_user_local'] = $ip->getLocation($val['answer_user_ip'])['country'];
            }
            $list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'answer',$uid);
            $list[$key]['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$val['id'],'uid'=>$uid])->value('id') ? 1 : 0;
            $list[$key]['has_uninterested'] = db('uninterested')->where(['item_id'=>$val['id'],'item_type'=>'answer','uid'=>$uid])->value('id')  ? 1 : 0;
        }
        return $list;
    }


    /**
     * 获取回答评论
     * @param $answer_id
     * @param int $page
     * @param int $per_page
     * @return array
     */
	public static function getAnswerComments($answer_id,int $page=1,int $per_page=5): array
    {
		$list = db('answer_comment')
			->where(['answer_id' => $answer_id, 'status' => 1])
            ->paginate([
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>request()->param()
            ])->toArray();

		foreach ($list['data'] as $key => &$value) {
            $list['data'][$key]['user_info'] = Users::getUserInfo($value['uid']);
            $list['data'][$key]['message'] = htmlspecialchars_decode($value['message']);
		}
        return $list;
	}

    /**
     * 根据问题ids获取最后回答内容列表
     * @param $question_ids
     * @param string $field
     * @return array|false
     */
	public static function getLastAnswerByIds($question_ids,$field='*')
	{
		if (!is_array($question_ids) || count($question_ids) == 0)
		{
			return false;
		}

		array_walk_recursive($question_ids, 'intval');
		$last_answer_ids = db('question')->whereIn('id',implode(',', $question_ids))->where([['status','=',1]])->column('last_answer');

		$result = array();

		if ($last_answer_ids)
		{
			$last_answer = db('answer')->where([['status','=',1]])->whereIn('id',implode(',', $last_answer_ids))->field($field)->select()->toArray();
			if ($last_answer)
			{
				foreach ($last_answer AS $key => $val)
				{
                    $val['content'] = htmlspecialchars_decode($val['content']);
					$result[$val['question_id']] = $val;
				}
			}
		}
		return $result;
	}

    /**
     * 根据问题ids获取最后回答内容列表
     * @param $question_ids
     * @param string $field
     * @return array|false
     */
    public static function getHotAnswerByIds($question_ids,string $field='*')
    {
        if (!is_array($question_ids) || count($question_ids) == 0)
        {
            return false;
        }

        array_walk_recursive($question_ids, 'intval');

        $hot_answer = db('answer')
            ->where([['status','=',1]])
            ->order(['is_best'=>'DESC','agree_count'=>'DESC','comment_count'=>'DESC'])
            ->whereIn('question_id',implode(',', $question_ids))
            ->group('question_id')
            ->field($field)
            ->select()
            ->toArray();

        $result = array();

        if ($hot_answer)
        {
            foreach ($hot_answer AS $key => $val)
            {
                $val['content'] = htmlspecialchars_decode($val['content']);
                $result[$val['question_id']] = $val;
            }
        }
        return $result;
    }


    /**
     * 获取最佳回复
     * @param $question_ids
     * @param $field
     * @return array|false
     */
    public static function getBestAnswerByQuestionIds($question_ids,$field='*')
    {
        if (!is_array($question_ids) || count($question_ids) == 0)
        {
            return false;
        }

        array_walk_recursive($question_ids, 'intval');
        $last_answer_ids = db('question')->whereIn('id',implode(',', $question_ids))->where([['status','=',1]])->column('best_answer');

        $result = array();

        if ($last_answer_ids)
        {
            $last_answer = db('answer')->where([['status','=',1]])->whereIn('id',implode(',', $last_answer_ids))->field($field)->select()->toArray();
            if ($last_answer)
            {
                foreach ($last_answer AS $key => $val)
                {
                    $val['content'] = htmlspecialchars_decode($val['content']);
                    $result[$val['question_id']] = $val;
                }
            }
        }
        return $result;
    }

    /**
     * 保存回答评论
     * @param $data
     * @return false
     */
	public static function saveComments($data)
	{
		$arr['answer_id'] = $data['answer_id'];
		$arr['uid'] = $data['uid'];
        $at_info = Users::parseAtUser(htmlspecialchars_decode($data['message']),true);
        $allUsers = $at_info[1];
		$arr['message'] = htmlspecialchars($at_info[0]);

		if (isset($data['id']) and $data['id'] > 0) {
			$arr['id'] = $data['id'];
			$arr['update_time'] = time();
		} else {
			$arr['create_time'] = time();
		}

		$result = db('answer_comment')->insertGetId($arr);

		if(!$result)
		{
			self::setError('评论失败');
			return false;
		}

        if($allUsers)
        {
            foreach ($allUsers as $k=>$v)
            {
                send_notify($data['uid'],$v['uid'],'QUESTION_ANSWER_AT_ME','question',$data['question_info']['id']);
            }
        }

        $publish_info=db('answer')->field('uid,id')->find($data['answer_id']);

		self::updateCommentCount($data['answer_id']);

        //自己发表的评论就不通知了
        if($publish_info['uid']!=$data['uid'])
        {
            send_notify($data['uid'],$publish_info['uid'],'NEW_ANSWER_COMMENT','question',$data['question_info']['id']);
        }

        $arr['id'] = $result;
        $arr['create_time_label'] = date_friendly($arr['create_time']);
        return $arr;
	}

    /**
     * 更新回答评论数
     * @param $answer_id
     * @return Answer|null
     */
	public static function updateCommentCount($answer_id): ?Answer
    {
		$count = db('answer_comment')->where(['answer_id' => $answer_id, 'status' => 1])->count();
		return self::update(['comment_count' => $count],['id' => $answer_id]);
	}

    /**
     * 删除问题回答评论
     * @param $comment_id
     * @param $uid
     * @return Answer|false
     */
	public static function deleteComment($comment_id, $uid)
	{
		if(!$info = db('answer_comment')->find($comment_id))
		{
			self::setError('回答评论不存在');
			return false;
		}

		$user_info = $uid; //Users::getUserInfo($uid);
		if ($info['uid'] != $user_info['uid'] && $user_info['group_id'] >= 3)
		{
			self::setError('您没有删除回答评论的权限');
			return false;
		}

		if(!db('answer_comment')->where(['id'=>$comment_id])->whereOr(['pid'=>$comment_id])->update(['status'=>0]))
		{
			self::setError('问题评论删除失败');
			return false;
		}

		$comment_count = db('answer_comment')->where(['answer_id'=>$info['answer_id'],'status'=>1])->count();
		return self::update(['comment_count'=>$comment_count],['id'=>$info['answer_id']]);
	}

    /**
     * 折叠回答
     * @param $answer_id
     * @param $uid
     * @return false
     */
    public static function forceAnswer($answer_id,$uid): bool
    {
        if(!$answer_id || !$answer_info=self::getAnswerInfoById($answer_id)){
            self::setError('回答不存在');
            return false;
        }

        $question_uid = db('question')->where(['id'=>$answer_info['question_id'],'status'=>1])->value('uid');
        if(!$question_uid)
        {
            self::setError('问题不存在');
            return false;
        }

        if(!isNormalAdmin() && !isSuperAdmin())
        {
            self::setError('您没有操作该回答的权限');
            return false;
        }

        db()->startTrans();
        try {
            $force_fold = $answer_info['force_fold'] ?0:1;
            db('answer')->where(['id'=>$answer_info['id']])->update(['force_fold'=>$force_fold]);
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
     * 自动设置最佳回复
     * @return bool
     */
    public static function autoSetBestAnswer()
    {
        if (!$best_answer_day = intval(get_setting('auto_set_best_answer_day')))
        {
            return false;
        }
        $start_time = time() - $best_answer_day * ONE_DAY;
        $questions = db('question')->whereRaw("create_time < " . $start_time. " AND  best_answer = 0 AND answer_count > " . get_setting('best_answer_min_count',0))->column('id');
        if ($questions)
        {
            foreach ($questions AS $question_id)
            {
                $best_answer = db('answer')->where(['question_id'=>$question_id])->order('agree_count', 'DESC')->find();
                if ($best_answer['agree_count'] > get_setting('best_agree_min_count'))
                {
                    if(db('question')->where(['id'=>$best_answer['question_id']])->update(['best_answer'=>$best_answer['id']]))
                    {
                        db('answer')->where(['id'=>$best_answer['id']])->update(['is_best'=>1,'best_uid'=>1,'best_time'=>time()]);
                        //添加积分记录
                        LogHelper::addIntegralLog('BEST_ANSWER',$best_answer['id'],'answer',$best_answer['uid']);
                        //系统通知用户
                        send_notify(0,$best_answer['uid'],'BEST_ANSWER','question',$best_answer['question_id']);
                    }
                }
            }
        }
        return true;
    }
}