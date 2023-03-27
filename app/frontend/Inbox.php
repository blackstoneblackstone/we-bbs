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
namespace app\frontend;

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
        //私信列表钩子
        hook('inboxIndex',$this->request->param());

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
		$id = $this->request->post('id',0);
		$uid = $this->request->post('uid');

		$user = Users::getUserInfo($uid);
		$this->assign('user',$user);

		$list = InboxModel::getMessageByDialogId($id,$this->user_id);
		$this->assign('list',$list);

		return $this->fetch();
	}

    /**
     * 发送私信
     */
	public function send()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();

            //发送私信钩子
            hook('sendInbox',$postData);

			if(!InboxModel::sendMessage($this->user_id, $postData['recipient_uid'], remove_xss($postData['message'])))
			{
				$this->error(InboxModel::getError());
			}
			$this->success('私信发送成功');
		}
	}
}