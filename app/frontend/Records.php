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
use app\model\BrowseRecords;

class Records extends Frontend
{
    protected $needLogin = [
        'index'
    ];

    public function index()
    {
        $page = $this->request->param('page',1,'intval');
        $data = BrowseRecords::getRecordViewList($this->user_id,$page,get_setting('contents_per_page'),'tabMain');
        $this->assign($data);
        return $this->fetch();
    }
}