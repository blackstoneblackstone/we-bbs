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

namespace app\widget;
use app\model\Column as ColumnModel;
use app\common\controller\Widget;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\Common;
use app\model\Users;
use app\model\Invitation as InvitationModel;

/**
 * 通用小部件
 * Class Common
 * @package app\ask\widget
 */
class Member extends Widget
{
    /**
     * 用户内容列表
     * @param $uid
     * @param $type
     * @return mixed
     */
	public function getUserPost($uid,$type)
	{
        $action = 'publish_'.$type;
	    if($type=='dynamic')
        {
            $action=[
                'publish_question',
                'publish_article',
                'publish_answer',
                /*'agree_question',
                'agree_article',
                'agree_answer',
                'focus_question',*/
            ];
        }

	    $page = request()->param('page', 1, 'intval');
	    if (in_array($type, ['column', 'fans', 'friend', 'topic'])) {
            $res = Common::getUserFocus($uid, $type, $page, 10, 'tabMain');

            if (!empty($res['data'])) {
                foreach ($res['data'] as $key => $val)
                {
                    if ($type == 'friend') $val['uid'] = $val['friend_uid'];
                    if ($type == 'fans') $val['uid'] = $val['fans_uid'];
                    $res['data'][$key]['item_type'] = $type;
                    $res['data'][$key]['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, $type == 'fans' ? 'user' : $type, $val['uid']);
                }
            }
            $data = ['list' => $res['data'], 'page' => $res['page']];
        } else {
            $data = LogHelper::getUserActionLogList($action,intval($uid),$this->user_id,$page,10,'tabMain');
        }

		$this->assign($data);
        $this->assign('type',$type);
		return $this->fetch('member/posts');
	}

    /**
     * 解析内容列表
     * @param $list
     * @param $page
     * @return mixed
     */
	public function parse($list,$page)
	{
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch('member/lists');
	}

    /*用户中心侧边栏导航*/
    public function userNav($uid)
    {
        $uid = $uid ? intval($uid) : $this->user_id;
        $user = Users::getUserInfo($uid);
        $user['draft_count'] = db('draft')->where(['uid' => $uid])->count();
        $user['favorite_count'] = db('users_favorite')->where(['uid' => $uid])->count();
        $user['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id,'user',$user['uid']);
        $this->assign('new_question_count',LogHelper::getActionLogCount('publish_question',$user['uid'],$this->user_id));
        $this->assign('publish_answer_count',LogHelper::getActionLogCount('publish_answer',$user['uid'],$this->user_id));
        $this->assign('publish_article_count',LogHelper::getActionLogCount('publish_article',$user['uid'],$this->user_id));
        $this->assign('user',$user);
        // 用户邀请注册名额
        $this->assign('invite_quota', InvitationModel::availableCount($this->user_info));

        return $this->fetch('member/nav');
    }
}
