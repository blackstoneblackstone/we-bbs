<?php

namespace app\frontend;

use app\common\library\helper\LogHelper;
use app\model\Users;
use app\model\UsersActive;
use app\common\controller\Frontend;
use app\validate\User as UserValidate;
use think\exception\ValidateException;
use app\common\library\helper\IpHelper;
use app\common\library\helper\MailHelper;
use app\common\library\helper\RandomHelper;
use app\model\Invitation as InvitationModel;

class Account extends Frontend
{
    protected $needLogin = [
        'send_valid_mail',
        'save_profile',
        'modify_email',
        'modify_mobile',
        'modify_password',
        'modify_deal_password',
        'welcome_first_login',
    ];

    //用户登录
    public function login()
    {
        //登录钩子
        hook('userLogin');
        if($this->request->isPost())
        {
            $data = $this->request->post();
            if(isset($data['password']))
            {
                $data['password'] = authCode($data['password'],'DECODE',$data['token']);
            }
            $return_url = (isset($data['return_url']) && $data['return_url']) ? base64_decode($data['return_url']) : $this->returnUrl;

            if(session('__token__')!=$data['token'])
            {
                $this->error('请不要重复提交');
            }

            $remember = 0;

            if($data['remember'] && $this->settings['remember_login_enable']=='Y')
            {
                $remember = intval($this->settings['remember_login_time'])*ONE_DAY;
            }

            if(isset($data['code']) || isset($data['mobile']))
            {
                if (($this->settings['register_type']=='mobile' || $this->settings['register_type']=='all') && get_plugins_config('sms','enable')!='N' && (!isset($data['code']) || !$data['code']))
                {
                    $this->error('请输入手机验证码');
                }

                if(isset($data['code']) && cache('sms_'.$data['mobile'])!=$data['code'])
                {
                    $this->error('手机验证码不正确');
                }
                $user = Users::loginByMobile($data['mobile'],$data['code'],$data,$remember);
            }else{
                $user= Users::getLogin($data['username'],$data['password'],$remember);
            }

            if(!$user)
            {
                $this->error(Users::getError());
            }
            session('__token__',null);
            $this->success('登录成功',$return_url);
        }

        $url = $this->returnUrl;
        $currentUrl = $this->request->url(true);
        $baseUrl = request()->domain();
        if ($this->user_id)
        {
            if ((!$url OR strstr($url, '://') AND substr($url, 0, strlen($baseUrl)) != $baseUrl) || $currentUrl==$url)
            {
               $this->redirect('/');
            }else{
                header('Location: ' . $url);
            }
        }

        $login_type = $this->request->param('type','email');

        $this->assign([
            'login_type'=>$login_type,
            'return_url'=>$url
        ]);
        return $this->fetch();
    }

