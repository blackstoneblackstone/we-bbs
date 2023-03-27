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
use app\logic\common\FocusLogic;
use app\model\Common;

/**
 * 我的关注
 * Class Focus
 * @package app\ask\controller
 */
class Focus extends Frontend
{
	public function index()
	{
        if(!$this->user_id)
        {
            $this->redirect('/');
        }

        $type = $this->request->get('type','question');

        $this->assign([
            'type'=>$type
        ]);
		return $this->fetch();
	}

	public function focus_list()
    {
        $types = ['fans', 'friend', 'column', 'topic', 'question'];
        $type = $this->request->post('type','question');
        $uid = $this->request->post('uid',0);
        $page = $this->request->param('page',1);
        $data = Common::getUserFocus($uid, $type, $page);

        if (!empty($data['data'])) {
            foreach ($data['data'] as $key =>$val)
            {
                if(in_array($type, $types)) {
                    if ($type == 'fans') $val['uid'] = $val['fans_uid'];
                    if ($type == 'friend') $val['uid'] = $val['friend_uid'];
                   $data['data'][$key]['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, $type == 'fans' ? 'friend' : $type, $val['item_id']);
                }
            }
        }
        $list = $data;
        $data['list'] = $list['data'];
        $data['type'] = $type;
        $data['html'] = $this->fetch('',$data);
        return $this->apiResult($data);
    }
}