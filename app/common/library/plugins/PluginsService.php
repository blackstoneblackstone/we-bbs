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
namespace app\common\library\plugins;
use app\common\library\helper\FileHelper;
use think\Route;
use think\facade\Lang;
use think\facade\Event;
use think\Service;

/**
 * 插件服务
 * Class Service
 * @package
 */
class PluginsService extends Service
{
    protected $plugins_path;

    public function register()
    {
        $this->plugins_path = $this->getPluginsPath();
        // 加载系统语言包
        Lang::load([
            $this->app->getRootPath() . 'app/common/library/plugins/lang/zh-cn.php'
        ]);
        // 加载插件事件
        $this->loadEvent();

        // 绑定插件容器
        $this->app->bind('plugins', PluginsService::class);
        define('PLUGINS_PATH',$this->plugins_path);
    }

    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            // 路由脚本
            $execute = '\\app\\common\\library\\plugins\\Route::execute';

            // 注册插件公共中间件
            if (is_file($this->getPluginsPath() . 'middleware.php')) {
                $this->app->middleware->import(include $this->getPluginsPath() . 'middleware.php', 'route');
            }

            // 注册控制器路由
            $route->rule("plugins/:plugin/[:controller]/[:action]", $execute);

            $plugins = FileHelper::getList(PLUGINS_PATH);
            foreach ($plugins as $name) {
                $pluginDir = PLUGINS_PATH  . $name . DIRECTORY_SEPARATOR;
                if (!is_dir($pluginDir)) {
                    continue;
                }

                if (file_exists($pluginDir.'info.php'))
                {
                    //加载插件中间件
                    if (is_file($pluginDir . 'middleware.php')) {
                        $this->app->middleware->import(include $pluginDir . 'middleware.php', 'route');
                    }

                    //加载多语言
                    $lang = cookie('aws_lang')?:'zh-cn';
                    if(is_dir($pluginDir.'lang') && file_exists($pluginDir.'lang'.DS.$lang.'.php'))
                    {
                        Lang::load([
                            $pluginDir.'lang'.DS.$lang.'.php'
                        ]);
                    }

                    //加载自定义路由规则
                    if(file_exists($pluginDir.'rewrite.php'))
                    {
                        $routes = include $pluginDir.'rewrite.php';
                        $domain = $routes['domain']??'';
                        foreach ($routes as $key => $val) {
                            if (!$val) {
                                continue;
                            }
                            if($domain)
                            {
                                $rules = [];
                                foreach ($routes['rule'] as $k => $rule) {
                                    [$plugin, $controller, $action] = explode('/', $rule);
                                    $rules[$k] = [
                                        'plugin'        => $plugin,
                                        'controller'    => $controller,
                                        'action'        => $action,
                                    ];
                                }
                                $route->domain($domain, function () use ($rules, $route, $execute) {
                                    // 动态注册域名的路由规则
                                    foreach ($rules as $k => $rule) {
                                        $route->rule($k, $execute)
                                            ->name($k)
                                            ->completeMatch(true)
                                            ->append($rule);
                                    }
                                });
                            }else {
                                list($plugin, $controller, $action) = explode('/', $val);
                                $route->rule($key, $execute)
                                    ->name($key)
                                    ->completeMatch(true)
                                    ->append([
                                        'plugin' => $plugin,
                                        'controller' => $controller,
                                        'action' => $action
                                    ]);
                            }
                        }
                    }
                }
            }
        });
    }

    /**
     * 插件事件
     */
    private function loadEvent()
    {
        $hookPlugins    = db('hook_plugins')
            ->order('sort','DESC')
            ->where('status', 1)
            ->field('hook,plugins')
            ->select()
            ->toArray();
        $hook_plugin = [];
        foreach ($hookPlugins as $values)
        {
            $hook_plugin[$values['hook']][] = $values['plugins'];
        }

        $hookEvent = [];

        foreach ($hook_plugin as $key=>$values)
        {
            if (is_string($values)) {
                $values = explode(',', $values);
            } else {
                $values = (array) $values;
            }
            $hookEvent[$key] = array_filter(array_map(function ($v) use ($key) {
                return [get_plugins_class($v), $key];
            }, $values));
        }

        // 插件初始化行为
        if (isset($hookEvent['appInit'])) {
            foreach ($hookEvent['appInit'] as $value) {
                Event::trigger('appInit', $value);
            }
        }
        Event::listenEvents($hookEvent);
    }

    /**
     * 获取 plugins 路径
     * @return string
     */
    public function getPluginsPath(): string
    {
        // 初始化插件目录
        $plugins_path = $this->app->getRootPath() . 'plugins' . DIRECTORY_SEPARATOR;
        // 如果插件目录不存在则创建
        if (!is_dir($plugins_path)) {
            @mkdir($plugins_path, 0755, true);
        }
        return $plugins_path;
    }
}
