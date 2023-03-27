<?php

namespace app\common\library\token\driver;
use app\common\library\token\Driver;
use think\facade\Config;

/**
 * Token操作类
 */
class Redis extends Driver
{

    protected $options = [
        'host'        => '127.0.0.1',
        'port'        => 6379,
        'password'    => '',
        'select'      => 0,
        'timeout'     => 0,
        'expire'      => 0,
        'persistent'  => false,
        'user_prefix'  => 'up:',
        'token_prefix' => 'aws:',
    ];

    /**
     * 构造函数
     * @param array $options 缓存参数
     * @throws \BadFunctionCallException
     * @access public
     */
    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $this->handler = new \Redis;
        if ($this->options['persistent']) {
            $this->handler->pconnect($this->options['host'], $this->options['port'], $this->options['timeout'], 'persistent_id_' . $this->options['select']);
        } else {
            $this->handler->connect($this->options['host'], $this->options['port'], $this->options['timeout']);
        }

        if ('' != $this->options['password']) {
            $this->handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            $this->handler->select($this->options['select']);
        }
    }

    /**
     * 获取加密后的Token
     * @param string $token Token标识
     * @return string
     */
    protected function getEncryptedToken($token)
    {
        $config = Config::get('token');
        return $this->options['token_prefix'] . hash_hmac($config['hash_algo'], $token, $config['key']);
    }

    /**
     * 获取会员的key
     * @param $uid
     * @return string
     */
    protected function getUserKey($uid)
    {
        return $this->options['user_prefix'] . $uid;
    }

    /**
     * 存储Token
     * @param   string $token   Token
     * @param   int    $uid 会员ID
     * @param   int    $expire  过期时长,0表示无限,单位秒
     * @return bool
     */
    public function set($token, $uid, $expire = 0)
    {
        if (is_null($expire)) {
            $expire = $this->options['expire'];
        }
        if ($expire instanceof \DateTime) {
            $expire = $expire->getTimestamp() - time();
        }
        $key = $this->getEncryptedToken($token);
        if ($expire) {
            $result = $this->handler->setex($key, $expire, $uid);
        } else {
            $result = $this->handler->set($key, $uid);
        }
        //写入会员关联的token
        $this->handler->sAdd($this->getUserKey($uid), $key);
        return $result;
    }

    /**
     * 获取Token内的信息
     * @param   string $token
     * @return  array
     */
    public function get($token)
    {
        $key = $this->getEncryptedToken($token);
        $value = $this->handler->get($key);
        if (is_null($value) || false === $value) {
            return [];
        }
        //获取有效期
        $expire = $this->handler->ttl($key);
        $expire = $expire < 0 ? 365 * 86400 : $expire;
        $expire_time = time() + $expire;
        return ['token' => $token, 'uid' => $value, 'expire_time' => $expire_time, 'expires_in' => $expire];
    }

    /**
     * 判断Token是否可用
     * @param   string $token   Token
     * @param   int    $uid 会员ID
     * @return  boolean
     */
    public function check($token, $uid)
    {
        $data = self::get($token);
        return $data && $data['uid'] == $uid;
    }

    /**
     * 删除Token
     * @param   string $token
     * @return  boolean
     */
    public function delete($token)
    {
        $data = $this->get($token);
        if ($data) {
            $key = $this->getEncryptedToken($token);
            $uid = $data['uid'];
            $this->handler->del($key);
            $this->handler->sRem($this->getUserKey($uid), $key);
        }
        return true;

    }

    /**
     * 删除指定用户的所有Token
     * @param   int $uid
     * @return  boolean
     */
    public function clear($uid)
    {
        $keys = $this->handler->sMembers($this->getUserKey($uid));
        $this->handler->del($this->getUserKey($uid));
        $this->handler->del($keys);
        return true;
    }

}
