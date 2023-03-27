<?php

namespace app\validate;

use think\Validate;

use app\model\Users;

class User extends Validate
{
    protected $rule = [
        'mobile' => 'require|checkMobile',
        'username'  => 'require|checkUsername',
        'password'   => 'require|checkPassword',
        're_password' => 'require|confirm:password',
        'code' => 'require|checkCode',
    ];

    protected $message = [
        'mobile.require' => '请输入手机号码',
        'code.require' => '请输入短信验证码',
        'username.require' => '用户名不能为空',
        'password.require'     => '密码不能为空',
        're_password' => '再次输入的密码不一致'
    ];

    // 验证场景
    protected $scene = [
        'register' => ['username', 'password'],
        'password' => ['password', 're_password'],
        'mobile_reset_password' => ['mobile', 'password', 're_password', 'code']
    ];

    // 手机号检查
    public function checkMobile($value, $rule, $data = [])
    {
        if (!preg_match('/^1[3-9]\d{9}$/', $value)) return '请输入正确的手机号码';
        if (!Users::checkUserExist($value)) return '未找到使用该手机号的用户';

        return true;
    }

    // 验证码检查
    public function checkCode($value, $rule, $data = [])
    {
        if ($value != cache('sms_'.$data['mobile'])) return '短信验证码不正确';
        return true;
    }

    // 用户名检查
    protected function checkUsername($value, $rule, $data = [])
    {
        // 验证用户名长度
        $len = mb_strlen($value);
        if ($len < get_setting('username_min_length') || $len > get_setting('username_max_length')) {
            return '请输入'.get_setting('username_min_length').' - '.get_setting('username_max_length').' 字数的用户名';
        }

        if (Users::checkUserExist($value)) return '用户名已存在';

        return true;
    }

    // 密码检查
    protected function checkPassword($value, $rule, $data = [])
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
