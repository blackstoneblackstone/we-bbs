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

class Index extends Frontend
{
	public function index()
    {
        $sort = $this->request->param('sort','new');
        $type = $this->request->param('type');
        $this->assign([
            'sort'=> $sort,
            'type'=>$type
        ]);
        $this->TDK(get_setting('site_name'));
		return $this->fetch();
	}
}