    //用户注册
    public function register()
    {
        //注册钩子
        hook('userRegister');

        if($this->settings['register_type']=='close')
        {
            $this->error('网站关闭注册',url('/'));
        }

        $invitation_code = $this->request->param('invitation_code', '', 'trim');
        if ($this->settings['invite_register_enable'] == 'Y') {
            if(!$invitation_code){
                $this->error('本站只能通过邀请注册',url('/'));
            }
            $invite_code_info = db('invitation')->where(['invitation_code' => $invitation_code])->find();
            if(!$invite_code_info || $invite_code_info['active_status'] == 2 || $invite_code_info['active_expire'] < time()) {
                $this->error('注册邀请不存在或已使用或已过期',$this->returnUrl);
            }
        }

        if($this->user_id)
        {
            $this->redirect('/');
        }

        if($this->request->isPost())
        {
            $data = $this->request->post();
            $user_name =$data['username'];
            $password = authCode($data['password'],'DECODE',$data['token']);
            $return_url = (isset($data['return_url']) && $data['return_url']) ? base64_decode($data['return_url']) : $this->returnUrl;
            if(session('__token__')!=$data['token'])
            {
                $this->error('请不要重复提交');
            }

            if($this->settings['register_type'] == 'email' || $this->settings['register_type']=='all')
            {
                if(!$data['email'] || !MailHelper::isEmail($data['email']))
                {
                    $this->error('请填写正确的邮箱地址');
                }

                if(Users::checkUserExist($data['email']))
                {
                    $this->error('该邮箱已被使用请更换邮箱');
                }
            }

            // 字段规则验证
            try {
                $data['password'] = $password;
                validate(UserValidate::class)->scene('register')->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if (isset($data['code']) || isset($data['mobile'])) {
                if($data['mobile'] && Users::checkUserExist($data['mobile']))
                {
                    $this->error('该手机号已被使用');
                }

                if ($this->settings['register_valid_type']=='mobile' && get_plugins_info('sms')['status'] && !$data['code'])
                {
                    $this->error('请输入手机验证码');
                }

                if(isset($data['code']) && cache('sms_'.$data['mobile'])!=$data['code'])
                {
                    $this->error('手机验证码不正确');
                }
                $uid = Users::loginByMobile($data['mobile'], $data['code'], ['username'=>$user_name,'password'=>$password]);
            } else {
                $uid = Users::registerUser($user_name, $password, ['email' => $data['email']]);
            }

            if(!$uid)
            {
                $this->error(Users::getError(),Users::getError()=='注册成功，等待管理员审核'?get_url('login'):'');
            }

            // 更新邀请注册信息
            if ($invitation_code) {
                InvitationModel::active($invitation_code, $uid);
            }

            $user_info = Users::getUserInfo($uid);
            //发送注册成功欢迎语
            if($user_info)
            {
                send_notify(0,$user_info['uid'],'TYPE_SYSTEM_NOTIFY','users',$user_info['uid']);
            }

            if($this->settings['register_valid_type']=='email')
            {
                $return_url = (string)url('account/send_valid_mail');
            }
            session('__token__',null);
            $this->success('注册成功',$return_url);
        }

        $register_type = $this->settings['register_type'];
        if (($register_type == 'mobile' || $register_type == 'all') && !(get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N')) {
            $this->error('网站尚未安装或配置短信插件', (string) url('account/register'));
        }

        $this->assign([
            'agreement'=>nl2br(get_setting("register_agreement")),
            'register_type'=>$register_type
        ]);
        return $this->fetch();
    }

    /*发送验证邮件*/
    public function send_valid_mail()
    {
        if(!$this->user_id)
        {
            $this->error('您访问的页面不存在', '/');
        }
        $sendResult = UsersActive::newValidEmail($this->user_id,$this->user_info['email']);
        if($sendResult && !$sendResult['code'])
        {
            $this->error($sendResult['message'], '/');
        }
        $this->success('验证邮件发送成功,请登录邮箱 '.$this->user_info['email'].' 进行验证', '/');
    }

    /**
     * 验证邮箱激活码
     */
    public function valid_email_verify()
    {
        $active_code_hash = $this->request->param('active_code_hash');
        if(!$active_code_hash)
        {
            $this->error('激活链接不正确,请重新发送验证链接','/');
        }

        $codeInfo = db('users_active')->where(['active_code'=>$active_code_hash])->find();
        if(!$codeInfo)
        {
            $this->error('激活链接不正确,请重新发送验证链接','/');
        }

        $valid_email_code = cache('valid_email_code_'.$codeInfo['uid']);

        if($valid_email_code!=$active_code_hash)
        {
            $this->error('激活链接不正确');
        }

        if($codeInfo['expire_time']<time())
        {
            $this->error('验证链接已过期，请登录后重新发送验证邮件');
        }

        //更新邮箱验证状态
        if(Users::updateUserFiled($codeInfo['uid'],['is_valid_email'=>1]))
        {
            //更新用户组
            db('users')->where(['uid'=>$codeInfo['uid']])->update(['group_id'=>4]);

            //更新用户激活时间
            db('users_active')->where(['id'=>$codeInfo['id']])->update([
                'active_time'=>time(),
                'active_ip'=>IpHelper::getRealIp()
            ]);

            cache('valid_email_code_'.$codeInfo['uid'],null);
        }

        $this->success('验证成功','login');
    }

    /**
     * 找回密码
     * @return mixed
     */
    public function find_password()
    {
        //找回密码钩子
        hook('userFindPassword');

        $login_type = $this->request->param('type','email');
        if ($login_type == 'mobile' && !(get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N')) {
            $this->error('网站尚未开通手机验证码', (string) url('account/find_password'));
        }

        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 邮箱找回
            if ($data['type'] == 'email') {
                if (!$data['email'] || !MailHelper::isEmail($data['email'])) $this->error('请填写正确的邮箱地址');
                if (!$user = Users::checkUserExist($data['email'])) $this->error('未找到绑定该邮箱的用户');
                $res = UsersActive::resetPasswordEmail($user);
                if ($res['code'] == 1) {
                    $this->success('重置密码邮件已发送');
                } else {
                    $this->error($res['message']);
                }
            } else {
                // 手机号找回
                // 字段规则验证
                try {
                    validate(UserValidate::class)->scene('mobile_reset_password')->check($data);
                } catch (ValidateException $e) {
                    $this->error($e->getError());
                }
                // 更新密码
                $uid = Users::checkUserExist($data['mobile'], 'uid')['uid'];
                if (Users::resetPassword($uid, $data['password'])) {
                    Users::directLogin($uid);
                    $this->success('密码重置成功', '/');
                } else {
                    $this->error('密码重置失败');
                }
            }
        }

        $this->assign([
            'login_type' => $login_type
        ]);
        return $this->fetch();
    }

    // 重置密码
    public function reset_password()
    {
        if ($this->user_id) $this->error('错误的请求', '/');
        $url = (string) url('account/find_password');
        if (!$active_code = $this->request->param('active_code')) $this->error('重置密码链接错误，请重新提交', $url);

        if (!$codeInfo = db('users_active')->where(['active_code' => $active_code])->find()) $this->error('重置密码链接错误，请重新提交', $url);

        if ($codeInfo['active_time']) $this->error('重置密码链接已被使用', $url);
        if ($codeInfo['expire_time'] < time()) $this->error('重置密码链接已过期，请重新提交', $url);

        if ($this->request->isPost()) {
            $data = $this->request->post();
            // 密码验证
            try {
                validate(UserValidate::class)->scene('password')->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if (Users::resetPassword($codeInfo['uid'], $data['password'])) {
                // 更新用户激活时间
                db('users_active')->where(['id' => $codeInfo['id']])->update([
                    'active_time' => time(),
                    'active_ip' => IpHelper::getRealIp()
                ]);

                Users::directLogin($codeInfo['uid']);

                $this->success('密码重置成功', '/');
            } else {
                $this->error('操作失败');
            }
        }

        $this->assign('active_code', $active_code);
        return $this->fetch();
    }

    /**
     * 保存用户资料
     */
    public function save_profile()
    {
        //保存用户资料钩子
        hook('userSaveProfile');

        if($this->request->isPost())
        {
            $postData = $this->request->post();
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }
            unset($postData['uid']);
            $postData['birthday'] = strtotime($postData['birthday']);

            if($postData['url_token'] && $postData['url_token']!=$this->user_info['url_token'] && db('users')->where('url_token',trim($postData['url_token']))->value('uid'))
            {
                $this->error('该自定义链接已存在！');
            }

            if(Users::updateUserFiled($this->user_id,$postData))
            {
                if($postData['signature'] && $postData['signature']!=$this->user_info['signature'] && $postData['signature']!='')
                {
                    LogHelper::addIntegralLog('UPDATE_SIGNATURE',$this->user_id,'users',$this->user_id);
                }

                if($postData['avatar'] && $postData['avatar']!=$this->user_info['avatar'] && $postData['avatar']!='/static/common/image/default-cover.svg')
                {
                    LogHelper::addIntegralLog('UPLOAD_AVATAR',$this->user_id,'users',$this->user_id);
                }

                $this->success('资料更新成功','setting/profile');
            }
            $this->error('提交成功');
        }
    }

    /**
     * 修改邮箱
     */
    public function modify_email()
    {
        if(!$this->user_id)
        {
            $this->redirect('/');
        }

        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $info = db('users')->where('uid',$this->user_id)->field('password,salt,email')->find();
            if(compile_password($postData['password'],$info['salt'])!=$info['password'])
            {
                $this->error('密码不正确');
            }

            if(!$postData['new_email'])
            {
                $this->error('请输入新的邮箱地址');
            }

            Users::updateUserFiled($this->user_id,[
                'email'=>$postData['new_email'],
                'is_valid_email'=>0
            ]);

            $this->success('邮箱修改成功');
        }

        return $this->fetch();
    }

    /**
     * 修改手机号
     */
    public function modify_mobile()
    {
        if(!$this->user_id)
        {
            $this->redirect('/');
        }

        $step = $this->request->param('step',0);
        $this->assign('step',$step);

        if($this->request->isPost())
        {
            $postData = $this->request->post();
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }

            switch ($step)
            {
                case 0:
                    if(isset($postData['code']) && !$postData['code'])
                    {
                        $this->error('请输入短信验证码');
                    }

                    if($this->user_info['mobile'])
                    {
                        $cache_code = cache('sms_'.$this->user_info['mobile']);
                        if($cache_code == $postData['code'])
                        {
                            $this->success('验证成功','modify_mobile?step=1&_ajax_open=1');
                        }
                        $this->error('短信验证码不正确');
                    }else{
                        $info = db('users')->where('uid',$this->user_id)->field('password,salt')->find();
                        if(compile_password($postData['password'],$info['salt'])!=$info['password'])
                        {
                            $this->error('密码不正确');
                        }
                        $this->success('验证成功','modify_mobile?step=1&_ajax_open=1');
                    }
                    break;
                case 1:
                    if(!$postData['mobile'])
                    {
                        $this->error('请输入新手机号');
                    }

                    if($this->user_info['mobile']==$postData['mobile'])
                    {
                        $this->error('新手机号不能和原来的手机号一样');
                    }

                    if(isset($postData['code']) && !$postData['code'])
                    {
                        $this->error('请输入短信验证码');
                    }

                    $cache_code = cache('sms_'.$postData['mobile']);
                    if(isset($postData['code']) && $cache_code != $postData['code'])
                    {
                        $this->error('验证码不正确');
                    }

                    if(Users::checkUserExist($postData['mobile'],'uid') && $postData['mobile']!=$this->user_info['mobile'])
                    {
                        $this->error('该手机号已存在');
                    }
                    Users::updateUserFiled($this->user_id,['mobile'=>$postData['mobile']]);
                    $this->success('修改成功','setting/profile');
                    break;
            }
        }
        return $this->fetch();
    }

