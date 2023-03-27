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

namespace app\logic\search;
use app\logic\search\driver\ElasticSearch;
use app\logic\search\driver\RegexpSearch;
use think\helper\Str;

/**
 * 搜索引擎逻辑层
 * Class Search
 * @package app\common\logic
 */
class Search
{
	//搜索引擎
	protected $handler;

    /**
     * 架构函数
     * @access public
     * @param null $search
     */
	public function __construct($search=null)
	{
		$this->handler = $search=='ElasticSearch' ? new ElasticSearch(): new RegexpSearch() ;
	}

	public function search($keywords,$type,$uid,$order,$page=1,$per_page=10)
	{
        //其他搜索引擎钩子
        $keywords = is_array($keywords) ? $keywords : explode(' ',$keywords);
        $keywords = array_filter($keywords);
        $where = [];
        if($page<=1) $page=1;
        if($per_page<=0) $per_page = 5;
        if($type=='all')
        {
            $searchResult = $this->handler->search($keywords,'',$uid,[],$page,$per_page);
        }else{
            if(in_array($type,['question','users','topic','answer','article']))
            {
                $searchResult = $this->handler->search($keywords,$type,$uid,[],$page,$per_page);
            }else{
                //自定义搜索类型
                $searchResult = hook('search'.Str::title($type),['keywords'=>$keywords,'where'=>$where,'uid'=>$uid,'order'=>$order,'page'=>$page,'per_page'=>$per_page]);
                $searchResult = json_decode($searchResult,true);
            }
        }
        return $searchResult;
	}
}