<?php
namespace app\model;

class DictType extends BaseModel
{
    /**
     * 获取字典数据
     * @param string $DictTypeName
     * @return array
     */
    public static function getDictData(string $DictTypeName=''): array
    {
        $dict_type_id = db('dict_type')->where('name',$DictTypeName)->value('id');
        if(!$dict_type_id) return [];

        $dict_data = db('dict')->where('dict_id',$dict_type_id)->column('name','value');

        return $dict_data?:[];
    }
}