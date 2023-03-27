<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------
use think\facade\Env;
return [
    // 应用地址
    'app_host'         => Env::get('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 是否启用事件
    'with_event'       => true,
    // 开启应用快速访问
    'app_express'      => true,
    // 默认应用
    'default_app' => '',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 应用映射（自动多应用模式有效）
    'app_map'          => [   
    ],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ['common'],
    // 异常页面的模板文件
	'exception_tmpl' => Env::get('app_debug') ? app()->getThinkPath() . 'tpl/think_exception.tpl' : app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'think_exception.tpl',
	// 跳转页面的成功模板文件
	'dispatch_success_tmpl' => app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
	// 跳转页面的失败模板文件
	'dispatch_error_tmpl' => app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'dispatch_jump.tpl',
	// 错误显示信息,非调试模式有效
	'error_message' => '页面错误！请稍后再试～',
	// 显示错误信息
	'show_error_msg' => true,
	'admin'=>'admin.php',
	'session'=>[
        'name'=>'wsVKyvzjpDhPcInT',
        'prefix'=>'wWCglFYVhp'
    ],
    'cache'=>[
        'prefix'=>'BctMeLsUHz'
    ],
    'token'=>[
        'key'=>'wVSPbLuEiN'
    ],
	'fieldType'=>[
        'text' => '单文本',
        'password' => '密码',
        'textarea' => '多文本',
        'array' => '数组',
        'bool' => '布尔',
        'select' => '下拉',
        'radio' => '单选',
        'checkbox' => '多选',
        'number' => '数字',
        'datetime' => '时间',
        'date' => '日期',
        'editor' => '编辑器',
        'image' => '单图片',
        'images' => '多图片',
        'file' => '单文件',
        'files' => '多文件',
        'code' => '代码编辑器',
        'color' => '取色器',
        'html' => '自定义html',
        'hidden' => '隐藏域',
        'daterange' => '日期范围',
        'tags' => '标签',
    ]
];