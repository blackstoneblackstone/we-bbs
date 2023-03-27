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

use app\common\library\helper\HttpHelper;

class Github
{
    /**
     * 获取requestCode的api接口
     * @var string
     */
    protected $GetRequestCodeURL = 'https://github.com/login/oauth/authorize';

    /**
     * 获取access_token的api接口
     * @var string
     */
    protected $GetAccessTokenURL = 'https://github.com/login/oauth/access_token';

    /**
     * API根路径
     * @var string
     */
    protected $ApiBase = 'https://api.github.com/';

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
            "client_id"     => $this->config['github_app_id'],
            "redirect_uri"  => $this->config['qq_callback'] ?? (string)plugins_url('third://Index/callback', ['platform' => 'github']),
            "scope"         => $this->config['qq_scope'] ? $this->config['qq_scope'] :'get_user_info',
            "state"         => $state,
        );
        request()->isMobile() && $queryArr['display'] = 'mobile';
        return $this->ApiBase . '?' . http_build_query($queryArr);
    }

    /**
     * 获取用户信息
     * @param array $params
     * @return array
     */
    public function getUserInfo(array $params = []): array
    {
        $params = $params ? $params : $_GET;
        if (isset($params['access_token']) || (isset($params['state']) && $params['state'] == session('state') && isset($params['code']))) {
            //获取access_token
            $data = isset($params['code']) ? $this->getAccessToken($params['code']) : $params;
            $access_token = $data['access_token'] ?? '';
            $refresh_token = $data['refresh_token'] ?? '';
            $expires_in = $data['expires_in'] ?? 0;
            if ($access_token) {
                $openid = $this->getOpenId($access_token);
                //获取用户信息
                $queryArr = [
                    "access_token"       => $access_token,
                    "oauth_consumer_key" => $this->config['qq_app_id'],
                    "openid"             => $openid,
                ];
                $ret = HttpHelper::get(self::GET_USERINFO_URL, $queryArr);
                $userInfo = (array)json_decode($ret, true);
                if (!$userInfo || !isset($userInfo['ret']) || $userInfo['ret'] !== 0) {
                    return [];
                }
                $userInfo['avatar'] = $userInfo['figureurl_qq_2'] ?? '';
                return [
                    'access_token'  => $access_token,
                    'refresh_token' => $refresh_token,
                    'expires_in'    => $expires_in,
                    'openid'        => $openid,
                    'userinfo'      => $userInfo
                ];
            }
        }
        return [];
    }

    /**
     * 获取access_token
     * @param string $code
     * @return array
     */
    public function getAccessToken($code = ''): array
    {
        if (!$code) {
            return [];
        }
        $queryArr = array(
            "grant_type"    => "authorization_code",
            "client_id"     => $this->config['qq_app_id'],
            "client_secret" => $this->config['qq_app_secret'],
            "redirect_uri"  => $this->config['qq_callback'] ?? (string)plugins_url('third://Index/callback', ['platform' => 'qq']),
            "code"          => $code,
        );
        $ret = HttpHelper::get($this->GetAccessTokenURL,$queryArr);
        $params = [];
        parse_str($ret, $params);
        return $params ? $params : [];
    }

    /**
     * 获取open_id
     * @param string $access_token
     * @return string
     */
    private function getOpenId(string $access_token = ''): string
    {
        $response = HttpHelper::get(self::GET_OPENID_URL, ['access_token' => $access_token]);
        if (strpos($response, "callback") !== false) {
            $left_pos = strpos($response, "(");
            $right_pos = strrpos($response, ")");
            $response = substr($response, $left_pos + 1, $right_pos - $left_pos - 1);
        }
        $user = (array)json_decode($response, true);
        return $user['openid'] ?? '';
    }
}
