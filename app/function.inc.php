<?php
// 应用公共文件
use app\common\library\helper\ImageHelper;
use app\common\library\helper\StringHelper;
use app\model\Notify;
use app\common\library\helper\PluginsHelper;
use app\model\Config;
use app\model\Users;
use Overtrue\Pinyin\Pinyin;
use think\Container;
use think\facade\Event;
use think\facade\Route;
use think\helper\Str;

// 插件类库自动载入
spl_autoload_register(function ($class) {
    $class = ltrim($class, '\\');
    $dir = app()->getRootPath();
    $namespace = 'plugins';

    if (strpos($class, $namespace) === 0) {
        $class = substr($class, strlen($namespace));
        $path = '';
        if (($pos = strripos($class, '\\')) !== false) {
            $path = str_replace('\\', '/', substr($class, 0, $pos)) . '/';
            $class = substr($class, $pos + 1);
        }
        $path .= str_replace('_', '/', $class) . '.php';
        $dir .= $namespace . $path;

        if (file_exists($dir)) {
            include $dir;
            return true;
        }
        return false;
    }
    return false;
});

/**
 * 数字转换为字符串单位
 * @param $num
 * @return string
 */
if (! function_exists('num2string')) {
    function num2string($num): string
    {
        if ($num >= 10000) {
            $num = round($num / 10000 * 100) / 100 . 'W+';
        } elseif ($num >= 1000) {
            $num = round($num / 1000 * 100) / 100 . 'K';
        }
        return $num;
    }
}

//后台使用前台地址替换
if(!function_exists('get_url'))
{
    function get_url(string $url = '', array $vars = [], $suffix = true, $domain = false)
    {
        $url = (string)Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
        $admin_url = config('app.admin');
        return str_replace($admin_url.'/','',$url);
    }
}

//前台使用后台地址替换
if(!function_exists('backend_url'))
{
    function backend_url(string $url = '', array $vars = [], $suffix = true, $domain = true): string
    {
        $url = (string)Route::buildUrl($url, $vars)->suffix($suffix)->domain($domain);
        $admin_url = config('app.admin');
        return strstr($url,$admin_url.'/') ? $url : '/'.$admin_url.'/'.$url;
    }
}

/**
 * @param $q
 * @param string $string 要分割的字符串
 * @param int $length 指定的长度
 * @param string $class
 * @param String $end 在分割后的字符串块追加的内容
 * @param bool $once
 * @return string
 */
if(!function_exists('mb_chunk_split'))
{
    function mb_chunk_split($q, string $string, int $length,string $class='text-danger', string $end='\r\n', bool $once = false): string
    {
        $array = array();
        $str_len = mb_strlen($string);
        $b=0;
        while($str_len){
            $array[] = mb_substr($string, 0, $length, "utf-8");
            if($once)
                return $array[0] . $end;
            $string = mb_substr($string, $length, $str_len, "utf-8");
            $str_len = mb_strlen($string);
        }
        $q = is_array($q) ? end($q) : $q;
        foreach ($array as $key => $value) {
            if(stripos($value,$q)){
                $b=$key;
                break;
            }
        }
        $data=array_slice($array,$b,3);
        $result = count($data)>1 ? implode(" ", $data).'...':implode(" ", $data);
        return str_ireplace($q,'<b class="'.$class.'">'.$q.'</b>',$result);
    }
}

/**
 * 数据库实例
 * @param string $name 类名或标识 默认获取当前应用实例
 * @param bool $newInstance 是否每次创建新的实例
 * @return mixed
 */
if (! function_exists('db')) {
    function db(string $name='', bool $newInstance = false)
    {
        if($name)
        {
            return Container::getInstance()->make('db', [], $newInstance)->name($name);
        }
        return Container::getInstance()->make('db', [], $newInstance);
    }
}

//实例化小部件函数
if (! function_exists('widget')) {
    /**
     * 小部件函数
     * @param mixed $name 调用小部件控制器
     * @param array $param 小部件请求参数
     * @param string $layer 小部件模型层
     * @return mixed
     */
    function widget($name, array $param = [],string $layer='widget')
    {
        $name  = str_replace('/', '\\', $name);
        $module_exp = explode('@',$name);
        $array = explode('\\', end($module_exp));
        if(count($module_exp)==2)
        {
            $model = $module_exp[0];
            $action = $array[1];
            $class = app()->make('\\app\\'.$model .'\\'.$layer.'\\' . ucfirst($array[0]));
        }else {
            $action = $array[1];
            $class = app()->make(app()->getNamespace() .'\\'.$layer.'\\' . ucfirst($array[0]));
        }
        $call = [$class, $action];
        return app()->invoke($call, $param);
    }
}

