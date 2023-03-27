<?php
namespace app\common\middleware;

use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class AppInit
{
    public function handle(Request $request, \Closure $next)
    {
        if(!defined('ENTRANCE'))
        {
            $client = request()->header('AccessToken');
            $apiEnable = false;
            $client = authCode($client);
            if ($client && db('app_token')->where(['token' => $client])->value('id')) {
                $apiEnable = true;
            }
            //手机wap
            if(\think\facade\Request::isMobile() && !$apiEnable && get_setting('mobile_enable')=='Y')
            {
                define('ENTRANCE','mobile');
            }elseif (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false && !$apiEnable && get_setting('wechat_enable')=='Y') {
                define('ENTRANCE','wechat');
            }elseif($apiEnable)
            {
                define('ENTRANCE','api');
            }elseif(strstr($request->url(),app()->config->get('app.admin'))!==false)
            {
                define('ENTRANCE','backend');
            }else{
                define('ENTRANCE','frontend');
            }
        }
        if(ENTRANCE) {
            $controller_layer = ENTRANCE;
        }else{
            $controller_layer = 'controller';
        }

        $mobile_host = get_setting('mobile_host','');
        $frontend_host = get_setting('pc_host','');

        //启用了手机端单独域名进行处理
        if($mobile_host && $frontend_host)
        {
            $scheme=  request()->scheme().'://';
            $query = request()->url();
            if(ENTRANCE=='frontend' && request()->host(true)==$mobile_host)
            {
                $response = Response::create($scheme.$frontend_host.$query, 'redirect', 302);
                throw new HttpResponseException($response);
            }
            if(ENTRANCE=='mobile' && request()->host(true)!=$mobile_host)
            {
                $response = Response::create($scheme.$mobile_host.$query, 'redirect', 302);
                throw new HttpResponseException($response);
            }
        }

        //定义一天时间戳
        define('ONE_DAY',86400);
        app()->config->set(['controller_layer' => $controller_layer], 'route');

        //设置默认首页
        if(ENTRANCE=='frontend')
        {
            if(db_field_exits('menu_rule', 'is_home'))
            {
                if($menu_rule = db('menu_rule')->where(['is_home'=>1,'status'=>1,'type'=>1,'group'=>'nav'])->value('name'))
                {
                    $menus = explode('/',$menu_rule);
                    app()->config->set([
                        'default_controller' => ucfirst($menus[0])??'Index',
                        'default_action' => $menus[1]??'index'
                    ], 'route');
                }
            }
        }

        //设置默认语言
        $defaultLang = cookie('aws_lang');
        if (get_setting('default_language','zh-cn') && (!$defaultLang || $defaultLang!=get_setting('default_language','zh-cn')))
        {
            if(get_setting('enable_multilingual')!='Y')
            {
                cookie('aws_lang',get_setting('default_language','zh-cn'));
            }
        }
        return $next($request);
    }
}