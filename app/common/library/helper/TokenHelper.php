<?php

namespace app\common\library\helper;

use app\common\library\token\Driver;
use think\facade\App;
use think\facade\Config;
use think\facade\Log;

/**
 * Token操作类
 */
class TokenHelper
{
    /**
     * @var array Token的实例
     */
    public static $instance = [];

    /**
     * @var object 操作句柄
     */
    public static $handler;

    /**
     * 连接Token驱动
     * @access public
     * @param  array       $options 配置数组
     * @param  bool|string $name    Token连接标识 true 强制重新连接
     * @return Driver
     */
    public static function connect(array $options = [], $name = false): Driver
    {
        $type = !empty($options['type']) ? $options['type'] : 'File';
        if (false === $name) {
            $name = md5(serialize($options));
        }
        if (true === $name || !isset(self::$instance[$name])) {
            $class = false === strpos($type, '\\') ?
                '\\app\\common\\library\\token\\driver\\' . ucwords($type) :
                $type;
            if (true === $name) {
                return new $class($options);
            }
            self::$instance[$name] = new $class($options);
        }

        return self::$instance[$name];
    }

    /**
     * 自动初始化Token
     * @access public
     * @param  array $options 配置数组
     * @return Driver
     */
    public static function init(array $options = [])
    {
        if (is_null(self::$handler)) {
            if (empty($options) && 'complex' == config('token.type')) {
                $default = config('token.default');
                // 获取默认Token配置，并连接
                $options = config('token.' . $default['type']) ?: $default;
            } elseif (empty($options)) {
                $options = config('token');
            }

            self::$handler = self::connect($options);
        }

        return self::$handler;
    }

    /**
     * 判断Token是否可用(check别名)
     * @access public
     * @param string $token Token标识
     * @param $user_id
     * @return bool
     */
    public static function has(string $token, $user_id): bool
    {
        return self::check($token, $user_id);
    }

    /**
     * 判断Token是否可用
     * @param string $token Token标识
     * @param $user_id
     * @return bool
     */
    public static function check(string $token, $user_id): bool
    {
        return self::init()->check($token, $user_id);
    }

    /**
     * 读取Token
     * @access public
     * @param  string $token   Token标识
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public static function get(string $token, $default = false)
    {
        return self::init()->get($token) ?: $default;
    }

    /**
     * 写入Token
     * @param  string   $token   Token标识
     * @param  mixed    $user_id 存储数据
     * @param  int|null $expire  有效时间 0为永久
     * @return bool
     */
    public static function set(string $token, $user_id,int $expire = null)
    {
        return self::init()->set($token, $user_id, $expire);
    }

    /**
     * 删除Token(delete别名)
     * @param  string $token Token标识
     * @return bool
     */
    public static function rm(string $token)
    {
        return self::delete($token);
    }

    /**
     * 删除Token
     * @param string $token 标签名
     * @return bool
     */
    public static function delete(string $token)
    {
        return self::init()->delete($token);
    }

    /**
     * 清除Token
     * @access public
     * @param int|null $user_id
     * @return boolean
     */
    public static function clear(int $user_id = null)
    {
        return self::init()->clear($user_id);
    }
}
