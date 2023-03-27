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
        $limit = $this->request->param('limit',get_setting('contents_per_page'),'intval');
        $page = $this->request->param('page',1,'intval');
        $keywords = $this->request->param('q','');
        $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
        $keywords = sqlFilter($keywords);
        if(!$keywords)
        {
            $this->redirect();
        }

        $cache_key = 'search_result_q'.md5(trim($keywords).'_search_type'.$type.'_page'.$page);
        $data = cache($cache_key);
        if(!$data)
        {
            $handle = new \app\logic\search\Search(get_setting('search_handle'));
            $data = $handle->search($keywords,$type,$this->user_id,$order,intval($page),intval($limit));
            cache($cache_key,$data,60);
            //记录搜索记录
            db('search_log')->insert([
                'uid'=>$this->user_id,
                'ip'=>IpHelper::getRealIp(),
                'keyword'=>$keywords,
                'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
                'from'=>$_SERVER['HTTP_REFERER'] ?? $this->request->server('HTTP_REFERER'),
                'create_time'=>time()
            ]);
        }
        //搜索类型标签
        $tab_name = db('search_engine')->where(['status'=>1,'search_engine'=>'regexp'])->column('name,title');
        if(!$data)
        {
            $this->assign([
                'type'=>$type,
                'sort'=>$order,
                'list'=>[],
                'total'=>0,
                'keywords'=>$keywords,
                'page'=>'',
                'tab_list'=>$tab_name,
                'search_list'=>Common::getHotSearchList(1,10)
            ]);
            return $this->fetch();
        }
        $result = AWS::make($data['list'], intval($limit), intval($page), $data['total'], false, [
            'path'=>$this->request->baseUrl(),
            'list_rows'=> intval($limit),
            'query'=>$this->request->param(),
            'page' => intval($page),
            'pjax'=>'SearchResultMain',
        ]);
        $this->assign([
            'type'=>$type,
            'sort'=>$order,
            'list'=>$data['list'],
            'total'=>$data['total'],
            'keywords'=>$keywords,
            'page'=>$result->render(),
            'tab_list'=>$tab_name,
            'search_list'=>Common::getHotSearchList(1,10)
        ]);
        return $this->fetch();
	}
}