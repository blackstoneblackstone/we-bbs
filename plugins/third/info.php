<?php
return [
    'title'=>'第三方登录',
    'name'=>'third',
    'description'=>'第三方登录',
    'author'=>'WeCenter官方',
    'version'=>'1.0.0',
    'author_url'=>'https://www.wecenter.com',
    'plugin_url'=>'',//插件默认说明页
    'status'=>0,
    'type'=>'module',
    'config'=>[
        'base' =>[
            'title'=>'基础配置',
            'config'=>[
                'enable'=>[
                    'title' => '启用第三方登录',
                    'type' => 'checkbox',
                    'value' => 'qq,wechat,weibo',
                    'options' =>[
                        'qq'     => 'QQ',
                        'wechat' => '微信',
                        'weibo'  => '微博',
                    ],
                ],
                'bind_account'=>[
                    'title' => '账号绑定',
                    'type' => 'radio',
                    'value' => 1,
                    'options' =>[
                        1 => '开启',
                        0 => '关闭',
                    ],
                    'tips' => '账号绑定',
                ],
            ]
        ],
        'qq' =>[
            'title'=>'QQ配置',
            'config'=>[
                'app_id'=>[
                    'title' => 'AppID',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => 'AppID',
                ],
                'app_secret'=>[
                    'title' => 'AppSecret',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => 'AppSecret',
                ],
                'scope'=>[
                    'title' => '授权模式',
                    'type' => 'radio',
                    'value' => 'get_user_info',
                    'options' =>[
                        'get_user_info'=>'get_user_info',
                    ],
                    'tips' => '授权模式,回调地址为:'.get_url('ThirdAuth/callback', ['platform' => 'qq'],true,true),
                ]
            ]
        ],
        'wechat' =>[
            'title'=>'微信配置',
            'config'=>[
                'app_id'=>[
                    'title' => 'AppID',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => 'AppID',
                ],
                'app_secret'=>[
                    'title' => 'AppSecret',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => 'AppSecret',
                ],
                'scope'=>[
                    'title' => '授权模式',
                    'type' => 'radio',
                    'value' => 'snsapi_userinfo',
                    'options' =>[
                        'snsapi_userinfo'=>'snsapi_userinfo',
                        'snsapi_base'=>'snsapi_base'
                    ],
                    'tips' => '授权模式,回调地址为:'.get_url('ThirdAuth/callback', ['platform' => 'wechat'],true,true),
                ],
            ]
        ],
        'weibo' =>[
            'title'=>'微博配置',
            'config'=>[
                'app_id'=>[
                    'title' => 'AppID',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => 'AppID',
                ],
                'app_secret'=>[
                    'title' => 'AppSecret',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => 'AppSecret,回调地址为:'.get_url('ThirdAuth/callback', ['platform' => 'weibo'],true,true),
                ],
            ]
        ],
    ],
    'menu'=>[
        'is_nav' => 0,//1导航栏；0 非导航栏
        'menu' =>[
            'name' => 'plugin.Third/index',
            'title' => '第三方登录',
            'status' => 1,
            'icon' => 'fas fa-comments-dollar',
            'menu_list' => [
                ['name' => 'plugin.Third/delete', 'title' => '操作-删除', 'status' => 0],
            ]
        ]
    ]
];