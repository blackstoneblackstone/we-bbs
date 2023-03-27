<?php
namespace app\frontend;

use app\common\controller\Frontend;
use app\model\Help as HelpModel;
class Help extends Frontend
{
    public function index()
    {
        $page = $this->request->param('page',1,'intval');
        $data = HelpModel::getHelpChapterList($page);
        $this->assign($data);
        return $this->fetch();
    }

    public function detail()
    {
        $token = $this->request->param('token','','trim');
        $page = $this->request->param('page',1,'intval');
        $info = db('help_chapter')->where(['url_token'=>$token,'status'=>1])->find();
        if(!$info) $this->error('帮助章节不存在','index');
        $data = HelpModel::getRelationHelpChapterList($info['id'],$page);

        $this->assign($data);
        $this->assign([
            'info'=>$info
        ]);
        return $this->fetch();
    }
}