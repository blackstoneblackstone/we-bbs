<?php
namespace app\mobile;
use app\common\controller\Frontend;

class Error extends Frontend
{
    public function __call($method, $args)
    {
       $this->error404();
    }
}