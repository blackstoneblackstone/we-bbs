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

namespace app\frontend;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\common\library\helper\StringHelper;
use app\logic\common\FocusLogic;
use app\model\Users as UsersModel;

/**
 * 用户模块
 * Class People
 */
class People extends Frontend
{
    protected $needLogin = [];
	public function index()
	{
	    $url = $this->request->param('name');
        //防止sql注入报错
        $url = str_replace(['"','"'],'',$url);
        $url = is_array($url)?end($url):$url;

        $url = $url?: $this->user_info['url_token'];
        if(!$url)
        {
            $this->error('访问页面不存在');
        }

        $uid = db('users')->whereRaw('url_token = "'.$url.'" OR user_name="'.$url.'"')->value('uid');
        if(!$uid)
        {
            $this->error('用户不存在','/');
        }

        $user =UsersModel::getUserInfo((int)$uid);

        if(!$user || $user['status']===2)
        {
            $this->error('当前用户不存在','/');
        }

        UsersModel::updateUsersViews($uid,$this->user_id);
        $user['draft_count'] = db('draft')->where(['uid'=> (int)$uid])->count();
        $user['favorite_count'] = db('users_favorite')->where(['uid'=> (int)$uid])->count();
        $user['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user['uid']);

        $type = $this->request->param('type','dynamic');
        $this->assign('type',$type);

        $this->assign('question_count',LogHelper::getActionLogCount('publish_question',$user['uid'],$this->user_id));
        $this->assign('answer_count',LogHelper::getActionLogCount('publish_answer',$user['uid'],$this->user_id));
        $this->assign('article_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('user',$user);

        $this->TDK($user['nick_name'].'的主页');
        return $this->fetch();
	}

	/**
	 * 用户列表
	 */
	public function lists()
	{
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','integral');
        if($page>5)
        {
            $page=5;
        }
        $order = [];
        $where[] =['status','=',1];
        switch ($sort)
        {
            //威望榜
            case 'reputation':
                $order['reputation']='DESC';
                $where[] = ['reputation','>',3];
                break;
            //活跃榜
            default :
                $order['integral']='DESC';
                $where[] = ['integral','>',500];
                break;
        }
		$data = UsersModel::getUserList($where,$order,$page,20,$this->user_id);
		$this->assign($data);
		$this->assign('sort',$sort);
		$this->TDK('大咖');
		return $this->fetch();
	}
}