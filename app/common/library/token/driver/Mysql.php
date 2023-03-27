<?php

namespace app\common\library\token\driver;
use app\common\library\token\Driver;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use think\facade\Db;

/**
 * Token操作类
 */
class Mysql extends Driver
{

    /**
     * 默认配置
     * @var array
     */
    protected $options = [
        'table'      => 'users_token',
        'expire'     => 2592000,
        'connection' => [],
    ];


    /**
     * 构造函数
     * @param array $options 参数
     * @throws DbException
     * @access public
     */
    public function __construct($options = [])
    {
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        if ($this->options['connection']) {
            $this->handler = Db::connect($this->options['connection'])->name($this->options['table']);
        } else {
            $this->handler = db($this->options['table']);
        }
        $time = time();
        $token_time = cache('token_time');
        if (!$token_time || $token_time < $time - 86400) {
            cache('token_time', $time);
            $this->handler->where('expire_time', '<', $time)->where('expire_time', '>', 0)->delete();
        }
    }

    /**
     * 存储Token
     * @param string $token   Token
     * @param int    $user_id 会员ID
     * @param int    $expire  过期时长,0表示无限,单位秒
     * @return bool
     */
    public function set($token, $user_id, $expire = null)
    {
        $expire_time = !is_null($expire) && $expire !== 0 ? time() + $expire : 0;
        $token = $this->getEncryptedToken($token);
        $this->handler->insert(['token' => $token, 'uid' => $user_id, 'create_time' => time(), 'expire_time' => $expire_time]);
        return true;
    }

    /**
     * 获取Token内的信息
     * @param string $token
     * @return  array
     * @throws DbException
     * @throws DataNotFoundException
     * @throws ModelNotFoundException
     */
    public function get($token)
    {
        $data = $this->handler->where('token', $this->getEncryptedToken($token))->find();
        if ($data) {
            if (!$data['expire_time'] || $data['expire_time'] > time()) {
                //返回未加密的token给客户端使用
                $data['token'] = $token;
                //返回剩余有效时间
                $data['expires_in'] = $this->getExpiredIn($data['expire_time']);
                return $data;
            } else {
                self::delete($token);
            }
        }
        return [];
    }

    /**
     * 判断Token是否可用
     * @param string $token Token
     * @param int $user_id 会员ID
     * @return  boolean
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    public function check($token, $user_id)
    {
        $data = $this->get($token);
        return $data && $data['uid'] == $user_id;
    }

    /**
     * 删除Token
     * @param string $token
     * @return  boolean
     * @throws DbException
     */
    public function delete($token)
    {
        $this->handler->where('token', $this->getEncryptedToken($token))->delete();
        return true;
    }

    /**
     * 删除指定用户的所有Token
     * @param int $user_id
     * @return  boolean
     * @throws DbException
     */
    public function clear($user_id)
    {
        $this->handler->where('uid', $user_id)->delete();
        return true;
    }
}
