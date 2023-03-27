<?php

namespace app\backend\member;
use app\common\controller\Backend;

class ReputationGroup extends Backend
{
    protected $table = 'users_reputation_group';

    //用户组列表
    public function index()
    {
        $columns = [
            ['id','组ID'],
            ['group_icon','组标识','image'],
            ['min_reputation', '最小条件'],
            ['max_reputation', '最大条件'],
            ['reputation_factor','威望系数'],
            ['title', '组名称'],
            ['status', '状态', 'status', '0',['0' => '禁用','1' => '启用']],
        ];

        $search = [
            ['text', 'title', '组名称', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'asc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db($this->table)
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => $this->request->get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setPageTips('特别提醒：<br>1、当前台配置使用威望组时，此分组中的权限生效<br>2、填写威望最小最大值时请注意不要有交叉的情况出现')
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setPagination('false')
            ->addRightButtons([
                'edit',
                'delete',
                'permission'=>[
                    'type'          =>'permission',
                    'title'         => '权限管理',
                    'icon'          => 'fa fa-cogs',
                    'class'         => 'btn btn-success btn-xs aw-ajax-open',
                    'url'           => (string)url('permission',['id'=>'__id__']),
                    'href'          =>''
                ]
            ])
            ->addTopButtons(['add','delete'])
            ->fetch();
    }

    //添加用户组
    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $permission = db('users_permission')->column('name,value');
            $permission_arr =[];
            foreach ($permission as $key=>$val)
            {
                $permission_arr[$val['name']]=$val['value'];
            }
            $data['permission'] = json_encode($permission_arr,JSON_UNESCAPED_UNICODE);

            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('title','组名称','填写组名称')
            ->addImage('group_icon','组标识','上传组标识')
            ->addText('min_reputation','最小条件','升级到该组最小条件,请勿出现重叠条件')
            ->addText('max_reputation','最大条件','升级到该组最大条件,请勿出现重叠条件')
            ->addNumber('reputation_factor','威望系数')
            ->addTextarea('remark','威望组备注')
            ->addRadio('status','状态','是否启用',['0' => '禁用','1' => '启用'],1)
            ->fetch();
    }

    public function edit( $id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            $result = db($this->table)->where('id',$data['id'])->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =db($this->table)->find($id);
        // 获取字段信息
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('title','组名称','填写组名称',$info['title'])
            ->addImage('group_icon','组标识','上传组标识',$info['group_icon'])
            ->addText('min_reputation','最小条件','升级到该组最小条件,请勿出现重叠条件',$info['min_reputation'])
            ->addText('max_reputation','最大条件','升级到该组最大条件,请勿出现重叠条件',$info['max_reputation'])
            ->addNumber('reputation_factor','威望系数','',$info['reputation_factor'])
            ->addTextarea('remark','威望组备注','',$info['remark'])
            ->addRadio('status','状态','是否启用',['0' => '禁用','1' => '启用'],$info['status'])
            ->fetch();
    }

    public function permission($id=0)
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post()) {
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
                $result=db($this->table)->where(['id'=>$id])->update(['permission'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('提交失败或数据无变化');
                }
            }
        }

        $permission = db($this->table)->where(['id'=>$id])->value('permission');
        $permission = json_decode($permission,true);
        $list = db('users_permission')->whereRaw('`group`="common" OR `group`= "reputation"')->column('type,name,title,tips,option,value');

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

    // 删除
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
}