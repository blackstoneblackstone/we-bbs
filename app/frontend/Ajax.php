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
namespace app\frontend;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\Answer;
use app\model\Draft;
use app\model\Question as QuestionModel;
use app\model\QuestionInvite;
use app\model\Topic as TopicModel;
use app\model\UsersFavorite;
use app\model\UsersInbox as InboxModel;
use app\model\Users;
use app\model\Verify;
use app\model\Report;
use app\model\Topic;
use app\model\Vote;
use think\exception\ValidateException;

class Ajax extends Frontend
{
	//投票操作
	public function set_vote()
    {
		$item_id = $this->request->post('item_id',0,'intval');
		$item_type = $this->request->post('item_type');
		$vote_value = intval($this->request->post('vote_value'));
		$result = Vote::saveVote($this->user_id, $item_id, $item_type, $vote_value);
		if (!$result) {
			$this->result([], 0, Vote::getError());
		}
		$this->result($result, 1, '操作成功');
	}

    /**
     * 获取用户信息
     */
	public function get_user_info()
    {
		$uid = $this->request->param('uid');
		$user_info = Users::getUserInfo($uid);
        $user_info['is_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user_info['uid']);
        $user_info['check_online'] = get_setting('online_check');
		$this->result($user_info);
	}

    /**
     * 获取话题信息
     */
    public function get_topic_info()
    {
        $id = $this->request->param('id');
        $topic_info = db('topic')->where('id',intval($id))->find();
        $topic_info['is_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'topic',$topic_info['id']);
        $topic_info['description'] = $topic_info['description'] ? str_cut(strip_tags(htmlspecialchars_decode($topic_info['description'])), 0, 45) : '暂无话题简介';
        $topic_info['url'] = (string)url('topic/detail',['id'=>$id]);
        $topic_info['pic'] = $topic_info['pic'] ? : '/static/common/image/topic.svg';
        $this->result($topic_info);
    }

	/**
	 * 保存草稿
	 */
	public function save_draft()
    {
		if ($this->request->isPost()) {
			$item_id = $this->request->post('item_id',0,'intval');
			$item_type = $this->request->post('item_type');
			$data = $this->request->post('data');

            if($item_type!='answer')
            {
                if (empty($data) || !$data['title']) {
                    $this->error('保存草稿失败');
                }
            }else{
                if (empty($data) || !$data['content']) {
                    $this->error('保存草稿失败');
                }
            }

            $data['is_anonymous'] = isset($data['is_anonymous']) ? intval( $data['is_anonymous']) : 0;
			unset($data['__token__']);
			if (Draft::saveDraft($this->user_id, $item_type, $data, $item_id)) {
				$this->success('保存草稿成功');
			}
			$this->error('保存草稿失败');
		}
	}

	/**
	 * 收藏
	 * @param $item_type
	 * @param $item_id
	 * @return mixed
	 */
	public function favorite($item_type, $item_id) {
		if ($this->request->isPost()) {
			$tag_id = $this->request->param('tag_id');
			if ($return = UsersFavorite::saveFavorite($this->user_id, $tag_id, $item_id, $item_type)) {
				$this->result($return, 1);
			}
			$this->result([], 0);
		}

		$favorite_list = UsersFavorite::getFavoriteTags($this->user_id);
		foreach ($favorite_list['list'] as $key => $value) {
			$favorite_list['list'][$key]['is_favorite'] = UsersFavorite::where(['item_type' => $item_type, 'item_id' => (int) $item_id, 'tag_id' => $value['id'], 'uid' => $this->user_id])->value('id');
		}
		$this->assign($favorite_list);
		$this->assign('item_type', $item_type);
		$this->assign('item_id', $item_id);
		return $this->fetch();
	}

	/**
	 * 举报
	 * @param $item_type
	 * @param $item_id
	 * @return mixed
	 */
	public function report($item_type, $item_id) {
		if ($this->request->isPost()) {
			$reason = $this->request->post('reason');
			$report_type = $this->request->post('report_type');
            if(!$reason || removeEmpty($reason)=='')
            {
                $this->error('请填写举报理由');
            }

			$result = Report::saveReport($item_id, $item_type, $report_type, $reason, $this->user_id);
			$this->result([], $result['code'], $result['msg']);
		}
		$this->assign('item_id', $item_id);
		$this->assign('item_type', $item_type);
		return $this->fetch();
	}

	/**
	 * 更新关注
	 */
	public function update_focus()
    {
		$item_id = $this->request->post('id',0,'intval');
		$item_type = $this->request->post('type');
		if (!$data = FocusLogic::updateFocusAction($item_id, $item_type, $this->user_id)) {
			$this->result([], 0, FocusLogic::getError());
		}
		$this->result($data, 1, '操作成功');
	}

    /**
     * 锁定话题
     */
	public function lock()
    {
        $id = $this->request->param('id');
        if($this->user_id && ($this->user_info['group_id']===1 || $this->user_info['group_id']===2))
        {
            if(Topic::lockTopic((int)$id,$this->user_id)){
        		$this->success('操作成功');
            }
        }
        $this->error('您没有锁定话题权限');
    }

    /**
     * 私信对话记录ajax请求
     */
    public function dialog()
    {
        $user_name = $this->request->param('recipient_uid','');
        $page = $this->request->param('page',1);
        $recipient_uid = db('users')->where('nick_name',$user_name)->value('uid');
        $dialog_info = InboxModel::getDialogByUser($this->user_id, $recipient_uid);
        $list = $dialog_info ? InboxModel::getMessageByDialogId($dialog_info['id'],$this->user_id,intval($page)) : [];
        if($this->user_info['inbox_unread'] && $dialog_info)
        {
            InboxModel::updateRead(intval($dialog_info['id']),$this->user_id);
        }
        $this->assign($list);
        return $this->fetch();
    }

    /*私信弹窗*/
    public function inbox()
    {
        hook('inboxDialog',$this->request->param());

        $user_name = $this->request->param('user_name','');
        $this->assign(['user_name'=>$user_name]);
        return $this->fetch();
    }

    /**
     * 发送短信
     */
    public function sms()
    {
        $mobile = $this->request->param('mobile','','intval');
        if(!$mobile)
        {
            return ['code'=>0,'msg'=>'请输入正确的手机号'];
        }
        $result = hook('sms',['mobile'=>$mobile]);
        if($result=='')
        {
            return ['code'=>0,'msg'=>'短信功能未启用'];
        }
        return json_decode($result,true);
    }

    /**
     * 认证类型
     * @return mixed
     */
    public function verify_type()
    {
        $type = $this->request->param('type');
        $where = ['verify_type'=>$type,'verify_show'=>1];
        $info = db('users_verify')->where(['uid'=>intval($this->user_id)])->find();
        $result = [];
        if($info)
        {
            $result = json_decode($info['data'],true);
            $result['status']=$info['status'];
            $result['type'] = $info['type'];
            $result['enable'] = (isset($info['status']) && $info['status']!=0 && $info['status']!=3) ? 1 : 0;
        }else{
            $result['type'] = $type;
            $result['enable'] = 0;
        }
        $data = array(
            'keyList' => Verify::getConfigList($where),
            'info'=>$result
        );
        $this->assign('verify_info',$info);
        $this->assign($data);
        return $this->fetch();
    }

    /**
     * 不感兴趣
     */
    public function uninterested()
    {
        if(!$this->user_id){
            $this->error('请先登录');
        }

        $item_id = $this->request->post('id');
        $item_type = $this->request->post('type');
        if(db('uninterested')->where(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$this->user_id])->value('id'))
        {
            $this->error('您已进行过此操作');
        }

        if(db('uninterested')->insert(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$this->user_id,'create_time'=>time()]))
        {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    /**
     * 切换语言
     */
    public function change_lang()
    {
        if (get_setting('enable_multilingual','N')!='Y') return false;

        $lang = $this->request->param('lang',config('lang.default_lang'));
        if(!in_array($lang,config('lang.allow_lang_list')))
        {
            $this->error('切换失败');
        }
        cookie('aws_lang',$lang);
        $this->success('切换成功');
    }

    //错误反馈
    public function report_bug()
    {
        if($this->request->isPost())
        {
            $msg = $this->request->post('msg');
            $from = $_SERVER['HTTP_REFERER'];
            $message = '来源页面：'.$from.'<br>错误信息：'.$msg;
            send_email(get_setting('report_bug_email'),'网站BUG反馈',$message);
            $this->success();
        }
    }

    /**
     * 回答编辑
     * @return mixed
     */
    public function editor()
    {
        hook('answer_editor_get_before',$this->request->param());

        $question_id = $this->request->param('question_id',0,'intval');
        $answer_id = $this->request->param('answer_id',0,'intval');
        $captcha_enable = 0;
        $error  = '';
        $answer_info =[];
        $question_is_lock = db('question')->where(['status'=>1,'id'=>$question_id])->value('is_lock');

        if($answer_id)
        {
            $answer_info = Answer::getAnswerInfoById($answer_id);
            $question_id = $answer_info['question_id'];
        }else{
            // 判断是否已回复过问题
            if ((get_setting('answer_unique') == 'Y') && db('answer')->where(['uid'=>$this->user_id,'question_id'=>intval($question_id),'status'=>1])->count())
            {
                $error = '一个问题只能回复一次，你可以编辑回复过的回复';
            }
        }

        if($question_is_lock)
        {
            $error = '该问题已被锁定，无法进行回答';
        }

        $draft_info = Draft::getDraftByItemID($this->user_id,'answer');
        if(isset($draft_info['data']))
        {
            $answer_info['content'] = htmlspecialchars_decode($draft_info['data']['content']);
        }

        if(get_setting('publish_content_verify_time') && !Users::checkUserPublishTimeAndCount($this->user_id,'answer'))
        {
            $captcha_enable = 1;
        }

        $this->assign([
            'question_id'=>$question_id,
            'is_focus'=>FocusLogic::checkUserIsFocus($this->user_id,'question',$question_id),
            'access_key'=>md5($this->user_id.time()),
            'answer_id'=>$answer_id,
            'error'=>$error,
            'answer_captcha_enable'=>$captcha_enable,
            'answer_info'=>$answer_info
        ]);

        hook('answer_editor_get_after',$this->request->param());
        return $this->fetch();
    }

    /**
     * 获取邀请
     * @param $question_id
     * @return mixed
     */
    public function invite()
    {
        if($this->request->isPost())
        {
            $question_id = $this->request->param('question_id',0,'intval');
            $name = $this->request->post('name', '');
            $page = $this->request->post('page');
            $where[] = ['uid', '<>', $this->user_id];
            $where[] = ['nick_name', 'like', "%" . $name . "%"];
            $where[] = ['status', '=', 1];
            $data = db('users')
                ->where($where)
                ->order(['reputation'=>'DESC'])
                ->field('nick_name,user_name,avatar,reputation,answer_count,question_count,uid')
                ->paginate([
                    'list_rows'=> 10,
                    'page' => $page,
                ])->toArray();

            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['has_invite'] = 0;
                $data['data'][$key]['remark'] = '回答:'.$val['answer_count'].'  提问:'.$val['question_count'].'  威望:'.$val['reputation'];
                if( db('question_invite')->where(['sender_uid'=>$this->user_id,'recipient_uid'=>$val['uid'],'question_id'=>intval($question_id)])->value('id'))
                {
                    $data['data'][$key]['has_invite'] = 1;
                }
            }
            $data['question_id'] = $question_id;
            $this->assign($data);
            $data['html'] = $this->fetch('invite_users');
            $this->result($data,1);
        }

        $question_id = $this->request->param('question_id',0,'intval');
        $data = QuestionInvite::getRecommendInviteUsers($this->user_id,$question_id);
        $data['question_id'] = intval($question_id);
        $this->assign($data);
        $this->assign('invite_list',QuestionInvite::getInvitedUsers($question_id));
        return $this->fetch();
    }

    /**
     * 保存问题邀请
     * @param $question_id
     */
    public function save_question_invite($question_id)
    {
        $invite_uid = $this->request->post('uid',0,'intval');
        $has_invite = $this->request->post('has_invite');

        if($invite_uid==$this->user_id)
        {
            $this->error('不可以邀请自己回答问题');
        }

        if(db('answer')->where(['question_id'=> $question_id, 'uid'=> $invite_uid])->value('id'))
        {
            $this->error('该用户已回答过该问题,不能继续邀请');
        }

        //验证用户积分是否满足积分操作条件
        if(!LogHelper::checkUserIntegral('INVITE_ANSWER',$this->user_id))
        {
            $this->error('您的积分不足,无法邀请用户回答问题');
        }

        if(!$question_info=QuestionModel::getQuestionInfo($question_id,'id,uid,title'))
        {
            $this->error('问题不存在或已被删除');
        }
        if(!QuestionModel::saveQuestionInvite($question_info,$this->user_id,$invite_uid))
        {
            $this->error('该用户已邀请过啦','',[]);
        }
        $this->success('操作成功','',['invite'=> (int)!$has_invite]);
    }

    /**
     * 获取话题列表
     */
    public function get_topic()
    {
        $item_type = $this->request->param('item_type');
        $item_id = $this->request->param('item_id',0,'intval');
        $keywords = $this->request->param('keywords');
        $cache_key = 'get_topic_q'.md5(trim($keywords).'_search_type'.$item_type.'_id'.$item_id);
        $topic_list = cache($cache_key);
        if(!$topic_list)
        {
            $where[] = 'status=1';
            if ($keywords) {
                $keywords = is_array($keywords) ? [] : explode(',',$keywords);
                $where[] = "(title regexp '".implode('|', $keywords)."' OR id regexp '".implode('|', $keywords)."')";
            }

            $topic_list = TopicModel::getTopic($where);

            foreach ($topic_list as $key=>$val)
            {
                $topic_list[$key]['is_checked'] = 0;
                if(TopicModel::checkTopicRelation($val['id'], $item_id, $item_type))
                {
                    $topic_list[$key]['is_checked'] = 1;
                }
            }
            cache($cache_key,$topic_list,60);
        }
        $this->assign('search_list',$topic_list);
        return $this->fetch('topic/select');
    }

    /**
     * 创建话题
     * @return mixed
     */
    public function create()
    {
        if(!$this->user_id)
        {
            $this->error('请先登录','account/login');
        }
        if ($this->user_info['permission']['create_topic_enable'] == 'N') $this->error('你没有创建话题权限');
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            try {
                validate(\app\validate\Topic::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $result = TopicModel::saveTopic($data['title'],$this->user_id);
            if (!$result) {
                $this->error(TopicModel::getError());
            } else {
                $this->success('添加成功',url('index'),['id'=>$result,'title'=>$data['title']]);
            }
        }
    }

    /**
     * 话题选择
     * @param int $item_id
     * @return mixed
     */
    public function merge_topic(int $item_id = 0)
    {
        if ($this->request->isPost()) {
            $topics = $this->request->post('tags');
            TopicModel::updateRelated($item_id, $topics);
            if(!empty($topics))
            {
                $list = TopicModel::getTopicByIds($topics);
                $this->result(['list' => $list, 'total' => count($list)], 1, '保存成功');
            }
            $this->result(['list' => [], 'total' => 0], 1, '保存成功');
        }
        $topics = TopicModel::getRelatedTopicBySourceId($item_id);
        //推荐话题
        $this->assign([
            'item_id'=>$item_id,
            'topics'=>$topics?:[]
        ]);
        return $this->fetch();
    }

    /**
     * 获取关注问题用户
     * @return void
     */
    public function get_question_focus_users()
    {
        $question_id = $this->request->param('question_id',0,'intval');
        $users = QuestionModel::getQuestionFocusUsers($question_id);
        $this->result($users);
    }
}