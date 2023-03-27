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

namespace app\backend\plugin;
use app\common\controller\Backend;
use app\common\library\helper\UpgradeHelper;
use think\App;

class Upgrade extends Backend
{
    protected $upgradeHelper;
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->upgradeHelper = UpgradeHelper::instance();
        $this->assign([
            'info'=>$this->upgradeHelper->checkVersion(),
        ]);
    }

    public function index()
    {
        return $this->fetch();
    }

    public function check()
    {
        return $this->fetch();
    }

    public function download()
    {
        $result = $this->upgradeHelper->update();
        if($result['code'])
        {
            $this->success($result['msg']);
        }

        $this->error($result['msg']);
    }

    //云平台绑定
    public function bind()
    {
        if($this->request->isPost())
        {
            $post = $this->request->post();
            $result = $this->upgradeHelper->bind($post['username'],$post['password']);
            if($result['code'])
            {
                $this->success($result['data']['msg']);
            }

            $this->error($result['data']['msg']);
        }

        return $this->formBuilder
            ->addText('username','云平台账号')
            ->addPassword('password','云平台密码')
            ->fetch();
    }

    //解绑账号
    public function unbind()
    {
        $result = $this->upgradeHelper->unbind();
        if($result['code'])
        {
            $this->success($result['data']['msg']);
        }
        $this->error($result['data']['msg']);
    }
}