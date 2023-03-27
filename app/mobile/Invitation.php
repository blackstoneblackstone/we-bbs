<?php
// +----------------------------------------------------------------------
// | WeCenter社交化问答系统
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2022 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@WeCenter.com>
// +----------------------------------------------------------------------

namespace app\mobile;

use app\common\controller\Frontend;

use app\model\Users;
use app\common\library\helper\MailHelper;
use app\model\Invitation as InvitationModel;

class Invitation extends Frontend
{
    protected $needLogin = ['index', 'records', 'operate'];

    public function index()
    {
        $this->assign('active_types', InvitationModel::$active_types);
        $this->assign('quota', InvitationModel::availableCount($this->user_info));
        return $this->fetch();
    }

    public function records()
    {
        $where = ['uid' => $this->user_id];
        $page = $this->request->param('page',1,'intval');
        $data = InvitationModel::records($where, $page, intval($this->settings['contents_per_page']) ?: 10);
        $data['html'] = $this->fetch('', $data);
        $this->apiResult($data);
    }

    // 邀请页面
    public function page()
    {
        $type = $this->request->get('type', InvitationModel::$link_type, 'trim');

        $invitation_code = InvitationModel::generateCode($this->user_id);
        $this->assign('invitation_code', $invitation_code);
        if (InvitationModel::$link_type == $type) {
            $this->assign('link', (string) url('Account/register', ['invitation_code' => $invitation_code])->domain(true));
        }
        $this->assign('type', $type);
        return $this->fetch('action');
    }
    
    // 邀请操作
    public function operate()
    {
        if (InvitationModel::availableCount($this->user_info) <= 0) $this->ajaxResult(['code' => 0, 'msg' => '邀请名额已用完']);

        $data = $this->request->post();

        if (InvitationModel::$email_type == $data['active_type']) {
            if (!$data['invitation_email'] || !MailHelper::isEmail($data['invitation_email'])) $this->ajaxResult(['code' => 0, 'msg' => '请填写正确的邮箱地址']);
            if (Users::checkUserExist($data['invitation_email'])) $this->ajaxResult(['code' => 0, 'msg' => '该邮箱已被使用请更换邮箱']);
            if (InvitationModel::emailHadInvitation($this->user_id, $data['invitation_email'])) $this->ajaxResult(['code' => 0, 'msg' => '该邮箱已在邀请注册中']);
        }

        $data['active_status'] = 1;
        $data['uid'] = $this->user_id;
        $data['create_time'] = time();
        $data['add_ip'] = ip2long($this->request->ip());
        $data['active_expire'] = 3600 * $this->settings['invitation_expire_time'] + time();
        if (InvitationModel::add($data)) {
            // 发送邮件
            if (InvitationModel::$email_type == $data['active_type']) {
                $subject = $this->user_info['name'].L('邀请您注册').$this->settings['site_name'].L('用户');
                $message = L('注册链接将在').$this->settings['invitation_expire_time'].L('小时后过期').'。<a href="'.(string) url('Account/register', ['invitation_code' => $data['invitation_code']], true, true).'">'.L('立即前往注册').'</a>';
                $res = MailHelper::sendEmail($data['invitation_email'], $subject, $message);
                if ($res['code'] == 0) $this->ajaxResult(['code' => 1, 'msg' => '邮件发送失败']);
            }

            $this->ajaxResult(['code' => 1, 'msg' => '记录成功']);
        } else {
            $this->ajaxResult(['code' => 0, 'msg' => '记录失败']);
        }
    }
}