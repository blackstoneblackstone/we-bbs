<?php

namespace app\backend\content;
use app\common\controller\Backend;
use think\facade\Request;

class Page extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\model\Page();
        $this->table = 'page';
    }

    public function index()
    {;
        $columns = [
            ['id'  , 'ID'],
            ['title','单页标题','link',get_url('page/index',['url_name'=>'__url_name__'])],
            ['url_name','页面链接','link',get_url('page/index',['url_name'=>'__url_name__'])],
            ['keywords', '关键词'],
            ['description','页面描述'],
            ['status', '是否启用', 'status', '0',['0' => '否','1' => '是']],
        ];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db($this->table)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }
        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('title','页面标题','填写页面标题')
            ->addText('url_name','页面链接','填写页面标识,一般为字母')
            ->addText('keywords','关键词','填写关键词')
            ->addTextarea('description','页面简介','请填写页面简介')
            ->addEditor('contents','页面内容','','','page')
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =$this->model->find($id);

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('title','页面标题','填写页面标题',$info['title'])
            ->addText('url_name','页面链接','填写页面标识,一般为字母',$info['url_name'])
            ->addText('keywords','关键词','填写关键词',$info['keywords'])
            ->addTextarea('description','页面简介','请填写页面简介',$info['description'])
            ->addEditor('contents','页面内容','',htmlspecialchars_decode($info['contents']),'page')
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }
}
