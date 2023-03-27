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
use think\facade\Config;
use think\facade\Db;
use think\facade\Request;
use think\facade\Session;

class AuthHelper
{
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
	//默认配置
	protected $config = [
		'auth_on' => 1, // 权限开关
		'auth_type' => 1, // 认证方式，1为实时认证；2为登录认证。
		'auth_group' => 'admin_group', // 用户组数据表名
		//'auth_group_access' => 'admin_group_access', // 用户-用户组关系表
		'auth_rule' => 'admin_auth', // 权限规则表
		'auth_user' => 'users', // 用户信息表
		'auth_user_pk' => 'uid', // 用户表ID字段名
		'url' => '',
        'auth_rule_unified'=>0
	];

    protected $error;

	/**
	 * 类架构函数
	 * AuthService constructor.
	 */
	public function __construct() {

		//$this->auth_user = new Users();
		// 初始化request
		$this->request = request();
	}

    /**
     * 初始化
     * @return AuthHelper
     */
	public static function instance(): AuthHelper
    {
		if (is_null(self::$instance)) {
			self::$instance = new static();
		}
		return self::$instance;
	}

    /**
     * 检查权限
     * @param string|array $name 需要验证的规则列表，支持逗号分隔的权限规则或索引数组
     * @param int $uid 认证用户ID
     * @param int $type 规则类型
     * @param string $mode 执行check的模式
     * @param string $relation 如果为 'or' 表示满足任一条规则即通过验证;如果为 'and' 则表示需满足所有规则才能通过验证
     * @return boolean           通过验证返回true;失败返回false
     */
    public function check($name, int $uid,int $type = 1,string $mode = 'url',string $relation = 'or'): bool
    {
        if (!$this->config['auth_on']) {
            return true;
        }
        // 获取用户需要验证的所有有效规则列表
        $authList = $this->getAuthList($uid, $type);

        if (is_string($name)) {
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = [$name];
            }
        }
        $list = []; //保存验证通过的规则名
        if ('url' == $mode) {
            $REQUEST = $this->request->param();
        }

        foreach ($authList as $auth)
        {
            $query = preg_replace('/^.+\?/U', '', $auth);
            if ('url' == $mode && $query != $auth) {
                parse_str($query, $param); //解析规则中的param
                $intersect = array_intersect_assoc($REQUEST, $param);
                $auth = preg_replace('/\?.*$/U', '', $auth);
                if (in_array($auth, $name) && $intersect == $param) {
                    //如果节点相符且url参数满足
                    $list[] = $auth;
                }
            } else {
                if (in_array($auth, $name)) {
                    $list[] = $auth;
                }
            }
        }
        if ('or' == $relation && !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ('and' == $relation && empty($diff)) {
            return true;
        }
        return false;
    }

    /**
     * 根据用户ID获取用户组，返回值为数组
     * @param int $uid 用户ID
     * @return mixed      用户所属用户组 ['uid'=>'用户ID', 'group_id'=>'用户组ID', 'title'=>'用户组名', 'rules'=>'用户组拥有的规则ID，多个用英文,隔开']
     */
    public function getGroups(int $uid=0)
    {
        static $groups = [];
        if (isset($groups[$uid])) {
            return $groups[$uid];
        }
        $user_groups = db('users')
            ->alias('a')
            ->where('a.uid', intval($uid))
            ->join($this->config['auth_group'].' g', "a.group_id = g.id",'LEFT')
            ->where('g.status', 1)
            ->field('uid,group_id,title,rules')
            ->select();
        $groups[$uid] = $user_groups ?: [];
        return $groups[$uid];
    }

