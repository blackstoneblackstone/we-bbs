<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------

namespace app\model;

use app\common\library\helper\IpHelper;
use app\common\library\helper\MailHelper;
use app\common\library\helper\RandomHelper;

/**
 * 用户激活验证
 * Class UsersActive
 * @package app\common\model
 */
class UsersActive extends BaseModel
{
    /**
     * 新建激活码
     * @param $uid
     * @param $expire_time
     * @param $active_code
     * @param null $active_type_code
     * @return mixed
     */
    public static function createActiveCode($uid, $expire_time, $active_code, $active_type_code = null)
    {
        if ($active_id = db('users_active')->insertGetId(array(
            'uid' => intval($uid),
            'expire_time' => intval($expire_time),
            'active_code' => $active_code,
            'active_type' => $active_type_code,
            'create_time' => time(),
            'create_valid_ip' => IpHelper::getRealIp()
        )))
        {
            db('users_active')->where([['uid','=',intval($uid)],['active_type','=',$active_type_code],['id','<>',intval($active_id)]])->delete();
        }

        return $active_id;

    }

    /**
     * 发送验证邮件
     * @param $uid
     * @param null $email
     * @return mixed
     */
    public static function newValidEmail($uid, $email = null)
    {
        if (!$uid) return false;
        $active_code_hash = RandomHelper::alnum(20);
        cache('valid_email_code_'.$uid,$active_code_hash,['expire'=>time() + 60 * 60 * 24]);
        self::createActiveCode($uid, (time() + 60 * 60 * 24), $active_code_hash, 'VALID_EMAIL');
        $url = (string)url('account/valid_email_verify',['active_code_hash'=>$active_code_hash],true,true);
        return MailHelper::sendEmail($email,'在 '.get_setting('site_name').' 上的验证邮件','此邮件为验证邮箱邮件，请点击 <a href="'.$url.'">邮件验证</a> 进行验证！');
    }

    // 发送重置密码邮件
    public static function resetPasswordEmail($user)
    {
        $expire = time() + 3600 * 24;
        $active_code_hash = RandomHelper::alnum(20);
        self::createActiveCode($user['uid'], $expire, $active_code_hash, 'RESET_PASSWORD');
        $url = (string) url('account/reset_password', ['active_code' => $active_code_hash],true,true);
        return MailHelper::sendEmail($user['email'],get_setting('site_name').'重置密码','此邮件为验证邮件，请点击 <a href="'.$url.'">此链接</a> 重置密码！');
    }
}
