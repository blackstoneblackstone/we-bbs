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
namespace app\mobile;

use app\common\controller\Frontend;
use app\common\library\builder\FormBuilder;
use app\common\library\helper\NotifyHelper;
use app\model\Users;
use app\model\Verify;
use think\App;

/**
 * 公用设置模块
 */
class Setting extends Frontend
{
    protected $needLogin=['index','profile','notify','inbox','security','openid','nav'];
    
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new Users();
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
	}

    public function index()
    {
        return $this->fetch();
    }

	/**
	 * 个人资料设置
	 */
	public function profile()
	{
		if($this->request->isPost())
		{
			$postData = $this->request->post();
			$result = Users::updateUserFiled($this->user_id,$postData);
			if(!$result)
			{
				$this->error('基本信息更新失败');
			}
			$this->success('更新成功');
		}
		return $this->fetch();
	}

	/**
	 * 通知设置
	 */
	public function notify()
	{
	    if($this->request->isPost())
        {
            $postData = $this->request->post();
            $setting_id = db('users_extends')->where('uid',$this->user_id)->value('id');
            if($setting_id)
            {
                $res = db('users_extends')->where(['id'=>$setting_id])->update([
                    'inbox_setting'=>$postData['inbox_setting'],
                    'notify_setting'=>json_encode($postData['notify_setting'],JSON_UNESCAPED_UNICODE),
                ]);
            }else{
                $res = db('users_extends')->insert([
                    'uid'=>$this->user_id,
                    'inbox_setting'=>$postData['inbox_setting'],
                    'notify_setting'=>json_encode($postData['notify_setting'],JSON_UNESCAPED_UNICODE),
                ]);
            }

            if($res)
            {
                $this->success('保存成功');
            }
            $this->error('保存失败');
        }

        $user_setting = db('users_extends')->where('uid',$this->user_id)->find();
	    if($user_setting)
        {
            $user_setting['notify_setting'] = json_decode($user_setting['notify_setting'],true);
        }
        $type = json_decode(db('config')->where(['name'=>'notify_type'])->value('option'),true);
	    $this->assign([
	        'notify_setting'=> NotifyHelper::getNotifyConfigItem(),
            'user_setting'=>$user_setting,
            'types'=>$type
        ]);
		return $this->fetch();
	}

	/**
	 * 私信设置
	 */
	public function inbox()
	{
		return $this->fetch();
	}

	/**
	 * 安全设置
	 */
	public function security()
	{
		return $this->fetch();
	}

	/**
	 * 账号绑定
	 */
	public function openid()
	{
        $config = get_plugins_config('third','base');
        if($config)
        {
            $third = db('third')->where(['uid' => $this->user_id])->column('platform,uid');
            $this->assign([
                'config'=>$config,
                'third'=>array_column($third,'platform')
            ]);
        }
		return $this->fetch();
	}

    /**
     * 账号认证
     */
	public function verified()
    {
        if($this->request->isPost()) {
            $params = $this->request->post();
            $id = $params['id'];
            if(!isset($params['type']))
            {
                $this->error('请选择认证类型!');
            }

            $type = $params['type'];
            unset($params['id'],$params['type']);
            $field = db('verify_field')->where(['verify_type'=>$type])->column('title,name');
            foreach ($field as $k=>$v)
            {
                if(removeEmpty($params[$v['name']])=='')
                {
                    $this->error($v['title'].'不能为空!');
                }
            }
            if(!$id)
            {
                db('users_verify')->insert(['create_time'=>time(),'type'=>$type,'data'=>json_encode($params),'status'=>1,'uid'=>intval($this->user_id),'reason'=>'']);
            }else{
                db('users_verify')->where('id',$id)->update(['type'=>$type,'data'=>json_encode($params),'status'=>1]);
            }

            $this->success('提交成功');
        }
        $verify_type = db('users_verify_type')->where(['status'=>1])->column('title','name');
        $this->assign([
            'info'=> db('users_verify')->where(['uid'=>intval($this->user_id)])->find(),
            'verify_type'=>$verify_type
        ]);
        return $this->fetch();
    }

	//设置导航
	private function nav()
	{
		$this->view->engine()->layout(false);
		return $this->fetch();
	}
}