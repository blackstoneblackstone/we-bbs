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

namespace app\backend\wechat;
use app\common\controller\Backend;
use app\common\library\helper\WeChatHelper;
use think\App;
class WeChatFactor extends Backend
{
    protected $wechatHelper;
    protected $wechatAccountId;
    protected $wechatFactor;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->wechatHelper = WeChatHelper::instance();
        $this->wechatFactor = $this->wechatHelper->getOfficialAccount();
        $this->wechatAccountId = $this->wechatHelper->getWeChatAccount(0,'id');
        if(!$this->wechatAccountId)
        {
            $this->error('请先添加微信账号');
        }
    }

    //获取媒体素材
    protected function getMaterialByMediaId($mediaId){
        $resource = $this->wechatFactor->material->get($mediaId);
        $this->showError($resource);
        return $resource;
    }

    protected  function showError($res)
    {
        if(isset($res['errcode']) && $res['errcode']>0){
            $this->error($res['errmsg']);
        }
    }
}