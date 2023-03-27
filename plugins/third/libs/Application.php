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

namespace plugins\third\libs;

class Application
{

    /**
     * 配置信息
     * @var array
     */
    private $config = [];

    /**
     * 服务提供者
     * @var array
     */
    private $providers = [
        'qq'      => 'Qq',
        'weibo'   => 'Weibo',
        'wechat'  => 'Wechat',
    ];

    /**
     * 服务对象信息
     * @var array
     */
    protected $services = [];

    public function __construct($options = [])
    {
        $options = array_intersect_key($options, $this->providers);
        $options = array_merge($this->config, is_array($options) ? $options : []);
        $this->config = $options;
        //注册服务器提供者
        $this->registerProviders();
    }

    /**
     * 注册服务提供者
     */
    private function registerProviders()
    {
        foreach ($this->providers as $k => $v) {
            $this->services[$k] = function () use ($k, $v) {
                $options = $this->config;
                $options['callback'] = isset($options['callback']) && $options['callback'] ? $options['callback'] : (string) url('ThirdAuth/callback', ['platform' => $k], false, true);
                $objName = __NAMESPACE__ . "\\sdk\\{$v}";
                return new $objName($options);
            };
        }
    }

    public function __set($key, $value)
    {
        $this->services[$key] = $value;
    }

    public function __get($key)
    {
        return isset($this->services[$key]) ? $this->services[$key]($this) : null;
    }
}
