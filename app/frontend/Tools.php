<?php
namespace app\frontend;
use app\common\controller\Frontend;
use app\common\library\helper\CaptchaHelper;
use app\common\library\helper\FileHelper;
use app\common\library\helper\HttpHelper;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Tools extends Frontend
{
    //点选验证码
    public function captcha()
    {
        $captcha = new CaptchaHelper();
        if(request()->post('do') == 'check'){
            if($captcha->check(request()->post('info'), false))
            {
                $this->success('验证成功');
            }
            $this->error('验证失败');
        }
        $captcha->creat();
    }
}