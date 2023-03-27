<?php
namespace app\frontend;

use app\common\controller\Frontend;
use app\common\library\helper\WeChatHelper;

class Wechat extends Frontend
{
    public function index()
    {
        $response = WeChatHelper::instance()->getOfficialAccount()->server->serve();
        $response->send();
    }
}