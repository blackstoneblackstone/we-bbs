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
use app\common\controller\Widget;
use app\model\Feature;
use app\model\PostRelation;
use app\model\Category;

/**
 * 通用小部件
 * Class Common
 * @package app\ask\widget
 */
class Common extends Widget
{
    /**
     * 通用内容列表
     * @param null $item_type
     * @param null $sort
     * @param null $topic_ids
     * @param null $category_id
     * @return mixed
     */
	public function lists($item_type=null, $sort = null, $topic_ids = null, $category_id = null)
	{
        if($html = hook('widget_common_list',$this->request->param()))
        {
            return $this->view->display($html);
        }
		$data = PostRelation::getPostRelationList($this->user_id,$item_type,$sort,$topic_ids,$category_id,$this->request->param('page',1));
		$this->assign($data);
		if($sort=='focus')
        {
            return $this->fetch('common/focus');
        }
		return $this->fetch('common/lists');
	}

    /**
     * @param int $category 当前分类id
     * @param string $type 分类分组
     * @param string $show_type 分类显示方式，list列表导航形式，image 图片导航方式
     * @return false|mixed
     */
    public function category(int $category=0,string $type='all',string $show_type='list')
    {
        $this->assign([
            'category_list'=>Category::getCategoryListByType($type),
            'category'=>$category,
            'show_type'=>$show_type
        ]);
        return $this->fetch('common/category');
    }

    /**
     * 友情链接小部件
     * @return mixed
     */
    public function links()
    {
        $this->assign('links',db('links')->where('status',1)->select()->toArray());
        return $this->fetch('common/links');
    }

    //专题关联内容
    public function feature_content($feature_id = 0,$sort='hot',$theme='lists')
    {
        if($html = hook('widget_common_features',$this->request->param()))
        {
            return $this->view->display($html);
        }
        $data = Feature::getRelationFeatureList($this->user_id,$feature_id,$sort,$this->request->param('page',1));
        $this->assign($data);
        return $this->fetch('common/'.$theme);
    }
}
