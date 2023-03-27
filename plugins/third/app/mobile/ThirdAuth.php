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

namespace app\mobile;
use think\App;
use app\common\controller\Frontend;
use plugins\third\libs\Application;

use plugins\third\model\Third;
use plugins\third\libs\Service;

/**
 * 第三方登录插件
 * Class Index
 */
class ThirdAuth extends Frontend
{
    protected $application = null;
    protected $options = [];
    protected $config;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->config = get_plugins_config('third');
        $this->application = new Application($this->config);
    }

    /**
     * 发起授权
     */
    public function connect()
    {
        $platform = $this->request->param('platform');
        if (!$this->application->{$platform}) $this->error('参数错误');

        $url = $this->request->request('url', $this->request->server('HTTP_REFERER', '/'), 'trim');
        if ($url) session("redirect_url", $url);

        // 跳转到登录授权页面
        $url = $this->application->{$platform}->getAuthorizeUrl();
        return redirect($url);
    }

    /**
     * 通知回调
     */
    public function callback()
    {
        $platform = $this->request->param('platform');

        // 成功后返回之前页面
        $url = session("redirectUrl") ? session("redirectUrl") : ((string) url('/'));
        $token = $this->request->get('token','');
        // 授权成功后的回调
        if (!$user_info = $this->application->{$platform}->getUserInfo()) $this->error('操作失败', $url);
        $user_info['token'] = $token;
        session("{$platform}-user-info", $user_info);

        //要求绑定账号
        if ($this->config['base']['bind_account']) {
            return redirect((string) url('ThirdAuth/prepare', ['platform' => $platform, 'url' => $url]));
        }

        if (Service::connect($platform, $user_info)) {
            return redirect($url);
        }
    }

    /**
     * 准备绑定
     */
    public function prepare()
    {
        $platform = $this->request->request('platform');
        $url = $this->request->get('url', '/');
        if ($this->user_id) $this->redirect((string) url("ThirdAuth/bind",['platform' => $platform, 'url' => $url]));
        $user_info = session("{$platform}-user-info");
        if($user_info)
        {
            $third = db('third')->where(['platform' => $platform, 'openid' => $user_info['openid']])->find();
            if($third)
            {
                if (Service::connect($platform, $user_info)) {
                    return redirect($url);
                }
            }
        }
        $this->assign('url', $url);
        $this->assign('platform', $platform);
        $this->assign('bind_url', (string) url("ThirdAuth/bind", ['platform' => $platform, 'url' => $url]));
        return $this->fetch();
    }

    /**
     * 绑定账号
     */
    public function bind()
    {
        if (!$platform = $this->request->request('platform')) $this->error("参数不正确");

        $url = $this->request->get('url', $this->request->server('HTTP_REFERER'));
        // 授权成功后的回调
        if (!$userinfo = session("{$platform}-user-info")) {
            return redirect((string)url('ThirdAuth/connect', ['platform' => $platform,'url'=>urlencode($url)]));
        }

        if ($this->user_id && db('third')->where('uid', $this->user_id)->where('platform', $platform)->find()) $this->error("已绑定账号，请勿重复绑定");

        if(!$this->user_id)
        {
            $userinfo = session("{$platform}-user-info");
            if($userinfo)
            {
                if (Service::connect($platform, $userinfo)) {
                    return redirect($url);
                }
            }
            $this->error("账号绑定失败，请重试", $url);
        }

        $time = time();
        $values = [
            'platform'      => $platform,
            'uid'       => $this->user_id,
            'openid'        => $userinfo['openid'],
            'open_username'      => $userinfo['user_info']['nickname'] ?? '',
            'access_token'  => $userinfo['access_token'],
            'refresh_token' => $userinfo['refresh_token'],
            'expires_in'    => $userinfo['expires_in'],
            'login_time'     => $time,
            'expire_time'    => $time + $userinfo['expires_in'],
            'token' =>$userinfo['token'] ?? '',
        ];

        if($id = db('third')->where(['openid'=>$values['openid'],'platform'=>$values['platform']])->value('id'))
        {
            db('third')->where(['id'=>$id])->update($values);
            $this->success("账号绑定成功", $url);
        }else{
            if (Third::create($values)) {
                $this->success("账号绑定成功", $url);
            }
        }
        $this->error("账号绑定失败，请重试", $url);
    }

    /**
     * 解绑账号
     */
    public function unbind()
    {
        $platform = $this->request->request('platform');
        $third = Third::where('uid', $this->user_id)->where('platform', $platform)->find();
        if (!$third) $this->error("未找到指定的账号绑定信息");

        $third->delete();
        $this->success("账号解绑成功");
    }
}
