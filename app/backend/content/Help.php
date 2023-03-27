<?php
namespace app\backend\content;

use app\common\controller\Backend;
use think\exception\ValidateException;
use think\facade\Request;

class Help extends Backend
{
    protected $table = 'help_chapter';

    public function index()
    {
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db($this->table)
                ->order(['sort'=>'ASC',$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addText('id','ID','','','','true')
            ->addImage('image','章节图标')
            ->addLink('title','章节标题',get_url('help/detail',['token'=>'__url_token__']))
            ->addText('description','章节描述','','','','true')
            ->addStatus('status','状态',1)
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

            // 字段规则验证
            try {
                validate(\app\validate\Help::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if(db($this->table)->where(['url_token'=>$data['url_token']])->value('id'))
            {
                $this->error('章节标识已存在');
            }

            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }
        return $this->formBuilder
            ->addText('title','章节标题','填写章节标题')
            ->addText('url_token','章节别名','填写章节别名')
            ->addImage('image','章节图标','','','','','','','help')
            ->addTextarea('description','章节描述','请输入章节描述')
            ->addText('sort','排序值','值越大越靠前')
            ->addRadio('status','启用状态','',['1' => '启用','0' => '禁用'],'1')
            ->fetch();
    }

    public function edit()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');

            // 字段规则验证
            try {
                validate(\app\validate\Help::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            $result = db($this->table)->update($data);
            if (!$result) {
                $this->error('更新失败');
            } else {
                $this->success('更新成功','index');
            }
        }

        $id = $this->request->param('id',0,'intval');
        $info = db($this->table)->where('id',$id)->find();
        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addText('title','章节标题','填写章节标题')
            ->addText('url_token','章节别名','填写章节别名')
            ->addImage('image','章节图标','','','','','','','help')
            ->addTextarea('description','章节描述','请输入章节描述')
            ->addText('sort','排序值','值越大越靠前')
            ->addRadio('status','启用状态','',['1' => '启用','0' => '禁用'],'1')
            ->fetch();
    }
}