/**
 * 字符串截取
 * @param $string
 * @param $start
 * @param $length
 * @param string $charset
 * @param string $dot
 * @return string
 */
if (! function_exists('str_cut')) {
    function str_cut($string, $start, $length, $charset = 'UTF-8', $dot = '...'): string
    {
        if (mb_strlen($string, $charset) <= $length) {
            return $string;
        }

        if (function_exists('mb_substr')) {
            return mb_substr($string, $start, $length, $charset) . $dot;
        }

        return iconv_substr($string, $start, $length, $charset) . $dot;
    }
}

/**
 * 获取系统配置
 * @param string $name
 * @param null $default
 * @return mixed
 */
if (! function_exists('get_setting')) {
    function get_setting(string $name = '', $default = null)
    {
        return Config::getConfigs($name, $default);
    }
}

/**
 * 获取字典数据
 */
if (! function_exists('get_dict')) {
    function get_dict(string $name = ''): array
    {
        return \app\model\DictType::getDictData($name);
    }
}

/**
 * 获取模板配置
 * @param string $name
 * @param null $default
 * @return mixed
 */
if (! function_exists('get_theme_setting')) {
    function get_theme_setting(string $name = '',$default='')
    {
        $config = \app\common\library\helper\TemplateHelper::instance()->getTemplatesConfigs();

        if(!$name)
        {
            return $config;
        }

        $name    = explode('.', $name);
        $name[0] = strtolower($name[0]);

        // 按.拆分成多维数组进行判断
        foreach ($name as $val) {
            if (isset($config[$val])) {
                $config = $config[$val];
            } else {
                return $default;
            }
        }

        return $config;
    }
}


/**
 * 加密解密
 */
