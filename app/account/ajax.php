<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/
define('IN_AJAX', TRUE);
if (!defined('IN_ANWSION'))
{
	die;
}

class ajax extends AWS_CONTROLLER
{
	public function get_access_rule()
	{
		$rule_action['rule_type'] = 'white'; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
		$rule_action['actions'] = array(
			'check_username',
			'check_email',
			'check_mobile',
			'register_process',
			'login_process',
			'login_mobile_process',
            'send_mobile_sense',
			'register_agreement',
			'send_valid_mail',
			'valid_email_active',
			'request_find_password',
            'first_find_password',
			'find_password_modify',
			'find_password_modify_final',
			'weixin_login_process',
			'pay_member',
			'member_before',
			'check_status',
			'synch_img',
			'call_back'
		);
		return $rule_action;
	}

	public function setup()
	{
		HTTP::no_cache_header();
	}

	public function check_username_action()
	{
		if ($res=$this->model('account')->check_username_char($_POST['username']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t($res)));
		}
		if ( $this->model('account')->check_username_sensitive_words($_POST['username']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名含敏感词')));
		}
		if ($this->model('account')->check_username($_POST['username']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名已被注册')));
		}

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

    public function check_mobile_action()
    {
        if($_POST['type'] == 'regist')
        {
            if ($this->model('account')->check_mobile($_POST['mobile']))
            {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('手机号已被注册')));
            }
        } elseif ($_POST['type'] == 'find_password')
        {
            if (!$this->model('account')->check_mobile($_POST['mobile']))
            {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('手机号不存在')));
            }
        }

        H::ajax_json_output(AWS_APP::RSM(null, 1, null));
    }

	public function check_email_action()
	{
		if (!$_GET['email'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入邮箱地址')));
		}

		if ($this->model('account')->check_email($_GET['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邮箱地址已被使用')));
		}

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

    public function register_process_action()
	{
	    /*用户注册前置钩子*/
	    run_hook('save_user_register_hook',['action'=>'before','data'=>$_POST]);
		if (get_setting('register_type') == 'close')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站目前关闭注册')));
		}
		else if (get_setting('register_type') == 'invite' AND !$_POST['icode'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站只能通过邀请注册')));
		}
		else if (get_setting('register_type') == 'weixin')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('本站只能通过微信注册')));
		}
		if ($_POST['icode'])
		{
			if (!$invitation = $this->model('invitation')->check_code_available($_POST['icode']) AND $_POST['email'] == $invitation['invitation_email'])
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邀请码无效或与邀请邮箱不一致')));
			}
		}
		if (trim($_POST['user_name']) == '')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入用户名')));
		}
		else if ($this->model('account')->check_username($_POST['user_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名已经存在')));
		}
		else if ($check_rs = $this->model('account')->check_username_char($_POST['user_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名包含无效字符')));
		}
		else if ($this->model('account')->check_username_sensitive_words($_POST['user_name']) OR trim($_POST['user_name']) != $_POST['user_name'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名中包含敏感词或系统保留字')));
		}

		$regist_type = $_GET['type'] ? $_GET['type'] : 'email';
		if($regist_type == 'email'){
            if ($this->model('account')->check_email($_POST['email']))
            {
                H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('E-Mail 已经被使用, 或格式不正确')));
            }
        } elseif ($regist_type == 'mobile'){
            hook('mobile_regist','register',array('mobile'=>$_POST['mobile'],'smscode'=>$_POST['smscode']));
        }

        /*对js加密的密码进行解密*/
		$_POST['password'] = online_decrypt($_POST['password']);
		if (cjk_strlen($_POST['password']) < 6 OR cjk_strlen($_POST['password']) > 16)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入6-16位的密码')));
		}

		if (! $_POST['agreement_chk'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('你必需同意用户协议才能继续')));
		}

        if(get_setting('register_seccode') == 'Y' and $regist_type=='email' and !$this->model('tools')->geetest($_POST)){
        	H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('验证码错误')));
        }

		if (get_setting('ucenter_enabled') == 'Y')
		{
			$result = $this->model('ucenter')->register($_POST['user_name'], $_POST['password'], $_POST['email']);

			if (is_array($result))
			{
				$uid = $result['user_info']['uid'];
			}
			else
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, $result));
			}
		}
		else
		{
			$uid = $this->model('account')->user_register($_POST['user_name'], $_POST['password'], $_POST['email'],$_POST['mobile'],$regist_type);
		}

		if ($_POST['email'] == $invitation['invitation_email'])
		{
			$this->model('active')->set_user_email_valid_by_uid($uid);
			$this->model('active')->active_user_by_uid($uid);
		}

		if (isset($_POST['sex']))
		{
			$update_data['sex'] = intval($_POST['sex']);
			if ($_POST['province'])
			{
				$update_data['province'] = htmlspecialchars($_POST['province']);
				$update_data['city'] = htmlspecialchars($_POST['city']);
			}

			if ($_POST['job_id'])
			{
				$update_data['job_id'] = intval($_POST['job_id']);
			}

			$update_attrib_data['signature'] = htmlspecialchars($_POST['signature']);

			// 更新主表
			$this->model('account')->update_users_fields($update_data, $uid);

			// 更新从表
			$this->model('account')->update_users_attrib_fields($update_attrib_data, $uid);
		}

		$this->model('account')->logout();
		if ($_POST['icode'])
		{
			$follow_users = $this->model('invitation')->get_invitation_by_code($_POST['icode']);
		}
		else if (HTTP::get_cookie('fromuid'))
		{
			$follow_users = $this->model('account')->get_user_info_by_uid(HTTP::get_cookie('fromuid'));
		}

		if ($follow_users['uid'])
		{
			$this->model('follow')->user_follow_add($uid, $follow_users['uid']);
			$this->model('follow')->user_follow_add($follow_users['uid'], $uid);
			$this->model('integral')->process($follow_users['uid'], 'INVITE', get_setting('integral_system_config_invite'), '邀请注册: ' . $_POST['user_name'], $follow_users['uid']);
		}
		if ($_POST['icode'])
		{
			$this->model('invitation')->invitation_code_active($_POST['icode'], time(), fetch_ip(), $uid);
		}

		if (get_setting('register_valid_type') == 'N' OR (get_setting('register_valid_type') == 'email' AND get_setting('register_type') == 'invite'))
		{
			$this->model('active')->active_user_by_uid($uid);
		}
		$user_info = $this->model('account')->get_user_info_by_uid($uid);

        /*用户注册后置钩子*/
        run_hook('save_user_register_hook',['action'=>'after','data'=>$_POST,'uid'=>$user_info['uid']]);

		if (get_setting('register_valid_type') == 'N' OR $user_info['group_id'] != 3 OR $_POST['email'] == $invitation['invitation_email'])
		{
			$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $_POST['password'], $user_info['salt']);
			if (!$_POST['_is_mobile'])
			{
				H::ajax_json_output(AWS_APP::RSM(array(
					'url' => get_js_url('/home/first_login-TRUE')
				), 1, null));
			}
		}
		else
		{
			AWS_APP::session()->valid_email = $user_info['email'];
			$this->model('active')->new_valid_email($uid);
			$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $_POST['password'], $user_info['salt']);
			if (!$_POST['_is_mobile'])
			{
				H::ajax_json_output(AWS_APP::RSM(array(
					'url' => get_js_url('/account/valid_email/')
				), 1, null));
			}
		}

		if ($_POST['_is_mobile'])
		{
			$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $_POST['password'], $user_info['salt']);
			if ($_POST['return_url'])
			{
				$this->model('account')->get_user_info_by_uid($uid);
				$return_url = strip_tags($_POST['return_url']);
			}
			else
			{
				$return_url = get_js_url('/m/');
			}

			H::ajax_json_output(AWS_APP::RSM(array(
				'url' => $return_url
			), 1, null));
		}
	}

	public function get_user_info($username,$password)
    {
		if(preg_match("/^1[345789]\d{9}$/", $username)){
			$user_info = $this->model('account')->login_check_mobile($username, $password);
		}else{
			$user_info = $this->model('account')->check_user_name($username, $password);
		}
		return $user_info;
	}

	public function login_process_action()
	{
        /*用户登陆前置钩子*/
        run_hook('save_user_login_hook',['action'=>'before','data'=>$_POST]);

		if(!trim($_POST['user_name'])){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请填写账号')));
		}
						
		if(!trim($_POST['password'])){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请填写密码')));
		}

		/*对js加密的密码进行解密*/
		$_POST['password'] = online_decrypt($_POST['password']);

		if (get_setting('ucenter_enabled') == 'Y')
		{
			if (!$user_info = $this->model('ucenter')->login($_POST['user_name'], $_POST['password']))
			{
				$user_info = $this->model('account')->check_login($_POST['user_name'], $_POST['password']);
			}
		}
		else
		{
			$user_name=trim($_POST['user_name']);
			if(get_hook_info('mobile_regist')['state']==1){
				$login_type=get_hook_config('mobile_regist')['login_type']['value'];
				if(preg_match("/^1[345789]\d{9}$/", $user_name)){
					if($login_type=='email')
						H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('当前只能用邮箱或者用户名登录')));
					else
						$user_info = $this->get_user_info($_POST['user_name'], $_POST['password']);
				}else if(H::valid_email($user_name)){
					if($login_type=='mobile')
						H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('当前只能用手机号或者用户名登录')));
					else
						$user_info = $this->get_user_info($_POST['user_name'], $_POST['password']);
						
				}else{
					$user_info = $this->get_user_info($_POST['user_name'], $_POST['password']);
				}
			}else{
				$user_info = $this->get_user_info($_POST['user_name'], $_POST['password']);

			}
		}

		if (!$user_info)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确的帐号或密码')));
		}
		else if ($user_info == 'no_user_name')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确的帐号')));
		}
		else if ($user_info == 'no_password')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确的密码')));
		}
		else
		{
			if ($user_info['forbidden'] == 1)
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('抱歉, 你的账号已经被禁止登录')));
			}

			if (get_setting('site_close') == 'Y' AND $user_info['group_id'] != 1)
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, get_setting('close_notice')));
			}

			if (get_setting('register_valid_type') == 'approval' AND $user_info['group_id'] == 3)
			{
				$url = get_js_url('/account/valid_approval/');
			}
			else
			{
				if ($_POST['net_auto_login'])
				{
					$expire = 60 * 60 * 24 * 360;
				}

				$this->model('account')->update_user_last_login($user_info['uid']);
				$this->model('account')->logout();


				$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $_POST['password'], $user_info['salt'], $expire);

				if (get_setting('register_valid_type') == 'email' AND !$user_info['valid_email'])
				{
					AWS_APP::session()->valid_email = $user_info['email'];

					$url = get_js_url('/account/valid_email/');
				}
				else if ($user_info['is_first_login'] AND !$_POST['_is_mobile'])
				{
					$url = get_js_url('/home/first_login-TRUE');
				}
				else if ($_POST['return_url'] AND !strstr($_POST['return_url'], '/logout') AND !strstr($_POST['return_url'], '/find_password') AND
					($_POST['_is_mobile'] AND strstr($_POST['return_url'], '/m/') OR
					strstr($_POST['return_url'], '://') AND strstr($_POST['return_url'], base_url())))
				{
					$url = get_js_url($_POST['return_url']);
				}
				else if ($_POST['_is_mobile'])
				{
					$url = get_js_url('/m/');
				}

				if (get_setting('ucenter_enabled') == 'Y')
				{
					$sync_url = get_js_url('/account/sync_login/');
					$url = ($url) ? $sync_url . 'url-' . base64_encode($url) : $sync_url;
				}
			}
            /*用户登陆前置钩子*/
            run_hook('save_user_login_hook',['action'=>'after','data'=>$_POST,['uid'=>$user_info['uid']]]);

			H::ajax_json_output(AWS_APP::RSM(array(
				'url' => $url
			), 1, null));
		}
	}

	public function login_mobile_process_action()
	{
		if (get_setting('ucenter_enabled') == 'Y')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('该版本暂无uc用户对接服务请联系后台关闭该配置')));
		}
		else
		{
			$user_info = $this->model('account')->check_login_mobile($_POST['mobile']);
		}

		if (! $user_info)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确的号码或验证码')));
		}
		else
		{
			if ($user_info['forbidden'] == 1)
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('抱歉, 你的账号已经被禁止登录')));
			}

			if (get_setting('site_close') == 'Y' AND $user_info['group_id'] != 1)
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, get_setting('close_notice')));
			}

			hook('mobile_regist','login',array('mobile'=>$_POST['mobile'],'smscode'=>$_POST['smscode']));

			if (get_setting('register_valid_type') == 'approval' AND $user_info['group_id'] == 3)
			{
				$url = get_js_url('/account/valid_approval/');
			}
			else
			{
				if ($_POST['net_auto_login'])
				{
					$expire = 60 * 60 * 24 * 360;
				}

				$this->model('account')->update_user_last_login($user_info['uid']);
				$this->model('account')->logout();

				$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $user_info['password'], $user_info['salt'], $expire ,false);

				if (get_setting('register_valid_type') == 'email' AND !$user_info['valid_email'])
				{
					AWS_APP::session()->valid_email = $user_info['email'];

					$url = get_js_url('/account/valid_email/');
				}
				else if ($user_info['is_first_login'] AND !$_POST['_is_mobile'])
				{
					$url = get_js_url('/home/first_login-TRUE');
				}
				else if ($_POST['return_url'] AND !strstr($_POST['return_url'], '/logout') AND
					($_POST['_is_mobile'] AND strstr($_POST['return_url'], '/m/') OR
					strstr($_POST['return_url'], '://') AND strstr($_POST['return_url'], base_url())))
				{
					$url = get_js_url($_POST['return_url']);
				}
				else if ($_POST['_is_mobile'])
				{
					$url = get_js_url('/m/');
				}

				if (get_setting('ucenter_enabled') == 'Y')
				{
					$sync_url = get_js_url('/account/sync_login/');

					$url = ($url) ? $sync_url . 'url-' . base64_encode($url) : $sync_url;
				}
			}

			H::ajax_json_output(AWS_APP::RSM(array(
				'url' => $url
			), 1, null));
		}
	}

	public function register_agreement_action()
	{
		H::ajax_json_output(AWS_APP::RSM(null, 1, nl2br(get_setting('register_agreement'))));
	}

	public function welcome_message_template_action()
	{
		TPL::assign('job_list', $this->model('work')->get_jobs_list());
		TPL::output('account/ajax/welcome_message_template');
	}

	public function welcome_get_topics_action()
	{
		if ($topics_list = $this->model('topic')->get_topic_list(null, 'RAND()', 8))
		{
			foreach ($topics_list as $key => $topic)
			{
				$topics_list[$key]['has_focus'] = $this->model('topic')->has_focus_topic($this->user_id, $topic['topic_id']);
			}
		}
		TPL::assign('topics_list', $topics_list);
		TPL::output('account/ajax/welcome_get_topics');
	}

	public function welcome_get_users_action()
	{
		if ($welcome_recommend_users = trim(rtrim(get_setting('welcome_recommend_users'), ',')))
		{
			$welcome_recommend_users = explode(',', $welcome_recommend_users);
			$users_list = $this->model('account')->get_users_list("user_name IN('" . implode("','", $welcome_recommend_users) . "')", 6, true, true, 'RAND()');
		}

		if (!$users_list)
		{
			$users_list = $this->model('account')->get_activity_random_users(6);
		}

		if ($users_list)
		{
			foreach ($users_list as $key => $val)
			{
				$users_list[$key]['follow_check'] = $this->model('follow')->user_follow_check($this->user_id, $val['uid']);
			}
		}

		TPL::assign('users_list', $users_list);

		TPL::output('account/ajax/welcome_get_users');
	}

	public function clean_first_login_action()
	{
		$this->model('account')->clean_first_login($this->user_id);
		die('success');
	}

	public function delete_draft_action()
	{
		if (!$_POST['type'])
		{
			die;
		}

		if ($_POST['type'] == 'clean')
		{
			$this->model('draft')->clean_draft($this->user_id);
		}
		else
		{
			$this->model('draft')->delete_draft($_POST['item_id'], $_POST['type'], $this->user_id);
		}

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	public function save_draft_action()
	{
	    if($_POST['message'])
        {
            $_POST['message'] = htmlspecialchars($_POST['message']);
        }
		$this->model('draft')->save_draft($_GET['item_id'], $_GET['type'], $this->user_id, $_POST);
		H::ajax_json_output(AWS_APP::RSM(null, 1, AWS_APP::lang()->_t('已保存草稿, %s', date('H:i:s', time()))));
	}

	public function modify_unvalid_email_action()
	{
		if (!$user_info = $this->model('account')->get_user_info_by_email(AWS_APP::session()->valid_email))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户不存在')));
		}

		if ($user_info['valid_email'] == 1)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('不允许已认证邮箱用户更改邮箱')));
		}
		if (!trim($_POST['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邮箱不能为空')));
		}
		if (! H::valid_email($_POST['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邮箱格式不正确')));
		}
		if ($this->model('account')->check_email($_POST['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邮箱地址已被使用')));
		}

		$this->model('account')->update_users_fields(array(
			'email' => strtolower($_POST['email'])
		), $user_info['uid']);

		$this->model('active')->new_valid_email($this->user_id);

		AWS_APP::session()->valid_email = strtolower($_POST['email']);

		H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邮箱更改成功, 请前往邮箱接收验证邮件')));
	}

	public function send_valid_mail_action()
	{
		if (!$this->user_id)
		{
			if ( H::valid_email(AWS_APP::session()->valid_email))
			{
				$this->user_info = $this->model('account')->get_user_info_by_email(AWS_APP::session()->valid_email);
				$this->user_id = $this->user_info['uid'];
			}
		}

		if (! H::valid_email($this->user_info['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('错误, 用户没有提供 E-mail')));
		}

		if ($this->user_info['valid_email'] == 1)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户邮箱已经认证')));
		}

		$this->model('active')->new_valid_email($this->user_id);

		H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('邮件发送成功')));
	}

	public function valid_email_active_action()
	{
		if (!$active_data = $this->model('active')->get_active_code($_POST['active_code'], 'VALID_EMAIL'))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('激活失败, 无效的链接')));
		}

		if ($active_data['active_time'] OR $active_data['active_ip'])
		{
			H::ajax_json_output(AWS_APP::RSM(array(
				'url' => get_js_url('/account/login/'),
			), 1, null));
		}

		if (!$user_info = $this->model('account')->get_user_info_by_uid($active_data['uid']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('激活失败, 无效的链接')));
		}

		if ($user_info['valid_email'])
		{
			H::ajax_json_output(AWS_APP::RSM(array(
				'url' => get_js_url('/account/login/'),
			), 1, null));
		}

		if ($this->model('active')->active_code_active($_POST['active_code'], 'VALID_EMAIL'))
		{
			if (AWS_APP::session()->valid_email)
			{
				unset(AWS_APP::session()->valid_email);
			}

			$this->model('active')->set_user_email_valid_by_uid($user_info['uid']);

			if (get_setting('register_valid_type') == 'email' OR get_setting('register_valid_type') == 'N')
			{
				if ($user_info['group_id'] == 3)
				{
					$this->model('active')->active_user_by_uid($user_info['uid']);
				}

				// 帐户激活成功，切换为登录状态跳转至首页
				$this->model('account')->logout();

				$this->model('account')->update_user_last_login($user_info['uid']);

				$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $user_info['password'], $user_info['salt'], null, false);
			}

			$this->model('account')->welcome_message($user_info['uid'], $user_info['user_name'], $user_info['email']);

			if (get_setting('register_valid_type') == 'email' OR get_setting('register_valid_type') == 'N')
			{
				$url = $user_info['is_first_login'] ? '/first_login-TRUE' : '/';

				H::ajax_json_output(AWS_APP::RSM(array(
					'url' => get_js_url($url)
				), 1, null));
			}
			else
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('激活成功, 请等待管理员审核账户')));
			}
		}
	}

	public function request_find_password_action()
	{
		if (!H::valid_email($_POST['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('请填写正确的邮箱地址')));
		}
		if(!$this->model('tools')->geetest($_POST)){
        	H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('验证码错误')));
        }
		if (!$user_info = $this->model('account')->get_user_info_by_email($_POST['email']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('邮箱地址错误或帐号不存在')));
		}

		$this->model('active')->new_find_password($user_info['uid']);

		AWS_APP::session()->find_password = $user_info['email'];

		if (is_mobile())
		{
			$url = get_js_url('/m/account/find_password_success/');
		}
		else
		{
			$url = get_js_url('/account/find_password/process_success/');
		}

		H::ajax_json_output(AWS_APP::RSM(array(
			'url' => $url
		), 1, null));
	}

    public function first_find_password_action()
    {
        hook('mobile_regist','find_password',array('mobile'=>$_POST['mobile'],'smscode'=>$_POST['smscode']));
       if (is_mobile())
       {
            if (!$user_info = $this->model('account')->get_user_info_by_mobile($_POST['mobile']))
            {
                H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('帐号不存在')));
            }
            $key=$this->model('active')->new_find_password($user_info['uid'],'master','mobile');
            $url = get_js_url('/m/account/find_password_modify/mobile-'.$_POST['mobile'].'__type-mobile__key-'.$key);
       } else {
            $_SESSION['find_password_mobile'] = $_POST['mobile'];
            $url = get_js_url('/account/find_password/next_find_password/mobile-'.$_POST['mobile'].'__type-mobile');
       }
        H::ajax_json_output(AWS_APP::RSM(array(
            'url' => $url
        ), 1, null));
    }

	public function find_password_modify_action()
	{
		if (!$_POST['password'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('请输入密码')));
		}

		/*对js加密的密码进行解密*/
		$_POST['password'] = online_decrypt($_POST['password']);
		$_POST['re_password'] = online_decrypt($_POST['re_password']);

        if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入6-16位的新密码')));
        }
		if ($_POST['password'] != $_POST['re_password'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('两次输入的密码不一致')));
		}
		/*if (!trim($_POST['seccode_verify']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1',  AWS_APP::lang()->_t('验证码不能为空')));
		}		if (!AWS_APP::captcha()->is_validate($_POST['seccode_verify']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('请填写正确的验证码')));
		}*/
		if(!$this->model('tools')->geetest($_POST)){
        	H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('验证码错误')));
        }
		$active_data = $this->model('active')->get_active_code($_POST['active_code'], 'FIND_PASSWORD');

		if ($active_data)
		{
			if ($active_data['active_time'] OR $active_data['active_ip'])
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('链接已失效，请重新找回密码')));
			}
		}
		else
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('链接已失效，请重新找回密码')));
		}


		if (! $uid = $this->model('active')->active_code_active($_POST['active_code'], 'FIND_PASSWORD'))
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1,  AWS_APP::lang()->_t('链接已失效，请重新找回密码')));
		}

		$user_info = $this->model('account')->get_user_info_by_uid($uid);

		$this->model('account')->update_user_password_ingore_oldpassword($_POST['password'], $uid, $user_info['salt']);

		$this->model('active')->set_user_email_valid_by_uid($user_info['uid']);

		if ($user_info['group_id'] == 3)
		{
			$this->model('active')->active_user_by_uid($user_info['uid']);
		}

		$this->model('account')->logout();

		unset(AWS_APP::session()->find_password);

		H::ajax_json_output(AWS_APP::RSM(array(
			'url' => get_js_url('/account/login/'),
		), 1, AWS_APP::lang()->_t('密码修改成功, 请返回登录')));
	}

    public function find_password_modify_final_action()
    {
        if (!$_POST['password']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入密码')));
        }

        /*对js加密的密码进行解密*/
		$_POST['password'] = online_decrypt($_POST['password']);
		$_POST['re_password'] = online_decrypt($_POST['re_password']);
		
        if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入6-16位的新密码')));
        }

        if ($_POST['password'] != $_POST['re_password']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('两次输入的密码不一致')));
        }
        /*if (!AWS_APP::captcha()->is_validate($_POST['seccode_verify'])) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请填写正确的验证码')));
        }*/
        if(!$this->model('tools')->geetest($_POST)){
        	H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('验证码错误')));
        }
        $user_info = $this->model('account')->get_user_info_by_mobile($_SESSION['find_password_mobile']);

        $res = $this->model('account')->update_user_password_ingore_oldpassword($_POST['password'], $user_info['uid'], $user_info['salt']);

        if ($user_info['group_id'] == 3) {
            $this->model('active')->active_user_by_uid($user_info['uid']);
        }

        $this->model('account')->logout();

        H::ajax_json_output(AWS_APP::RSM(array(
            'url' => get_js_url('/account/login/'),
        ), 1, AWS_APP::lang()->_t('密码修改成功, 请返回登录')));
    }

	public function avatar_upload_action()
	{
        if(get_hook_info('osd')['state']==1 and get_plugins_config('osd')['base']['status']!='no')
        {
            $ret=hook('osd','upload_files',['cat'=>'avatar','field'=>'aws_upload_file']);
            $this->model('account')->update('users',['avatar_file'=>$ret['pic']],"uid=".$this->user_id);
            echo htmlspecialchars(json_encode(array(
                'success' => true,
                'thumb' => $ret['pic']
            )), ENT_NOQUOTES);
        }else{
            AWS_APP::upload()->initialize(array(
                'allowed_types' => 'jpg,jpeg,png,gif',
                'upload_path' => get_setting('upload_dir') . '/avatar/' . $this->model('account')->get_avatar($this->user_id, '', 1),
                'is_image' => TRUE,
                'max_size' => get_setting('upload_avatar_size_limit'),
                'file_name' => $this->model('account')->get_avatar($this->user_id, '', 2),
                'encrypt_name' => FALSE
            ))->do_upload('aws_upload_file');

            if (AWS_APP::upload()->get_error())
            {
                switch (AWS_APP::upload()->get_error())
                {
                    default:
                        die("{'error':'错误代码: " . AWS_APP::upload()->get_error() . "'}");
                    break;

                    case 'upload_invalid_filetype':
                        die("{'error':'文件类型无效'}");
                    break;

                    case 'upload_invalid_filesize':
                        die("{'error':'文件尺寸过大, 最大允许尺寸为 " . get_setting('upload_avatar_size_limit') .  " KB'}");
                    break;
                }
            }

            if (! $upload_data = AWS_APP::upload()->data())
            {
                die("{'error':'上传失败, 请与管理员联系'}");
            }

            if ($upload_data['is_image'] == 1)
            {
                foreach(AWS_APP::config()->get('image')->avatar_thumbnail AS $key => $val)
                {
                    $thumb_file[$key] = $upload_data['file_path'] . $this->model('account')->get_avatar($this->user_id, $key, 2);

                    AWS_APP::image()->initialize(array(
                        'quality' => 90,
                        'source_image' => $upload_data['full_path'],
                        'new_image' => $thumb_file[$key],
                        'width' => $val['w'],
                        'height' => $val['h']
                    ))->resize();
                }
            }

            $update_data['avatar_file'] = $this->model('account')->get_avatar($this->user_id, null, 1) . basename($thumb_file['min']);

            // 更新主表
            $this->model('account')->update_users_fields($update_data, $this->user_id);

            if (!$this->model('integral')->fetch_log($this->user_id, 'UPLOAD_AVATAR'))
            {
                $this->model('integral')->process($this->user_id, 'UPLOAD_AVATAR', round((get_setting('integral_system_config_profile') * 0.2)), '上传头像');
            }
            echo htmlspecialchars(json_encode(array(
                'success' => true,
                'thumb' => get_setting('upload_url') . '/avatar/' . $this->model('account')->get_avatar($this->user_id, null, 1) . basename($thumb_file['max'])
            )), ENT_NOQUOTES);
        }
	}

    public function add_edu_action()
	{
		$school_name = htmlspecialchars($_POST['school_name']);
		$education_years = intval($_POST['education_years']);
		$departments = htmlspecialchars($_POST['departments']);
		if (!$_POST['school_name'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入学校名称')));
		}
		if (!$_POST['departments'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入院系')));
		}

		if ($_POST['education_years'] == AWS_APP::lang()->_t('请选择') OR !$_POST['education_years'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择入学年份')));
		}

		if (preg_match('/\//is', $_POST['school_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('学校名称不能包含 /')));
		}

		if (preg_match('/\//is', $_POST['departments']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('院系名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y')
		{
			$this->model('topic')->save_topic($_POST['school_name']);
			$this->model('topic')->save_topic($_POST['departments']);
		}

		$edu_id = $this->model('education')->add_education_experience($this->user_id, $school_name, $education_years, $departments);

		if (!$this->model('integral')->fetch_log($this->user_id, 'UPDATE_EDU'))
		{
			$this->model('integral')->process($this->user_id, 'UPDATE_EDU', round((get_setting('integral_system_config_profile') * 0.2)), AWS_APP::lang()->_t('完善教育经历'));
		}

		H::ajax_json_output(AWS_APP::RSM(array(
			'id' => $edu_id
		), 1, null));

	}

    public function remove_edu_action()
	{
		$this->model('education')->del_education_experience($_POST['id'], $this->user_id);
		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	function add_work_action()
	{
		if (!$_POST['company_name'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入公司名称')));
		}
		if (!$_POST['job_id'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择职位')));
		}

		if (!$_POST['start_year'] OR !$_POST['end_year'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择工作时间')));
		}

		if (preg_match('/\//is', $_POST['company_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('公司名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y')
		{
			$this->model('topic')->save_topic($_POST['company_name']);
		}

		$work_id = $this->model('work')->add_work_experience($this->user_id, $_POST['start_year'], $_POST['end_year'], $_POST['company_name'], $_POST['job_id']);

		if (!$this->model('integral')->fetch_log($this->user_id, 'UPDATE_WORK'))
		{
			$this->model('integral')->process($this->user_id, 'UPDATE_WORK', round((get_setting('integral_system_config_profile') * 0.2)), AWS_APP::lang()->_t('完善工作经历'));
		}

		H::ajax_json_output(AWS_APP::RSM(array(
			'id' => $work_id
		), 1, null));
	}

	function remove_work_action()
	{
		$this->model('work')->del_work_experience($_POST['id'], $this->user_id);
		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	//修改教育经历
	function edit_edu_action()
	{
		if (!$_POST['school_name'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入学校名称')));
		}

		if (!$_POST['departments'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入院系')));
		}

		if (!$_POST['education_years'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择入学年份')));
		}

		$update_data['school_name'] = htmlspecialchars($_POST['school_name']);
		$update_data['education_years'] = intval($_POST['education_years']);
		$update_data['departments'] = htmlspecialchars($_POST['departments']);

		if (preg_match('/\//is', $_POST['school_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('学校名称不能包含 /')));
		}

		if (preg_match('/\//is', $_POST['departments']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('院系名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y')
		{
			$this->model('topic')->save_topic($_POST['school_name']);
			$this->model('topic')->save_topic($_POST['departments']);
		}

		$this->model('education')->update_education_experience($update_data, $_GET['id'], $this->user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	//修改工作经历
	function edit_work_action()
	{
		if (!$_POST['company_name'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入公司名称')));
		}

		if (!$_POST['job_id'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择职位')));
		}

		if (!$_POST['start_year'] OR !$_POST['end_year'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请选择工作时间')));
		}

		$update_data['job_id'] = intval($_POST['job_id']);
		$update_data['company_name'] = htmlspecialchars($_POST['company_name']);

		$update_data['start_year'] = intval($_POST['start_year']);
		$update_data['end_year'] = intval($_POST['end_year']);

		if (preg_match('/\//is', $_POST['company_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('公司名称不能包含 /')));
		}

		if (get_setting('auto_create_social_topics') == 'Y')
		{
			$this->model('topic')->save_topic($_POST['company_name']);
		}

		$this->model('work')->update_work_experience($update_data, $_GET['id'], $this->user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	public function privacy_setting_action()
	{
		if ($notify_actions = $this->model('notify')->notify_action_details)
		{
			$notification_setting = array();

			foreach ($notify_actions as $key => $val)
			{
				if (! isset($_POST['notification_settings'][$key]) AND $val['user_setting'])
				{
					$notification_setting[] = intval($key);
				}
			}
		}

		$email_settings = array(
			'FOLLOW_ME' => 'N',
			'QUESTION_INVITE' => 'N',
			'NEW_ANSWER' => 'N',
			'NEW_MESSAGE' => 'N',
			'QUESTION_MOD' => 'N',
		);

		if ($_POST['email_settings'])
		{
			foreach ($_POST['email_settings'] AS $key => $val)
			{
				unset($email_settings[$val]);
			}
		}



		$weixin_settings = array(
			'AT_ME' => 'N',
			'NEW_ANSWER' => 'N',
			'NEW_ARTICLE_COMMENT',
			'NEW_COMMENT' => 'N',
			'QUESTION_INVITE' => 'N'
		);

		if ($_POST['weixin_settings'])
		{
			foreach ($_POST['weixin_settings'] AS $key => $val)
			{
				unset($weixin_settings[$val]);
			}
		}

		$this->model('account')->update_users_fields(array(
			'email_settings' => serialize($email_settings),
			'weixin_settings' => serialize($weixin_settings),
			'weibo_visit' => intval($_POST['weibo_visit']),
			'inbox_recv' => intval($_POST['inbox_recv'])
		), $this->user_id);

		$this->model('account')->update_notification_setting_fields($notification_setting, $this->user_id);

		H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('隐私设置保存成功')));
	}

	public function profile_setting_action()
	{
		if (!$this->user_info['user_name'] OR $this->user_info['user_name'] == $this->user_info['email'] AND $_POST['user_name'])
		{
			$update_data['user_name'] = htmlspecialchars(trim($_POST['user_name']));

			if ($check_result = $this->model('account')->check_username_char($_POST['user_name']))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', $check_result));
			}
		}
		if ($_POST['url_token'] AND $_POST['url_token'] != $this->user_info['url_token'])
		{
			if ($this->user_info['url_token_update'] AND $this->user_info['url_token_update'] > (time() - 3600 * 24 * 30))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('你距离上次修改个性网址未满 30 天')));
			}

			if (!preg_match("/^(?!__)[a-zA-Z0-9_]+$/i", $_POST['url_token']))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('个性网址只允许输入英文或数字')));
			}

			if ($this->model('account')->check_url_token($_POST['url_token'], $this->user_id))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('个性网址已经被占用请更换一个')));
			}

			if (preg_match("/^[\d]+$/i", $_POST['url_token']))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('个性网址不允许为纯数字')));
			}

			$this->model('account')->update_url_token($_POST['url_token'], $this->user_id);
		}
		if ($update_data['user_name'] and $this->model('account')->check_username($update_data['user_name']) and $this->user_info['user_name'] != $update_data['user_name'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('已经存在相同的姓名, 请重新填写')));
		}
		if(isset($_POST['consult_price'])){
			if( (get_hook_config('consult')['consult_plugin_enable']['value'] == 'Y' && !is_numeric($_POST['consult_price'])) || $_POST['consult_price']<0){
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('咨询单价填写有误，请重新填写')));
			}

			if(get_hook_config('consult')['consult_plugin_enable']['value'] == 'Y'){
				$update_attrib_data['consult_price'] = $_POST['consult_price'];
			}
		}
		if($_POST['common_email'] && H::valid_email($_POST['common_email']))
        {
            $update_data['common_email'] = $_POST['common_email'];
        }
		$update_data['sex'] = intval($_POST['sex']);
		$update_data['province'] = htmlspecialchars($_POST['province']);
		$update_data['city'] = htmlspecialchars($_POST['city']);
		if ($_POST['birthday_y'])
		{
			$update_data['birthday'] = intval(strtotime(intval($_POST['birthday_y']) . '-' . intval($_POST['birthday_m']) . '-' . intval($_POST['birthday_d'])));
		}

		if (!$this->user_info['verified'])
		{
			$update_attrib_data['signature'] = htmlspecialchars($_POST['signature']);
		}
		if($_POST['introduction'])
        {
            $update_attrib_data['introduction'] = htmlspecialchars($_POST['introduction']);
        }
		$update_data['job_id'] = intval($_POST['job_id']);

		if ($_POST['signature'] AND !$this->model('integral')->fetch_log($this->user_id, 'UPDATE_SIGNATURE'))
		{
			$this->model('integral')->process($this->user_id, 'UPDATE_SIGNATURE', round((get_setting('integral_system_config_profile') * 0.1)), AWS_APP::lang()->_t('完善一句话介绍'));
		}

		$update_attrib_data['qq'] = htmlspecialchars($_POST['qq']);
		$update_attrib_data['homepage'] = htmlspecialchars($_POST['homepage']);
		if($_POST['mobile'])
		{
			if(!preg_match("/^1[345789]\d{9}$/", $_POST['mobile']))
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('手机号码格式不正确')));
			$update_data['mobile'] = htmlspecialchars($_POST['mobile']);
		}
		if (($update_attrib_data['qq'] OR $update_attrib_data['homepage'] OR $update_data['mobile']) AND !$this->model('integral')->fetch_log($this->user_id, 'UPDATE_CONTACT'))
		{
			$this->model('integral')->process($this->user_id, 'UPDATE_CONTACT', round((get_setting('integral_system_config_profile') * 0.1)), AWS_APP::lang()->_t('完善联系资料'));
		}

		if (get_setting('auto_create_social_topics') == 'Y')
		{
			if ($_POST['city'])
			{
				$this->model('topic')->save_topic($_POST['city']);
			}

			if ($_POST['province'])
			{
				$this->model('topic')->save_topic($_POST['province']);
			}
		}

		run_hook('save_user_profile_hook',['data'=>$_POST]);
		// 更新主表
		$this->model('account')->update_users_fields($update_data, $this->user_id);
		// 更新从表
		$this->model('account')->update_users_attrib_fields($update_attrib_data, $this->user_id);
		$this->model('account')->set_default_timezone($_POST['default_timezone'], $this->user_id);
		H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('个人资料保存成功')));
	}

	public function modify_password_action()
	{
		if (!$_POST['old_password'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入当前密码')));
		}

		if ($_POST['password'] != $_POST['re_password'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('两次输入的密码不一致')));
		}

		if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16)
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入6-16位的新密码')));
		}

		if (get_setting('ucenter_enabled') == 'Y')
		{
			if ($this->model('ucenter')->is_uc_user($this->user_info['email']))
			{
				$result = $this->model('ucenter')->user_edit($this->user_id, $this->user_info['user_name'], $_POST['old_password'], $_POST['password']);

				if ($result !== 1)
				{
					H::ajax_json_output(AWS_APP::RSM(null, -1, $result));
				}
			}
		}

		if ($this->model('account')->update_user_password($_POST['old_password'], $_POST['password'], $this->user_id, $this->user_info['salt']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('密码修改成功, 请牢记新密码')));
		}
		else
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入正确的当前密码')));
		}
	}

	public function integral_log_action()
	{
        if(!is_mobile())
        {
            $_GET['page'] = intval($_GET['page'])+1;
        }
	    $page = calc_page_limit(intval($_GET['page']) ,10);
	    $page = explode(',',$page);
		if ($log = $this->model('integral')->fetch_all('integral_log', 'uid = ' . $this->user_id, 'time DESC,integral ASC',$page[0],$page[1] ))
		{
			foreach ($log AS $key => $val)
			{
				$parse_items[$val['id']] = array(
					'item_id' => $val['item_id'],
					'action' => $val['action']
				);
			}
			TPL::assign('log', $log);
			TPL::assign('log_detail', $this->model('integral')->parse_log_item($parse_items));
		}

		if(is_mobile())
        {
            $total = $this->model('integral')->count('integral_log','uid = ' . $this->user_id);
            TPL::assign('total',ceil($total /10));
            TPL::output('m/ajax/integral_log');
        }else{
            TPL::output('account/ajax/integral_log');
        }
	}

	public function verify_action()
	{
		if ($this->is_post() AND !$this->user_info['verified'])
		{
			if (trim($_POST['name']) == '')
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入真实姓名或企业名称')));
			}

			if (trim($_POST['reason']) == '')
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入申请认证说明')));
			}
			if ($_FILES['attach']['name'])
			{
				AWS_APP::upload()->initialize(array(
					'allowed_types' => 'jpg,png,gif',
					'upload_path' => get_setting('upload_dir') . '/verify',
					'is_image' => FALSE,
					'encrypt_name' => TRUE
				))->do_upload('attach');

				if (AWS_APP::upload()->get_error())
				{
					switch (AWS_APP::upload()->get_error())
					{
						default:
							H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('错误代码') . ': ' . AWS_APP::upload()->get_error()));
						break;

						case 'upload_invalid_filetype':
							H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('文件类型无效')));
						break;
					}
				}
				if (! $upload_data = AWS_APP::upload()->data())
				{
					H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('上传失败, 请与管理员联系')));
				}
			}
			$verify_id = $this->model('verify')->add_apply($this->user_id, $_POST['name'], $_POST['reason'], $_POST['type'], array(
				'id_code' => htmlspecialchars($_POST['id_code']),
				'contact' => htmlspecialchars($_POST['contact'])
			), basename($upload_data['full_path']));

			$recipient_uid = get_setting('report_message_uid') ? get_setting('report_message_uid') : 1;
			$message = AWS_APP::lang()->_t('有新的认证请求, 请登录后台查看处理: %s', get_js_url('/admin/user/verify_approval_list/'));
			$this->model('notify')->send(0, $recipient_uid, notify_class::TYPE_USER_VERIFY, notify_class::CATEGORY_QUESTION,$verify_id,array(
				'title'=>$message,'from_uid'=>$recipient_uid
			));
		}
		H::ajax_json_output(AWS_APP::RSM(array('url'=>get_js_url('/account/setting/verify/')), 1, null));
	}

	public function clean_user_recommend_cache_action()
	{
		AWS_APP::cache()->delete('user_recommend_' . $this->user_id);
	}

	public function unbinding_weixin_action()
	{
		if (! $this->user_info['email'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('当前帐号没有绑定 Email, 不允许解除绑定')));
		}

		if (get_setting('register_type') == 'weixin')
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('当前系统设置不允许解除绑定')));
		}

		$this->model('openid_weixin_weixin')->weixin_unbind($this->user_id);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

	public function weixin_login_process_action()
	{
		if (!get_setting('weixin_app_id') OR !get_setting('weixin_app_secret') OR get_setting('weixin_account_role') != 'service')
		{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('当前微信公众号暂不支持此功能')));
		}

		if ($user_info = $this->model('openid_weixin_weixin')->weixin_login_process(session_id()))
		{
			$this->model('account')->setcookie_login($user_info['uid'], $user_info['user_name'], $user_info['password'], $user_info['salt'], null, false);

			H::ajax_json_output(AWS_APP::RSM(null, 1, null));
		}

		H::ajax_json_output(AWS_APP::RSM(null, -1, null));
	}

	public function complete_profile_action()
	{
		if ($this->user_info['email'])
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('当前帐号已经完善资料')));
		}

		$_POST['user_name'] = htmlspecialchars(trim($_POST['user_name']));

		if ($check_result = $this->model('account')->check_username_char($_POST['user_name']))
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', $check_result));
		}

		if ($this->user_info['user_name'] != $_POST['user_name'])
		{
			if ($this->model('account')->check_username_sensitive_words($_POST['user_name']) || $this->model('account')->check_username($_POST['user_name']))
			{
				H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户名已被注册')));
			}
		}

		$update_data['user_name'] = $_POST['user_name'];

		if (! H::valid_email($this->user_info['email']))
		{
			if (! H::valid_email($_POST['email']))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请输入正确的 E-Mail 地址')));
			}

			if ($this->model('account')->check_email($_POST['email']))
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('邮箱已经存在, 请使用新的邮箱')));
			}

			$update_data['email'] = $_POST['email'];

			$this->model('active')->new_valid_email($this->user_id, $_POST['email']);
		}

		$this->model('account')->update_users_fields($update_data, $this->user_id);

		$this->model('account')->update_user_password_ingore_oldpassword($_POST['password'], $this->user_id, $this->user_info['salt']);

		$this->model('account')->setcookie_login($this->user_info['uid'], $update_data['user_name'], $_POST['password'], $this->user_info['salt']);

		H::ajax_json_output(AWS_APP::RSM(null, 1, null));
	}

    //设置交易密码
    public function modify_trade_action()
    {
        $search = '/^0?1[3|4|5|6|7|8|9][0-9]\d{8}$/';
        if (!preg_match( $search, trim($_POST['mobile']) ) ) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请输入正确的手机号码')));
        }
        $this->model('tools')->checkSmsCode(trim($_POST['mobile']),trim($_POST['smscode']),1);
        if ($_POST['password'] != $_POST['re_password'])
        {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('两次输入的密码不一致')));
        }

        if (strlen($_POST['password']) < 6 OR strlen($_POST['password']) > 16)
        {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('密码长度6-16位')));
        }
        if ($this->model('account')->update_user_trade($_POST['mobile'], $_POST['password'], $this->user_id))
        {
            $this->model('account')->update('users',array('valid_mobile'=>1,'mobile'=>$_POST['mobile']),'uid='.$this->user_id);
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('交易密码修改成功, 请牢记新密码')));
        }
        else
        {
            H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('交易密码修改失败')));
        }
    }

    //提现记录
	public function withdraw_log_action()
	{
		if($this->user_id)
		{
			if ($log = $this->model('integral')->fetch_all('user_withdraw', 'uid = ' . $this->user_id, 'addtime DESC', (intval($_GET['page']) * 10) . ', 10'))
			{
				foreach ($log AS $key => $val)
				{
					$parse_items[$val['id']] = array(
						'order_id' => $val['order_id'],
						'type' => $val['type']
					);
				}
				TPL::assign('log', $log);
				TPL::assign('page', intval($_GET['page']));
			}
		}
		TPL::output('account/ajax/withdraw_log');
	}

	//内容审核记录
	public function approval_log_action()
	{
		if($this->user_id)
		{
			if ($log = $this->model('publish')->fetch_all('approval', 'uid = ' . intval($this->user_id), 'time DESC', (intval($_GET['page']) * 10) . ', 10'))
			{
				foreach ($log AS $key => $val)
				{
					$data = unserialize($val['data']);
					switch ($val['type']) {
						case 'answer':
							$log[$key]['type_text'] = '问题回复';
							$log[$key]['remarks'] = '#'.$data['question_id'].' '.html_entity_decode($data['answer_content']);
							break;
						case 'article':
							$log[$key]['type_text'] = '文章';
							$log[$key]['remarks'] = $data['title'];
							break;
						case 'article_comment':
							$log[$key]['type_text'] = '文章评论';
							$log[$key]['remarks'] = '#'.$data['article_id'].' '.html_entity_decode($data['message']);
							break;
						default:
							$log[$key]['type_text'] = '问题';
							$log[$key]['remarks'] = $data['question_content'];
							break;
					}
				}
				TPL::assign('log', $log);
				TPL::assign('page', intval($_GET['page']));
			}
		}
		TPL::output('account/ajax/approval_log');
	}

	//审核内容预览
    public function preview_action()
    {
        switch ($_GET['type'])
        {
            case 'weibo_msg':
                if (get_setting('weibo_msg_enabled') != 'question')
                {
                    H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('导入微博消息至问题未启用')));
                }

                $approval_item = $this->model('openid_weibo_weibo')->get_msg_info_by_id($_GET['id']);

                if ($approval_item['question_id'])
                {
                    exit();
                }

                $approval_item['type'] = 'weibo_msg';

                break;

            case 'received_email':
                $receiving_email_global_config = get_setting('receiving_email_global_config');

                if ($receiving_email_global_config['enabled'] != 'question')
                {
                    H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('导入邮件至问题未启用')));
                }

                $approval_item = $this->model('edm')->get_received_email_by_id($_GET['id']);

                if ($approval_item['question_id'])
                {
                    exit();
                }

                $approval_item['type'] = 'received_email';

                break;

            default:
                $approval_item = $this->model('publish')->get_approval_item($_GET['id']);

                break;
        }

        if (!$approval_item)
        {
            exit();
        }

        switch ($approval_item['type'])
        {
            case 'question':
                $approval_item['title'] = htmlspecialchars($approval_item['data']['question_content']);

                $approval_item['content'] = htmlspecialchars($approval_item['data']['question_detail']);

                $approval_item['topics'] = htmlspecialchars(implode(',', $approval_item['data']['topics']));

                break;

            case 'answer':
                $approval_item['content'] = htmlspecialchars($approval_item['data']['answer_content']);

                break;

            case 'article':
                $approval_item['title'] = htmlspecialchars($approval_item['data']['title']);

                $approval_item['content'] = htmlspecialchars($approval_item['data']['message']);

                break;

            case 'article_comment':
                $approval_item['content'] = htmlspecialchars($approval_item['data']['message']);

                break;

            case 'weibo_msg':
                $approval_item['content'] = htmlspecialchars($approval_item['text']);

                if ($approval_item['has_attach'])
                {
                    $approval_item['attachs'] = $this->model('publish')->get_attach('weibo_msg', $_GET['id']);
                }

                break;

            case 'received_email':
                $approval_item['title'] = htmlspecialchars($approval_item['subject']);

                $approval_item['content'] = htmlspecialchars($approval_item['content']);

                break;
        }

        if ($approval_item['data']['attach_access_key'])
        {
            $approval_item['attachs'] = $this->model('publish')->get_attach_by_access_key($approval_item['type'], $approval_item['data']['attach_access_key']);
        }

        if ($_GET['action'] != 'edit')
        {
            $approval_item['content'] = html_entity_decode(nl2br(FORMAT::parse_bbcode($approval_item['content'])));
        }
        TPL::assign('approval_item', $approval_item);
        TPL::output('account/ajax/preview');
    }

    //提现申请
	public function apply_withdraw_action()
	{
		if (!$this->user_id)
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请先登录')));
		}
		if (empty($_POST['username'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写持卡人名称')));
		}
		if (empty($_POST['mobile']) || strlen($_POST['mobile']) != 11){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写正确的11位银行预留手机号码')));
		}
		if (empty($_POST['bank'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写银行名称')));
		}
		if (empty($_POST['open_bank'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写开户行')));
		}
		if (empty($_POST['address'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写省市区')));
		}
		if (empty($_POST['identity'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写身份证')));
		}
		if (empty($_POST['card'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写卡号')));
		}
		if (!(float)$_POST['money']){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写提现金额')));
		}
		if (empty($_POST['pwd'])){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('请填写交易密码')));
		}
		
		$user = $this->model('account')->fetch_row('user_account','uid='.$this->user_id);
		if(compile_password($_POST['pwd'],$user['deal_salt']) != $user['deal_pwd']){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('交易密码不正确')));
		}
        $fee = get_plugins_config('reward')['withdraw']['charges_rate'];//手续费比列
        $num = get_plugins_config('reward')['withdraw']['withdraw_date'];//每周提现次数
        $min_money = get_plugins_config('reward')['withdraw']['withdraw_min'];//单次提现最小金额
        $max_money = get_plugins_config('reward')['withdraw']['withdraw_max'];//单次提现最大金额
		if($_POST['money'] < $min_money && $min_money)
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('单笔提现最小金额'.$min_money.'元')));
		}

		if($_POST['money'] > $max_money && $max_money){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('单笔提现最大金额'.$max_money.'元')));
		}
		if(get_setting('cash_method')==2){
	        if($num < $this->model('withdraw')->get_month_withdraw_info_by_uid($this->user_id) || $num == $this->model('withdraw')->get_month_withdraw_info_by_uid($this->user_id)){
	        	H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('每月最多提现'.$num.'次')));
	        }
        }

        $total = $_POST['money'] * (1+$fee);
        if($total > $user['balance']){
        	H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('余额不足')));
        }
        
        if($_POST['ssid'] != $_SESSION['ssid']) {
            H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('请不要重复提交')));
        }
        
        $datas = array(
        		'uid' => $this->user_id,
        		'bank' => $_POST['bank'],
        		'open_bank' => $_POST['open_bank'],
        		'username' => $_POST['username'],
        		'mobile' => $_POST['mobile'],
        		'address' => $_POST['address'],
        		'identity' => $_POST['identity'],
        		'card' => $_POST['card'],
        		'money' => $_POST['money'],
        		'fee' => $fee*$_POST['money'],
        		'status' => 0,
        		'addtime' => time()
        	);

		if($this->model('withdraw')->insert_withdraw_datas($datas)){
			$_SESSION['ssid'] = '';
			H::ajax_json_output(AWS_APP::RSM(null, 11, AWS_APP::lang()->_t('申请成功')));
		}else{
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('申请失败')));
		}
	}

    //开通会员
    public function pay_member_action()
    {   
        $config=get_hook_config('consult');
    	if (!$config['member_price']['value'] || !is_numeric($config['member_price']['value'])) {
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('暂未开通此功能')));
		}
		if($_POST['uid'] != $this->user_id){
			H::ajax_json_output(AWS_APP::RSM(null, -1, AWS_APP::lang()->_t('用户信息错误,请重新登陆')));
		}
		H::ajax_json_output(AWS_APP::RSM(['uid'=>$_POST['uid'],'price'=>$config['member_price']['value']], '1', AWS_APP::lang()->_t('开始启动')));
    }

    public function member_before_action()
	{   
		$mode = $_POST['pay_method'];
		switch($mode){
			case 1:
				$mode = 'alipay';
				break;
			case 2:
				$mode = 'wechat';
				break;
			default:
				$mode = 'yepay';
				break;
		}

		if($_POST['uid'] != $this->user_id){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('开通用户错误')));
		}

		if($user_info = $this->model('account')->get_user_info_by_uid($_POST['uid'])){
			if($user_info['user_member'] == 1)
			{
				H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('用户已为会员,暂未开通续费功能,请到期之后再进行操作')));
			}
		}else
		{
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('用户不存在')));
		}

		if(get_hook_config('consult')['member_price']['value'] != $_POST['price']){
			H::ajax_json_output(AWS_APP::RSM(null, '-1', AWS_APP::lang()->_t('支付金额不正确')));
		}

		$yue = $this->model('account')->fetch_one('user_account','balance','uid='.$this->user_id);
		$after = $mode == 'yepay'?($yue-$_POST['price']):$yue;
		$consume_data['uid'] = $this->user_id;
		$consume_data['consume_type'] = 'member_pay';
		$consume_data['mode'] = $mode;
		$consume_data['relation_type'] = 'member';
		$consume_data['relation_id'] = $_POST['uid'];
		$consume_data['consume_status'] = 'undone';
		$consume_data['amount'] = $_POST['price'];
		$consume_data['amount_before'] = $yue;
		$consume_data['amount_after'] = $after;
		$consume_data['remark'] = '开通会员';

		$info = $this->model('consume')->add_consume($consume_data);//生成流水
        
		$money = $mode == 'wechat'?$_POST['price']*100:$_POST['price'];
		$pay_data['uid'] = $_POST['uid'];
		$pay_data['driver']=$mode;
		$pay_data['gateway']='scan';
		$pay_data['out_trade_no']=$info['order_no'];
		$pay_data['money']=$money;
		$pay_data['remarks']='开通会员';
		$notify_url = 'http://'.$_SERVER['HTTP_HOST'].'/account/ajax/call_back/';
		$pay_data['notify_url']=$notify_url;
		$url = $this->model('pay_unipay')->dopay($pay_data);
		H::ajax_json_output(['img_url'=>$url,'order_id'=>$info['order_no'],'check_url'=>get_js_url('/account/ajax/check_status/')]);
	}

	/**
	 * 检测订单是否支付成功
	 */
	public function check_status_action()
	{
		$item_id = $_POST['order_id'];
		$info = $this->model('consume')->get_info_member_by_no($item_id);//查询流水
		if($info['consume_status']==1){
			$arr['url']=get_js_url('/people/'.$info['relation_id']);
			H::ajax_json_output(AWS_APP::RSM(null, 1, $arr));
		}
	}

	/**
	 * 支付完成回调
	 */
	public function call_back_action()
	{   
		if($_POST['trade_status'] == 'OK'){
			$zx_no = $_POST['trade_no'];//咨询流水号
			$update_time = $_POST['pay_time'];
		}else{
			$pay = $this->model('pay_unipay')->get_pay_obj();
			$arr = $pay->driver('alipay')->gateway()->verify($_POST);//支付宝或余额
			if($arr){//支付宝或余额
				if($arr['trade_status'] == 'TRADE_SUCCESS'){
					$zx_no = $arr['out_trade_no'];//咨询流水号
					$out_no = $arr['trade_no'];//支付宝订单号
					$update_time = strtotime($arr['gmt_payment']);
				}else{
					exit();
				}
			}else{//微信
				$arr = $pay->driver('wechat')->gateway('scan')->verify(file_get_contents('php://input'));
				if($arr['return_code'] == 'SUCCESS'){
					$zx_no = $arr['out_trade_no'];//咨询流水号
					$out_no = $arr['transaction_id'];//微信订单号
					$update_time = strtotime($arr['time_end']);
				}else{
					exit();
				}
			}
		}

		$consume = $this->model('consume')->get_info_member_by_no($zx_no);//流水数据
		if($consume['consume_status'] == 0){
			$this->model('consume')->update_consume($zx_no,['trade_no'=>$out_no,'consume_status'=>1,'update_time'=>$update_time]);//更新支付数据
		}else{
			exit();
		}

		$this->model('account')->update_member($consume['relation_id']);//更新用户数据
		$this->model('notify')->send(0, $consume['relation_id'], notify_class::TYPE_MEMBER_SUCCESS, notify_class::CATEGORY_MEMBER,$consume['relation_id'],array('consume_info'=>$consume,'from_uid'=>$consume['relation_id']));
	}

	public function synch_img_action()
    {
		$users=$this->model('account')->fetch_all('users','is_del=0 and ISNULL(avatar_file)','',1000);
		foreach ($users as $key => $value) {
			$wxuser=$this->model('account')->fetch_row('users_weixin','uid='.$value['uid'].' and headimgurl IS NOT NULL');
			if($wxuser){
				$this->model('account')->associate_remote_avatar($wxuser['uid'],$wxuser['headimgurl']);
			}
		}
	}
}
