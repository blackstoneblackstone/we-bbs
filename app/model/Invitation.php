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

use app\common\library\helper\LogHelper;

class Invitation extends BaseModel
{
    protected $autoWriteTimestamp = false;

    public static $link_type = 'link';
    public static $email_type = 'email';
    public static $active_status = ['全部', '未使用', '已使用'];
    public static $active_types = ['link' => '生成链接邀请', 'email' => '通过邮件邀请'];
    protected $name = 'invitation';

    // 邀请记录
    public static function records($where = [], $page = 1, $per_page = 10, $pjax = 'tabMain'): array
    {
        $list = db('invitation')->where($where)->order('create_time', 'DESC')->paginate(
            [
                'list_rows' => $per_page,
                'page' => $page,
                'query' => request()->param(),
                'pjax' => $pjax
            ]
        );
        $pageVar = $list->render();
        $data = $list->toArray();

        $time = time();
        $link = (string) url('Account/register')->domain(true);
        foreach ($data['data'] as &$val) {
            $val['active_type_label'] = self::$active_types[$val['active_type']];
            $val['active_status_label'] = self::$active_status[$val['active_status']];
            $val['invitation_link'] = $link."?invitation_code={$val['invitation_code']}";
            $val['expire'] = $time >= $val['active_expire'];
        }
        return ['list' => $data['data'], 'page' => $pageVar, 'total' => $data['last_page']];
    }

    // email是否正在邀请中
    public static function emailHadInvitation($uid, $email)
    {
        return self::where('uid', $uid)->where('invitation_email', $email)
            ->where('active_status', 1)->where('active_expire', '>', time())->value('id');
    }

    // 用户已邀请注册数量
    public static function hadInvitationCount($uid)
    {
        $time = time();
        return self::whereRaw("uid={$uid} AND (active_status=2 OR active_expire>{$time})")->count();
    }
    
    // 用户可用邀请注册数量
    public static function availableCount($user)
    {
        if (!$count = max((int) $user['available_invite_count'], (int) $user['permission']['available_invite_count'])) return 0;
        return $count - self::hadInvitationCount($user['uid']);
    }

    // 生成邀请码
    public static function generateCode($uid)
    {
        return md5(uniqid(microtime().$uid));
    }

    // 新增
    public static function add($data)
    {
        return (new self())->allowField([
            'uid', 'invitation_code', 'active_type', 'invitation_email',
            'add_ip', 'active_expire', 'active_status', 'create_time'])->save($data);
    }

    // 注册激活
    public static function active($code, $uid)
    {
        if (!$invitation = self::where('invitation_code', $code)->find()) return false;
        if ($invitation->active_status != 1) return false;
        if ($invitation->active_expire < time()) return false;

        $invitation->save([
            'active_status' => 2,
            'active_uid' => $uid,
            'active_time' => time(),
            'active_ip' => ip2long(request()->ip())
        ]);

        // 添加积分记录
        LogHelper::addIntegralLog('INVITE_REGISTER', $uid, 'users', $invitation->uid);

        return true;
    }
}