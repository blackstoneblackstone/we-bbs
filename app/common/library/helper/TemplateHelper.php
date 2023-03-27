<?php

namespace app\common\library\helper;
use think\facade\Cache;

/**
 * 模板管理
 * Class TemplateHelper
 * @package app\common\library\helper
 */
class TemplateHelper
{
    // 模板目录
    public $TemplatePath='';

    private static $instance;
    public $error;

    // 构造方法
    public function __construct()
    {
        $this->TemplatePath = public_path('templates');
    }

    public static function instance(): TemplateHelper
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // 获得本地模板列表
    public function getTemplatesList(): array
    {
        $map                = [];
        $templates_list = db('theme')->order('sort,id')
            ->select()->toArray();

        $list = [];
        $all_templates  = db('theme')->order('sort,id')->column('id,name', 'name');
        $templates = FileHelper::getList($this->TemplatePath);
        foreach ($templates as $name) {
            // 排除系统模板和已存在数据库的模板
            if (array_key_exists($name, $all_templates))
            {
                continue;
            }

            $templateDir = $this->TemplatePath . $name . DIRECTORY_SEPARATOR;
            if (!is_dir($templateDir)) {
                continue;
            }

            if (file_exists($templateDir.'info.php'))
            {
                // 获取模板基础信息
                $info = include $templateDir.'info.php';

                //模板和目录名不一致时跳过安装
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
                $id = db('theme')->insertGetId($sql);
                $sql['id'] = $id;
                $templates_list = array_merge($templates_list, [$sql]);
            }
        }

        foreach ($templates_list as $key=> $val)
        {
            // 增加右侧按钮组
            $str = '';
            if ($val['status'] == 1) {
                // 已安装，增加配置按钮
                if($val['config'])
                {
                    $str .= '<a class="btn btn-primary btn-sm" href="javascript:void(0)" onclick="AWS_ADMIN.operate.edit(\''.$val['name'].'\')">配置</a> ';
                    $str .= '<a class="btn btn-danger btn-sm aw-ajax-get" data-confirm="是否更新该模板配置？" data-url="'.url('upgrade',['name'=>$val['name'],'status'=>2]).'">更新配置</a> ';
                }
            }else {
                $str .= '<a class="btn btn-success btn-sm aw-ajax-get" data-url="'.url('install',['name'=>$val['name']]).'">启用</a> ';
            }
            $val['button'] = $str;
            $list[$key] = $val;
        }
        return $list;
    }

    // 获取模板信息
    public function config(string $name)
    {
        return $this->getConfig($name,true);
    }

