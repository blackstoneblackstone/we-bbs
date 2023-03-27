<?php

namespace app\model\admin;

use app\model\BaseModel;

class AdminGroup extends BaseModel
{
    // 定义时间戳字段名
    protected $createTime = 'create_time';
    protected $updateTime = 'update_time';
}