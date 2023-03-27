<?php

namespace app\common\library\helper;
use app\model\admin\AdminAuth;
use Exception;
use FilesystemIterator;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;

/**
 * 插件管理
 * Class PluginsHelper
 * @package app\common\library\helper
 */
class PluginsHelper
{
    // 插件目录
    public $pluginsPath='';

    private static $instance;
    public $error;

    // 构造方法
    public function __construct()
    {
        $this->pluginsPath = app()->getRootPath() . 'plugins';
    }

    public static function instance(): PluginsHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 获得本地插件列表
    public function getPluginsList($status=0,$type=''): array
    {
        $map                = [];
        if($status!=3)
        {
            $map['status']      = $status;
        }
        if($type)
        {
            $map['type']      = $type;
        }
        $plugins_list = db('plugins')->where($map)
            ->order('sort,id')
            ->select()->toArray();

        $list = [];
        if($status==0)
        {
            $all_plugins  = db('plugins')->order('sort,id')->column('id,name', 'name');
            $plugins = FileHelper::getList($this->pluginsPath);
            foreach ($plugins as $name) {
                // 排除系统插件和已存在数据库的插件
                if (array_key_exists($name, $all_plugins))
                {
                    continue;
                }

                $pluginDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
                if (!is_dir($pluginDir)) {
                    continue;
                }

                if (file_exists($pluginDir.'info.php'))
                {
                    // 获取插件基础信息
                    $info = include $pluginDir.'info.php';

                    //插件名和目录名不一致时跳过安装
                    if($info['name']!=$name) continue;

                    $sql                = [];
                    $sql['title']        = $info['title'];
                    $sql['name']  = $info['name']?:'';
                    $sql['description']   = $info['description'];
                    $sql['config']      = '';
                    $sql['author']      = $info['author'];
                    $sql['status']      = 0;
                    $sql['version']     = $info['version'];
                    $sql['author_url']         = $info['author_url']??'';
                    $sql['plugin_url']         = $info['plugin_url']??'';
                    $sql['type']         = $info['type']??'plugins';
                    $sql['identifier'] = $info['identifier'] ?? $info['author'].'_'.$info['name'];
                    $sql['setting']     = isset($info['setting']) ? json_encode($info['setting'],JSON_UNESCAPED_UNICODE) : '';
                    $id = db('plugins')->insertGetId($sql);
                    $sql['id'] = $id;
                    $plugins_list = array_merge($plugins_list, [$sql]);
                }
            }
        }

        foreach ($plugins_list as $key=> $val)
        {
            // 增加右侧按钮组
            $str = '';
            if ($val['status'] == 1) {
                // 已安装，增加配置按钮
                if($val['config'])
                {
                    $str .= '<a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="AWS_ADMIN.operate.edit(\''.$val['name'].'\')">配置</a> ';
                }
                $str .= '<a class="btn btn-warning btn-sm aw-ajax-get" data-confirm="是否清除数据库数据？<br><font color=green>选择‘否’时将保留数据库数据</font><br><font color=red>选择‘是’将完全卸载数据库</font>"  data-url="'.url('uninstall',['name'=>$val['name'],'real'=>1]).'"  data-cancel="'.url('uninstall',['name'=>$val['name'],'real'=>0]).'">卸载</a> ';
                $str .= '<a class="btn btn-danger btn-sm aw-ajax-get" data-confirm="是否禁用该插件？" data-url="'.url('status',['name'=>$val['name'],'status'=>2]).'">禁用</a> ';
                //$str .= '<a class="btn btn-primary btn-sm aw-ajax-get" data-confirm="是否更新插件？更新插件时会同步更新插件下的配置文件" data-url="'.url('upgrade',['name'=>$val['name']]).'"><i class="fa fa-edit"></i> 更新</a>';
                if($val['plugin_url'])
                {
                    $str .= '<a class="btn btn-primary btn-sm aw-ajax-open" data-title="插件说明" data-url="'.$val['plugin_url'].'">说明</a>';
                }
            }else if($val['status'] == 2){
                /*if($val['config'])
                {
                    $str .= '<a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="AWS_ADMIN.operate.edit(\''.$val['name'].'\')"><i class="fa fa-edit"></i> 配置</a> ';
                }*/
                $str .= '<a class="btn btn-warning btn-sm aw-ajax-get" data-confirm="是否清除数据库数据？<br><font color=green>选择‘否’时将保留数据库数据</font><br><font color=red>选择‘是’将完全卸载数据库</font>"  data-url="'.url('uninstall',['name'=>$val['name'],'real'=>1]).'"  data-cancel="'.url('uninstall',['name'=>$val['name'],'real'=>0]).'">卸载</a> ';
                $str .= '<a class="btn btn-primary btn-sm aw-ajax-get" data-confirm="是否启用该插件？"  data-url="'.url('status',['name'=>$val['name'],'status'=>1]).'">启用</a> ';
                $str .= '<a class="btn btn-danger btn-sm aw-ajax-get" data-confirm="是否删除该插件？删除插件会删除该插件的源文件代码及资源"  data-url="'.url('delete',['name'=>$val['name']]).'">删除</a> ';
            } else {
                // 未安装，增加安装按钮
                $conflict_str = '';
                if($conflict_list = $this->checkConflict($val['name'])){
                    $system_str = $template_str = '';
                    foreach ($conflict_list as $k=>$path)
                    {
                        if($k=='system')
                        {
                            $system_str.=implode('<br>',$path).'<br>';
                        }

                        if($k=='template')
                        {
                            $template_str.=implode('<br>',$path).'<br>';
                        }
                    }
                    $conflict_str = '系统检测到该插件与系统文件有冲突!<br>'.($system_str?'系统冲突文件：<br>'.$system_str : '').($template_str?'模板冲突文件：<br>'.$template_str : '').'<font color=red>注意：继续安装可能会覆盖现有功能！</font> <br>是否继续安装?';
                }
                if($conflict_str)
                {
                    $str .= '<a class="btn btn-primary btn-sm aw-ajax-get" data-confirm="'.$conflict_str.'"  data-url="'.url('install',['name'=>$val['name']]).'">安装</a> ';
                }else{
                    $str .= '<a class="btn btn-primary btn-sm aw-ajax-get" data-url="'.url('install',['name'=>$val['name']]).'">安装</a> ';
                }
                $str .= '<a class="btn btn-danger btn-sm aw-ajax-get" data-confirm="是否删除该插件？删除插件会删除该插件的源文件代码及资源"  data-url="'.url('delete',['name'=>$val['name']]).'">删除</a> ';
            }
            $val['button'] = $str;
            $list[$key] = $val;
        }
        return $list;
    }