if (! function_exists('authCode')) {
    function authCode($string, $operation = 'DECODE', $key = '', $expiry = 0)
    {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $c_key_length = 4;
        // 密匙
        $key = md5($key ? $key : 'wecenter');
        // 密匙a会参与加解密
        $key_a = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $key_b = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $key_c = $operation == 'DECODE' ? substr($string, 0, $c_key_length) : substr(md5(microtime()), -$c_key_length);
        // 参与运算的密匙
        $cryptKey = $key_a . md5($key_a . $key_c);
        $key_length = strlen($cryptKey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$key_b(密匙b)，
        //解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$c_key_length位开始，因为密文前$c_key_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $c_key_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $key_b), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $randKey = array();
        // 产生密匙簿
        for ($i = 0; $i <= 255; $i++) {
            $randKey[$i] = ord($cryptKey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $randKey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if ((substr($result, 0, 10) == 0 || intval(substr($result, 0, 10)) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $key_b), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $key_c . str_replace('=', '', base64_encode($result));
        }
    }
}

/**
 * 格式化Bytes字符
 */
if(!function_exists('formatBytes'))
{
    function formatBytes($size, $delimiter = ''): string
    {
        $size = $size*1024;
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}

/**
 * 格式化时间格式
 */
if(!function_exists('date_friendly'))
{
    function date_friendly($timestamp, $time_limit = 604800, $out_format = 'Y-m-d H:i', $formats = null, $time_now = null)
    {
        if (!$timestamp)
        {
            return false;
        }

        if(get_setting('date_friendly_enable')=='normal')
        {
            return date($out_format,$timestamp);
        }

        if ($formats == null)
        {
            $formats = array(
                'YEAR' => L('%s 年前'),
                'MONTH' => L('%s 月前'),
                'DAY' => L('%s 天前'),
                'HOUR' => L('%s 小时前'),
                'MINUTE' => L('%s 分钟前'),
                'SECOND' => L('%s 秒前'),
                'YEARS' => L('%ss 年前'),
                'MONTHS' => L('%ss 月前'),
                'DAYS' => L('%ss 天前'),
                'HOURS' => L('%ss 小时前'),
                'MINUTES' => L('%ss 分钟前'),
                'SECONDS' => L('%ss 秒前')
            );
        }
        $time_now = $time_now == null ? time() : $time_now;
        $seconds = $time_now - $timestamp;

        if ($seconds == 0)
        {
            $seconds = 1;
        }

        if (!$time_limit OR $seconds > $time_limit)
        {
            return date($out_format, $timestamp);
        }
        $minutes = floor($seconds / 60);
        $hours = floor($minutes / 60);
        $days = floor($hours / 24);
        $months = floor($days / 30);
        $years = floor($months / 12);
        $flag = '';
        if ($years > 0)
        {
            $diffFormat = 'YEAR';
            if($years > 1){
                $flag = 's';
            }
        }
        else
        {
            if ($months > 0)
            {
                $diffFormat = 'MONTH';
                if($months > 1){
                    $flag = 's';
                }
            }
            else
            {
                if ($days > 0)
                {
                    $diffFormat = 'DAY';
                    if($days > 1){
                        $flag = 's';
                    }
                }
                else
                {
                    if ($hours > 0)
                    {
                        $diffFormat = 'HOUR';
                        if($hours > 1){
                            $flag = 's';
                        }
                    }
                    else
                    {
                        if($minutes > 0){
                            $diffFormat = 'MINUTE';
                            if($minutes > 1){
                                $flag = 's';
                            }
                        }else{
                            $diffFormat = 'SECOND';
                            if($seconds > 1){
                                $flag = 's';
                            }
                        }
                    }
                }
            }
        }

        $dateDiff = null;
        switch ($diffFormat)
        {
            case 'YEAR' :
                $dateDiff = sprintf($formats[$diffFormat], $years, $flag);
                break;
            case 'MONTH' :
                $dateDiff = sprintf($formats[$diffFormat], $months, $flag);
                break;
            case 'DAY' :
                $dateDiff = sprintf($formats[$diffFormat], $days, $flag);
                break;
            case 'HOUR' :
                $dateDiff = sprintf($formats[$diffFormat], $hours, $flag);
                break;
            case 'MINUTE' :
                $dateDiff = sprintf($formats[$diffFormat], $minutes, $flag);
                break;
            case 'SECOND' :
                $dateDiff = sprintf($formats[$diffFormat], $seconds, $flag);
                break;
        }
        return $dateDiff;
    }
}

/**
 * 时间戳格式化时间
 * @param $time
 * @param string $format
 * @return false|string
 */
if(!function_exists('formatTime'))
{
    function formatTime($time, $format = 'Y-m-d H:i:s')
    {
        return date($format, intval($time));
    }
}

/**
 * 发送通知，系统内置通知，只发送用户允许接收的类型
 * @param int $sender_uid 发送用户id
 * @param int $recipient_uid 接收用户id
 * @param int $action_type 通知类型
 * @param string $item_type 通知内容类型
 * @param int $item_id 通知内容id
 * @param array $content 通知详细数据
 * @return bool
 */
if(!function_exists('send_notify'))
{
    function send_notify($sender_uid=0, $recipient_uid=0, $action_type='', $item_type='', $item_id = 0, $content = array(),$anonymous=0): bool
    {
        return \app\common\library\helper\NotifyHelper::sendNotify(intval($sender_uid),intval($recipient_uid),$action_type,intval($item_id),$item_type,$content);
    }
}

/**
 * 发送系统自定义通知
 * @param int $sender_uid 发送人
 * @param int $recipient_uid 接受用户
 * @param string $subject 通知标题
 * @param string $content 通知内容
 * @param int $anonymous 是否匿名发送
 * @return false|mixed
 */
if(!function_exists('send_site_diy_notify')){
    function send_site_diy_notify($sender_uid = 0, $recipient_uid = 0,$subject = '',$content='',$anonymous=0)
    {
        return Notify::sendNotify($sender_uid, $recipient_uid,$subject,$content,$anonymous);
    }
}

/**
 * 发送自定义邮件
 * @param mixed $email 邮件地址
 * @param $subject 邮件主题
 * @param $message 邮件内容
 * @return array
 */
if(!function_exists('send_email')) {
    function send_email( $email, $subject = '', $message = '')
    {
        return \app\common\library\helper\MailHelper::sendEmail($email, $subject, $message);
    }
}

// 用户相关
/**
 * 获取用户链接地址
 * @param $uid
 * @param array $param
 * @return string
 */
if(!function_exists('get_user_url'))
{
    function get_user_url($uid, array $param=[],$domain=false): string
    {
        static $userInfo;
        if(!isset($userInfo[$uid]))
        {
            $userInfo[$uid] = db('users')->where('uid',$uid)->field('user_name,url_token')->find();
        }

        if(!$userInfo[$uid])
        {
            return (string)url('people/index',$param);
        }

        if($userInfo[$uid]['url_token'])
        {
            $user_name = $userInfo[$uid]['url_token'];
        }else{
            $pinyin = new Pinyin();
            $user_name = $pinyin->permalink($userInfo[$uid]['user_name'],'');
        }
        $param['name'] = $user_name;
        return (string)get_url('people/index',$param,'',$domain);
    }
}

if(!function_exists('get_username')){
    function get_username($uid = 0): string
    {
        static $list;
        if (!($uid && is_numeric($uid))) {
            //获取当前登录用户名
            return session('login_user_info.nick_name');
        }

        /* 获取缓存数据 */
        if (empty($list)) {
            $list = cache('sys_user_username_list');
        }

        /* 查找用户信息 */
        $key = "u{$uid}";
        if (isset($list[$key])) {
            //已缓存，直接使用
            $name = $list[$key];
        } else {
            //调用接口获取用户信息
            $info = db('users')->field('nick_name')->find($uid);

            if ($info !== false && $info['nick_name']) {
                $nickname = $info['nick_name'];
                $name     = $list[$key]     = $nickname;
                /* 缓存用户 */
                $count = count($list);
                $max   = get_setting('user_max_cache');
                while ($count-- > $max) {
                    array_shift($list);
                }
                cache('sys_user_username_list', $list);
            } else {
                $name = '';
            }
        }
        return $name;
    }
}

if(!function_exists('get_link_username')){
    function get_link_username($uid=0)
    {
        if (!($uid && is_numeric($uid))) {
            //获取当前登录用户名
            return session('login_user_info.nick_name');
        }

        /* 获取缓存数据 */
        $linkUsernameList = cache('sys_user_username_list');

        /* 查找用户信息 */
        $key = "u{$uid}";
        if (isset($linkUsernameList[$key])) {
            //已缓存，直接使用
            $name = $linkUsernameList[$key];
        } else {
            //调用接口获取用户信息
            $info = db('users')->field('nick_name')->find($uid);
            if ($info && $info['nick_name']) {
                $nickname = $info['nick_name'];
                $name     = $linkUsernameList[$key]     = $nickname;
                cache('sys_user_username_list', $linkUsernameList);
            } else {
                $name = '';
            }
        }
        return '<a href="'.get_user_url($uid).'" class="aw-username" data-id="'.$uid.'" target="_blank">'.$name.'</a>';
    }
}

if(!function_exists('get_user_info')) {
    function get_user_info($uid = 0, $field = '')
    {
        if (!$uid && !$uid = getLoginUid()) {
            return false;
        }

        $user_info = cache('user_info_' . $uid);
        if (!$user_info) {
            $user_info = Users::getUserInfo($uid);
            cache('user_info_' . $uid, $user_info,300);
        }
        return $field ? $user_info[$field] : $user_info;
    }
}

// 过滤危险标签：防SS攻击
if (!function_exists('remove_xss')) {
    function remove_xss($string): string
    {
        if(get_setting('remove_xss')!='Y')
        {
            return $string;
        }

        // 生成配置对象
        $config = HTMLPurifier_Config::createDefault();
        $config->set('AutoFormat.RemoveEmpty', true);
        $config->set('HTML.Trusted',true);
        $config->set('HTML.SafeEmbed',true);
        $config->set('HTML.SafeObject',true);
        $config->set('Output.FlashCompat',true);
        // 以下就是配置：
        $config->set('Core.Encoding', 'UTF-8');
        // 设置允许使用的HTML标签
        $config->set('HTML.Allowed', 'div,b,strong,i,em,a[href|title],ul,ol,li,br,p[style],span[style],img[width|height|alt|src]');
        // 设置允许出现的CSS样式属性
        $config->set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
        // 设置a标签上是否允许使用target="_blank"
        $config->set('HTML.TargetBlank', TRUE);
        // 使用配置生成过滤用的对象
        $obj = new HTMLPurifier($config);
        // 过滤字符串
        return $obj->purify($string);
    }
}

if(!function_exists('isSuperAdmin'))
{
    /**
     * 是否是超级管理员
     * @return bool
     */
    function isSuperAdmin(): bool
    {
        if(!getLoginUid()) return false;
        $uid = getLoginUid();
        $userInfo = Users::getUserInfo($uid);
        return $userInfo['group_id']==1;
    }
}

if(!function_exists('isNormalAdmin'))
{
    /**
     * 是否是普通管理员
     * @return bool
     */
    function isNormalAdmin(): bool
    {
        if(!getLoginUid()) return false;
        $uid = getLoginUid();
        $userInfo = Users::getUserInfo($uid);
        return $userInfo['group_id']==2;
    }
}


if(!function_exists('get_user_permission'))
{
    /**
     * 获取用户权限
     * @param mixed $permission
     * @param int $uid
     * @return mixed
     */
    function get_user_permission($permission='',int $uid=0)
    {
        if(!$uid && !$uid = getLoginUid()) return false;
        $permission_list = Users::getUserGroupInfo($uid);
        return $permission ? ($permission_list['permission'][$permission] ?? false) : $permission_list['permission'];
    }
}
/**
 * 唯一日期编码
 * @param integer $size 编码长度
 * @param string $prefix 编码前缀
 * @return string
 */
if(!function_exists('uniqueDate')) {
    function uniqueDate(int $size = 16, string $prefix = ''): string
    {
        if ($size < 14) $size = 14;
        $code = $prefix . date('Ymd') . (date('H') + date('i')) . date('s');
        while (strlen($code) < $size) $code .= rand(0, 9);
        return $code;
    }
}

//插件相关
if (!function_exists('hook')) {
    /**
     * 处理插件钩子
     * @param string $event 钩子名称
     * @param mixed $params 传入参数
     * @param bool $once 是否只返回一个结果
     * @return string
     */
    function hook(string $event, $params = [],bool $once = false): string
    {
        $result = Event::trigger($event, $params, $once);
        return join('', $result);
    }
}

if (!function_exists('get_plugins_info')) {
    /**
     * 读取插件的基础信息
     * @param string $name 插件名
     * @return array
     */
    function get_plugins_info(string $name): array
    {
        $info = db('plugins')->where(['status'=>1,'name'=>$name])->find();
        return $info??[];
    }
}

if (!function_exists('get_plugins_instance')) {
    /**
     * 获取插件的单例
     * @param string $name 插件名
     * @return mixed|null
     */
    function get_plugins_instance(string $name)
    {
        static $_plugins = [];
        if (isset($_plugins[$name])) {
            return $_plugins[$name];
        }
        $class = get_plugins_class($name);
        if (class_exists($class)) {
            $_plugins[$name] = new $class(app());
            return $_plugins[$name];
        } else {
            return null;
        }
    }
}

if (!function_exists('get_plugins_class')) {
    /**
     * 获取插件类的类名
     * @param string $name 插件名
     * @param string $type 返回命名空间类型
     * @param string|null $class 当前类名
     * @return string
     */
    function get_plugins_class(string $name, string $type = 'hook', string $class = null): string
    {
        $name = trim($name);
        // 处理多级控制器情况
        if (!is_null($class) && strpos($class, '.')) {
            $class = explode('.', $class);
            $class[count($class) - 1] = Str::studly(end($class));
            $class = implode('\\', $class);
        } else {
            $class = Str::studly(is_null($class) ? $name : $class);
        }
        switch ($type) {
            case 'controller':
                $namespace = '\\plugins\\' . $name . '\\controller\\' . $class;
                break;
            default:
                $namespace = '\\plugins\\' . $name . '\\Plugin';
        }

        return class_exists($namespace) ? $namespace : '';
    }
}

if (!function_exists('get_plugins_route')) {
    /**
     * 获取插件路由规则
     * @param $plugin
     * @return array|mixed
     */
    function get_plugins_route($plugin)
    {
        $routes = [];
        if (file_exists(PLUGINS_PATH . $plugin . DIRECTORY_SEPARATOR . 'rewrite.php')) {
            $routes = include PLUGINS_PATH . $plugin . DIRECTORY_SEPARATOR . 'rewrite.php';
        }
        return $routes;
    }
}

if (!function_exists('plugins_url')) {
    /**
     * 插件显示内容里生成访问插件的url前台地址
     * @param string $url
     * @param array $param
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return string
     */
    function plugins_url(string $url = '', array $param = [], $suffix = true, $domain = false): string
    {
        $request = app('request');
        if (empty($url)) {
            // 生成 url 模板变量
            $plugins = $request->plugin;
            $controller = $request->controller();
            $controller = str_replace('/', '.', $controller);
            $action = $request->action();
        } else {
            $url_scheme = parse_url($url);
            //解决下划线问题
            if(strstr($url,'://')!==false)
            {
                if (isset($url_scheme['scheme'])) {
                    $plugins = strtolower($url_scheme['scheme']);
                    $controller = $url_scheme['host'];
                    $action = trim($url_scheme['path'], '/');
                }else{
                    $urlParse = explode('://', $url);
                    if($urlParse)
                    {
                        $plugins = $urlParse[0];
                        $controller_action = isset($urlParse[1]) ? explode('/',$urlParse[1]) : ['Index','index'];
                        $action = array_pop($controller_action);
                        $controller = array_pop($controller_action) ?: $request->controller();
                    }
                }
            }else {
                $route = explode('/', $url_scheme['path']);
                $plugins = $request->plugin;
                $action = array_pop($route);
                $controller = array_pop($route) ?: $request->controller();
            }

            /* 解析URL带的参数 */
            if (isset($url_scheme['query'])) {
                parse_str($url_scheme['query'], $query);
                $param = array_merge($query, $param);
            }

            /*$url = parse_url($url);

            if (isset($url['scheme'])) {
                $plugins = strtolower($url['scheme']);
                $controller = $url['host'];
                $action = trim($url['path'], '/');
            } else {
                $route = explode('/', $url['path']);
                $plugins = $request->plugin;
                $action = array_pop($route);
                $controller = array_pop($route) ?: $request->controller();
            }*/
            //$controller = Str::snake((string)$controller);
        }

        $route = get_plugins_route($plugins);
        $rewrite = $route ? ($route['rule'] ?? $route):[];
        $route_url = "@plugins/{$plugins}/{$controller}/{$action}";
        if ($rewrite) {
            foreach ($rewrite as $key => $val) {
                if($val=="{$plugins}/{$controller}/{$action}")
                {
                    $route_url = "{$key}";
                }
            }
        }else{
            if($controller=='index' && $action=='index')
            {
                $route_url = "@plugins/{$plugins}";
            }
        }
        $plugins_url =  (string)Route::buildUrl($route_url, $param)->suffix($suffix)->domain($domain);
        $admin_url = config('app.admin');
        return strstr($plugins_url,$admin_url.'/') ? str_replace($admin_url.'/','',$plugins_url) : $plugins_url;
    }
}

if (!function_exists('backend_plugins_url')) {
    /**
     * 插件显示内容里生成访问插件的url后台地址
     * @param string $url
     * @param array $param
     * @param bool|string $suffix 生成的URL后缀
     * @param bool|string $domain 域名
     * @return string
     */
    function backend_plugins_url(string $url = '', array $param = [], $suffix = true, $domain = false): string
    {
        $plugins_url = plugins_url($url,$param,$suffix,false);
        $admin_url = config('app.admin');
        return strstr($plugins_url,$admin_url.'/') ? $plugins_url : '/'.$admin_url.'/'.$plugins_url;
    }
}

/**
 * 获取插件配置
 * @param string $plugin_name 插件名称
 * @param string $config_name 配置名称
 * @return array|false
 */
if (! function_exists('get_plugins_config')) {
    function get_plugins_config($plugin_name = '', $config_name = '')
    {
        return PluginsHelper::instance()->getPluginsConfigs($plugin_name, $config_name,1);
    }
}

/**
 * 获取数据表全名
 * @param string $table 数据表名
 * @return string
 */
if (! function_exists('get_table')) {
    function get_table($table): string
    {
        return config('database.connections.mysql.prefix').$table;
    }
}

if (!function_exists('get_month_list')) {
    /**
     * 生成一段时间的月份列表
     */
    function get_month_list($timestamp1, $timestamp2, $year_format = 'Y', $month_format = 'm'): array
    {
        $years = date($year_format, $timestamp1);
        $months = date($month_format, $timestamp1);
        $days = date('d', $timestamp1);

        $yearn = date($year_format, $timestamp2);
        $month = date($month_format, $timestamp2);
        $day = date('d', $timestamp2);
        $monthInterval = 0;
        if ($years == $yearn)
        {
            $monthInterval = $month - $months;
        }
        else if ($years < $yearn)
        {
            $yearInterval = $yearn - $years -1;
            $monthInterval = (12 - $months + $month) + 12 * $yearInterval;
        }

        $timeData = array();
        for ($i = 0; $i <= $monthInterval; $i++)
        {
            $tmpTime = mktime(0, 0, 0, $months + $i, 1, $years);
            $timeData[$i]['year'] = date($year_format, $tmpTime);
            $timeData[$i]['month'] = date($month_format, $tmpTime);
            $timeData[$i]['beginday'] = '01';
            $timeData[$i]['endday'] = date('t', $tmpTime);
        }
        $timeData[0]['beginday'] = $days;
        $timeData[$monthInterval]['endday'] = $day;
        unset($tmpTime);
        return $timeData;
    }
}

if (!function_exists('db_field_exists')) {
    /**
     * 数据表字段是否存在
     * @param string $table 数据表名
     * @param string $field 字段名
     * @return bool
     */
    function db_field_exits(string $table,string $field): bool
    {
        return in_array($field, db($table)->getTableFields());
    }
}

if (!function_exists('cjk_strlen')) {
    function cjk_strlen($string, $charset = 'UTF-8')
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($string, $charset);
        } else {
            return iconv_strlen($string, $charset);
        }
    }
}

if (!function_exists('parseSql')) {
    function parseSql($sql, $to='aws_', $from='aws_')
    {
        [$pure_sql, $comment] = [[], false];
        $sql = explode("\n", trim(str_replace(["\r\n", "\r"], "\n", $sql)));
        foreach ($sql as $key => $line) {
            if ($line == '') {
                continue;
            }
            if (preg_match("/^(#|--)/", $line)) {
                continue;
            }
            if (preg_match("/^\/\*(.*?)\*\//", $line)) {
                continue;
            }
            if (substr($line, 0, 2) === '/*') {
                $comment = true;
                continue;
            }
            if (substr($line, -2) === '*/') {
                $comment = false;
                continue;
            }
            if ($comment) {
                continue;
            }
            if ($from != '') {
                $line = str_replace('`' . $from, '`' . $to, $line);
            }
            if ($line === 'BEGIN;' || $line === 'COMMIT;') {
                continue;
            }
            $pure_sql[] = $line;
        }
        $pure_sql = implode("\n", $pure_sql);
        return explode(";\n", $pure_sql);
    }
}

if (!function_exists('compile_password')) {
    function compile_password($password, $salt): string
    {
        return md5(md5($password) . $salt);
    }
}

//系统自定义序列化
if (!function_exists('wc_serialize')) {
    function wc_serialize( $obj ): string
    {
        return base64_encode(gzcompress(serialize($obj)));
    }
}

//系统自定义反序列化
if (!function_exists('wc_unserialize')) {
    function wc_unserialize($txt)
    {
        return unserialize(gzuncompress(base64_decode($txt)));
    }
}

if (!function_exists('sqlFilter')) {
    function sqlFilter(string $str): string
    {
        $str = addslashes($str);
        $str = str_replace("%", "\%", $str);
        $str = nl2br($str);
        $farr = array(
            "/<(\\/?)(script|i?frame|style|html|body|title|link|meta|object|\\?|\\%)([^>]*?)>/isU",
            "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
            "/select\b|insert\b|update\b|delete\b|drop\b|;|\"|\'|\/\*|\*|\.\.\/|\.\/|union|into|load_file|outfile|dump/is"
        );
        $str = preg_replace($farr, '', $str);
        return strip_tags($str);
    }
}

if (!function_exists('L')) {
    /**
     * 全局多语言函数
     */
    function L($str, $vars = [], $lang = '')
    {
        if (is_numeric($str) || empty($str)) {
            return $str;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return lang($str, $vars, $lang);
    }
}

if (!function_exists('copyDir')) {
    // 拷贝目录
    function copyDir($dir, $toDir)
    {
        if (!is_dir($toDir)) {
            mkdir($toDir); // 如果$toDir没有创建则创建目录
        }

        if ($handle = @opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file == '.' || $file == '..') {
                    continue; // 避免删除当前目录和上级目录
                }
                if (is_dir($dir . '/' . $file)) {
                    // 如果是目录就递归
                    copyDir($dir . '/' . $file,$toDir . '/' . $file);
                } else {
                    // 如果是文件直接复制过去
                    copy($dir . '/' . $file,$toDir . '/' . $file);
                }
            }
            closedir($handle);
        }
    }
}

if(!function_exists('getServerIp'))
{
    function getServerIp()
    {
        if (!empty($_SERVER['SERVER_ADDR'])) {
            $ip = $_SERVER['SERVER_ADDR'];
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        } else {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }
}

if(!function_exists('isWx')) {
    function isWx()
    {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (preg_match('/micromessenger/i', $user_agent)) {
            return true;
        }
        return false;
    }
}

/**
 * 去除空格和换行
 * @param $str
 * @return array|string|string[]|null
 */
if(!function_exists('removeEmpty')) {
    function removeEmpty($str): string
    {
        $str = str_replace(array("\r\n", "\r", "\n"), "", $str);
        $str = htmlspecialchars_decode($str);
        $str = str_replace('&nbsp;','',$str);
        $str = preg_replace('# #', '', $str);
        if($img = ImageHelper::src($str))
        {
            return strip_tags($img);
        }
        return strip_tags($str);
    }
}

if(!function_exists('array_key_sort_asc_callback')) {
    function array_key_sort_asc_callback($a, $b): int
    {
        if ($a['sort'] == $b['sort']) {
            return 0;
        }
        return ($a['sort'] < $b['sort']) ? -1 : 1;
    }
}

// 密码检查
if (!function_exists('wcCheckPassword')) {
    function wcCheckPassword($value)
    {
        // 验证密码长度
        $len = mb_strlen($value);
        if ($len < get_setting('password_min_length') || $len > get_setting('password_max_length')) {
            return '请输入'.get_setting('password_min_length').' - '.get_setting('password_max_length').' 位的密码';
        }

        $types = get_setting('password_type');
        if (empty($types)) return true;
        if (in_array('number', $types) && !preg_match("/[0-9]+/", $value)) return '密码需包含数字';

        if (in_array('special', $types) && !preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $value)) return '密码需包含特殊字符';

        if (in_array('letter', $types) && !preg_match("/[a-zA-Z]+/", $value)) return '密码需包含大小写字母';

        return true;
    }
}

//图片地址替换
if(!function_exists('replacePic'))
{
    function replacePic($pic='',$uploadUrl='')
    {
        $uploadUrl = $uploadUrl ?: request()->domain();
        if(!$pic) return $pic;

        if(!strstr($pic,'http') && !strstr($pic,'https'))
        {
            $pic = $uploadUrl.$pic;
        }
        return $pic;
    }
}

//参数过滤
if(!function_exists('filterParams')) {
    function filterParams($params)
    {
        if (is_array($params)) {
            foreach ($params as $k => &$v) {
                if (is_array($v)) {
                    StringHelper::filterParams($v);
                } else {
                    StringHelper::filterWords($v);
                }
            }
        } else {
            StringHelper::filterWords($params);
        }
        return $params;
    }
}

//执行sql文件
if(!function_exists('executeSql')) {
    function executeSql($lines = []): bool
    {
        if (!$lines) return false;
        $tempLine = '';
        foreach ($lines as $line) {
            if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                continue;
            }
            $tempLine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                // 不区分大小写替换前缀
                $tempLine = str_ireplace('aws_', \think\facade\Config::get('database.connections.mysql.prefix'), $tempLine);
                // 忽略数据库中已经存在的数据
                $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                try {
                    db()->execute($tempLine);
                } catch (Exception $e) {
                    return false;
                }
                $tempLine = '';
            }
        }

        return true;
    }
}