    // 安装模板
    public function install(string $name): array
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }

        $templatesDir = $this->TemplatePath. $name . DIRECTORY_SEPARATOR;
        try {
            $info = include $templatesDir.'info.php';
            $sql                = [];
            $sql['title']        = $info['title'];
            $sql['name']  = $info['name']?:'';
            $sql['description']   = $info['description'];
            $sql['author']      = $info['author'];
            $sql['version']     = $info['version'];
            $sql['author_url']         = $info['author_url']??'';
            // 导入模板配置
            if ((isset($info['config']) && !empty($info['config'])))
            {
                $sql['config'] = $info['config'];
                if(file_exists($this->TemplatePath.$name.DS.'config.php'))
                {
                    $old_config = include $this->TemplatePath.$name.DS.'config.php';
                    //合并模板配置
                    $sql['config']=array_merge($old_config,$info['config']);
                }
                $sql['config'] = json_encode($sql['config'], JSON_UNESCAPED_UNICODE);
            }

            db('theme')->where('status', 1)->update(['status'=>0]);
            //更新模板状态
            $sql['status']      = 1;
            db('theme')->where('name', $name)->update($sql);
            $this->getDefaultTheme($name);
            return [
                'code' => 1,
                'msg'  => '模板安装成功',
            ];
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '安装失败：' . $e->getMessage(),
            ];
        }
    }

    // 卸载模板
    public function uninstall(string $name): array
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }
        try {
            //更新模板状态
            if(!db('theme')->where([['status','=', 1],['name','<>',$name]])->find())
            {
                return [
                    'code' => 0,
                    'msg'  => '请至少保留一个默认模板',
                ];
            }
            db('theme')->where('name', $name)->update(['status'=>0]);
            return [
                'code' => 1,
                'msg'  => '模板卸载成功',
            ];
        }catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '模板卸载失败：' . $e->getMessage(),
            ];
        }
    }

    //更新模板配置
    public function upgrade(string $name)
    {
        if(!$check = $this->check($name))
        {
            return $check;
        }

        $templatesDir = $this->TemplatePath. $name . DIRECTORY_SEPARATOR;
        try {
            $info = include $templatesDir.'info.php';
            // 导入模板配置
            if ((isset($info['config']) && !empty($info['config'])))
            {
                $sql['config'] = $info['config'];
                if(file_exists($this->TemplatePath.$name.DS.'config.php'))
                {
                    $old_config = include $this->TemplatePath.$name.DS.'config.php';
                    //合并模板配置
                    $sql['config']=array_merge($old_config,$info['config']);
                }
                $sql['config'] = json_encode($sql['config'], JSON_UNESCAPED_UNICODE);
            }
            db('theme')->where('name', $name)->update($sql);
            $this->getDefaultTheme($name);
            return [
                'code' => 1,
                'msg'  => '模板配置更新成功',
            ];
        } catch (\Exception $e) {
            return [
                'code' => 0,
                'msg'  => '模板配置更新失败：' . $e->getMessage(),
            ];
        }
    }

    // 判断模板配置文件是否进行了分组
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

    public function getDefaultTheme($theme=''): string
    {
        if($theme)
        {
            cache('default_theme',$theme);
        }
        if($defaultTheme = cache('default_theme'))
        {
            return $defaultTheme;
        }

        $defaultTheme = db('theme')->where(['status'=>1])->value('name');
        cache('default_theme',$defaultTheme);
        return $defaultTheme?:'default';
    }

    // 验证模板是否完整
    private function check(string $name)
    {
        if (!is_dir($this->TemplatePath. $name)) {
            return [
                'code' => 0,
                'msg'  => '未发现该模板,请先下载并放入到templates目录中',
            ];
        }

        $db_info = db('theme')->where('name', $name)->find();

        if (!$db_info) {
            return [
                'code' => 0,
                'msg'  => '模板不存在',
            ];
        }

        if (!$db_info['status']) {
            return [
                'code' => 0,
                'msg'  => '模板未安装',
            ];
        }

        $templatesDir = $this->TemplatePath. $name . DIRECTORY_SEPARATOR;

        if (!file_exists($templatesDir.'info.php')) {
            return [
                'code' => 0,
                'msg'  => '模板配置文件不存在[info.php]',
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
        $result = Cache::get('templates_'.$name.'_config');
        if (!$result || $update) {
            $config = db('theme')->where(['status'=>1,'name'=>$name])->value('config');
            if(!$config)
            {
                return false;
            }
            $result = json_decode($config,true);
            Cache::set('templates_'.$name.'_config', $result);
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
    public function getTemplatesConfigs(string $name='', string $configName = '', int $update=0)
    {
        $name = $name ?:$this->getDefaultTheme();
        $config = cache('cache_templates_config_'.$name);

        if ($config && !$update) {
            return $configName ? $config[$configName] : $config;
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

        cache( 'cache_templates_config_'.$name,$newConfig);
        return $configName ? $newConfig[$configName] : $newConfig;
    }

    /**
     * 更新插件的配置文件
     * @param string $name 插件名
     * @param array $array
     * @return mixed
     */
    public function setTemplatesConfig(string $name, array $array): array
    {
        $file = $this->TemplatePath . $name . DIRECTORY_SEPARATOR . 'config.php';
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