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

class Page extends Frontend
{
    public function index()
    {
        $id = $this->request->get('id',0);
        $url_name = $this->request->get('url_name','');
        $from = $this->request->get('from','');
        $where[]=['status','=',1];
        if($id)
        {
            $where[]=['id','=',$id];
        }

        if($url_name)
        {
            $where[]=['url_name','=',$url_name];
        }

        $info = db('page')->where($where)->find();
        $this->assign('from',$from);
        if($info)
        {
            $info['contents'] = htmlspecialchars_decode($info['contents']);
            $this->assign(['info' => $info]);
            $this->TDK($info['title'],$info['keywords'],$info['description']);
            return $this->fetch();
        }
        $this->error('页面不存在','/');
    }

    /**
     * 积分规则页面
     */
    public function score()
    {
        $list = db('integral_rule')->where([['integral','<>','0']])->select()->toArray();

        $this->assign([
            'list'=>$list
        ]);
        return $this->fetch();
    }
}