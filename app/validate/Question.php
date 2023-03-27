<?php

namespace app\validate;

use think\Validate;

class Question extends Validate
{
    protected $rule = [
        'title|问题标题' => [
            'require' => 'require',
            'max' => '100',
        ],
    ];
}