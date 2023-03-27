<?php
namespace app\validate;
use think\Validate;
class Help extends Validate
{
    protected $rule = [
        'title' => 'require',
        'url_token' => 'require',
    ];

    protected $message = [
        'title.require' => '请输入章节标题',
        'url_token.require' => '请输入章节别名',
    ];
}
