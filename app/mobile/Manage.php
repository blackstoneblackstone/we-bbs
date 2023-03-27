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
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\Common;

class Manage extends Frontend
{
    /**
     * 动态
     * @return void
     */
    public function dynamic()
    {
        return $this->fetch();
    }

    public function question()
    {
        return $this->fetch();
    }

    public function article()
    {
        return $this->fetch();
    }

    public function answer()
    {
        return $this->fetch();
    }

    public function message()
    {
        $notify_group = get_dict('notify_group');
        $this->assign('notify_group',$notify_group);
        return $this->fetch();
    }

    /**
     * 用户内容列表
     * @return mixed
     */
    public function get_user_post()
    {
        if($this->request->isPost())
        {
            $uid = $this->request->post('uid',0);
            $type = $this->request->post('type','');

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
            $data['type'] = $type;
            $data['html'] = $this->fetch('',$data);
            $this->apiResult($data);
        }
    }
}
