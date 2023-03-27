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

namespace app\common\library\helper;
use EasyWeChat\Factory;

class WeChatHelper
{
    protected static $config = [
        'log' => [
            'default' => 'dev', // 默认使用的 channel，生产环境可以改为下面的 prod
            'channels' => [
                // 测试环境
                'dev' => [
                    'driver' => 'single',
                    'path' => '../runtime/wechat.log',
                    'level' => 'debug',
                ],
                // 生产环境
                'prod' => [
                    'driver' => 'daily',
                    'path' => '../runtime/wechat.log',
                    'level' => 'info',
                ],
            ],
        ],
        'http' => [
            'max_retries' => 1,
            'retry_delay' => 500,
            'timeout' => 5.0,
        ],
        'oauth' => [
            'scopes' => ['snsapi_userinfo'],
            'callback' => '',
        ],
    ];

    protected static $instance;

    public static function instance(): WeChatHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    //获取默认微信号
    public function getWeChatAccount($accountId=0,$field=null)
    {
        if($accountId)
        {
            if($field)
            {
                return db('wechat_account')->where(['status'=> 1,'id'=>$accountId])->value($field);
            }
            return db('wechat_account')->where(['status'=> 1,'id'=>$accountId])->find();
        }

        if($field)
        {
            return db('wechat_account')->where(['status'=> 1])->value($field);
        }
        return db('wechat_account')->where(['status'=> 1])->find();
    }

    //获取默认小程序号
    public function getMinAppAccount()
    {
        return db('wechat_minapp')->where(['status'=> 1])->find();
    }

    //获取公众号实例
    public function getOfficialAccount($accountId=0)
    {
        $wechatAccount = self::getWeChatAccount($accountId);
        if ($wechatAccount)
        {
            self::$config = array_merge(self::$config,[
                'app_id' => $wechatAccount['app_id'],
                'secret' => $wechatAccount['app_secret'],
                'token' => $wechatAccount['token'],
                'aes_key'=>$wechatAccount['aes_key'],
                'response_type' => 'array',
            ]);
            return Factory::officialAccount(self::$config);
        } else {
            return false;
        }
    }

    //获取小程序实例
    public function getMiniProgram()
    {
        if ($wechatAccount = get_plugins_config('minapp','minapp'))
        {
            self::$config = array_merge(self::$config,[
                'app_id' => $wechatAccount['app_id'],
                'secret' => $wechatAccount['app_secret'],
                'response_type' => 'array',
            ]);
            return Factory::miniProgram(self::$config);
        } else {
            return false;
        }
    }

    /**
     * 获取jssdk配置
     */
    public function getJsSdkConfig(array $APIs=['updateAppMessageShareData', 'updateTimelineShareData',"onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", 'onMenuShareWeibo', 'getLocation'], $debug = false, $beta = false, $json = true)
    {
        $app = $this->getOfficialAccount();
        if(!$app)
        {
            return [];
        }
        return $app->jssdk->buildConfig($APIs, $debug, $beta, $json);
    }
}