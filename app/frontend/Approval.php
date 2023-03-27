<?php
// +----------------------------------------------------------------------
// | WeCenter社交化问答系统
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2022 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@WeCenter.com>
// +----------------------------------------------------------------------

namespace app\frontend;
use app\common\controller\Frontend;

class Approval extends Frontend
{
    protected $needLogin = [
        'index',
        'preview'
    ];
    public function index()
    {
        $approvalTypes = [
            'question' => '问题',
            'article' => '文章',
            'answer' => '回答',
            'modify_question'=>'修改问题',
            'modify_article'=>'修改文章',
            'modify_answer'=>'修改回答',
            'article_comment'=>'文章评论'
        ];
        $status = $this->request->param('status',0);
        $page = $this->request->param('page',1,'intval');
        $data = \app\model\Approval::getApprovalListByType(['uid'=>$this->user_id,'status'=>$status],$page,get_setting('contents_per_page'),'tabMain');
        $this->assign($data);
        $this->assign([
            'approvalTypes'=>$approvalTypes,
            'status'=>$status
        ]);
        return $this->fetch();
    }

    /**
     * 审核预览
     * @return false|mixed
     */
    public function preview()
    {
        $id = $this->request->param('id',0,'intval');
        $info = db('approval')->where('id',$id)->find();
        if(!$info || $this->user_id!=$info['uid']) $this->error404();
        $data = json_decode($info['data'],true);
        $topics = [];
        if(isset($data['topics']) && $data['topics'])
        {
            $topics = \app\model\Topic::getTopicByIds($data['topics']);
        }

        $category_list = \app\model\Category::getCategoryListByType();
        $category_list = $category_list ? array_column($category_list,'title','id') : [];
        $column_list = db('column')->where(['verify'=>1])->column('name','id');

        if($info['type']=='answer' || $info['type']=='modify_answer')
        {
            $data['title'] = db('question')->where(['status'=>1,'id'=>$data['question_id']])->value('title');
        }

        $this->assign([
            'info'=>$info,
            'data'=>$data,
            'category_list'=>$category_list,
            'topics'=>$topics,
            'column_list'=>$column_list
        ]);
        return $this->fetch();
    }
}