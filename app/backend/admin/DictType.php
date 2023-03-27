<?php
namespace app\backend\admin;
use app\common\controller\Backend;

class DictType extends Backend
{
    protected $table = 'dict_type';

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['title', '字典名称'],
            ['name', '字典标识'],
            ['remark','备注'],
        ];

        $search = [];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',15);
            // 排序处理
            return db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate($pageSize)
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
            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('name','字典标识','请填写字典标识,一般为英文')
            ->addText('title','字典名称','请填写字典名称')
            ->addText('remark','备注','请填写备注')
            ->fetch();
    }

    public function edit()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = db($this->table)->update($data);
            if (!$result) {
                $this->error('更新失败');
            }

            $this->success('更新成功','index');
        }
        $id = $this->request->param('id',0);
        $info = db($this->table)->where('id',$id)->find();
        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addText('name','字典标识','请填写字典标识,一般为英文')
            ->addText('title','字典名称','请填写字典名称')
            ->addText('remark','备注','请填写备注')
            ->fetch();
    }
}