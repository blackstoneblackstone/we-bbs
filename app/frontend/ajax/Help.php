<?php
namespace app\frontend\ajax;

use app\common\controller\Frontend;
use app\model\Help as HelpModel;

class Help extends Frontend
{
    protected $needLogin = [
        'select_chapter'
    ];

    public function select_chapter()
    {
        if($this->request->isPost())
        {
            $item_id = $this->request->post('item_id',0,'intval');
            $item_type = $this->request->post('item_type','','trim');
            $chapter_id=$this->request->post('id',0,'intval');
            if(!$item_id || !$chapter_id || !$item_type) $this->error('请求参数不正确');
            if($id = db('help_chapter_relation')->where(['item_id'=>$item_id,'item_type'=>$item_type,'chapter_id'=>$chapter_id])->value('id'))
            {
                db('help_chapter_relation')->delete($id);
                $this->success('取消加入帮助成功');
            }else{
                db('help_chapter_relation')->insert([
                    'item_id'=>$item_id,
                    'item_type'=>$item_type,
                    'chapter_id'=>$chapter_id,
                    'status'=>1
                ]);
                $this->success('加入帮助成功');
            }
        }

        $item_id = $this->request->param('item_id',0,'intval');
        $item_type = $this->request->param('item_type','','trim');
        $page = $this->request->param('page',1,'intval');
        $data = HelpModel::getHelpChapterList($page,8,false);
        foreach ($data['list'] as $k=>$v)
        {
            $data['list'][$k]['selected'] = HelpModel::checkRelationHelpItemExist($v['id'],$item_id,$item_type);
        }

        $this->assign($data);
        $this->assign([
            'item_id'=>$item_id,
            'item_type'=>$item_type
        ]);
        return $this->fetch();
    }
}