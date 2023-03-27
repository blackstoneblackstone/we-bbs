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
use app\common\library\helper\TemplateHelper;
use app\model\Users;
use app\common\traits\Jump;
use think\App;
use think\exception\ValidateException;
use think\facade\Request;
use think\Validate;

/**
 * 控制器基础类
 */
class Widget
{
	use Jump;
    /**
     * Request实例
     * @var Request
     */
    protected $request;

    /**
     * 应用实例
     * @var App
     */
    protected $app;

    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;

    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];

	/**
	 * 当前模型
	 * @Model
	 * @var object
	 */
	protected $model;
	protected $settings;
	protected $user_info;
	protected $user_id;
	protected $view;
	protected $theme;

    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
	    $this->request = $this->app->request;
	    $this->view = $this->app->view;
        $this->theme =  TemplateHelper::instance()->getDefaultTheme();
        $this->user_id = (int)session('login_uid');
        $this->user_info = $this->user_id ? Users::getUserInfo($this->user_id) : [];
		$config = [
            'view_path' =>  public_path().'templates'.DS.$this->theme.DS.'html'.DS.'widget'.DS
        ];
        $this->view->config($config);
	    $this->view->engine()->layout(false);

        $this->settings = get_setting();
        $this->assign([
            'baseUrl'=> \think\facade\Request::domain(),
            'thisController'       => parse_name($this->request->controller()),
            'thisAction'           => $this->request->action(),
            'thisRequest'          => parse_name("{$this->request->controller()}/{$this->request->action()}"),
            'cdnUrl' => get_setting('cdn_url',Request::domain()),
            '_ajax' =>$this->request->param('_ajax', 0),
            '_ajax_open'=>$this->request->param('_ajax_open', 0),
            '_pjax'=>$this->request->isPjax() ? 1 : 0,
            'setting'=>$this->settings,
            'user_info'=>$this->user_info,
            'user_id'=>$this->user_id,
            'isMobile'=>Request::isMobile(),
            'theme_config'=>get_theme_setting(),//模板配置调用
            'params'=>$this->request->param()
        ]);
    }

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return bool|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], $batch = false)
    {
	    try {
		    if (is_array($validate)) {
			    $v = new Validate();
			    $v->rule($validate);
		    } else {
			    if (strpos($validate, '.')) {
				    // 支持场景
				    list($validate, $scene) = explode('.', $validate);
			    }
			    $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
			    $v     = new $class();
			    if (!empty($scene)) {
				    $v->scene($scene);
			    }
		    }

		    $v->message($message);

		    // 是否批量验证
		    if ($batch || $this->batchValidate) {
			    $v->batch(true);
		    }
		    return $v->failException(true)->check($data);
	    } catch (\Exception $e) {
		    $this->error($e->getMessage());
	    }
	    return true;
    }

	/**
	 * 模板变量赋值
	 * @param string|array $name 模板变量
	 * @param mixed $value 变量值
	 * @return mixed
	 */
	public function assign($name, $value = null)
	{
		return $this->view->assign($name, $value);
	}

	/**
	 * 解析和获取模板内容 用于输出
	 * @param string $template
	 * @param array $vars
	 * @return mixed
	 */
	public function fetch(string $template = '',array $vars = [])
	{
        $view_path = public_path().'templates'.DS.$this->theme.DS.'html'.DS.'widget'.DS;
        if(get_setting('mobile_enable')=='Y' && ENTRANCE=='mobile')
            $view_path = public_path().'templates'.DS.$this->theme.DS.'mobile'.DS.'widget'.DS;

        if(get_setting('wechat_enable')=='Y' && ENTRANCE=='wechat')
            $view_path = public_path().'templates'.DS.$this->theme.DS.'wechat'.DS.'widget'.DS;

        $view_suffix = 'php';
        //检查模板是否存在，不存在继续使用默认模板
        if ($this->theme != 'default' && !file_exists($view_path . $template . '.' . $view_suffix)) {
            $view_path = public_path().'templates'.DS.'default'.DS.'html'.DS.'widget'.DS;;
            $this->theme = 'default';
            if(get_setting('mobile_enable')=='Y' && ENTRANCE=='mobile')
                $view_path = public_path().'templates'.DS.$this->theme.DS.'mobile'.DS.'widget'.DS;

            if(get_setting('wechat_enable')=='Y' && ENTRANCE=='wechat')
                $view_path = public_path().'templates'.DS.$this->theme.DS.'wechat'.DS.'widget'.DS;
        }
        $this->view->config([
            'view_path' => $view_path,
        ]);
        return $this->view->fetch($template, $vars);
	}
}
