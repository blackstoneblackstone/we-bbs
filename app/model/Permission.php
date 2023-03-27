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

use think\Model;
class Permission extends Model
{
	protected $name = 'users_permission';

    public static function updateUsersPermission()
    {
        $list = db('users_permission')->order('sort','asc')->column('type,name,title,option,value,group');

        $admin_permission = db('admin_group')->column('id,permission');

        $integral_permission = db('users_integral_group')->column('id,permission');

        $reputation_permission = db('users_reputation_group')->column('id,permission');

        //系统组权限
        foreach ($admin_permission as $k=>$v)
        {
            $permission_option = json_decode($v['permission'],true);
            foreach ($list as $key=>$val)
            {
                if($val['group']=='system' || $val['group']=='common')
                {
                    if(!isset($permission_option[$val['name']]))
                    {
                        $permission_option[$val['name']] = $val['value'];
                    }
                }
            }
            db('admin_group')->where(['id'=>$v['id']])->update(['permission'=>json_encode($permission_option,JSON_UNESCAPED_UNICODE)]);
        }
        //积分组权限
        foreach ($integral_permission as $k=>$v)
        {
            $permission_option = json_decode($v['permission'],true);
            foreach ($list as $key=>$val)
            {
                if($val['group']=='integral' || $val['group']=='common') {
                    if (!isset($permission_option[$val['name']])) {
                        $permission_option[$val['name']] = $val['value'];
                    }
                }
            }
            db('users_integral_group')->where(['id'=>$v['id']])->update(['permission'=>json_encode($permission_option,JSON_UNESCAPED_UNICODE)]);
        }
        //威望组权限
        foreach ($reputation_permission as $k=>$v)
        {
            $permission_option = json_decode($v['permission'],true);
            foreach ($list as $key=>$val)
            {
                if($val['group']=='reputation' || $val['group']=='common') {
                    if (!isset($permission_option[$val['name']])) {
                        $permission_option[$val['name']] = $val['value'];
                    }
                }
            }
            db('users_reputation_group')->where(['id'=>$v['id']])->update(['permission'=>json_encode($permission_option,JSON_UNESCAPED_UNICODE)]);
        }

        return true;
    }
}