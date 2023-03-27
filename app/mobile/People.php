<?php
namespace app\mobile;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\Users as UsersModel;

class People extends Frontend
{
    public function index()
    {
        $url = $this->request->param('name');
        //防止sql注入报错
        $url = str_replace(['"','"'],'',$url);
        $url = $url?: $this->user_info['url_token'];
        if(!$url)
        {
            $this->error('访问页面不存在');
        }

        $uid = db('users')->whereRaw('url_token = "'.$url.'" OR user_name="'.$url.'"')->value('uid');
        if(!$uid)
        {
            $this->error('用户不存在');
        }

        $user =UsersModel::getUserInfo((int)$uid);

        if(!$user || $user['status']===2)
        {
            $this->error('当前用户不存在');
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

    public function lists()
    {
        $sort = $this->request->param('sort','integral');
        $this->assign('sort',$sort);
        $this->TDK('大咖');
        return $this->fetch();
    }
}