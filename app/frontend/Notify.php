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
use app\model\Notify as NotifyModel;
use think\App;

/**
 * 通知模块控制器
 * Class Notify
 * @package app\ask\controller
 */
class Notify extends Frontend
{
    protected $needLogin=['*'];
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new NotifyModel();
        if(!$this->user_id)
        {
            $this->redirect('/');
        }
	}

	/**
	 * 通知列表
	 * @return mixed
	 */
	public function index()
	{
		$page = $this->request->param('page',1,'intval');
		$type = $this->request->param('type','');
		$header = $this->request->param('header',0);
		$read_status = $header ? 2 : 0;
		$data = NotifyModel::getNotifyListByGroup($this->user_id,$page,$header ? 5 : 10,$read_status,$type,'tabMain');
		$this->assign($data);
        $notifyType = get_dict('notify_group');
        $this->assign([
            'type'=>$type,
            'notify'=>$notifyType
        ]);

        if($header)
        {
            return $this->fetch('header_notify');
        }
		return $this->fetch();
	}

    /**
     * 删除通知
     */
	public function delete()
    {
        $id = $this->request->post('id');
        if(NotifyModel::removeNotify($id,$this->user_id))
        {
            $this->result([],1);
        }
        $this->result([],0);
    }

    /**
     * 标记已读
     */
    public function read()
    {
        $id = $this->request->post('id');
        if(NotifyModel::setNotifyRead($id,$this->user_id))
        {
            $this->result([],1);
        }
        $this->result([],0);
    }

    public function read_all()
    {
        if(NotifyModel::setNotifyReadAll($this->user_id))
        {
            $this->error('操作失败');
        }
        $this->success('操作成功');
    }
}