    /**
     * 获得权限列表
     * @param mixed $uid 用户ID
     * @param int $type 规则类型
     * @return array       权限列表
     */
    protected function getAuthList(int $uid = null,int $type = 1) {
        if (!$uid) {
            $uid = session('admin_login_uid');
        }

        static $_authList = []; //保存用户验证通过的权限列表
        $t = implode(',', (array) $type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }
        if (2 == $this->config['auth_type'] && session('_auth_list_' . $uid . $t)) {
            return session('_auth_list_' . $uid . $t);
        }
        //读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }

        $map=[];

        if (!in_array('*', $ids, true)) {
            $map[] = ['id', 'in', $ids];
        }

        if ($type != -1) {
            $map[] = ['type', '=', $type];
        }

        //读取用户组所有权限规则
        $rules = db($this->config['auth_rule'])->where($map)->field('condition,name,title')->select();
        //循环规则，判断结果。
        $authList = []; //
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user = $this->getUserInfo($uid); //获取用户信息,一维数组
                /*$command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                $condition = null;
                @(eval('$condition =(' . $command . ');'));*/
                $condition = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                if ($condition) {
                    $authList[] = $rule['name'];
                }
            } else {
                //只要存在就记录
                $authList[] = $rule['name'];
            }
        }
        $_authList[$uid . $t] = $authList;
        if (2 == $this->config['auth_type']) {
            //规则列表结果保存到session
            session('_auth_list_' . $uid . $t, $authList);
        }
        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库
     * @param $uid
     * @return mixed
     */
    protected function getUserInfo($uid) {
        static $userinfo = [];
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = db($this->config['auth_user'])->where('uid', $uid)->find();
        }
        unset($userinfo[$uid]['password']);
        return $userinfo[$uid];
    }

    /**
     * 获得面包导航
     * @param string $route
     * @return array
     */
    public function getBreadCrumb(string $route = ''): array
    {
        //当前URL

        $route = $route ?: Request::controller() . '/' . lcfirst(Request::action());

        //查找名称
        $data = db($this->config['auth_rule'])->where('name', '=', $route)->find();

        $result = [];
        if ($data) {
            $result[] = [
                'url'   => $data['name'],
                'title' => $data['title'],
                'icon'  => $data['icon'],
            ];
            //查找是否有上级别
            if ($data['pid']) {
                //查询上级url
                $route = db($this->config['auth_rule'])->where('id', '=', $data['pid'])->find();
                if($route)
                {
                    $crumb = $this->getBreadCrumb($route['name']);
                    foreach ($crumb as $k => $v) {
                        $result[] = [
                            'url'   => $v['url'],
                            'title' => $v['title'],
                            'icon'  => $v['icon']
                        ];
                    }
                }
            }
        } else if ($route == 'Index/index') {
            $result[] = [
                'url'   => 'Index/index',
                'title' => '后台首页',
                'icon'  => 'fa fa-dashboard',
            ];
        }
        return $result;
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

	/**
	 * 获取所有权限表
	 */
	public function getRule() {
		if (!$this->config['auth_rule_unified']) {
			$auth_rule = cache('login_auth_rule') ?: [];
			if (empty($auth_rule)) {
				$auth_rule = db($this->config['auth_rule'])->select()->toArray();
				cache('login_auth_rule', $auth_rule, 3600);
			}
		} else {
			$auth_rule = cache('login_auth_rule0') ?: [];
			if (empty($auth_rule)) {
				$auth_rule = db($this->config['auth_rule'])->select()->toArray();
				cache('login_auth_rule0', $auth_rule, 3600);
			}
		}

		return $auth_rule;
	}

	/**
	 * 获取用户所有权限
	 * @param int|null $uid 用户UID
	 * @return array 权限ID数组
	 */
	public function getRuleIds(int $uid = null): array
    {
		if (!$uid) {
			$uid = session('admin_login_uid');
		}

		//读取用户所属用户组
		$groups = $this->getGroups($uid);

		$ids = []; //保存用户所属用户组设置的所有权限规则id

		foreach ($groups as $g) {
			$ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
		}

		if (in_array('*', $ids, true)) {
			$ids = db($this->config['auth_rule'])->column('id');
		}
        return array_unique($ids);
	}

	/**
	 * 是否是超级管理员
	 * @return bool
	 */
	public function isSuperAdmin(): bool
    {
		return session('admin_user_info')['group_id']==1;
	}

	//获取用户权限数
	public function getRuleMenu($ruleIds, $menu = 1) {
		$map[] = ['id', 'in', $ruleIds];
        return db($this->config['auth_rule'])
            ->where($map)
            ->order(['sort'=>'DESC'])
            ->field('id,name,title,pid,icon,sort,status')
            ->select()->toArray();
	}

	//获取树形菜单
    public function getTreeMenu()
    {
        $controller = request()->controller();
        $actionName = request()->action();
        //检验权限地址
        $checkPath = $controller.'/'.strtolower($actionName);
        $plugin = request()->plugin;
        $_menu =db($this->config['auth_rule'])->where('status', 1)
            ->order(['sort'=>'asc','id'=>'asc'])
            ->select()
            ->toArray();
        $user_rule_list = $this->getAuthList();

        if(!$_menu)
        {
            return false;
        }
        $selected = [];
        foreach ($_menu as $key => $val)
        {
            if(!in_array($val['name'],$user_rule_list))
            {
                unset($_menu[$key]);
                continue;
            }

            //兼容插件后台检测
            if($plugin)
            {
                $checkPath = 'plugins/'.$plugin.'/'.$controller.'/'.strtolower($actionName);
            }

            if ($val['name'] == $checkPath) {
                $selected = $val;
            }

            if($val['param']!='')
            {
                parse_str($val['param'],$output_array);
                $_menu[$key]['url'] = (string)url($val['name'],$output_array);
            }else{
                $_menu[$key]['url'] = (string)url($val['name']);
            }

            //兼容插件后台
            if((strstr($val['name'],'plugins/')))
            {
                if($val['param']!='')
                {
                    parse_str($val['param'],$output_array);
                    $_menu[$key]['url'] = (string)url('/'.$val['name'],$output_array);
                }else{
                    $_menu[$key]['url'] = (string)url('/'.$val['name']);
                }
            }
        }

        $select_id = $selected ? $selected['id'] : 0;
        $selectParentIds = [];
        $tree = TreeHelper::instance();
        $tree->init($_menu);
        $menu = $nav = '';
        if ($select_id) {
            $selectParentIds = $tree->getParentsIds($select_id, true);
        }
        if (get_setting('admin_menu_type')=='top' && !Request::isMobile()) {
            $topList = [];
            foreach ($_menu as $index => $item) {
                if (!$item['pid']) {
                    $topList[] = $item;
                }
            }
            $tree = TreeHelper::instance();
            $tree->init($_menu);
            foreach ($topList as $index => $item) {
                $childList = TreeHelper::instance()->getTreeMenu($item['id'], '<li class="nav-item" data-pid="@pid"><a href="@url" class="nav-link @current" data-url="@url" data-id="@id" data-pjax="mainTab"><i class="nav-icon @icon"></i><p>@title <i class="right fas fa-angle-left"></i></p></a> @childlist</li>', [$select_id], '', 'ul', 'class="nav nav-treeview"');
                $current = in_array($item['id'], $selectParentIds);
                $url = $childList ? 'javascript:;' : $item['url'];
                $childList = str_replace('" data-pid="' . $item['id'] . '"', ' has-treeview ' . ($current ? '' : 'd-none') . '" data-pid="' . $item['id'] . '"', $childList);
                $nav .= '<li class="nav-item swiper-slide ' . ($current ? 'active' : '') .'"><a href="' . $url . '"  data-id="' . $item['id'] . '" data-url="' . $url . '" class="nav-link" data-pjax="wrapMain"><i class="' . $item['icon'] . '"></i> <span>' . $item['title'] . '</span></a> </li>';
                $menu .= $childList;
            }
        } else {
            // 构造菜单数据
            TreeHelper::instance()->init($_menu);
            $menu = TreeHelper::instance()->getTreeMenu(0, '<li class="nav-item"><a href="@url" class="nav-link @class" data-pjax="mainTab"><i class="@icon"></i> <p>@title <i class="right fas fa-angle-left"></i></p></a> @childlist</li>', [$select_id], '', 'ul', 'class="nav nav-treeview"');
        }
        return ['_menu'=>$menu, '_nav'=>$nav, '_current_menu'=>$selected];
    }
	/**
	 * 获取配置文件
	 */
	public function getConfig(): array
    {
		return $this->config;
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
	public function getError() {
		return $this->error;
	}

	public function getAllRules()
    {
		$rules = db($this->config['auth_rule'])
			->order('pid asc,sort asc')
			->field('id,title as text,pid')
			->select()->toArray();

		$user_rule = $this->getRuleIds();

		foreach ($rules as $key => $value) {
			$rules[$key]['state']['opened'] = false;
            $rules[$key]['state']['selected'] = false;
			if (in_array($value['id'], $user_rule)) {
				$rules[$key]['state']['selected'] = true;
			}
		}

        return $this->getParentBtn($rules);
	}

    /**
     * 根据用户组获取改组的权限列表
     * @param int $group_id
     * @return array
     */
	public function getGroupAuthRule(int $group_id=0): array
    {
        $rules = db($this->config['auth_rule'])
            ->order('pid asc,sort asc')
            ->field('id,title as text,pid')
            ->select()
            ->toArray();

        $user_rule = db($this->config['auth_group'])->where(['id'=>$group_id])->value('rules');
        $user_rule = $user_rule ?explode(',',$user_rule): [] ;

        foreach ($rules as $key => $value)
        {
            $rules[$key]['state']['opened'] = true;
            $rules[$key]['state']['selected'] = false;
            if (in_array($value['id'], $user_rule)) {
                $rules[$key]['state']['selected'] = true;
            }
        }
        return $this->getParentBtn($rules);
    }

	public function getParentBtn($cate, $name = 'children', $pid = 0): array
    {
		$arr = array();
		foreach ($cate as $v) {
			if ($v['pid'] == $pid) {
				$v[$name] = $this->getParentBtn($cate, $name, $v['id']);
				$arr[] = $v;
			}
		}
		return $arr;
	}

    /**
     * 后台登录
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username,$password): bool
    {
        if(!$password || !$username) return false;
        $userInfo = db($this->config['auth_user'])->where(['user_name'=>$username])->find();
        if(!$userInfo) return false;

        if($userInfo['password']!=compile_password($password,$userInfo['salt']))
        {
            return  false;
        }
        session('admin_user_info',$userInfo);
        session('admin_login_uid',$userInfo['uid']);
        return true;
    }
}