/**
 * 注册异步处理任务
 * @param string $title 任务名称
 * @param string $command 执行脚本
 * @param mixed $later 首次执行延时时间
 * @param array $data 任务附加数据
 * @param int $rscript 任务类型(0单例,1多例)
 * @param int $loops 循环等待时间
 **/
if(!function_exists('registerTask'))
{
    function registerTask(string $title, string $command='', $later = 0,array $data = [],int $rscript = 0,int $loops = 0)
    {
        try {
            \app\common\service\TaskService::instance()->register($title, $command, $later, $data, $rscript, $loops);
        } catch (Exception $e) {
            return false;
        }
    }
}

//获取用户UID
if(!function_exists('getLoginUid'))
{
    function getLoginUid()
    {
        $cookieKey = config('app.token')['key'].'_'.md5('login_uid');
        $cookieUid = cookie($cookieKey);
        return session('login_uid') ?:(get_setting('remember_login_enable')=='Y' && $cookieUid ? authCode($cookieUid):0);
    }
}

/**
 * 执行sql文件
 * @param string $sql_query 要执行的目录
 * @return array 成功与否
 */
function fetchSql(string $sql_query)
{
    db()->startTrans();
    try {
        foreach ($sql_query as $line) {
            if (strpos($line, '--') === 0 || $line == '' || strpos($line, '/*') === 0) {
                continue;
            }
            $tempLine .= $line;
            if (substr(trim($line), -1, 1) == ';') {
                // 不区分大小写替换前缀
                $tempLine = str_ireplace('aws_', $prefix, $tempLine);
                // 忽略数据库中已经存在的数据
                $tempLine = str_ireplace('INSERT INTO ', 'INSERT IGNORE INTO ', $tempLine);
                $sql_res = db()->execute($tempLine);
                $tempLine = '';
            }
        }
        db()->commit();
    }catch (\Exception $e)
    {
        db()->rollback();
        return  [
            'code'=>0,
            'msg'=>$e->getMessage(),
            'data'=>''
        ];
    }
    return  [
        'code'=>1,
        'msg'=>'',
        'data'=>''
    ];
}

