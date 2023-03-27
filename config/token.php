<?php
return [
// 驱动方式
    'type'     => 'Mysql',
    // 缓存前缀
    'key'      => config('app.token')['key'],
    // 加密方式
    'hash_algo' => 'ripemd160',
    // 缓存有效期 0表示永久缓存
    'expire'   => 1400000,
];