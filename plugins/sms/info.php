<?php
return [
    'title'=>'短信插件',
    'name'=>'sms',
    'description'=>'短信插件',
    'author'=>'WeCenter官方',
    'version'=>'1.0.0',
    'author_url'=>'https://www.wecenter.com',
    'status'=>0,
    'type'=>'plugins',
    'config'=>[
        'base' =>[
            'title'=>'基础设置',
            'config'=>[
                'enable' =>[
                    'title' => '是否启用',
                    'type' => 'radio',
                    'value' => 'N',
                    'options' =>
                        array (
                            'N' => '不启用',
                            'ali' => '阿里云短信',
                            'tencent' => '腾讯云短信',
                        ),
                ]
            ]
        ],
        'ali' =>[
            'title'=>'阿里短信',
            'config'=>[
                'AccessKeyId' =>
                    array (
                        'title' => 'AccessKeyID',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => 'AccessKey ID',
                    ),
                'AccessKeySecret' =>
                    array (
                        'title' => 'AccessKeySecret',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => 'AccessKey Secret',
                    ),
                'SignName' =>
                    array (
                        'title' => '短信签名',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => '请在控制台国内消息或国际/港澳台消息页面中的签名管理页签下签名名称一列查看。',
                    ),
                'TemplateCode' =>
                    array (
                        'title' => '短信模板',
                        'type' => 'text',
                        'options' =>
                            array (
                            ),
                        'value' => '',
                        'tips' => '请在控制台国内消息或国际/港澳台消息页面中的模板管理页签下模板CODE一列查看',
                    ),
                'Endpoint' =>
                    array (
                        'title' => '域名节点',
                        'type' => 'text',
                        'value' => 'dysmsapi.aliyuncs.com',
                        'options' =>
                            array (
                            ),
                        'tips' => '域名节点',
                    ),
            ]
        ],
        'tencent' =>[
            'title'=>'腾讯短信',
            'config'=>[
                'SecretId' =>
                    array (
                        'title' => 'SecretId',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => 'SecretId',
                    ),
                'AccessKeyId' =>
                    array (
                        'title' => 'AccessKeyID',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => 'AccessKey ID',
                    ),
                'AccessKeySecret' =>
                    array (
                        'title' => 'AccessKeySecret',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => 'AccessKey Secret',
                    ),
                'SignName' =>
                    array (
                        'title' => '短信签名',
                        'type' => 'text',
                        'value' => '',
                        'options' =>
                            array (
                            ),
                        'tips' => '请在控制台国内消息或国际/港澳台消息页面中的签名管理页签下签名名称一列查看。',
                    ),
                'TemplateCode' =>
                    array (
                        'title' => '短信模板',
                        'type' => 'text',
                        'options' =>
                            array (
                            ),
                        'value' => '',
                        'tips' => '请在控制台国内消息或国际/港澳台消息页面中的模板管理页签下模板CODE一列查看',
                    ),
            ]
        ]
    ],
    'menu'=>[
        'is_nav' => 0,//1导航栏；0 非导航栏
        'menu' =>[
            'name' => 'plugins/sms/Index/index',
            'title' => '短信记录',
            'status' => 1,
            'icon' => 'fab fa-rocketchat ',
            'menu_list' => [
                ['name' => 'plugins/sms/Index/delete', 'title' => '操作-删除', 'status' => 0],
            ]
        ]
    ]
];