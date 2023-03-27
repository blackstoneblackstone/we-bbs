<?php
return [
    'name' => 'default',
    'title' => '默认模板',
    'description' => 'bootstrap4模板',
    'author' => 'WeCenter官方',
    'author_url'=>'https://www.wecenter.com',
    'version' => '1.0.0',
    'status' => 0,
    'config'=>[
        'home' =>[
            'title' => '发现页',
            'config' =>[
                'search_enable' =>[
                    'title' => '首页搜索',
                    'type' => 'radio',
                    'value' => 'Y',
                    'options' =>[
                        'N' => '不开启',
                        'Y' => '开启',
                    ],
                    'tips' => '',
                ],
                'search_bg' =>[
                    'title' => '头部背景图',
                    'type' => 'image',
                    'value' => '',
                    'options' =>[],
                    'tips' => '',
                ],
                'search_title_text' =>[
                    'title' => '搜索标题文字',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => '',
                ],
                'search_min_text' =>[
                    'title' => '搜索介绍文字',
                    'type' => 'textarea',
                    'value' => '',
                    'options' =>[],
                    'tips' => '',
                ],
                'sidebar_show_items' =>[
                    'title' => '侧边栏项目',
                    'type' => 'checkbox',
                    'value' =>'write_nav,announce,focus_topic,column,hot_topic,hot_users',
                    'options' =>[
                        'write_nav' => '快速发起',
                        'announce' => '网站公告',
                        'focus_topic' => '关注话题',
                        'column' => '热门专栏',
                        'hot_topic' => '热门话题',
                        'hot_users' => '热门用户',
                        'diy_content' => '自定义内容'
                    ],
                    'tips' => '侧边栏显示的项目',
                ],
                'sidebar_diy_content' =>[
                    'title' => '自定义内容',
                    'type' => 'editor',
                    'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
                    'options' =>
                        array (
                        ),
                    'tips' => '侧边栏自定义内容,支持富文本',
                ],
                'links_show_type'=>[
                    'title' => '友情链接展示方式',
                    'type' => 'radio',
                    'value' => 'text',
                    'options' =>[
                        'text'=>'文字链接',
                        'image'=>'图片链接'
                    ],
                    'tips' => '侧边栏自定义内容,支持富文本',
                ]
            ]
        ],
        'question' =>[
            'title' => '问答页面',
            'config' =>[
                'sidebar_show_items' =>[
                    'title' => '侧边栏项目',
                    'type' => 'checkbox',
                    'value' =>'write_nav,announce,focus_topic,column,hot_topic,hot_users',
                    'options' =>[
                        'write_nav' => '快速发起',
                        'announce' => '网站公告',
                        'focus_topic' => '关注话题',
                        'column' => '热门专栏',
                        'hot_topic' => '热门话题',
                        'hot_users' => '热门用户',
                        'diy_content' => '自定义内容'
                    ],
                    'tips' => '侧边栏显示的项目',
                ],
                'sidebar_diy_content' =>[
                    'title' => '自定义内容',
                    'type' => 'editor',
                    'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
                    'options' =>[],
                    'tips' => '侧边栏自定义内容,支持富文本',
                ],
            ],
        ],
        'article' =>[
            'title' => '文章页面',
            'config' =>[
                'sidebar_show_items' =>[
                    'title' => '侧边栏项目',
                    'type' => 'checkbox',
                    'value' =>'write_nav,announce,focus_topic,column,hot_topic,hot_users',
                    'options' =>[
                        'write_nav' => '快速发起',
                        'announce' => '网站公告',
                        'focus_topic' => '关注话题',
                        'column' => '热门专栏',
                        'hot_topic' => '热门话题',
                        'hot_users' => '热门用户',
                        'diy_content' => '自定义内容'
                    ],
                    'tips' => '侧边栏显示的项目',
                ],
                'sidebar_diy_content' =>[
                    'title' => '自定义内容',
                    'type' => 'editor',
                    'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
                    'options' =>[],
                    'tips' => '侧边栏自定义内容,支持富文本',
                ],
            ],
        ],
        'column' =>[
            'title' => '专栏页',
            'config' =>[
                'navbar_bg' =>[
                    'title' => '头部背景图',
                    'type' => 'image',
                    'value' => '',
                    'options' =>[],
                    'tips' => '',
                ],
                'navbar_text' =>[
                    'title' => '标题文字',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => '',
                ],
            ]
        ],
        'common' =>[
            'title' => '通用设置',
            'config' =>[
                'bg_logo' =>[
                    'title' => '背景图logo',
                    'type' => 'image',
                    'value' => '',
                    'options' =>[],
                    'tips' => '导航带背景时的logo',
                ],
                'list_show_image' =>[
                    'title' => '列表显示图片',
                    'type' => 'radio',
                    'value' => 'Y',
                    'options' =>[
                        'N' => '不显示',
                        'Y' => '显示',
                    ],
                    'tips' => '',
                ],
                'fixed_navbar' =>[
                    'title' => '固定导航',
                    'type' => 'radio',
                    'value' => 'N',
                    'options' =>[
                        'N' => '不固定',
                        'Y' => '固定',
                    ],
                    'tips' => '',
                ],
                'login_type' =>[
                    'title' => '登录方式',
                    'type' => 'radio',
                    'value' => 'page',
                    'options' =>[
                        'page' => '新页面',
                        'dialog' => '弹窗',
                    ],
                    'tips' => '',
                ],
                'enable_mathjax'=>[
                    'title' => '启用Mathjax支持',
                    'type' => 'radio',
                    'value' => 'N',
                    'options' =>[
                        'N' => '不支持',
                        'Y' => '支持',
                    ],
                    'tips' => '',
                ],
                'filter-grey'=>[
                    'title' => '网站置灰',
                    'type' => 'radio',
                    'value' => 'N',
                    'options' =>[
                        'N' => '不启用',
                        'Y' => '启用',
                    ],
                    'tips' => '',
                ]
            ],
        ],
        'question_detail' =>[
            'title' => '问题详情',
            'config' =>[
                'sidebar_show_relation_question' =>[
                    'title' => '侧边栏显示相关问题',
                    'type' => 'radio',
                    'value' =>'Y',
                    'options' =>[
                        'N' => '不显示',
                        'Y' => '显示',
                    ],
                    'tips' => '侧边栏显示相关问题',
                ],
                'sidebar_show_recommend_post' =>[
                    'title' => '侧边栏显示推荐内容',
                    'type' => 'radio',
                    'value' =>'Y',
                    'options' =>[
                        'N' => '不显示',
                        'Y' => '显示',
                    ],
                    'tips' => '侧边栏显示推荐内容',
                ],
                'sidebar_show_items' =>[
                    'title' => '侧边栏项目',
                    'type' => 'checkbox',
                    'value' =>'',
                    'options' =>[
                        'write_nav' => '快速发起',
                        'announce' => '网站公告',
                        'focus_topic' => '关注话题',
                        'column' => '热门专栏',
                        'hot_topic' => '热门话题',
                        'hot_users' => '热门用户',
                        'diy_content' => '自定义内容'
                    ],
                    'tips' => '侧边栏显示的项目',
                ],
                'sidebar_diy_content' =>[
                    'title' => '自定义内容',
                    'type' => 'editor',
                    'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
                    'options' =>[],
                    'tips' => '侧边栏自定义内容,支持富文本',
                ],
            ],
        ],
        'article_detail' =>[
            'title' => '文章详情',
            'config' =>[
                'sidebar_show_relation_article' =>[
                    'title' => '侧边栏显示相关文章',
                    'type' => 'radio',
                    'value' =>'Y',
                    'options' =>[
                        'N' => '不显示',
                        'Y' => '显示',
                    ],
                    'tips' => '侧边栏显示相关文章',
                ],
                'sidebar_show_recommend_post' =>[
                    'title' => '侧边栏显示推荐内容',
                    'type' => 'radio',
                    'value' =>'Y',
                    'options' =>[
                        'N' => '不显示',
                        'Y' => '显示',
                    ],
                    'tips' => '侧边栏显示推荐内容',
                ],
                'sidebar_show_items' =>[
                    'title' => '侧边栏项目',
                    'type' => 'checkbox',
                    'value' =>'',
                    'options' =>[
                        'write_nav' => '快速发起',
                        'announce' => '网站公告',
                        'focus_topic' => '关注话题',
                        'column' => '热门专栏',
                        'hot_topic' => '热门话题',
                        'hot_users' => '热门用户',
                        'diy_content' => '自定义内容'
                    ],
                    'tips' => '侧边栏显示的项目',
                ],
                'sidebar_diy_content' =>[
                    'title' => '自定义内容',
                    'type' => 'editor',
                    'value' => '&lt;p class=&quot;p-3 bg-info mt-2&quot;&gt;这是自定义内容&lt;/p&gt;',
                    'options' =>[],
                    'tips' => '侧边栏自定义内容,支持富文本',
                ],
            ],
        ],
        'mobile' =>[
            'title' => '手机端设置',
            'config' =>[
                'index_hot_banner_enable' =>[
                    'title' => '是否启用首页热门',
                    'type' => 'radio',
                    'value' => 'Y',
                    'options' =>[
                        'N' => '不开启',
                        'Y' => '开启',
                    ],
                    'tips' => '是否启用首页热门',
                ],
                'index_hot_user_image' =>[
                    'title' => '首页热门用户图片',
                    'type' => 'image',
                    'value' => '/templates/mobile/img/hot-user.png',
                    'options' =>[],
                    'tips' => '首页热门用户图片',
                ],
                'index_hot_user_url'=>[
                    'title' => '首页热门用户URL',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => '首页热门用户URL',
                ],
                'index_hot_topic_image' =>[
                    'title' => '首页热门话题图片',
                    'type' => 'image',
                    'value' => '/templates/mobile/img/hot-topic.png',
                    'options' =>[],
                    'tips' => '首页热门用户图片',
                ],
                'index_hot_topic_url'=>[
                    'title' => '首页热门话题URL',
                    'type' => 'text',
                    'value' => '',
                    'options' =>[],
                    'tips' => '首页热门用户URL',
                ],
            ]
        ],
    ]
];