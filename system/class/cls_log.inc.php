<?php
class LOG
{
    /**
     * @param string $message
     * @param null $writeFileName
     * @param string $type
     * @throws Zend_Exception
     * @throws Zend_Log_Exception
     */
    public static function write($message='', $writeFileName=null, $type = 'debug')
    {

        $action = $_GET['app'].'/'.$_GET['c'].'/'.$_GET['act'];
        $remark = $_SERVER['REQUEST_URI'];

        $save_path = PUB_PATH.'tmp'.DIRECTORY_SEPARATOR.'log'.DIRECTORY_SEPARATOR;

        switch ($type)
        {
            case 'error'://系统错误
                $format = 'Time:%timestamp%'. PHP_EOL.'Uid:'.AWS_APP::user()->get_info('uid'). PHP_EOL.'File:'.$action. PHP_EOL. 'Message:%message%' . PHP_EOL.'--------------------------------------------------------' . PHP_EOL;
                $writeFileName = 'error_'.date('Y-m-d',time()).'.log';
                break;
            case 'access'://访问日志
                $format = 'Time:%timestamp%'. PHP_EOL.'Uid:'.AWS_APP::user()->get_info('uid').PHP_EOL."User Agent:" . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL."Accept Language: " . $_SERVER['HTTP_ACCEPT_LANGUAGE'] . PHP_EOL. "IP Address: " . fetch_ip().PHP_EOL. "URI: " . $_SERVER['REQUEST_URI'] .PHP_EOL.'--------------------------------------------------------' . PHP_EOL;
                $writeFileName = 'access_'.date('Y-m-d',time()).'.log';
                break;
            default:
                $format = 'Time:%timestamp%'. PHP_EOL.'Uid:'.AWS_APP::user()->get_info('uid'). PHP_EOL.'File:'.$action. PHP_EOL.'Url:'.$remark. PHP_EOL. 'Message:%message%' . PHP_EOL.'--------------------------------------------------------' . PHP_EOL;
                $writeFileName = $writeFileName ? 'debug_'.$writeFileName.'.log' : 'debug_'.date('Y-m-d',time()).'.log';
                break;

        }
        $writeFileName = str_replace(['/','\\'],'',$writeFileName);
        if(!is_dir($save_path))
        {
            make_dir($save_path);
        }
        $formatter = new Zend_Log_Formatter_Simple($format);
        $writer = new Zend_Log_Writer_Stream($save_path.$writeFileName,'a+');
        $writer->setFormatter($formatter);
        $log = new Zend_Log($writer);
        $log->log($message,7);
    }
}