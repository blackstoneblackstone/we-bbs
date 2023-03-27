<?php

namespace app\common\traits;
use app\common\library\helper\IpHelper;
use think\exception\HttpResponseException;
use think\Response;

/**
 * Trait Jump
 * @package app\traits
 */
trait Jump
{

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param null $url 跳转的 URL 地址
     * @param mixed $data 返回的数据
     * @param int $wait 跳转等待时间
     * @return void
     */
    protected function success($msg = '', $url = null, $data = '', int $wait = 3): void
    {
        $url = $this->getUrl($url);
        $result = [
            'code' => 1,
            'msg'  => L($msg),
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type === 'html') {
            $response = view(config('app.dispatch_success_tmpl'), $result);
        } elseif ($type === 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param mixed $msg 提示信息
     * @param null $url 跳转的 URL 地址
     * @param mixed $data 返回的数据
     * @param int $wait 跳转等待时间
     */
    protected function error($msg = '', $url = null, $data = '', int $wait = 3)
    {
        if (is_string($url)) {
            $url = (string)$url;
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }elseif(is_object($url))
        {
            $url = (string)$url;
        }else{
            $url = null;
        }

        $type   = $this->getResponseType();
        $result = [
            'code' => 0,
            'msg'  => L($msg),
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];
        if ($type === 'html') {
            $response = view(config('app.dispatch_error_tmpl'), $result);

        } elseif ($type === 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 自定义加载中动画
     * @access protected
     * @param null $url 跳转的 URL 地
     * @param int $wait 跳转等待时间
     * @return void
     */
    protected function loading($url = null, int $wait = 3): void
    {
        $url = $this->getUrl($url);

        $result = [
            'code' => 1,
            'msg'  => '',
            'data' => '',
            'url'  => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
        if ($type === 'html') {
            $response = view(app()->getBasePath() .'common'. DIRECTORY_SEPARATOR. 'tpl' . DIRECTORY_SEPARATOR . 'jump.tpl', $result);
        } elseif ($type === 'json') {
            $response = json($result);
        }
        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param int $code 返回的 code
     * @param mixed $msg 提示信息
     * @param string $type 返回数据格式
     * @param array $header 发送的 Header 信息
     * @return void
     */
    protected function result($data, int $code = 0, $msg = '', string $type = '', array $header = []): void
    {
        $result   = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $type     = $type ?: $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 自定义ajax返回
     * @param $result
     * @param array $header
     */
    protected function ajaxResult($result, array $header = []): void
    {
        $type     =  $this->getResponseType();
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * URL 重定向
     * @access protected
     * @param $url
     * @param array|int $params 其它 URL 参数
     * @param int $code http code
     * @return void
     */
    protected function redirect($url=null, $params = [], int $code = 302): void
    {
        if (is_int($params)) {
            $code   = $params;
        }
        $url = $this->getUrl($url);
        $response = Response::create($url, 'redirect', $code);
        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType(): string
    {
        return (request()->isJson() || request()->isAjax()) ? 'json' : 'html';
    }

    //跨域请求检测
    protected  function checkCrossRequest()
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN']) {
            $info = parse_url($_SERVER['HTTP_ORIGIN']);
            /*if(config('app.cors_request_domain'))
            {
                $domainArr = explode(',', config('app.cors_request_domain'));
            }*/
            $domainArr=['*'];

            $domainArr[] = request()->host(true);
            if (in_array("*", $domainArr) || in_array($_SERVER['HTTP_ORIGIN'], $domainArr) || (isset($info['host']) && in_array($info['host'], $domainArr))) {
                header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
            } else {
                $response = Response::create('跨域检测无效', 'html', 403);
                throw new HttpResponseException($response);
            }

            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');

            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
                }
                if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                    header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                }
                $response = Response::create('', 'html');
                throw new HttpResponseException($response);
            }
        }
    }

    // 检测IP是否允许
    protected function checkIpAllowed($ip=null)
    {
        $ip = is_null($ip) ? IpHelper::getRealIp() : $ip;
        $forbiddenArr = db('forbidden_ip')->column('ip');
        $forbiddenArr = !$forbiddenArr ? [] : $forbiddenArr;
        if ($forbiddenArr && in_array($ip,$forbiddenArr))
        {
            $response = Response::create('请求无权访问', 'html', 403);
            throw new HttpResponseException($response);
        }
    }

    /**
     * 404 页面
     * @param null $url
     */
    protected function error404($url = null)
    {
        header("HTTP/1.0 404 Not Found");
        $url = $this->getUrl($url);
        $result = [
            'code' => 1,
            'msg'  => '',
            'url'  => $url,
        ];
        $type = $this->getResponseType();
        if ($type === 'html') {
            $response = view(app()->getBasePath() . 'common' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . '404.tpl', $result,404);
        } elseif ($type === 'json') {
            $response = json($result,404);
        }
        throw new HttpResponseException($response);
    }

    /**
     * @param $url
     * @return mixed
     */
    protected function getUrl($url)
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = base64_decode(session('return_url'));
        } elseif (is_string($url)) {
            $url = (string)$url;
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : app('route')->buildUrl($url)->__toString();
        }elseif(is_object($url))
        {
            $url = (string)$url;
        }
        return $url;
    }

    /**
     * 定义接口返回数据类型
     * @param $data
     * @param int $code
     * @param string $msg
     * @param string $type
     * @param array $header
     * @return void
     */
    protected function apiResult($data, int $code = 1, string $msg = '请求成功', string $type = '', array $header = []): void
    {
        $result   = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $type     = 'json';
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 接口成功响应
     * @param string $msg
     * @param $data
     * @param array $header
     * @return void
     */
    protected function apiSuccess(string $msg = '请求成功', $data = [], array $header = []): void
    {
        $result   = [
            'code' => 1,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $type     = 'json';
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }

    /**
     * 接口错误响应
     * @param string $msg
     * @param $data
     * @param array $header
     * @return void
     */
    protected function apiError(string $msg = '请求失败', $data = [], array $header = []): void
    {
        $result   = [
            'code' => 0,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $type     = 'json';
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }
}
