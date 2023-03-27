<?php
use think\facade\Route;

if(get_setting('url_rewrite_enable')=='Y' && ENTRANCE!='api')
{
    $prefix = app()->db->getConfig('connections.mysql.prefix');
    if(db()->query("SELECT COUNT(*) FROM information_schema.TABLES WHERE table_name ='{$prefix}route_rule'")[0]['COUNT(*)'])
    {
        $routes = db('route_rule')->where(['status'=>1])->where('entrance','<>','api')->select()->toArray();
        foreach ($routes as $k=>$v)
        {
            //通用路由
            if($v['entrance']=='all'){
                if($v['method']!='*')
                {
                    $method = $v['method'];
                    Route::$method($v['rule'], $v['url']);
                }else{
                    Route::rule($v['rule'], $v['url']);
                }
            }

            //当前访问路由
            if($v['entrance']==ENTRANCE)
            {
                if($v['method']!='*')
                {
                    $method = $v['method'];
                    Route::$method($v['rule'], $v['url']);
                }else{
                    Route::rule($v['rule'], $v['url']);
                }
            }
        }
    }else{
        $urlRewrite = get_setting('url_rewrite');
        $urlRewrite = explode("\n", $urlRewrite);
        $routes = [];
        if($urlRewrite)
        {
            foreach ($urlRewrite as $key => $val)
            {
                $val = trim($val);
                list($replace, $pattern) = explode('===', $val);
                $routes[] = array($pattern, $replace);
            }

            foreach ($routes as $k=>$v)
            {
                Route::rule($v[1], $v[0]);
            }
        }
    }
}

//第三方登录插件重写地址
Route::rule('third/callback/[:platform]', 'ThirdAuth/callback');

//接口处理
if(ENTRANCE=='api')
{
    $version = request()->header('version');
    if($version==null)$version = "v1";
    Route::rule(':controller/:function', $version.'.:controller/:function');
    if(get_setting('url_rewrite_enable')=='Y')
    {
        $routes = db('route_rule')->where(['status'=>1,'entrance'=>'api'])->select()->toArray();
        foreach ($routes as $k=>$v)
        {
            if($v['method']!='*')
            {
                $method = $v['method'];
                Route::$method($v['rule'], $v['url']);
            }else{
                Route::rule($v['rule'], $v['url']);
            }
        }
    }
}