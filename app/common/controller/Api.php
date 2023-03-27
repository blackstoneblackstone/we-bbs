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
namespace app\common\controller;
use app\common\library\helper\StringHelper;
use app\common\library\helper\TokenHelper;
use app\common\library\helper\UserAuthHelper;
use app\common\library\helper\WeChatHelper;
use app\common\traits\Jump;
use app\model\Users;
use think\App;

//解决跨域问题
header('Access-Control-Allow-Origin:*');//允许所有来源访问
header("Access-Control-Allow-Credentials:true");
header('Access-Control-Allow-Method:GET,POST,OPTIONS');//允许访问的方式
header("Access-Control-Allow-Headers:Content-Type,AccessToken,version,UserToken,ClientType,origin");//OPTIONS请求，直接返回
if (request()->method() == "OPTIONS") {
    exit();
}

abstract class Api
{
    use Jump;
	protected $auth;
    /**
     * Request实例
     */
    protected $request;
    protected $user_info;
    protected $user_id;
    /**
     * 应用实例
     */
    protected $app;
    /**
     * 默认响应输出类型,支持json/xml
     * @var string
     */
    protected $responseType = 'json';

    /**
     * @var array 前置操作方法列表
     */
    protected $beforeActionList = [];

    /**
     * 无需登录的方法
     * @var array
     */
    protected $noNeedLogin = [];

    protected $needLogin=[];
	//控制器名称
	protected $controller;
	//方法名称
	protected $action;

    protected $settings;

	/**
	 * 构造方法
	 * Frontend constructor.
	 * @param App $app
	 */
	public function __construct(App $app)
	{
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->auth = UserAuthHelper::instance();
        $this->settings = get_setting();
        //检查IP封禁
        $this->checkIpAllowed();

        //前置操作
        if ($this->beforeActionList)
        {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ? $this->beforeAction($options) : $this->beforeAction($method, $options);
            }
        }
        $this->controller = strtolower($this->request->controller());
        $this->action = $this->request->action();
		// 控制器初始化
		$this->initialize();
	}

	public function initialize()
	{
        $this->user_id = 0;
        //检验权限地址
        $token = $this->request->header('UserToken', $this->request->server('UserToken', cookie('token')));
        if ($token) {
            $data = TokenHelper::get($token);
            if (!$data) {
                $this->user_id = 0;
            }else{
                $this->user_id = intval($data['uid']);
                if ($this->user_id) {
                    $this->user_info = Users::getUserInfo($this->user_id);
                }
            }
        }

        //跨域请求检测
        $this->checkCrossRequest();

        // 检测是否需要验证登录
        if ($this->auth->match($this->needLogin)) {
            //检测是否登录
            if (!$this->user_id) {
                $this->apiResult([],99,'请先登录后进行操作');
            }
        }
	}

    /**
     * 前置操作
     * @access protected
     * @param string $method  前置操作方法名
     * @param array  $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction(string $method, array $options = [])
    {
        if (isset($options['only']))
        {
            if (is_string($options['only'])) {
                $options['only'] = explode(',', $options['only']);
            }

            if (!in_array($this->request->action(), $options['only'])) {
                return;
            }
        } elseif (isset($options['except'])) {
            if (is_string($options['except'])) {
                $options['except'] = explode(',', $options['except']);
            }

            if (in_array($this->request->action(), $options['except'])) {
                return;
            }
        }
        call_user_func([$this, $method]);
    }

    /**
     * 关键词审核
     * @param null $content
     * @param string $approval_type
     * @return bool
     */
    public function publish_approval_valid($content = null,string $approval_type='publish_question_approval'): bool
    {
        //管理员不审核
        if ($this->user_info['group_id']==1 OR $this->user_info['group_id']==2)
        {
            return false;
        }

        //违禁词审核
        if ($content AND StringHelper::sensitive_word_exists($content))
        {
            return true;
        }
        //未开启审核
        if (!$this->user_info['permission']['publish_approval_time_start'] AND !$this->user_info['permission']['publish_approval_time_end'] AND $this->user_info['permission'][$approval_type] == 'N')
        {
            return false;
        }

        //规定时间内审核
        if ($this->user_info['permission']['publish_approval_time_start'] && $this->user_info['permission']['publish_approval_time_end'] && $this->user_info['permission'][$approval_type]=='Y')
        {
            $publish_approval_time_start =str_replace(':','',$this->user_info['permission']['publish_approval_time_start']);
            $publish_approval_time_end =str_replace(':','',$this->user_info['permission']['publish_approval_time_end']);

            //同一天
            if($publish_approval_time_start < $publish_approval_time_end)
            {
                if (time() >= strtotime(date('Y-m-d',time()).$this->user_info['permission']['publish_approval_time_start']) && time() <= strtotime(date('Y-m-d',time()).$this->user_info['permission']['publish_approval_time_end']))
                {
                    return true;
                }
            }

            //跨天
            if($publish_approval_time_start > $publish_approval_time_end)
            {
                if (time() >= strtotime(date('Y-m-d',time()).$this->user_info['permission']['publish_approval_time_start']) && time() <= strtotime(date('Y-m-d',strtotime('+1 day')).$this->user_info['permission']['publish_approval_time_end']))
                {
                    return true;
                }
            }
        }

        if($this->user_info['permission'][$approval_type]=='Y' && !$this->user_info['permission']['publish_approval_time_start'] && !$this->user_info['permission']['publish_approval_time_end'])
        {
            return true;
        }

        return false;
    }

    /**
     * 微信小程序内容安全检测
     * @param mixed $content
     * @param string $msg
     * @return mixed
     * */
    public function wxminiCheckText($content, $msg = '')
    {
        $msg = $msg ?: '输入内容不符合微信小程序安全检测';
        try {
            $app = WeChatHelper::instance()->getMiniProgram();
            if (is_array($content)) {
                foreach ($content as $c) {
                    if (!$c) continue;
                    // 微信小程序内容安全检测
                    $checkRes = $app->content_security->checkText($c);
                    if (isset($checkRes['errCode']) && $checkRes['errCode']) {
                        $this->apiError($msg);
                    }
                }

                return true;
            }
            // 微信小程序内容安全检测
            if (!$content) return true;
            $checkRes = $app->content_security->checkText($content);
            if (isset($checkRes['errCode']) && $checkRes['errCode']) {
                $this->apiError($msg);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 微信小程序内容安全检测
     * @param mixed $img
     * @param string $msg
     * @return mixed
     * */
    public function wxminiCheckImage($img, $msg = '')
    {
        $msg = $msg ?: '图片不符合微信小程序安全检测';
        try {
            $app = WeChatHelper::instance()->getMiniProgram();
            if (is_array($img)) {
                foreach ($img as $c) {
                    // 微信小程序内容安全检测
                    $checkRes = $app->content_security->checkImage($c);
                    if (isset($checkRes['errCode']) && $checkRes['errCode']) {
                        $this->apiError($msg);
                    }
                }

                return true;
            }
            // 微信小程序内容安全检测
            $checkRes = $app->content_security->checkImage($img);
            if (isset($checkRes['errCode']) && $checkRes['errCode']) {
                $this->apiError($msg);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}