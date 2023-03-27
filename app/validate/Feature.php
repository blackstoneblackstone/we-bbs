<?php
namespace app\validate;
use think\Validate;
class Feature extends Validate
{
    protected $rule = [
        'title' => 'require',
        'url_token' => 'require',
        'image'=>'require',
        'topics'=>'require',
    ];

    protected $message = [
        'title.require' => '请输入专题标题',
        'url_token.require' => '请输入专题别名',
        'image.require'=>'请上传专题封面',
        'topics.require'=>'请选择专题话题'
    ];
}
