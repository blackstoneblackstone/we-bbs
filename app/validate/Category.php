<?php
namespace app\validate;
use think\Validate;
class Category extends Validate
{
    protected $rule = [
        'title' => 'require',
    ];

    protected $message = [
        'title.require' => '请输入分类标题',
    ];
}
