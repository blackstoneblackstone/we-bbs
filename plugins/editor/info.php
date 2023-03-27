<?php
return [
    'title'=>'WEditor编辑器',
    'name'=>'editor',
    'description'=>'WEditor编辑器',
    'author'=>'WeCenter官方',
    'version'=>'1.0.0',
    'author_url'=>'https://www.wecenter.com',
    'plugin_url'=>'',//插件说明页面
    'status'=>0,
    'type'=>'plugins',
    'config'=>[
        'timeout'=>array(
            'title' => '超时时间',
            'type' => 'text',
            'options' => [],
            'value' => '30000',
            'tips' => '超时时间,单位(毫秒)'
        ),
    ],
    'setting'=>[//附加设置
        'publish'=>[//发起按钮

        ],
        'tabs'=>[

        ],
        'category'=>[//分类类型关联

        ],
        'relation'=>[//首页内容聚合类型关联

        ],
        'commands'=>[//定时任务

        ]
    ],
];