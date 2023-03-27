<?php
return [
    'name' => 'old2new',
    'title' => '老版本数据转换',
    'description' => 'WeCenter老版本数据转换程序',
    'author' => 'WeCenter官方',
    'author_url'=>'https://www.wecenter.com',
    'plugin_url'=>'/plugins/old2new/index/index',
    'version' => '1.0.0',
    'status' => 0,
    'type'=>'plugins',
    'config'=>[
        'db_host' =>[
            'title' => '老版本数据库主机',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写可访问的数据库地址'
        ],
        'port' =>[
            'title' => '老版本数据库端口',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写旧网站数据库端口'
        ],
        'db_username' =>[
            'title' => '老版本数据库账户',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写旧网站数据库账号'
        ],
        'db_password' =>[
            'title' => '老版本数据库密码',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写旧网站数据库密码'
        ],
        'db_name' =>[
            'title' => '老版本数据库名称',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写旧网站数据库名称'
        ],
        'db_prefix' =>[
            'title' => '老版本数据表前缀',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写旧网站数据库表前缀'
        ],
        'web_site' =>[
            'title' => '网站地址',
            'type' => 'text',
            'value' => '',
            'tips' =>'请填写旧网站网址,不带http://或https://'
        ],
    ],
    'menu'=>[]
];