    // 获取插件信息
    public function config(string $name)
    {
        return $this->getConfig($name,true);
    }

    // 获取插件实例
    private function getInstance(string $file)
    {
        $class = "\\plugins\\{$file}\\Plugin";
        try {
            if (class_exists($class)) {
                return app($class);
            }
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    // 安装插件
    public function install(string $name): array
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }

        $pluginsDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        $base = get_class_methods("\\app\\common\\controller\\Plugins");
        $object = $this->getInstance($name);
        if ($object->install()) {
            try {
                $info = include $pluginsDir.'info.php';

                //导入SQl
                $this->importSql($name);

                // 复制文件
                $this->copyDir($name);

                // 导入菜单
                if (isset($info['menu'])) {
                    $menu_config = $info['menu'];
                    if(!empty($menu_config))
                    {
                        if(isset($menu_config['is_nav']) && $menu_config['is_nav']==1){
                            $pid = 0;
                        }else{
                            $pid = db('admin_auth')->where(['name' => 'plugin'])->value('id');
                        }
                        $menu[] = $menu_config['menu'];
                        //导入菜单
                        $this->addMenu($menu,$pid);
                    }
                }

                $sql                = [];
                $sql['title']        = $info['title'];
                $sql['name']  = $info['name']?:'';
                $sql['description']   = $info['description'];
                $sql['author']      = $info['author'];
                $sql['version']     = $info['version'];
                $sql['author_url']         = $info['author_url']??'';
                $sql['plugin_url']  = $info['plugin_url']??'';
                $sql['identifier'] = $info['identifier'] ?? $info['author'].'_'.$info['name'];
                $sql['setting']     = isset($info['setting']) ? json_encode($info['setting'],JSON_UNESCAPED_UNICODE) : '';
                $sql['type']         = $info['type']??'plugins';
                // 导入插件配置
                if ((isset($info['config']) && !empty($info['config'])))
                {
                    $sql['config'] = $info['config'];
                    if(file_exists($this->pluginsPath.DS.$name.DS.'config.php'))
                    {
                        $old_config = include $this->pluginsPath.DS.$name.DS.'config.php';
                        //合并插件配置
                        $sql['config']=array_merge($info['config'],$old_config);
                    }
                    $sql['config'] = json_encode($sql['config'], JSON_UNESCAPED_UNICODE);
                }
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\Plugin" );
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);

                //安装关联钩子
                if($hooks)
                {
                    foreach ($hooks as $hook)
                    {
                        if(!db('hook_plugins')->where(['hook'=>$hook,'plugins'=>$name])->find()) {
                            db('hook_plugins')->insert([
                                'hook' => $hook,
                                'plugins' => $name,
                                'status' => 1,
                                'create_time' => time()
                            ]);
                        }
                        if(!db('hook')->where(['name'=>$hook])->find()) {
                            db('hook')->insert([
                                'name' => $hook,
                                'status' => 1,
                                'create_time' => time()
                            ]);
                        }
                    }
                }

                //更新插件状态
                $sql['status']      = 1;
                $sql['update_time']      = time();
                db('plugins')->where('name', $name)->update($sql);
                //设置插件附属配置
                $this->setting();

                $object->installAfter();
                return [
                    'code' => 1,
                    'msg'  => '插件安装成功',
                ];
            } catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '安装失败：' . $e->getMessage(),
                ];
            }
        }else {
            return [
                'code' => 0,
                'msg'  => '插件实例化失败',
            ];
        }
    }

    // 卸载插件
    public function uninstall(string $name,$real_uninstall=true): array
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }
        $pluginsDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        $base = get_class_methods("\\app\\common\\controller\\Plugins");

        $object = $this->getInstance($name);
        if (false !== $object->uninstall()){
            try {
                //执行卸载sql
                if($real_uninstall)
                {
                    $this->uninstallSql($name);
                }

                $info = include $pluginsDir.'info.php';
                //执行卸载菜单
                if (isset($info['menu'])) {
                    $menu_config = $info['menu'];
                    if (!empty($menu_config)) {
                        $menu[] = $menu_config['menu'];
                        $this->removeMenu($menu);
                    }
                }

                //删除文件
                $this->removeDir($name);

                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\Plugin" );
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);

                //卸载关联钩子
                db('hook_plugins')->where(['plugins'=>$name,])->delete();
                if($hooks)
                {
                    foreach ($hooks as $hook)
                    {
                        db('hook')->where(['name'=>$hook])->delete();
                    }
                }
                //更新插件状态
                db('plugins')->where('name', $name)->update(['status'=>0]);

                $this->setting();

                $object->uninstallAfter();
                return [
                    'code' => 1,
                    'msg'  => '插件卸载成功',
                ];
            }catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '插件卸载失败：' . $e->getMessage(),
                ];
            }
        }else{
            return [
                'code' => 0,
                'msg'  => '插件卸载失败',
            ];
        }
    }

    //删除插件
    public function delete(string $name)
    {
        /*if(!$check = $this->check($name))
        {
            return $check;
        }*/
        $pluginsDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;

        try {
            //更新插件状态
            db('plugins')->where('name', $name)->delete();

            if(file_exists($pluginsDir.'info.php'))
            {
                //执行卸载sql
                $this->uninstallSql($name);

                //执行卸载菜单
                $info = include $pluginsDir.'info.php';
                //执行卸载菜单
                if (isset($info['menu'])) {
                    $menu_config = $info['menu'];
                    if (!empty($menu_config)) {
                        $menu[] = $menu_config['menu'];
                        $this->removeMenu($menu);
                    }
                }

                //删除文件
                $this->removeDir($name,'delete');

                // 读取出所有公共方法
                $base = get_class_methods("\\app\\common\\controller\\Plugins");
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\Plugin" );
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                //卸载关联钩子
                if($hooks)
                {
                    foreach ($hooks as $hook)
                    {
                        db('hook_plugins')->where([
                            'hook'=>$hook,
                            'plugins'=>$name,
                        ])->delete();
                    }
                }
            }

            $this->setting();
        }catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '插件删除失败：' . $e->getMessage(),
            ];
        }
        return [
            'code' => 1,
            'msg'  => '插件删除成功',
        ];
    }

    //插件附属配置
    private function setting()
    {
        $plugins_list = db('plugins')->where('status',1)->column('name,setting');

        $settings = [
            'tabs'=>[],
            'publish'=>[],
            'category'=>[],
            'relation'=>[]
        ];

        $commands = [
            'commands'=>[
                'task' => 'app\common\command\Task',
                'we'=> 'app\common\command\WeCenter',
            ]
        ];

        foreach ($plugins_list as $item) {
            $setting = json_decode($item['setting'],true);
            if(isset($setting['tabs']) && !empty($setting['tabs']))
            {
                $settings['tabs']=array_merge($settings['tabs'],$setting['tabs']);
            }

            if(isset($setting['publish']) && !empty($setting['publish']))
            {
                $settings['publish']=array_merge($settings['publish'],$setting['publish']);
            }

            if(isset($setting['category']) && !empty($setting['category']))
            {
                $settings['category']=array_merge($settings['category'],$setting['category']);
            }

            if(isset($setting['relation']) && !empty($setting['relation']))
            {
                $settings['relation']=array_merge($settings['relation'],$setting['relation']);
            }

            if(isset($setting['commands']) && !empty($setting['commands']))
            {
                $commands['commands']=array_merge($commands['commands'],$setting['commands']);
            }

        }

        //系统配置
        if($settings)
        {
            $file = config_path().'aws.php';
            if ($handle = fopen($file, 'w')) {
                fwrite($handle, "<?php\n\n" . "return " . var_export($settings, TRUE) . ";\n");
                fclose($handle);
            }
        }

        //定时任务
        if($commands)
        {
            $file = config_path().'console.php';
            if ($handle = fopen($file, 'w')) {
                fwrite($handle, "<?php\n\n" . "return " . var_export($commands, TRUE) . ";\n");
                fclose($handle);
            }
        }
    }

    // 启用插件
    public function enable(string $name)
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }

        $base = get_class_methods("\\app\\common\\controller\\Plugins");
        $object = $this->getInstance($name);
        if (false !== $object->enable()) {
            try {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\Plugin" );
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                if($hooks)
                {
                    foreach ($hooks as $hook)
                    {
                        db('hook_plugins')->where([
                            'hook'=>$hook,
                            'plugins'=>$name,
                        ])->update([
                            'status'=>1,
                            'update_time'=>time()
                        ]);
                    }
                }

                $result = db('plugins')->where('name', $name)->update(['status'=>1]);

                $this->setting();
                if ($result) {
                    return [
                        'code' => 1,
                        'msg'  => '状态变动成功',
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '状态变动失败：' . $e->getMessage(),
                ];
            }
        }
        return [
            'code' => 1,
            'msg'  => '状态变动成功',
        ];
    }

    // 禁用插件
    public function disable(string $name)
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }

        $base = get_class_methods("\\app\\common\\controller\\Plugins");
        $object = $this->getInstance($name);
        if (false !== $object->disable()) {
            try {
                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\Plugin" );
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                if($hooks)
                {
                    foreach ($hooks as $hook)
                    {
                        db('hook_plugins')->where([
                            'hook'=>$hook,
                            'plugins'=>$name,
                        ])->update([
                            'status'=>2,
                            'update_time'=>time()
                        ]);
                    }
                }
                $result = db('plugins')->where('name', $name)->update(['status'=>2]);
                $this->setting();
                if ($result) {
                    return [
                        'code' => 1,
                        'msg'  => '状态变动成功',
                    ];
                }
            } catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '状态变动失败：' . $e->getMessage(),
                ];
            }
        }
        return [
            'code' => 1,
            'msg'  => '状态变动成功',
        ];
    }

    // 判断插件配置文件是否进行了分组
    public function checkConfigGroup($config): bool
    {
        if(!$config) {
            return false;
        }
        // 获取第一个元素
        $arrayShift = array_shift($config);
        if (!array_key_exists('config', $arrayShift)) {
            // 未开启分组
            return false;
        } else {
            // 开启分组
            return true;
        }
    }

    // 验证插件是否完整
    private function check(string $name)
    {
        if (!is_dir($this->pluginsPath . DIRECTORY_SEPARATOR . $name)) {
            return [
                'code' => 0,
                'msg'  => '未发现该插件,请先下载并放入到plugins目录中',
            ];
        }

        $db_info = db('plugins')->where('name', $name)->find();

        if (!$db_info) {
            return [
                'code' => 0,
                'msg'  => '插件不存在',
            ];
        }

        if (!$db_info['status']) {
            return [
                'code' => 0,
                'msg'  => '插件未安装',
            ];
        }

        $pluginsDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;

        if (!file_exists($pluginsDir.'info.php')) {
            return [
                'code' => 0,
                'msg'  => '插件配置文件不存在[info.php]',
            ];
        }

        if(!file_exists($pluginsDir.'Plugin.php') && !file_exists($pluginsDir.'plugin.php'))
        {
            return [
                'code' => 0,
                'msg'  => '插件文件不存在[Plugin.php]',
            ];
        }
        return true;
    }

    /**
     * 获取配置列表
     * @param string $name
     * @param bool $update
     * @return mixed
     */
    public function getConfig(string $name='', bool $update=false)
    {
        $result = Cache::get('plugins_'.$name.'_config');
        if (!$result || $update) {
            $config = db('plugins')->where(['status'=>1,'name'=>$name])->value('config');
            if(!$config)
            {
                return false;
            }
            $result = json_decode($config,true);
            Cache::set('plugins_'.$name.'_config', $result);
        }
        return $result;
    }

    /**
     * 解析配置为键值对
     * @param string $name
     * @param string $configName
     * @param int $update
     * @return array|false
     */
    public function getPluginsConfigs(string $name, string $configName = '', int $update=0)
    {
        $config = cache('cache_plugins_config_'.$name);
        $configName    = explode('.', $configName);
        if ($config && !$update) {
            // 按.拆分成多维数组进行判断
            foreach ($configName as $val) {
                if (isset($config[$val])) {
                    $config = $config[$val];
                } else {
                    return $config;
                }
            }
            return $config;
        }

        if(!$config = $this->getConfig($name,true)) {
            return false;
        }
        $newConfig = [];
        if($this->checkConfigGroup($config))
        {
            foreach ($config as $key=>$val)
            {
                foreach ($val['config'] as $k=>$v)
                {
                    if (in_array($v['type'], ['files','checkbox','images'])) {
                        $v['value'] = is_array($v['value']) ? $v['value'] :  explode(',', $v['value']);
                    }
                    if ($v['type'] == 'array') {
                        $v['value'] = json_decode($v['options'],true);
                    }
                    $newConfig[$key][$k]= $v['value'];
                }
            }
        }else{
            foreach ($config as $key=>$val)
            {
                if (in_array($val['type'], ['files','checkbox','images'])) {
                    $val['value'] = explode(',', $val['value']);
                }

                if ($val['type'] == 'array') {
                    $val['value'] = json_decode($val['options'],true);
                }
                $newConfig[$key]= $val['value'];
            }
        }

        cache( 'cache_plugins_config_'.$name,$newConfig);
        foreach ($configName as $val) {
            if (isset($newConfig[$val])) {
                $newConfig = $newConfig[$val];
            } else {
                return $newConfig;
            }
        }
        return $newConfig;
        //return $configName ? $newConfig[$configName] : $newConfig;
    }

    /**
     * 更新插件的配置文件
     * @param string $name 插件名
     * @param array $array
     * @return mixed
     */
    public function setPluginsConfig(string $name, array $array): array
    {
        Cache::set( 'cache_modules_config_'.$name,null);
        $file = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'config.php';
        if (!$this->checkFileWritable($file)) {
            return [
                'code' => 0,
                'msg' => '文件没有写入权限',
            ];
        }
        if ($handle = fopen($file, 'w')) {
            fwrite($handle, "<?php\n\n" . "return " . var_export($array, TRUE) . ";\n");
            fclose($handle);
        } else {
            return [
                'code' => 0,
                'msg'  => '文件没有写入权限',
            ];
        }
        return [
            'code' => 1,
            'msg' => '文件写入完毕',
        ];
    }

    /**
     * 判断文件或目录是否可写
     * @param    string $file 文件或目录
     * @return    bool
     */
    private function checkFileWritable(string $file): bool
    {
        $file = pathinfo($file, PATHINFO_DIRNAME);
        return is_writable($file);
    }

    /**
     * 导入SQL
     * @param string $name
     * @param string $fileName
     * @return array|void
     */
    private function importSql(string $name,string $fileName='')
    {
        $fileName = $fileName!=='' ? $fileName :'install.sql';
        $sqlFile = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . $fileName;
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                    continue;
                }
                $tempLine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    // 不区分大小写替换前缀
                    $tempLine = str_ireplace('aws_', Config::get('database.connections.mysql.prefix'), $tempLine);
                    // 忽略数据库中已经存在的数据
                    $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                    try {
                        Db::execute($tempLine);
                    } catch (Exception $e) {
                        return [
                           'code'=>0,
                           'msg' =>$e->getMessage()
                        ];
                    }
                    $tempLine = '';
                }
            }
        }
    }

    //卸载SQL
    private function uninstallSql(string $name)
    {
        $sqlFile = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'uninstall.sql';
        if (is_file($sqlFile)) {
            $lines = file($sqlFile);
            $tempLine = '';
            foreach ($lines as $line) {
                if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                    continue;
                }

                $tempLine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    // 不区分大小写替换前缀
                    $tempLine = str_ireplace('aws_', Config::get('database.connections.mysql.prefix'), $tempLine);
                    try {
                        Db::execute($tempLine);
                    } catch (Exception $e) {
                        return [
                            'code'=>0,
                            'msg' =>$e->getMessage()
                        ];
                    }
                    $tempLine = '';
                }
            }
        }
        return true;
    }

    // 导入菜单
    private function addMenu($menu=[],$pid=0)
    {
        foreach ($menu as $k=>$v)
        {
            $hasChild = isset($v['menu_list']) && $v['menu_list'];
            if(isset($v['name']))
            {
                $v['pid'] = $v['pid'] ?? $pid ;
                $v['name'] = trim($v['name'],'/');
                $v['icon'] = $v['icon'] ?? 'fa fa-folder';
                if(!$menu = AdminAuth::where('name',$v['name'])->find())
                {
                    $menu = AdminAuth::create($v);
                }
            }
            if ($hasChild) {
                $this->addMenu($v['menu_list'], $menu['id']??$pid);
            }
        }
    }

    //删除菜单
    private function removeMenu($menu)
    {
        foreach ($menu as $k=>$v){
            $hasChild = isset($v['menu_list']) && $v['menu_list'];
            if(isset($v['name']))
            {
                if($menu_rule = AdminAuth::where('name',$v['name'])->find()){
                    $menu_rule->delete();
                }
            }
            if ($hasChild) {
                $this->removeMenu($v['menu_list']);
            }
        }
    }

    // 安装时复制资源文件和系统文件
    private function copyDir(string $name)
    {
        $addonDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        if(!env('app_debug')) {
            $zipHelper = new ZipHelper();
            $ignore = [
                'plugin.php',
                'Plugin.php',
                'library',
                'info.php',
                'config.php',
                'view',
                'static',
                'install.sql',
                'uninstall.sql',
                'upgrade.sql',
                'validate',
                'model',
                'route.php'
            ];
            $zipHelper->zip(runtime_path('backup').'plugins' . DS . $name . '.zip', '../plugins/' . $name,$ignore);
        }
        //备份冲突文件
        $this->conflictBackup($name);

        if (is_dir($addonDir . 'static'))
        {
            FileHelper::copyDir($addonDir . 'static', app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'static'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$name);
        }

        if (is_dir($addonDir . 'app'))
        {
            FileHelper::copyDir($addonDir . 'app', app()->getRootPath() . 'app'.DIRECTORY_SEPARATOR);
            if(!env('app_debug'))
            {
                FileHelper::delDir($addonDir . 'app');
            }
        }

        if (is_dir($addonDir . 'templates'))
        {
            FileHelper::copyDir($addonDir . 'templates', app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'templates'.DIRECTORY_SEPARATOR);
            if(!env('app_debug')) {
                FileHelper::delDir($addonDir . 'templates');
            }
        }

        //复制路由
        if (file_exists($addonDir . 'route.php'))
        {
            copy($addonDir . 'route.php',app()->getRootPath().'route'.DS.$name.'.php');
        }
    }

    // 卸载时删除文件
    private function removeDir(string $name,$type='uninstall')
    {
        $addonDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;

        if(!env('app_debug')) {
            $zipHelper = new ZipHelper();
            if(file_exists(runtime_path('backup').'plugins'.DS.$name.'.zip'))
            {
                chmod($this->pluginsPath,0755);
                $zipHelper->unzip(runtime_path('backup').'plugins'.DS.$name.'.zip',$this->pluginsPath);
                @unlink(runtime_path('backup').'plugins'.DS.$name.'.zip');
            }
        }

        //删除资源文件
        if (is_dir($addonDir . 'static'))
        {
            $dest = app()->getRootPath() . 'public'.DIRECTORY_SEPARATOR. 'static'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.$name;
            FileHelper::delDir($dest);
        }

        //删除模块文件
        if (is_dir($addonDir . 'app')) {
            //匹配出所有的文件
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addonDir . 'app', FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                // 如果是文件
                if ($file->isFile()) {
                    $filePath = $file->getPathName();
                    $path = str_replace($addonDir, '', $filePath);
                    @unlink(root_path() . $path);
                    //还原冲突文件
                    if(file_exists(root_path() . $path.'.'.$name.'.bak'))
                    {
                        rename(root_path() . $path.'.'.$name.'.bak',root_path() . $path);
                    }
                }
            }
        }

        //删除模板文件
        if (is_dir($addonDir . 'templates'))
        {
            //匹配出所有的文件
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addonDir . 'templates', FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $file) {
                // 如果是文件
                if ($file->isFile()) {
                    $filePath = $file->getPathName();
                    $path = str_replace($addonDir, '', $filePath);
                    @unlink(public_path() . $path);
                    //还原冲突文件
                    if(file_exists(public_path() . $path.'.'.$name.'.bak'))
                    {
                        rename(public_path() . $path.'.'.$name.'.bak',public_path() . $path);
                    }
                }
            }
        }

        //删除路由
        if (file_exists(app()->getRootPath().'route'.DS.$name.'.php'))
        {
            unlink(app()->getRootPath().'route'.DS.$name.'.php');
        }

        //删除插件
        if($type=='delete')
        {
            FileHelper::delDir($addonDir);
        }
    }

    // 生成表单信息
    public function makeAddColumns($config)
    {
        if(!$config)
        {
            return false;
        }
        // 判断是否开启了分组
        if (!$this->checkConfigGroup($config)) {
            // 未开启分组
            return $this->makeAddColumnsArr($config);
        } else {
            $columns = [];
            // 开启分组
            foreach ($config as $k => $v) {
                $columns[$v['title']] = $this->makeAddColumnsArr($v['config'],$k);
            }
            return $columns;
        }
    }

    // 生成表单返回数组
    public function makeAddColumnsArr(array $config,$group='')
    {
        if(!$config)
        {
            return  false;
        }
        $columns = [];
        foreach ($config as $k => $field) {
            if(in_array($field['type'],['editor','textarea','code']))
            {
                $field['value'] = htmlspecialchars_decode($field['value']);
            }
            // 初始化
            if($group)
            {
                $field['field'] = $group.'_aws_'.$k;
            }else{
                $field['field'] = $k;
            }
            $field['name'] = $field['name'] ?? $field['title'];
            $field['tips'] = $field['tips'] ?? '';
            $field['required'] = $field['required'] ?? 0;
            $field['group'] = $field['group'] ?? '';
            if (!isset($field)) {
                $field = [
                    'default' => $field['value'] ?? '',
                    'extra_attr' => $field['extra_attr'] ?? '',
                    'extra_class' => $field['extra_class'] ?? '',
                    'placeholder' => $field['placeholder'] ?? '',
                ];
            }

            if ($field['type'] == 'text') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['value']??'',    // 默认值
                    $field['group'],               // 标签组，可以在文本框前后添加按钮或者文字
                    $field['extra_attr']??'', // 额外属性
                    $field['extra_class']??'',// 额外CSS
                    $field['placeholder']??'',// 占位符
                    $field['required']??0,            // 是否必填
                ];
            }
            elseif ($field['type'] == 'textarea' || $field['type'] == 'password') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',    // 默认值
                    $field['extra_attr']??'', // 额外属性 extra_attr
                    $field['extra_class'] ?? '',
                    $field['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'radio' || $field['type'] == 'checkbox') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['options'],             // 选项（数组）
                    $field['value']??'',    // 默认值
                    $field['extra_attr']??'', // 额外属性 extra_attr
                    $field['extra_class'] ?? '',                            // 额外CSS extra_class
                    $field['required'],            // 是否必填
                ];
            }
            elseif ($field['type'] == 'select' || $field['type'] == 'select2' ) {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['options'],                    // 选项（数组）
                    $field['value']??'',    // 默认值
                    $field['extra_attr']?? '', // 额外属性 extra_attr
                    $field['extra_class'] ?? '',
                    $field['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                    $field['url'] ?? '',
                    $field['multiple']?? 0
                ];
            }
            elseif ($field['type'] == 'number') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',           // 默认值
                    $field['min_length']??'',        // 最小值
                    $field['max_length']??'',        // 最大值
                    $field['step']??1,              // 步进值
                    $field['extra_attr']??'',        // 额外属性
                    $field['extra_class']??'',       // 额外CSS
                    $field['placeholder'] ?? '', // 占位符
                    $field['required']??false,                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'hidden') {
                $columns[] = [
                    $field['type'],                      // 类型
                    $field['field'],                     // 字段名称
                    $field['value']?? '',    // 默认值
                    $field['extra_attr'] ?? '', // 额外属性 extra_attr
                ];
            }
            elseif ($field['type'] == 'date' || $field['type'] == 'time' || $field['type'] == 'datetime') {
                // 使用每个字段设定的格式

                if(isset($field['format']))
                {
                    if ($field['type'] == 'time') {
                        $format = $field['format'] ?? 'H:i:s';
                        $field['format'] = str_replace("H", "HH", $format);
                        $field['format'] = str_replace("i", "mm", $format);
                    } else {
                        $format = $field['format'] ?? 'Y-m-d H:i:s';
                        $field['format'] = str_replace("Y", "yyyy", $format);
                        $field['format'] = str_replace("m", "mm", $format);
                        $field['format'] = str_replace("d", "dd", $format);
                        $field['format'] = str_replace("H", "hh", $format);
                        $field['format'] = str_replace("i", "ii", $format);
                    }
                    $field['format'] = str_replace("s", "ss", $format);
                    $field['value'] = $field['value'] > 0 ? date($format, $field['value']) : '';
                }
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['value']??'',    // 默认值
                    $field['format']??'',     // 日期格式
                    $field['extra_attr']??'', // 额外属性 extra_attr
                    $field['extra_class']??'', // 额外CSS extra_class
                    $field['placeholder']??'',// 占位符
                    $field['required']??false,            // 是否必填
                ];
            }
            elseif ($field['type'] == 'daterange') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',           // 默认值
                    $field['format'],            // 日期格式
                    $field['extra_attr'] ?? '',  // 额外属性
                    $field['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'tag') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',           // 默认值
                    $field['extra_attr'] ?? '',  // 额外属性
                    $field['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'image' || $field['type'] == 'images' || $field['type'] == 'file' || $field['type'] == 'files') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',    // 默认值
                    $field['size']??'',       // 限制大小（单位kb）
                    $field['ext']??'',        // 文件后缀
                    $field['extra_attr'] ?? '',  // 额外属性
                    $field['extra_class'] ?? '', // 额外CSS
                    $field['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'editor') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',           // 默认值
                    $field['height'] ?? 0,       // 高度
                    $field['extra_attr'] ?? '',  // 额外属性
                    $field['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            }
            elseif ($field['type'] == 'color') {
                $columns[] = [
                    $field['type'],                       // 类型
                    $field['field'],                      // 字段名称
                    $field['name'],                       // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['value']??'',           // 默认值
                    $field['extra_attr'] ?? '',  // 额外属性
                    $field['extra_class'] ?? '', // 额外CSS
                    $field['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
        }
        return $columns;
    }

    //更新插件
    public function upgrade(string $name,$online=false): array
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }
        $pluginsDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        $base = get_class_methods("\\app\\common\\controller\\Plugins");
        $object = $this->getInstance($name);
        if (false !== $object->upgrade()) {
            try {
                // 导入SQL
                $this->importSql($name,'upgrade.sql');

                $this->copyDir($name);

                // 读取出所有公共方法
                $methods = (array)get_class_methods("\\plugins\\" . $name . "\\Plugin" );
                // 跟插件基类方法做比对，得到差异结果
                $hooks = array_diff($methods, $base);
                //安装关联钩子
                if($hooks) {
                    foreach ($hooks as $hook) {
                        //更新插件钩子关联
                        if (!db('hook_plugins')->where(['hook' => $hook, 'plugins' => $name])->find()) {
                            db('hook_plugins')->insert([
                                'hook' => $hook,
                                'plugins' => $name,
                                'status' => 1,
                                'create_time' => time()
                            ]);
                        } else {
                            db('hook_plugins')->where([
                                'hook' => $hook,
                                'plugins' => $name,
                            ])->update([
                                'status' => 1,
                                'update_time' => time()
                            ]);
                        }
                        //更新钩子
                        if (!db('hook')->where(['name' => $hook])->find()) {
                            db('hook')->insert([
                                'name' => $hook,
                                'status' => 1,
                                'create_time' => time()
                            ]);
                        }
                    }
                }
                // 导入插件配置
                if (isset($info['config']) && !empty($info['config']))
                {
                    $old_config = [];
                    if(file_exists($this->pluginsPath.DS.$name.DS.'config.php'))
                    {
                        $old_config = include $this->pluginsPath.DS.$name.DS.'config.php';
                    }
                    //合并插件配置
                    $config=array_merge($info['config'],$old_config);

                    db('module')->where('name', $name)->update(['config'=>json_encode($config, JSON_UNESCAPED_UNICODE)]);
                }
            } catch (\Exception $e) {
                return [
                    'code' => 0,
                    'msg'  => '更新失败：' . $e->getMessage(),
                ];
            }
        }

        return [
            'code' => 1,
            'msg'  => '插件更新成功',
        ];
    }

    //检查备份冲突文件
    private function conflictBackup($name)
    {
        $addonDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        if (is_dir($addonDir . 'app'))
        {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addonDir. 'app', FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $filing) {
                // 如果是文件
                if ($filing->isFile()) {
                    $filePath = $filing->getPathName();
                    $path = str_replace($addonDir, '', $filePath);
                    $destPath = root_path() . $path;
                    if (is_file($destPath)) {
                        if (file_exists($destPath)) {
                            $name1 = explode('.',$path);
                            copy($destPath,root_path() .$name1[0].'.'.$name1[1].'.'.$name.'.bak');
                        }
                    }
                }
            }
        }
        if (is_dir($addonDir . 'templates'))
        {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addonDir. 'templates', FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $filing) {
                // 如果是文件
                if ($filing->isFile()) {
                    $filePath = $filing->getPathName();
                    $path = str_replace($addonDir, '', $filePath);
                    $destPath = public_path() . $path;
                    if (is_file($destPath)) {
                        if (file_exists($destPath)) {
                            $name1 = explode('.',$path);
                            copy($destPath,public_path() .$name1[0].'.'.$name1[1].'.'.$name.'.bak');
                        }
                    }
                }
            }
        }
    }

    //检查冲突
    private function checkConflict($name)
    {
        $addonDir = $this->pluginsPath . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR;
        $list = [];
        if (is_dir($addonDir . 'app'))
        {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addonDir. 'app', FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $filing) {
                // 如果是文件
                if ($filing->isFile()) {
                    $filePath = $filing->getPathName();
                    $path = str_replace($addonDir, '', $filePath);
                    $destPath = root_path() . $path;
                    if (is_file($destPath) && file_exists($destPath)) {
                        $list['system'][] = $path;
                    }
                }
            }
        }

        if (is_dir($addonDir . 'templates'))
        {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($addonDir. 'templates', FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($files as $filing) {
                // 如果是文件
                if ($filing->isFile()) {
                    $filePath = $filing->getPathName();
                    $path = str_replace($addonDir, '', $filePath);
                    $destPath = public_path() . $path;
                    if (is_file($destPath) && file_exists($destPath)) {
                        $list['template'][] = $path;
                    }
                }
            }
        }
        return $list;
    }

    //检查是否有升级
    private function checkUpgrade(string $name,$online=false)
    {

    }

    //设置错误信息
    public function setError($error)
    {
        $this->error = $error;
    }

    //获取错误信息
    public function getError()
    {
        return $this->error;
    }
}