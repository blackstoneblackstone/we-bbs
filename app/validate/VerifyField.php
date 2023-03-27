<?php

namespace app\validate;

use think\Validate;

class VerifyField extends Validate
{
    protected $rule = [
        'name' => 'require|unique:verify_field,name',
    ];

    protected $message = [
        'name.unique' => '字段标识已存在',
        'name.require' => '字段标识不能为空',
    ];
}
