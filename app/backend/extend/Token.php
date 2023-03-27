<?php
namespace app\backend\extend;
use app\common\controller\Backend;
use app\common\library\helper\RandomHelper;
use think\facade\Request;

class Token extends Backend
{
    protected $table = 'app_token';

    public function index()
    {
        $columns = [
            ['id','编号'],
            ['title', '客户端名称'],
            ['token', '客户端Token'],
            ['create_time', '创建时间','datetime'],
        ];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            return db('app_token')
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
            $data['token'] = RandomHelper::alpha(16);
            $result = db('app_token')->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('title','客户端名称','填写客户端名称')
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            $result = db('app_token')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db('app_token')->where('id',$id)->find();

        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addText('title','客户端名称','填写客户端名称')
            ->fetch();
    }
}