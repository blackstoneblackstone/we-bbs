<?php

namespace app\mobile;
use app\common\controller\Frontend;
use app\common\library\helper\ImageHelper;
use app\common\library\helper\LogHelper;
use app\model\Article;
use app\model\Column as ColumnModel;
use app\model\Topic;
use app\model\Users;
use app\model\Vote;

class Column extends Frontend
{
    protected $needLogin = [
        'apply','my','manager'
    ];

    /**
     * 专栏首页
     * @return mixed
     */
    public function index()
    {
        $sort = $this->request->param('sort','new');
        $this->assign('sort',$sort);
        return $this->fetch();
    }

    /**
     * 专栏详情
     */
    public function detail()
    {
        $column_id = $this->request->param('id');
        $column_info = db('column')->where(['id'=>$column_id])->find();
        $column_info['user_info'] = Users::getUserInfo($column_info['uid']);
        $focus = ColumnModel::checkFocus($column_id,$this->user_id);
        $sort = $this->request->param('sort','column');
        if(!$column_info['verify'] && $this->user_id!=$column_info['uid'])
        {
            $this->error('该专栏不存在或审核中，暂时无法访问');
        }
        $this->assign('column_info', $column_info);
        $this->assign('focus', $focus);
        $this->assign('sort',$sort);
        return $this->fetch();
    }

    // 我的专栏
    public function my()
    {
        $this->assign(['verify' => $this->request->param('verify',1)]);
        return $this->fetch();
    }

    // 申请专栏
    public function apply()
    {
        if($this->request->isPost())
        {
            $name = $this->request->post('name');
            $description = $this->request->post('description');
            $cover = $this->request->post('cover');
            $id = $this->request->post('id',0,'intval');

            if(!$name || removeEmpty($name)==''){
                $this->error('专栏标题不能为空');
            }

            if(!$description || removeEmpty($description)==''){
                $this->error('专栏描述不能为空');
            }

            if(!$id && db('column')->where(['name'=>$name])->value('id'))
            {
                $this->error('专栏已存在');
            }

            if(!$this->request->checkToken())
            {
                $this->error('请勿重复提交');
            }

            $verify = (isSuperAdmin() || isNormalAdmin()) ? 1 :0;
            $column_id= ColumnModel::applyColumn($this->user_id,$name,$description,$cover,intval($id),$verify);
            $this->success($verify ? '申请成功' : '申请成功,请等待管理员审核','column/detail?id='.$column_id);
        }

        if($id = $this->request->param('id'))
        {
            $column_info = db('column')->where(['uid'=>$this->user_id,'id'=>$id])->find();
            if(!$column_info)
            {
                $this->error('专栏信息不存在');
            }
            $this->assign('info',$column_info);
        }
        return $this->fetch();
    }
}