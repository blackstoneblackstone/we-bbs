<?php
namespace app\validate;
use think\Validate;
class Announce extends Validate
{
    protected $rule = [
        'title' => 'require',
        'message' => 'require',
    ];

    protected $message = [
        'title.require' => '请输入公告标题',
        'message.require' => '请输入公告详情',
    ];
}
