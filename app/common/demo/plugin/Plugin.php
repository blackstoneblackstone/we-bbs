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

namespace app\common\demo\plugin;

use app\common\controller\Plugins;

class Plugin extends Plugins
{
    /**
     * 安装前的业务处理，可在此方法实现，默认返回true
     */
    public function install()
    {
        return true;
    }

    /**
     * 卸载前的业务处理，可在此方法实现，默认返回true
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
}