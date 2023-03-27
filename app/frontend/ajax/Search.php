<?php
namespace app\frontend\ajax;

use app\common\controller\Frontend;

class Search extends Frontend
{
    //头部搜索
    public function ajax()
    {
        //搜索引擎处理方法
        hook('search_parse_ajax',$this->request->param());
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

    /**
     * ajax搜索结果页面
     */
    public function search_result()
    {
        if($this->request->isAjax())
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
            $this->result(json_decode($rs),true);
        }
    }
}