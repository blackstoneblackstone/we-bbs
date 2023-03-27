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

namespace plugins\third;
use app\common\controller\Plugins;

/**
 * 第三方登录
 */
class Plugin extends Plugins
{
	/**
	 * 插件安装方法
	 * @return bool
	 */
    public function install()
    {
        return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
    public function uninstall()
    {
        return true;
    }

    public function enable()
    {
        return true;
    }

    public function disable()
    {
        return true;
    }

    public function third_login()
    {
        $this->assign(['config'=>$this->getConfig(),'isMobile'=>request()->isMobile()]);
        return $this->fetch('/login');
    }
}
