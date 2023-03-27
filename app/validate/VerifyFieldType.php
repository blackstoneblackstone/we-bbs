<?php

namespace app\validate;

use think\Validate;

class VerifyFieldType extends Validate
{
    protected $rule = [
        'name' => 'require|unique:users_verify_type,name',
    ];

    protected $message = [
        'name.unique' => '字段标识已存在',
        'name.require' => '字段标识不能为空',
    ];
}
