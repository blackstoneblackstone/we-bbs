<?php
// +----------------------------------------------------------------------
// | WeCenter 简称 WC
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.wecenter.com
// +----------------------------------------------------------------------
// | WeCenter团队一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: WeCenter团队 <devteam@wecenter.com>
// +----------------------------------------------------------------------
namespace app\backend\extend;
use app\common\controller\Backend;
use think\App;
use think\exception\ValidateException;
use think\facade\Request;

class Links extends Backend
{
    protected $table = 'links';
	public function index()
	{
        $columns = [
            ['id'  , '编号'],
            ['name', '网站名称'],
            ['url','网站地址','text'],
            ['logo','网站logo','image'],
            ['description','描述'],
            ['sort','排序'],
            ['status', '状态', 'status', '0',['0' => '否','1' => '是']],
            ['create_time', '创建时间','datetime'],
            ['update_time', '更新时间','datetime'],
        ];

        $search = [
            ['text', 'name', '网站名称', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db('links')
                ->where($where)
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
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
	}

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $data['create_time'] = time();
            try {
                validate(\app\validate\Links::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $result = db('links')->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('name','网站名称','填写网站名称')
            ->addText('url','网站地址','填写网站地址,需带http://或https://')
            ->addImage('logo','网站logo','上传网站logo')
            ->addTextarea('description','描述','填写描述')
            ->addText('sort','排序','填写排序')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            try {
                validate(\app\validate\Links::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $data['update_time'] = time();
            $result = db('links')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db('links')->where('id',$id)->find();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','网站名称','填写网站名称',$info['name'])
            ->addText('url','网站地址','填写网站地址,需带http://或https://',$info['url'])
            ->addImage('logo','网站logo','上传网站logo',$info['logo'])
            ->addTextarea('description','描述','填写描述',$info['description'])
            ->addText('sort','排序','填写排序',$info['sort'])
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }
}