<?php
declare(strict_types=1);

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

use app\common\library\helper\RenderHelper;
use app\common\library\helper\TemplateHelper;
use app\common\library\helper\TokenHelper;
use app\common\traits\Jump;
use app\model\Users;
use think\App;
use think\facade\Config;
use think\facade\Lang;
use think\facade\Request;

abstract class Plugins
{
    use Jump;
    // app 容器
    protected $app;
    // 请求对象
    protected $request;
    // 当前插件标识
    protected $name;
    // 插件路径
    protected $plugin_path;
    // 视图模型
    protected $view;
    // 插件配置
    protected $plugin_config;
    // 插件信息
    protected $plugin_info;
    protected $user_id;
    protected $user_info;
    protected $theme = 'default';
    /**
     * javascript渲染文件列表
     * @var array
     */
    public static $scriptFile=[];

    /**
     * css渲染文件列表
     * @var array
     */
    public static $styleFile=[];
    protected $isMobile;
    /**
     * 插件构造函数
     * plugins constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        //检查IP封禁
        $this->checkIpAllowed();
        $this->app = $app;
        $this->request = $app->request;
        $this->name = $this->getName();
        $this->plugin_path = $app->plugins->getpluginsPath() . $this->name . DIRECTORY_SEPARATOR;
        $this->plugin_config = "cache_plugins_{$this->name}_config";
        $this->plugin_info = "cache_plugins_{$this->name}_info";
        $this->view = $this->app->view;
        $this->view->engine()->layout(false);
        $this->theme = TemplateHelper::instance()->getDefaultTheme();
        $this->view->config([
            'view_path' => $this->plugin_path . 'view' . DIRECTORY_SEPARATOR
        ]);

        //加载语言包
        $defaultLang = cookie('think_lang') ?:config('lang.default_lang');
        if(file_exists($this->plugin_path . 'lang' . DIRECTORY_SEPARATOR.$defaultLang.'.php')){
            Lang::load([
                $this->plugin_path . 'lang' . DIRECTORY_SEPARATOR.$defaultLang.'.php'
            ]);
        }

        //兼容API用户获取
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
        }else{
            $this->user_info = Users::getUserInfo(session('login_uid'));
            $this->user_id = $this->user_info['uid'] ?? 0;
        }

        if(file_exists($this->plugin_path.'vendor/autoload.php'))
        {
            require $this->plugin_path.'vendor/autoload.php';
        }

        if(get_setting('wechat_enable')=='Y' && ENTRANCE=='wechat')
        {
            $theme_block = public_path().'templates/'.$this->theme.'/wechat/block.php';
        }else if(get_setting('mobile_enable')=='Y' && ENTRANCE=='mobile')
        {
            $theme_block = public_path().'templates/'.$this->theme.'/mobile/block.php';
        }else{
            $theme_block = public_path().'templates/'.$this->theme.'/html/block.php';
        }
        $this->assign([
            'theme_block'=>$theme_block,
        ]);

        $this->isMobile = get_setting('mobile_enable')=='Y' && ENTRANCE=='mobile' && Request::isMobile();
        // 控制器初始化
        $this->initialize();
    }

    // 初始化
    protected function initialize()
    {}

    /**
     * 获取插件标识
     * @return mixed|null
     */
    final protected function getName()
    {
        $class = get_class($this);
        list(, $name, ) = explode('\\', $class);
        $this->request->plugin = $name;

        return $name;
    }

    /**
     * 加载模板输出
     * @param string $template
     * @param array $vars           模板文件名
     * @return false|mixed|string   模板输出变量
     */
    protected function fetch(string $template = '', array $vars = [])
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

    /**
     * 模板变量赋值
     * @access protected
     * @param  mixed $name  要显示的模板变量
     * @param  mixed $value 变量的值
     * @return $this
     */
    protected function assign($name, $value = '')
    {
        $this->view->assign($name,$value);
        return $this;
    }

