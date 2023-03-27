<?php

namespace app\model;

use app\common\library\builder\MakeBuilder;
use think\facade\Request;
use think\Model;

class UsersGroup extends Model {
	protected $name = 'users_group';

    public static function getList($where = array(), $order = ['sort', 'id' => 'desc'])
    {
        return self::where($where)
            ->order($order)
            ->paginate([
                'query'     => Request::get(),
                'list_rows' =>$this->request->param('pageSize',get_setting("contents_per_page",15))
            ])
            ->toArray();
    }
}