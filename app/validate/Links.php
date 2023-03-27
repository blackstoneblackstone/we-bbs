<?php
namespace app\validate;
use think\Validate;
class Links extends Validate
{
    protected $rule = [
        'name' => 'require',
        'url'=>'url'
    ];

    protected $message = [
        'name.require' => '请输入网站名称',
        'url.url'=>'请填写完整网站地址'
    ];
}