    /**
     * 初始化模板引擎
     * @access protected
     * @param  array|string $engine 引擎参数
     * @return $this
     */
    protected function engine($engine)
    {
        $this->view->engine($engine);
        return $this;
    }

    /**
     * 插件基础信息
     * @return array
     */
    final public function getInfo()
    {
        $info = Config::get($this->plugin_info, []);
        if ($info) {
            return $info;
        }
        // 文件属性
        $info = db('plugins')->where(['status'=>1,'name'=>$this->getName()])->find();
        Config::set($info, $this->plugin_info);
        return $info ?? [];
    }

    private function checkConfigGroup($config): bool
    {
        // 获取第一个元素
        $arrayShift = array_shift($config);
        if (array_key_exists('title', $arrayShift) && array_key_exists('type', $arrayShift)) {
            // 未开启分组
            return false;
        } else {
            // 开启分组
            return true;
        }
    }

    /**
     * 获取配置信息
     * @param bool $type 是否获取完整配置
     * @return array|mixed
     */
    final public function getConfig(bool $type = false)
    {
        $config = Config::get($this->plugin_config, []);
        if ($config) {
            return $config;
        }
        $config_file = db('plugins')->where(['status'=>1,'name'=>$this->getName()])->value('config');
        if ($config_file) {
            $temp_arr = json_decode($config_file,true);
            if ($type) {
                return $temp_arr;
            }
            if(empty($temp_arr)) return false;
            if($this->checkConfigGroup($temp_arr))
            {
                foreach ($temp_arr as $key=>$val)
                {
                    foreach ($val['config'] as $k=>$v)
                    {
                        if (in_array($v['type'], ['files','checkbox','images'])) {
                            $v['value'] = is_array($v['value']) ? $v['value'] : explode(',', $v['value']);
                        }
                        if ($v['type'] == 'array') {
                            $v['value'] = json_decode($v['option'],true);
                        }
                        $config[$key][$k]=$v['value'];
                    }
                }
            }else{
                foreach ($temp_arr as $key=>$val)
                {
                    if(isset($val['type']))
                    {
                        if (in_array($val['type'], ['files','checkbox','images'])) {
                            $val['value'] = explode(',', $val['value']);
                        }

                        if ($val['type'] == 'array') {
                            $val['value'] = json_decode($val['option'],true);
                        }
                    }

                    $config[$key]=$val['value'];
                }
            }
        }

        Config::set($config, $this->plugin_config);
        return $config;
    }

    protected function TDK($title='',$keywords='',$description='')
    {
        $tdk = array(
            '_page_title' =>$title ? $title .' - '.get_setting('seo_title'): get_setting('seo_title'),
            '_page_keywords' => $keywords ?: get_setting('seo_keywords'),
            '_page_description' => $description ?: get_setting('seo_description'),
        );
        $this->assign($tdk);
    }

    /**
     * 加载js
     * @param array $script
     */
    protected function script(array $script=[])
    {
        self::$scriptFile = array_merge(self::$scriptFile,$script);
        $scriptFile =$script ?  RenderHelper::script(self::$scriptFile) : '';
        $this->assign('_script',$scriptFile);
    }

    /**
     * 加载样式文件
     * @param array $style
     */
    protected function style(array $style=[])
    {
        self::$styleFile = array_merge(self::$styleFile,$style);
        $styleFile = $style ? RenderHelper::style(self::$styleFile) : '';
        $this->assign('_style',$styleFile);
    }

   //安装插件方法
   public function install(){
       return true;
   }

    //卸载插件方法
    public function uninstall(){
        return true;
    }

    //安装后插件方法
    public function installAfter(){
        return true;
    }

    //卸载后插件方法
    public function uninstallAfter(){
        return true;
    }

    //启用方法
    public function enable(){
        return true;
    }

    //禁用方法
    public function disable(){
        return true;
    }

    //升级方法
    public function upgrade(){
        return true;
    }
}
