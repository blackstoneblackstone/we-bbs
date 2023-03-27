<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace app\common\library\token;

use think\facade\Config;

/**
 * Token基础类
 */
abstract class Driver
{
    protected $handler = null;
    protected $options = [];

    /**
     * 存储Token
     * @param   string $token   Token
     * @param   int    $uid 会员ID
     * @param   int    $expire  过期时长,0表示无限,单位秒
     * @return bool
     */
    abstract function set($token, $uid, $expire = 0);

    /**
     * 获取Token内的信息
     * @param   string $token
     * @return  array
     */
    abstract function get($token);

    /**
     * 判断Token是否可用
     * @param   string $token   Token
     * @param   int    $uid 会员ID
     * @return  boolean
     */
    abstract function check($token, $uid);

    /**
     * 删除Token
     * @param   string $token
     * @return  boolean
     */
    abstract function delete($token);

    /**
     * 删除指定用户的所有Token
     * @param   int $uid
     * @return  boolean
     */
    abstract function clear($uid);

    /**
     * 返回句柄对象，可执行其它高级方法
     *
     * @access public
     * @return object|null
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * 获取加密后的Token
     * @param string $token Token标识
     * @return string
     */
    protected function getEncryptedToken(string $token): string
    {
        $config = config('token');
        return hash_hmac($config['hash_algo'], $token, $config['key']);
    }

    /**
     * 获取过期剩余时长
     * @param $expire_time
     * @return float|int|mixed
     */
    protected function getExpiredIn($expire_time)
    {
        return $expire_time ? max(0, $expire_time - time()) : 365 * 86400;
    }
}
