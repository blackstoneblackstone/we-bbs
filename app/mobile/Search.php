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
use app\common\library\helper\IpHelper;
use app\common\library\helper\StringHelper;
use app\common\paginator\AWS;
use app\model\Common;

/**
 * 公用搜索模块
 * Class Search
 * @package app\ask\controller
 */
class Search extends Frontend
{
    //搜索首页
    public function index()
    {
        //搜索引擎处理方法
        hook('search_parse_index',$this->request->param());
        $order = $this->request->param('sort','all','sqlFilter');
        $type = $this->request->param('type','all','sqlFilter');
        $keywords = $this->request->param('q','');
        $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
        $keywords = sqlFilter($keywords);
        //搜索类型标签
        $tab_name = db('search_engine')->where(['status'=>1,'search_engine'=>'regexp'])->column('name,title');
        $this->assign([
            'type'=>$type,
            'sort'=>$order,
            'keywords'=>$keywords,
            'tab_list'=>$tab_name,
            'search_list'=>Common::getHotSearchList(1,10)
        ]);
        return $this->fetch();
    }

	//头部搜索
	public function ajax()
	{
        //搜索引擎处理方法
        hook('search_parse_ajax',$this->request->param());
        if($this->request->isPost())
        {
            $type = $this->request->param('type','all','sqlFilter');
            $limit = $this->request->param('limit',get_setting('contents_per_page'),'intval');
            $page = $this->request->param('page',1,'intval');
            $keywords = $this->request->param('q','','sqlFilter');
            $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
            $handle = new \app\logic\search\Search();
            $data = $handle->search($keywords,$type,$this->user_id,null,$page,$limit);
            $this->assign([
                'keywords'=>$keywords,
                'list'=>$data ? $data['list'] : []
            ]);
            return $this->fetch();
        }
        return '';
	}

    /**
     * ajax搜索结果页面
     */
	public function search_result()
    {
        //搜索引擎处理方法
        $rs = hook('search_parse_search_result',$this->request->param());
        if(!$rs)
        {
            $order = $this->request->param('sort','all');
            $type = $this->request->param('type','all');
            $keywords = $this->request->param('q','');
            $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
            $page = $this->request->param('page',1);
            $limit = $this->request->param('limit',5);
            $handle = new \app\logic\search\Search();
            $data = $handle->search($keywords,$type,$this->user_id,$order,$page,$limit);
            if($data)
            {
                $data['keywords']=$keywords;
                $data['type']=$type;
                $this->result($data,1);
            }
            $this->result([],0,'暂无数据');
        }
        return $rs;
    }

    public function ajax_search()
    {
        //搜索引擎处理方法
        $rs = hook('search_parse_search_result',$this->request->param());
        if(!$rs)
        {
            $order = $this->request->param('sort','all');
            $type = $this->request->param('type','all');
            $keywords = $this->request->param('q','');
            $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
            $page = $this->request->param('page',1);
            $limit = $this->request->param('limit',15);
            $handle = new \app\logic\search\Search();
            $data = $handle->search($keywords,$type,$this->user_id,$order,$page,$limit);
            $data['keywords']=$keywords;
            $data['list'] = $data['list']??[];
            $data['type']=$type;
            $data['html']=$this->fetch('',$data);
            $this->apiResult($data,1);
        }
        return $rs;
    }
}