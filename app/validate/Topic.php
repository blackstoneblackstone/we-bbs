<?php
namespace app\validate;
use think\Validate;
class Topic extends Validate
{
    protected $rule = [
        'title' => 'require|unique:topic,title',
    ];

    protected $message = [
        'title.require' => '请输入话题标题',
        'title.unique' => '话题标题已存在',
    ];
}
