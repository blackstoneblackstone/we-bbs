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

namespace app\common\library\helper;

use think\facade\Request;

class CheckHelper
{
    /**
     * 检测用户在线状态
     */
    public static function checkOnline()
    {
        if (get_setting('online_check')=='Y')
        {
            //删除过期用户
            $map[] = array('last_login_time','<', time() - (int)get_setting('online_check_time') * 60);
            db('users_online')->where($map)->delete();

            //更新在线过期时间
            $user_info = session('login_user_info');
            if ($user_info) {
                $last_login_time = cache('last_login_time_'.$user_info['uid']);
                if(!$last_login_time || time() > $last_login_time +(get_setting('online_check_time') * 60))
                {
                    $ip = IpHelper::getRealIp();
                    $updateData = array(
                        'last_login_time'=>time(),
                        'last_url' =>Request::url(),
                        'user_agent'=>Request::server('HTTP_USER_AGENT'),
                        'last_login_ip'=>$ip,
                        'uid'=>$user_info['uid']
                    );

                    $id = db('users_online')->where(['uid'=>$user_info['uid']])->value('id');

                    if($id)
                    {
                        db('users_online')->where(['id'=> $id])->update($updateData);
                    }else{
                        db('users_online')->insert($updateData);
                    }

                    cache('last_login_time_'.$user_info['uid'],$last_login_time,60);
                }
            }
        }
    }

    /**
     * 检测用户是否在其他地方登录
     * @param $uid
     * @return bool
     */
    public static function checkUserIsLoginOtherPlatform($uid): bool
    {
        $map[] = array('uid', '=', $uid);
        $list = db('users_online')->where($map)->find();
        if(!$list) return true;
        if($list['last_login_ip']!=IpHelper::getRealIp() || $list['user_agent']!=Request::server('HTTP_USER_AGENT'))
        {
            return false;
        }
        return true;
    }

    /**
     * 检测网站是否关闭
     * @return bool false 关站
     */
    public static function checkSiteStatus(): bool
    {
        if(get_setting('site_close')=='Y')
        {
            if(session('login_uid')!=null)
            {
                //管理员不受限
                if(session('login_user_info')['group_id']==1 || session('login_user_info')['group_id']==2)
                {
                    return true;
                }
                session('login_uid',null);
                session('login_user_info',null);
            }elseif((strtolower(request()->controller()) == 'account' && strtolower(request()->action()) == 'login') || strtolower(request()->controller()) == 'tools')
            {
                return true;
            }else{
                return false;
            }
        }
        return true;
    }

    /**
     * 检查游客权限
     */
    public static function checkTouristPermission()
    {
        $permission = db('admin_group')->where('id',5)->value('permission');
        return json_decode($permission,true);
    }
}