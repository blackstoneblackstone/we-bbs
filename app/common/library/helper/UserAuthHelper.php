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
use app\common\library\helper\TreeHelper as TreeService;

class UserAuthHelper {
	/**
	 * @var object 对象实例
	 */
	protected static $instance;

	/**
	 * 当前请求实例
	 * @var $request
	 */
	protected $request;
	protected $rules = [];

	protected $error;

	/**
	 * 类架构函数
	 * AuthService constructor.
	 */
	public function __construct() {
		// 初始化request
		$this->request = request();
	}

    /**
     * 初始化
     * @return UserAuthHelper
     */
	public static function instance(): UserAuthHelper
    {
		if (is_null(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

    /**
     * 检测当前控制器和方法是否匹配传递的数组.
     * @param mixed $authArr 需要验证权限的数组
     * @return bool
     */
    public function match($authArr = null): bool
    {
        $authArr = is_array($authArr) ? $authArr : explode(',', $authArr);
        if (!$authArr) {
            return false;
        }
        $arr = array_map('strtolower', $authArr);
        // 是否存在
        return in_array(strtolower(request()->action()), $authArr) || in_array('*', $arr);
    }

    public function getNav($checkPath='',$menu_type='nav')
    {
        if(!$checkPath)
        {
            $checkPath = strtolower(request()->controller()).'/'.strtolower(request()->action());
        }

        $_menu =db('menu_rule')
            ->where(['status'=>1,'group'=>$menu_type])
            ->order(['sort'=>'asc','id'=>'asc'])
            ->select()
            ->toArray();
        if(!$_menu)
        {
            return false;
        }
        $menu = [];

        foreach ($_menu as $key => $val)
        {
            if($val['auth_open'] && !$this->getUserId())
            {
                unset($_menu[$key]);
                continue;
            }
            $menu[$val['id']] = $val;
        }

        $tree_menu = TreeService::instance()->init($menu);
        foreach ($menu as $key => $val)
        {
            if (strtolower($val['name']) == $checkPath)
            {
                $menu[$key]['active'] = 1;
                if($tree_menu->getParentsIds($val['id']))
                {
                    foreach ($tree_menu as $k=>$v)
                    {
                        $menu[$v]['active'] = 1;
                    }
                }
            }

            if($val['param']!='')
            {
                parse_str($val['param'],$output_array);
                $menu[$key]['url'] = (string)url($val['name'],$output_array);
            }else{
                $menu[$key]['url'] = (string)url($val['name']);
            }

            if($val['type']==2)
            {
                $menu[$key]['url'] = $val['name'];
            }
        }
        return TreeHelper::instance()->init($menu)->getTreeArray(0);
    }

    public function getUserId()
    {
        $uid = session('login_uid');
        if (!$uid) {
            $uid = 0;
        }
        return $uid;
    }

	/**
	 * 设置错误信息
	 * @param $error
	 */
	public function setError($error): void
    {
		$this->error = $error;
	}

	/**
	 * 获取错误信息
	 * @return mixed
	 */
	public function getError()
    {
		return $this->error;
	}
}
