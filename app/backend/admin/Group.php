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
use app\model\admin\AdminGroup;

class Group extends Backend
{
    protected $table = 'admin_group';
    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['title', '组名称'],
            ['status', '是否启用', 'status', '0',[
                ['0' => '禁用'],
                ['1' => '启用']
            ]],
        ];

        $search = [
            ['text', 'title', '组名称', 'LIKE'],
        ];
        // 搜索
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'asc';
            $where = $this->makeBuilder->getWhere($search);
            // 排序处理
            $list = db('admin_group')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();
            // 渲染输出
            return [
                'total'        => count($list),
                'per_page'     => 1000,
                'current_page' => 1,
                'last_page'    => 1,
                'data'         => $list,
            ];
        }
        // 构建页面
        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setPageTips('特别提醒：<br>1、当前台用户所在系统组为普通用户时，普通用户组权限会被前台默认使用组覆盖<br>2、前台其他非普通组权限会覆盖前台默认组的权限，请谨慎配置权限')
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setPagination('false')
            ->addRightButtons(['edit','delete','permission'=>[
                'type'          =>'permission',
                'title'         => '权限管理',
                'icon'          => 'fa fa-cogs',
                'class'         => 'btn btn-success btn-xs aw-ajax-open',
                'url'           => (string)url('permission',['id'=>'__id__']),
                'href'          =>''
            ]])
            ->addTopButtons(['add','delete'])
            ->fetch();

    }

    public function add()
    {
        if ($this->request->isPost())
        {
            $data = $this->request->post();

            $permission = db('users_permission')->column('name,value');
            $permission_arr =[];
            foreach ($permission as $key=>$val)
            {
                $permission_arr[$val['name']]=$val['value'];
            }
            $data['permission'] = json_encode($permission_arr,JSON_UNESCAPED_UNICODE);

            $result = db('admin_group')->insertGetId($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        return $this->formBuilder
            ->addFormItems([
                ['text', 'title', '系统组名称', '系统组名称'],
                ['checkbox2', 'rules', '权限规则', '', json_encode($this->auth->getGroupAuthRule())],
                ['radio', 'status', '启用状态', '', ['0' => '禁用','1' => '启用'], 0],
            ])
            ->fetch();
    }

	public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data = $this->request->post();
            if($data['id']==1)
            {
                $data['rules'] = '*';
            }
            $result = db('admin_group')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }
        $info =db('admin_group')->find($id);
        $columns = [
            ['hidden', 'id', $info['id']],
            ['text', 'title', '系统组名称', '系统组名称', $info['title']],
            ['radio', 'status', '启用状态', '', ['0' => '禁用','1' => '启用'], $info['status']],
            ['checkbox2', 'rules', '权限规则', '', json_encode($this->auth->getGroupAuthRule($id)),  $info['rules']],
        ];
        return $this->formBuilder->addFormItems($columns)->fetch();
    }

    public function delete()
    {
        if ($this->request->isPost())
        {
            $id= $this->request->post('id');
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);

                if(db($this->table)->where('system',0)->whereIn('id',$ids)->delete())
                {
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }
                return json(['error' => 1, 'msg' => '删除失败,删除组可能包含系统内置组']);
            }

            if(db($this->table)->where('system',0)->where('id',$id)->delete())
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return json(['error' => 1, 'msg' => '删除失败,删除组可能为系统内置组']);
        }
    }

    public function permission($id=0)
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post())
            {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        if(isset($v['key']) && isset($v['value']))
                        {
                            $value = [];
                            foreach ($v['key'] as $k1=>$v1)
                            {
                                $value[$v1] = $v['value'][$k1];
                            }
                            $data[$k] = $value;
                        }
                    }
                }
                $data = json_encode($data,JSON_UNESCAPED_UNICODE);
                $result= db('admin_group')->where('id',$id)->update(['permission'=>$data]);
                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('提交失败或数据无变化');
                }
            }
        }

        $permission = db($this->table)->where(['id'=>$id])->value('permission');
        $permission = json_decode($permission,true);
        //排除游客组权限
        if($id==5){
            $list = db('users_permission')
                ->whereIN('name',['visit_website'])
                ->order('sort','asc')
                ->column('type,name,title,tips,option,value');
        }else{
            $list = db('users_permission')->whereRaw('`group`="common" OR `group`= "system"')->order('sort','asc')->column('type,name,title,tips,option,value');
        }
        $columns = array();
        foreach ($list as $key=>$val)
        {
            $list[$key]['option'] = json_decode($val['option'],true);
            if(!in_array($val['type'],['radio','checkbox','select','array']))
            {
                unset($list[$key]['option']);
            }
            if(isset($permission[$val['name']]))
            {
                $list[$key]['value'] = $permission[$val['name']];
            }
        }
        foreach ($list as $key=>$val)
        {
            $columns[$key] = array_values($val);
        }

        // 构建页面
        return $this->formBuilder
            ->addFormItems($columns)
            ->setFormUrl((string)url('permission',['id'=>$id]))
            ->fetch();
    }
}