<?php

// +----------------------------------------------------------------------
// | 缓存设置
// +----------------------------------------------------------------------

return [
    // 默认缓存驱动
    'default' => env('cache.driver', 'file'),

    // 缓存连接方式配置
    'stores'  => [
        'file' => [
            // 驱动方式
            'type'       => 'File',
            // 缓存保存目录
            'path'       => '',
            // 缓存前缀
            'prefix'     => config('app.cache')['prefix'],
            // 缓存有效期 0表示永久缓存
            'expire'     => 0,
            // 缓存标签前缀
            'tag_prefix' => 'tag:',
            'serialize'  => ['wc_serialize','wc_unserialize'],
        ],
        'memcached' => [
            // 驱动方式
            'type' => 'memcached',
            'host' => config('aws.cache_host','127.0.0.1'),
            // 端口号
            'port' => config('aws.cache_port','11211'),
            // 密码
            'password'=> config('aws.cache_password'),
        ],
        'memcache' => [
            // 驱动方式
            'type' => 'memcache',
            // 服务器地址
            'host' => config('aws.cache_host','127.0.0.1'),
            // 端口号
            'port' => config('aws.cache_port','11211'),
            // 密码
            'password'=> config('aws.cache_password'),
        ],
        'redis' => [
            // 驱动方式
            'type' => 'redis',
            'host' => config('aws.cache_host','127.0.0.1'),
            // 端口号
            'port' => config('aws.cache_port','6379'),
            // 密码
            'password'=> config('aws.cache_password'),
            // 默认缓存时间
            'timeout' => 3600
        ],
    ],
];
