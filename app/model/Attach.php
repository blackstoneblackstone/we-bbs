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
use app\common\library\helper\DataHelper;
use think\Model;

class Attach extends Model
{
	protected $name = 'attach';

    public static function updateAttach($item_type, $item_id, $attach_access_key)
    {
        if (!is_numeric($item_id) OR !$attach_access_key)
        {
            return false;
        }

        //db('attach')->where(array('item_type'=>$item_type,'item_id' => $item_id))->delete();

        return self::update(array('item_id' => $item_id), ['item_type'=>$item_type,'item_id'=> 0 ,'access_key'=>$attach_access_key]);
    }

    public static function removeAttach($id, $access_key): bool
    {
        $attach = db('attach')->where(['id'=>intval($id),'access_key'=>$access_key])->find();
        if (!$attach)
        {
            return false;
        }
        db('attach')->where(['id'=>intval($id),'access_key'=>$access_key])->delete();
        @unlink($attach['path']);
        return true;
    }

    public static  function getAttach($item_type, $item_id)
    {
        if(!is_numeric($item_id))
        {
            return false;
        }
        $attach_list =  db('attach')->where(['item_type'=>$item_type,'item_id'=>$item_id])->order('id','DESC')->select()->toArray();

        if($attach_list)
        {
            foreach ($attach_list as $k => $v)
            {
                $attach_list[$k]['auth_key'] = authCode($v['id'],'ENCODE');
            }
        }
        return $attach_list;
    }

    // 批量删除附件
    public static function batchRemoveAttach($where)
    {
        $attaches = db('attach')->where($where)->select();
        foreach ($attaches as $attach) {
            @unlink($attach['path']);
        }

        return db('attach')->where($where)->delete();
    }

    /**
     * 删除附件
     * @param $item_type
     * @param $item_ids
     * @return mixed
     */
    public static function removeAttachByItemIds($item_type,$item_ids)
    {
        $attaches = db('attach')->where(['item_type'=>$item_type])->whereIn('item_id',$item_ids)->column('path,id');

        foreach ($attaches as $attach) {
            @unlink($attach['path']);
        }

        return db('attach')->where(['item_type'=>$item_type])->whereIn('item_id',$item_ids)->delete();
    }

    public static function attachInfo($where)
    {
        return self::where($where)->find()->toArray();
    }
}