    /**
     * 验证手机号
     */
    public function check_mobile()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            $uid = Users::checkUserExist($postData['mobile'],'uid');
            if($uid && $uid!=$this->user_id)
            {
                $this->error('该手机号已存在');
            }

            $this->success('手机号正确');
        }
    }

    /**
     * 修改密码
     */
    public function modify_password()
    {
        if ($this->request->isPost())
        {
            $postData = $this->request->post();
            if ($this->user_info['mobile']) {
                $scene = 'mobile_reset_password';
                $postData['mobile'] = $this->user_info['mobile'];
            } else {
                $scene = 'password';
                $info = db('users')->where('uid', $this->user_id)->field('password,salt')->find();
                if (compile_password($postData['old_password'], $info['salt']) != $info['password']) $this->error('账号密码不正确');
            }

            // 密码验证
            try {
                validate(UserValidate::class)->scene($scene)->check($postData);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if (!Users::updateUserFiled($this->user_id, ['salt' => RandomHelper::alnum(), 'password' => $postData['password']])) $this->error('提交失败或数据无变化');

            $this->success('密码修改成功');
        }

        return $this->fetch();
    }

    /**
     * 修改交易密码
     */
    public function modify_deal_password()
    {
        if ($this->request->isPost()) {
            $postData = $this->request->post();
            // if (!$postData['uid'] || $postData['uid']!=$this->user_id) $this->error('当前用户信息不正确');

            if ($this->user_info['mobile']) {
                if (!$postData['code'] || $postData['code'] != cache('sms_'.$this->user_info['mobile'])) $this->error('短信验证码不正确');
            } else {
                $password = db('users')->where('uid', $this->user_id)->value('deal_password');
                if (!password_verify($postData['old_password'], $password)) $this->error('密码不正确');
            }

            if ($postData['password'] != $postData['re_password']) $this->error('两次密码输入不一致');

            if (!Users::updateUserFiled($this->user_id, ['deal_password' => password_hash($postData['password'],1)])) {
                $this->error('提交失败或数据无变化');
            }

            $this->success('修改成功');
        }
        return $this->fetch();
    }

    /**
     * 首次登陆
     */
    public function welcome_first_login()
    {
        if($this->request->isPost())
        {
            $postData = $this->request->post();
            if(!$postData['uid'] || $postData['uid']!=$this->user_id)
            {
                $this->error('当前用户信息不正确');
            }
            unset($postData['uid']);
            $postData['birthday'] = strtotime($postData['birthday']);
            $postData['is_first_login'] = 0;
            Users::updateUserFiled($this->user_id,$postData);
            $this->success('资料更新成功');
        }
        return $this->fetch();
    }

    /**
     * 退出登录
     */
    public function logout()
    {
        Users::logout();
        $this->success('退出成功','login');
    }
}