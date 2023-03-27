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
use think\facade\Session;

/**
 * 微博
 */
class Weibo
{
    const GET_AUTH_CODE_URL = "https://api.weibo.com/oauth2/authorize";
    const GET_ACCESS_TOKEN_URL = "https://api.weibo.com/oauth2/access_token";
    const GET_USERINFO_URL = "https://api.weibo.com/2/users/show.json";

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
    public function getAuthorizeUrl(): string
    {
        $state = md5(uniqid(rand(), true));
        session('state', $state);
        $queryArr = array(
            "response_type" => "code",
            "client_id"     => $this->config['weibo']['app_id'],
            "redirect_uri"  => $this->config['weibo']['callback'] ?? (string)get_url('ThirdAuth/callback', ['platform' => 'weibo'],true,true),
            "state"         => $state,
        );
        request()->isMobile() && $queryArr['display'] = 'mobile';
        return self::GET_AUTH_CODE_URL . '?' . http_build_query($queryArr);
    }

    /**
     * 获取用户信息
     * @param array $params
     * @return array
     */
    public function getUserInfo($params = []): array
    {
        $params = $params ?: $_GET;
        if (isset($params['access_token']) || (isset($params['state']) && $params['state'] == session('state') && isset($params['code']))) {
            //获取access_token
            $data = isset($params['code']) ? $this->getAccessToken($params['code']) : $params;
            $access_token = $data['access_token'] ?? '';
            $refresh_token = $data['refresh_token'] ?? '';
            $expires_in = $data['expires_in'] ?? 0;
            if ($access_token) {
                $uid = $data['uid'] ?? '';
                //获取用户信息
                $queryArr = [
                    "access_token" => $access_token,
                    "uid"          => $uid,
                ];
                $ret = Http::get(self::GET_USERINFO_URL, $queryArr);
                $userInfo = (array)json_decode($ret['data'], true);
                if (!$userInfo || isset($userInfo['error_code'])) {
                    return [];
                }
                $userInfo['nickname'] = $userInfo['screen_name'] ?? '';
                $userInfo['avatar'] = $userInfo['profile_image_url'] ?? '';
                return [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in'    => $expires_in,
                    'openid'        => $uid,
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
            return '';
        }
        $queryArr = array(
            "grant_type"    => "authorization_code",
            "client_id"     => $this->config['weibo']['app_id'],
            "client_secret" => $this->config['weibo']['app_secret'],
            "redirect_uri"  => $this->config['weibo']['callback'] ?? (string)get_url('ThirdAuth/callback', ['platform' => 'weibo'],true,true),
            "code"          => $code,
        );
        $response = Http::post(self::GET_ACCESS_TOKEN_URL, $queryArr);
        if(!$response['code'])
        {
            return [];
        }
        $ret = json_decode($response['data'],true);
        return $ret ?: [];
    }
}
