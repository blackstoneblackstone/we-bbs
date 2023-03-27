<?php
declare (strict_types = 1);

namespace app\common\controller;

use app\common\traits\Jump;
use Psr\SimpleCache\InvalidArgumentException;
use think\App;
use think\exception\ValidateException;
use think\facade\Cache;
use think\facade\Config;
use think\Request;
use think\Validate;

/**
 * 控制器基础类
 */
abstract class Base
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

    protected $beforeActionList=[];
    protected $view;

    /**
     * 构造方法
     * @access public
     * @param App $app 应用对象
     * @throws InvalidArgumentException
     */
    public function __construct(App $app)
    {
        $this->app     = $app;
        $this->request = $this->app->request;
        $this->view = $this->app->view;

        //前置操作
        if ($this->beforeActionList) {
            foreach ($this->beforeActionList as $method => $options) {
                is_numeric($method) ? $this->beforeAction($options) : $this->beforeAction($method, $options);
            }
        }

        //缓存配置
        $cache_type = get_setting('cache_type','file',true);
        Config::set([
            // 服务器地址
            'host' => get_setting('cache_host','127.0.0.1'),
            // 端口号
            'port' => get_setting('cache_port','11211'),
            // 密码
            'password'=> get_setting('cache_password'),
        ],'aws');

        if($cache_type!='file')
        {
            try {
                if(!Cache::store($cache_type)->set('aws_cache_test', 'WeCenter'))
                {
                   Cache::store('file');
                    db('config')->where(['name'=>'cache_type'])->update(['value'=>'file']);
                }
            }catch (\Exception $e){
                Cache::store('file');
                db('config')->where(['name'=>'cache_type'])->update(['value'=>'file']);
            }
        }
        // 控制器初始化
        //$this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
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
    }

    /**
     * 前置操作
     * @access protected
     * @param string $method  前置操作方法名
     * @param array $options 调用参数 ['only'=>[...]] 或者 ['except'=>[...]]
     * @return void
     */
    protected function beforeAction(string $method, array $options = [])
    {
        if (isset($options['only'])) {
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
    public function fetch(string $template = '', array $vars = [])
    {
        return $this->view->fetch($template, $vars);
    }


    /**
     * 渲染内容输出
     * @access protected
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @return mixed
     */
    protected function display(string $content = '',array $vars = [])
    {
        return $this->view->display($content, $vars);
    }
}
