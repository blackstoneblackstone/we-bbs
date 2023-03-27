<?php
// +----------------------------------------------------------------------
// | WeCenter社交化问答系统
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2022 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@WeCenter.com>
// +----------------------------------------------------------------------
namespace app\mobile;

use app\common\controller\Frontend;
use app\model\UsersInbox as InboxModel;
use app\model\Users;
use think\App;

class Inbox extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
		$this->model = new InboxModel();
	}

	public function index()
	{
	    $page = $this->request->param('page',1);
		if($this->request->param('header',0))
        {
            $where = '(sender_unread=1 AND sender_uid = ' . intval($this->user_id) . ' AND sender_count > 0) OR (recipient_unread=1 AND recipient_uid = ' . intval($this->user_id) . ' AND recipient_count > 0)';

            $dialogList = InboxModel::getDialogListByUid($this->user_id,$where,1,5);
            $this->assign($dialogList);
            return $this->fetch('header_inbox');
        }

        $dialogList = InboxModel::getDialogListByUid($this->user_id,'',$page);
        $this->assign($dialogList);
		return $this->fetch();
	}

	public function detail()
	{
		$uid = $this->request->param('uid',0,'intval');
		$receiver = $this->request->param('receiver', '', 'trim');
        $page = $this->request->param('page',1, 'intval');
        $user_info = db('users')->where('nick_name', $receiver)->whereOr('uid',$uid)->field('uid,nick_name,user_name')->find();
        $dialog_info = InboxModel::getDialogByUser($this->user_id, $user_info['uid']);
        $list = $dialog_info ? InboxModel::getMessageByDialogId($dialog_info['id'], $this->user_id, $page, 10) : [];
        if ($this->user_info['inbox_unread'] && $dialog_info) {
            InboxModel::updateRead(intval($dialog_info['id']),$this->user_id);
        }

        $this->assign($list);
        $this->assign(['user' => $user_info]);

        if ($this->request->param('type', '') == 'list') {
            $list['html'] = $this->fetch();
            $this->success('', '', $list);
        }

		return $this->fetch();
	}

	// 分页数据
    public function page()
    {
        $uid = $this->request->param('receiver', '', 'trim');

        $page = $this->request->param('page',1);
        $dialog_info = InboxModel::getDialogByUser($this->user_id, db('users')->where('user_name', $uid)->value('uid'));

        if($dialog_info)
        {
            $list = InboxModel::getMessageByDialogId($dialog_info['id'], $this->user_id, $page, 10);
            if ($this->user_info['inbox_unread']) {
                InboxModel::updateRead(intval($dialog_info['id']),$this->user_id);
            }
            $list['html'] = $this->fetch('',$list);
            $this->apiResult($list);
        }else{
            $this->apiResult([
                'list'=>[],
                'total'=>0,
                'html'=>''
            ]);
        }
    }

    /**
     * 发送私信
     */
	public function send()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			if (InboxModel::sendMessage($this->user_id, $postData['recipient_uid'], remove_xss($postData['message']))) {
			    $this->success('发送成功');
			} else {
                $this->error(InboxModel::getError());
            }
		}
	}
}