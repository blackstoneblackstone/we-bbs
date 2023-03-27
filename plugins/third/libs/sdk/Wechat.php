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

namespace plugins\third\libs\sdk;

use app\common\library\helper\HttpHelper as Http;
use think\facade\Request;

/**
 * 微信
 */
class Wechat
{
    const GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/oauth2/authorize";
    const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
    const GET_USERINFO_URL = "https://api.weixin.qq.com/sns/userinfo";

    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    public function __construct($options = [])
    {
	    $pluginsConfig = get_plugins_config('third');
        $this->config = array_merge($this->config, $pluginsConfig);
        $this->config = array_merge($this->config, is_array($options) ? $options : []);
    }

    /**
     * 登陆
     */
    public function login()
    {
        header("Location:" . $this->getAuthorizeUrl());
    }

    /**
     * 获取authorize_url
     */
    public function getAuthorizeUrl($token=''): string
    {
        $state = md5(uniqid(rand(), true));
        session('state', $state);
        $queryArr = array(
            "appid"         => $this->config['wechat']['app_id'],
            "redirect_uri"  => $this->config['wechat']['callback'] ??(string)get_url('ThirdAuth/callback', ['platform' =>'wechat'],true,true),
            "response_type" => "code",
            "scope"         => $this->config['wechat']['scope'],
            "state"         => $state,
        );
        Request::isMobile() && $queryArr['display'] = 'mobile';
        $token && $queryArr['token'] = $token;
        return self::GET_AUTH_CODE_URL . '?' . http_build_query($queryArr) . '#wechat_redirect';
    }

    /**
     * 获取用户信息
     * @param array $params
     */
    public function getUserInfo($params = [])
    {
        $params = $params ?: request()->get();
        if (isset($params['access_token']) || (isset($params['state']) && isset($params['code']))) {
            //获取access_token
            $data = isset($params['code']) ? $this->getAccessToken($params['code']) : $params;
            $access_token = $data['access_token'] ?? '';
            $refresh_token = $data['refresh_token'] ?? '';
            $expires_in = $data['expires_in'] ?? 0;
            if ($access_token) {
                $openid = $data['openid'] ?? '';
                $unionid = $data['unionid'] ?? '';
                if (stripos($this->config['wechat']['scope'], 'snsapi_userinfo') !== false) {
                    //获取用户信息
                    $queryArr = [
                        "access_token" => $access_token,
                        "openid"       => $openid,
                        "lang"         => 'zh_CN'
                    ];
                    $ret = Http::get(self::GET_USERINFO_URL, $queryArr)['data'];
                    $userInfo = is_array($ret) ?: json_decode($ret,true);
                    if (!$userInfo || isset($userInfo['errcode'])) {
                        return [];
                    }
                    $userInfo['avatar'] = $userInfo['headimgurl'] ?? '';
                } else {
                    $userInfo = [];
                }
                return [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in'    => $expires_in,
                    'openid'        => $openid,
                    'unionid'       => $unionid,
                    'user_info'      => $userInfo
                ];
            }
        }
        return [];
    }

    /**
     * 获取access_token
     * @param string code
     */
    public function getAccessToken($code = '')
    {
        if (!$code) {
            return [];
        }
        $queryArr = array(
            "appid"      => $this->config['wechat']['app_id'],
            "secret"     => $this->config['wechat']['app_secret'],
            "code"       => $code,
            "grant_type" => "authorization_code",
        );
        $response = Http::get(self::GET_ACCESS_TOKEN_URL, $queryArr);
        if(!$response['code'])
        {
            return [];
        }
        return json_decode($response['data'],true);
    }
}
