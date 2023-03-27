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

namespace app\backend\admin;
use app\common\controller\Backend;
use app\common\library\helper\ArrayHelper;
use app\model\Config as ConfigModel;
use think\facade\Request;

class Config extends Backend
{
    protected $table = 'config';
    public function initialize()
    {
        parent::initialize();
        $this->model = new ConfigModel();
    }

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['name', '变量名'],
            /*['group','变量分组'],*/
            ['title','变量标题'],
            /*['tips','描述'],*/
            ['type','变量类型','tag'],
            /*['value','变量值'],*/
            ['sort','排序'],
        ];

        $config_group = db('config_group')->column('name','id');
        $search = [
            ['select', 'group', '配置分组', '=','',$config_group],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            $list = db('config')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();

            $fileType = config('app.fieldType');

            foreach ($list['data'] as $k=>$v) {
                $list['data'][$k]['group'] = db('config_group')->where(['id'=>$v['group']])->value('name');
                $list['data'][$k]['type'] = $fileType[$v['type']];
            }
            return $list;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->setLinkGroup([
                [
                    'title'=>'配置列表',
                    'link'=>(string)url('index'),
                    'active'=> 1
                ],
                [
                    'title'=>'配置分组',
                    'link'=>(string)url('group'),
                    'active'=> 0
                ],
            ])
            ->fetch();
    }

    public function config()
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post()) {
                foreach ($data as $k => $v) {
                    if (is_array($v) && isset($v['key']) && isset($v['value'])) {
                        $value = [];
                        foreach ($v['key'] as $k1=>$v1)
                        {
                            $value[$v1] = $v['value'][$k1];
                        }
                        $data[$k] = $value;
                    }
                }
                $configList = [];
                foreach (db('config')->select()->toArray() as $v)
                {
                    if (isset($data[$v['name']])) {
                        $value = $data[$v['name']];
                        $option = json_decode($v['option'],true);
                        if(in_array($v['type'],['array','images','files'])){
                            $option = $value;
                            $value = 0;
                        } else{
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $v['value'] = $value;
                        $v['option'] = json_encode($option,JSON_UNESCAPED_UNICODE);
                        $configList[] = $v;
                    }
                }
                $result=$this->model->saveAll($configList);
                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('提交失败或数据无变化');
                }
            }
        }

        $group = $this->request->param('group',1);
        $columns = [];
        $list =$this->model->where('group',$group)->order(['sort'=>'ASC','id'=>'ASC'])->column('type,name,title,tips,option,value,dict_code');
        foreach ($list as $key=>$val)
        {
            if(in_array($val['type'],['editor','textarea','code','html']))
            {
                $val['value'] = htmlspecialchars_decode($val['value']);
            }

            if($val['type']!='html')
            {
                $val['tips'] = ($val['tips'] ? $val['tips'].';' : '')."调用方式：<span class='badge badge-info'>get_setting('".$val['name']."')</span>";
            }

            if($val['dict_code'])
            {
                $val['option'] = db('dict')->where(['dict_id'=>$val['dict_code']])->column('name','value');
                unset($val['dict_code']);
            }else{
                $val['option'] = json_decode($val['option'],true);
            }
            if(!in_array($val['type'],['radio','checkbox','select','array','images','files','select2']))
            {
                unset($val['option']);
            }
            $columns[$key] = array_values($val);
        }
        $filedGroup = db('config_group')->column('name,id');
        $links = [];
        foreach ($filedGroup as $key => $value) {
            $links[] = [
                'link'=>(string)url('config',['group'=>$value['id']]),
                'title'=>$value['name'],
                'active'=>$group==$value['id'] ? 1 : 0
            ];
        }

        // 构建页面
        return $this->formBuilder
            ->addLinkGroup($columns,$links)
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }else{
                $data['option'] = '';
            }
            $data['settings'] = isset($data['settings']) ? json_encode($data['settings'],JSON_UNESCAPED_UNICODE) : '';
            $result = db('config')->insert($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        $config_group = db('config_group')->column('name','id');
        $dictionary = db('dict_type')->column('title','id');

        return $this->formBuilder
            ->addSelect('group','配置分组','',$config_group)
            ->addSelect('type','配置类型','',config('app.fieldType'))
            ->addText('name','变量名')
            ->addText('title','配置标题')
            ->addText('value','默认值')
            ->addSelect('source','数据源','',[0=>'本身数据',1=>'字典数据'])
            ->setSelectTrigger('source',0,'option','show','hide')
            ->setSelectTrigger('source',1,'dict_code','show','hide')
            ->addSelect('dict_code','字典类型','',$dictionary)
            ->addTextarea('option','配置信息','填写配置选项,格式为：配置值|配置名,一行一个')
            ->addTextarea('tips','提示信息')
            ->addText('sort','排序值')
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            unset($data['_ajax_open']);
            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $data['settings'] = isset($data['settings']) ? json_encode($data['settings'],JSON_UNESCAPED_UNICODE) : '';
            $result = db('config')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db($this->table)->where('id',$id)->find();
        $info['option'] = json_decode($info['option'],true);
        $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';
        $config_group = db('config_group')->column('name','id');
        $dictionary = db('dict_type')->column('title','id');
        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addSelect('group','配置分组','',$config_group)
            ->addSelect('type','配置类型','',config('app.fieldType'))
            ->addText('name','变量名')
            ->addText('title','配置标题')
            ->addText('value','默认值')
            ->addSelect('source','数据源','',[0=>'本身数据',1=>'字典数据'])
            ->setSelectTrigger('source',0,'option','show','hide')
            ->setSelectTrigger('source',1,'dict_code','show','hide')
            ->addSelect('dict_code','字典类型','',$dictionary)
            ->addTextarea('option','配置信息','填写配置选项,格式为：配置值|配置名,一行一个')
            ->addTextarea('tips','提示信息')
            ->addText('sort','排序值')
            ->fetch();
    }

    public function group()
    {
        $columns = [
            ['id'  , '编号'],
            ['name', '分组名称'],
            ['description','备注'],
            ['sort','排序'],
            ['status','状态'],
        ];
        $search = [
            ['text', 'name', '配置分组'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db('config_group')
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
            ->setAddUrl((string)url('group_add'))
            ->setEditUrl((string)url('group_edit',['id'=>'__id__']))
            ->setDelUrl((string)url('group_delete'))
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->setLinkGroup([
                [
                    'title'=>'配置列表',
                    'link'=>(string)url('index'),
                    'active'=> 0
                ],
                [
                    'title'=>'配置分组',
                    'link'=>(string)url('group'),
                    'active'=> 01
                ],
            ])
            ->fetch();
    }

    public function group_add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $data['create_time'] = time();
            $result = db('config_group')->insert($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        return $this->formBuilder
            ->addText('name','分组名称','选择分组名称')
            ->addTextarea('description','备注','填写备注')
            ->addText('sort','排序值','填写排序值默认0',0)
            ->addRadio('status','状态','状态（1 正常，0 锁定）',[0=>'锁定',1=>'正常'],1)
            ->fetch();
    }

    public function group_edit($id)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            $data['update_time'] = time();
            $result = db('config_group')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db('config_group')->where('id',$id)->find();
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','分组名称','填写分组名称',$info['name'])
            ->addTextarea('description','备注','填写备注',$info['description'])
            ->addText('sort','排序值','填写排序值默认0',$info['sort'])
            ->addRadio('status','状态','状态（1 正常，0 锁定）',[0=>'锁定',1=>'正常'],$info['status'])
            ->fetch();
    }

    public function group_delete()
    {
        if($this->request->isPost())
        {
            $id = $this->request->post('id',0,'intval');
            if($info = db('config_group')->where('id',$id)->find())
            {
                db('config_group')->where('id',$id)->delete();
            }
            return json(['error'=>0,'msg'=>'删除成功!']);
        }
    }

    public function state($id=0)
    {
        if($type = $this->request->param('type'))
        {
            $info = [];
            if($id)
            {
                $info = db('config')->where('id',$id)->find();
                $info['option'] = json_decode($info['option'],true);
                $info['settings'] = json_decode($info['settings'],true);
                $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';
            }
            $this->assign(['type'=>$type,'fieldInfo'=>$info]);
            return $this->fetch('type');
        }
    }
}