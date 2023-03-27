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

namespace app\model;

use think\Model;
use tools\Tree;

/**
 * 分类模型
 * Class Category
 * @package app\ask\model
 */
class Category extends Model
{
    /**
     * 根据分类类型获取分类列表
     * @param string $type
     * @param bool $only
     * @param string[] $sort
     * @return array
     */
	public static function getCategoryListByType(string $type='common',bool $only=false,$sort=['sort'=>'DESC']): array
    {
        $where = $only ? [$type] :  ['common',$type];
		$res = db('category')->where(['status'=>1])->whereIn('type',$where)->order($sort)->column('id,title,icon,pid,url_token');
        return Tree::toTree($res);
	}

    /**
     * 根据分类类型获取分类option树
     * @param string $type
     * @param int $selectId
     * @param bool $only
     * @param string[] $sort
     * @return array
     */
    public static function getCategoryListByTypeToOptions(string $type='common',$selectId=0,bool $only=false,$sort=['sort'=>'DESC'])
    {
        $where = $only ? [$type] :  ['common',$type];
        $res = db('category')->where(['status'=>1])->whereIn('type',$where)->order($sort)->column('id,title as name,icon,pid,url_token');
        $res = Tree::toTree($res);
        return Tree::toOptions($res,$selectId);
    }

    /**
     * 获取分类的所有子集
     * @param $category_id
     * @param bool $self
     * @return array|bool
     */
	public static function getCategoryWithChildIds($category_id,bool $self=false)
	{
		if(!$category_id) return false;
        $where = ['status'=>1,'pid'=>$category_id];
		$child_data = db('category')->where($where)->column('id');
        if($self)
        {
            $child_data[]= $category_id;
        }
        return $child_data;
	}

    /**
     * @param $ids
     * @return false
     */
    public static function getCategoryListByIds($ids)
    {
        if(!$ids) return false;
        $ids = is_array($ids) ? $ids : explode(',',$ids);
        return db('category')->whereIN('id',$ids)->column('id,title,icon,pid,url_token');
    }
}