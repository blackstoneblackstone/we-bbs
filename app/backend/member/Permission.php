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

namespace app\backend\member;
use app\common\controller\Backend;
use app\model\Permission as PermissionModel;
use app\common\library\helper\ArrayHelper;
use think\facade\Request;

class Permission extends Backend
{
    protected $table = 'users_permission';
	public function initialize()
    {
        parent::initialize();
        $this->model = new PermissionModel();
    }

	public function index()
	{
        $columns = [
            ['id','编号'],
            ['name', '配置名称'],
            ['title','配置标题'],
            ['type','配置类型','tag',0,config('app.fieldType')],
            ['value','默认值'],
            ['sort','排序'],
        ];
        $search = [
            ['text', 'name', '配置名称', 'LIKE'],
            ['select', 'type', '配置类型', '=','',config('app.fieldType')]
        ];
        $group = $this->request->param('group','common');
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return $this->model
                ->where($where)
                ->where(['group'=>$group])
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
            ->setDataUrl( Request::baseUrl().'?_list=1&group='.$group)
            ->setSearch($search)
            ->setPageTips('特别提醒：<br>1、前台默认会使用管理员配置的分组类型中的权限<br>2、系统组权限除普通用户组使用前台配置的分组权限，其余均使用系统组权限<br>3、通用权限在所有权限组均可使用')
            ->addColumn('right_button', '操作', 'btn')
            ->setPagination('false')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->setLinkGroup([
                [
                    'title'=>'通用权限',
                    'link'=>(string)url('index', ['group' => 'common']),
                    'active'=> $group=='common'
                ],
                [
                    'title'=>'系统组权限',
                    'link'=>(string)url('index', ['group' => 'system']),
                    'active'=> $group=='system'
                ],
                [
                    'title'=>'威望组权限',
                    'link'=>(string)url('index', ['group' =>'reputation']),
                    'active'=> $group=='reputation'
                ],
                [
                    'title'=>'积分组权限',
                    'link'=>(string)url('index', ['group' =>'integral']),
                    'active'=> $group=='integral'
                ],
            ])
            ->fetch();
	}

	public function add()
	{
        if ($this->request->isPost())
        {
            $data = $this->request->except(['file'], 'post');
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                PermissionModel::updateUsersPermission();
                $this->success('添加成功','index');
            }
        }
        return $this->formBuilder
            ->addText('name','配置名称','填写配置名称')
            ->addText('title','配置标题','填写配置标题')
            ->addSelect('type','配置类型','请选择配置类型',config('app.fieldType'))
            ->addTextarea('tips','配置简介','填写配置简介')
            ->addText('value','默认值','填写默认值')
            ->addTextarea('option','配置选项','填写配置选项')
            ->addSelect('group','配置分组','填写配置分组',['common'=>'通用权限','system'=>'系统组权限','reputation'=>'威望组权限','integral'=>'积分组权限'])
            ->addText('sort','排序值','填写排序值','0')
            ->fetch();
	}

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'], 'post');
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $result = $this->model->update($data);

            if ($result) {
                PermissionModel::updateUsersPermission();
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =$this->model->find($id)->toArray();
        $info['option'] = json_decode($info['option'],true);
        $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','配置名称','填写配置名称',$info['name'])
            ->addText('title','配置标题','填写配置标题',$info['title'])
            ->addSelect('type','配置类型','请选择配置类型',config('app.fieldType'),$info['type'])
            ->addTextarea('tips','配置简介','填写配置简介',$info['tips'])
            ->addText('value','默认值','填写默认值',$info['value'])
            ->addTextarea('option','配置选项','填写配置选项',$info['option'])
            ->addText('sort','排序值','填写排序值',$info['sort'])
            ->addSelect('group','配置分组','填写配置分组',['common'=>'通用权限','system'=>'系统组权限','reputation'=>'威望组权限','integral'=>'积分组权限'],$info['group'])
            ->fetch();
    }
}