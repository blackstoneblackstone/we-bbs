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
use app\common\library\helper\ArrayHelper;
use think\exception\ValidateException;
use think\facade\Request;

/**
 * 用户认证
 * Class Verify
 */
class Verify extends Backend
{
    protected $table = 'users_verify';
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\model\Verify();
    }

    public function index()
    {
        $verify_type = db('users_verify_type')->where(['status'=>1])->column('title','name');
        $columns = [
            ['id' , '编号'],
            ['nick_name', '认证用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['type', '认证类型','tag', '', $verify_type],
            ['reason','审核理由'],
            ['create_time', '创建时间','datetime'],
        ];
        $type =  $this->request->param('type','');
        $search = [
            ['select', 'type', '审核类型', '=',$type,$verify_type]
        ];
        $status = $this->request->param('status',2);
        $buttons = [
            'config' => [
                'title'       => '预览',
                'icon'        => '',
                'class'       => 'btn btn-success btn-xs aw-ajax-open',
                'href'        =>'',
                'url'        => (string)url('preview', ['id' => '__id__']),
            ],
            'approval'=>[
                'title'       => '通过',
                'icon'        => '',
                'class'       => 'btn btn-warning btn-xs aw-ajax-get',
                'url'        => (string)url('manager', ['type'=>'approval','id' => '__id__']),
                'href' =>''
            ],
            'refuse'=>[
                'title'       => '拒绝',
                'icon'        => '',
                'class'       => 'btn btn-danger btn-xs aw-ajax-open',
                'url'        => (string)url('manager', ['type'=>'refuse','id' => '__id__']),
                'href' =>''
            ]
        ];

        if($status>1)
        {
            unset($buttons['approval'],$buttons['refuse']);
        }

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            $data = db($this->table)
                ->where($where)
                ->where(['status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])->toArray();

            foreach ($data['data'] as $k=>$v) {
                $data['data'][$k]['url_token'] = db('users')->where('uid',$v['uid'])->value('url_token');
                $data['data'][$k]['nick_name'] = db('users')->where('uid',$v['uid'])->value('nick_name');
            }
            return $data;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->addRightButtons($buttons)
            ->setLinkGroup([
                [
                    'title'=>'已审核',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'已拒绝',
                    'link'=>(string)url('index', ['status' => 3]),
                    'active'=> $status==3
                ]
            ])
            ->fetch();
    }

    /**
     * 预览认证内容
     */
    public function preview($id)
    {
        if ($this->request->isPost())
        {
            if ($data=$this->request->post()) {
                foreach ($data as $k => $v)
                {
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
                $result= db($this->table)->where(['id'=>$id])->update(['data'=>json_encode($data,JSON_UNESCAPED_UNICODE)]);
                if ($result) {
                    $this->success('修改成功');
                } else {
                    $this->error('提交失败或数据无变化');
                }
            }
        }

        $info = db($this->table)->where(['id'=>$id])->find();
        $data = json_decode($info['data'],true);
        $list = db('verify_field')->where('verify_type',$info['type'])->column('type,name,title,tips,option,value');

        $columns = [];
        foreach ($list as $key=>$val)
        {
            $list[$key]['option'] = json_decode($val['option'],true);
            if(!in_array($val['type'],['radio','checkbox','select','array']))
            {
                unset($list[$key]['option']);
            }

            if(isset($data[$val['name']]))
            {
                $list[$key]['value'] = $data[$val['name']];
            }

            $list[$key]['extra_attr'] = 'disabled readonly';
        }

        foreach ($list as $key=>$val)
        {
            $columns[$key] = array_values($val);
        }

        // 构建页面
        if($info['status']==3)
        {
            return $this->formBuilder
                ->addFormItems($columns)
                ->addTextarea('reason','拒绝原因','',$info['reason'],'disabled readonly')
                ->setFormUrl((string)url('preview',['id'=>$id]))
                ->hideBtn('submit')
                ->fetch();
        }
        return $this->formBuilder
            ->addFormItems($columns)
            ->setFormUrl((string)url('preview',['id'=>$id]))
            ->hideBtn('submit')
            ->fetch();
    }

    /**
     * 认证审核管理
     */
    public function manager($type,$id)
    {
        if (!$verifyInfo = db($this->table)->find($id)) $this->error('审核数据不存在');

        if (!in_array($type,['refuse','approval'])) $this->error('操作类型错误');

        if($this->request->isPost())
        {
            db($this->table)->where(['id' => $this->request->post('id')])->update(['status' => 3,'reason'=>$this->request->post('reason')]);
            //用户认证拒绝审核钩子
            hook('userVerifyRefuse',['post_data'=>$this->request->post(),'info'=>$verifyInfo]);
            $this->success('操作成功');
        }

        if($type=='refuse')
        {
            return $this->formBuilder
                ->addHidden('id',$id)
                ->addHidden('type','refuse')
                ->addTextarea('reason','拒绝原因')
                ->fetch();
        }

        $status = $type=='approval' ? 2 : 3;
        $res = db($this->table)->where(['id' => $id])->update(['status' => $status]);
        if ($status == 2 && $res){
            \app\model\Users::updateUserFiled($verifyInfo['uid'], ['verified' => $verifyInfo['type']]);
            //用户认证通过审核钩子
            hook('userVerifyApproval',$verifyInfo);
        };
        $this->success('操作成功');
    }

    /**
     * 认证字段管理
     * @return string
     */
    public function field()
    {
        $verify_type = db('users_verify_type')->where(['status'=>1])->column('title','name');
        $columns = [
            ['id'  , '编号'],
            ['name', '变量名'],
            ['verify_type','认证类型','tag','',$verify_type],
            ['validate', '是否必填','tag', '', [0=>'否',1=>'是']],
            ['verify_show', '是否认证显示','tag', '', [0=>'否',1=>'是']],
            ['title','变量标题'],
            ['type','变量类型','tag','',config('app.fieldType')],
        ];
        $search = [
            ['select', 'verify_type', '认证类型', '=','',$verify_type]
        ];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            $result = db('verify_field')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ]);
            return $result->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->setSearch($search)
            ->setDelUrl((string)url('delete_field'))
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

            try {
                validate(\app\validate\VerifyField::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }
            $data['create_time'] = time();
            $result = db('verify_field')->insert($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        $verify_type = db('users_verify_type')->where(['status'=>1])->column('title','name');
        return $this->formBuilder
            ->addSelect('verify_type','认证类型','选择认证类型',$verify_type)
            ->addSelect('type','变量类型','选择变量类型',config('app.fieldType'))
            ->addText('name','变量名','填写变量名')
            ->addText('title','变量标题','填写变量标题')
            ->addText('value','变量值','填写变量值')
            ->addTextarea('option','字段选项','填写字段选项,填写格式如：键|值')
            ->addRadio('validate','是否必填')
            ->addRadio('verify_show','是否在认证页面显示')
            ->addNumber('sort','排序值','默认为0',0)
            ->addTextarea('tips','描述','填写描述')
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');

            try {
                validate(\app\validate\VerifyField::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }


            if(isset($data['option']))
            {
                $data['option'] = json_encode(ArrayHelper::strToArr($data['option']),JSON_UNESCAPED_UNICODE);
            }

            $data['update_time'] = time();
            $result = db('verify_field')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info = db('verify_field')->where('id',$id)->find();
        $info['option'] = $info['option'] ? json_decode($info['option'],true) : [];
        $info['option'] = is_array($info['option']) ? ArrayHelper::arrToStr($info['option']) : '';
        $verify_type = db('users_verify_type')->where(['status'=>1])->column('title','name');
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('verify_type','认证类型','选择认证类型',$verify_type)
            ->addSelect('type','变量类型','选择变量类型',config('app.fieldType'),$info['type'])
            ->addText('name','变量名','填写变量名',$info['name'])
            ->addText('title','变量标题','填写变量标题',$info['title'])
            ->addText('value','变量值','填写变量值',$info['value'])
            ->addTextarea('option','字段选项','填写字段选项,填写格式如：键|值', $info['option'])
            ->addRadio('validate','是否必填','是否必填',$info['validate'])
            ->addRadio('verify_show','是否在认证页面显示','是否在认证页面显示',$info['verify_show'])
            ->addNumber('sort','排序值','默认为0',$info['sort'])
            ->addTextarea('tips','描述','填写描述',$info['tips'])
            ->fetch();
    }

    // 删除
    public function delete_field()
    {
        if ($this->request->isPost())
        {
            $id= $this->request->post('id');
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if(db('verify_field')->whereIn('id',$ids)->delete())
                {
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }
                return json(['error' => 1, 'msg' => '删除失败']);
            }
            if(db('verify_field')->where('id',$id)->delete())
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return json(['error' => 1, 'msg' => '删除失败']);
        }
    }

    //认证类型
    public function type()
    {
        $columns = [
            ['id'  , '编号'],
            ['icon', '类型图标','image'],
            ['name', '类型标识'],
            ['title', '类型名称'],
            ['status', '是否启用', 'status', '0',[
                ['0' => '禁用'],
                ['1' => '启用']
            ]],
        ];

        $search = [
            ['text', 'title', '类型名称', 'LIKE'],
        ];
        // 搜索
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'asc';
            $where = $this->makeBuilder->getWhere($search);
            // 排序处理
            return db('users_verify_type')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$this->request->param('pageSize',get_setting("contents_per_page",15)),
                ])
                ->toArray();
        }
        // 构建页面
        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->setDelUrl((string)url('delete_type'))
            ->setAddUrl((string)url('add_type'))
            ->setEditUrl((string)url('edit_type',['id'=>'__id__']))
            ->setStateUrl((string)url('state_type'))
            ->fetch();

    }

    public function add_type()
    {
        if ($this->request->isPost())
        {
            $data = $this->request->post();
            try {
                validate(\app\validate\VerifyFieldType::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $result = db('users_verify_type')->insertGetId($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        return $this->formBuilder
            ->addFormItems([
                ['image', 'icon', '类型图标', '类型图标'],
                ['text', 'name', '类型标识', '类型标识'],
                ['text', 'title', '类型名称', '类型名称'],
                ['radio', 'status', '启用状态', '',['0' => '禁用','1' => '启用'], 1],
                ['textarea', 'remark', '备注'],
            ])
            ->fetch();
    }

    public function edit_type($id=0)
    {
        if ($this->request->isPost())
        {
            $data = $this->request->post();

            try {
                validate(\app\validate\VerifyFieldType::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }

            $result = db('users_verify_type')->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }
        $info =db('users_verify_type')->find($id);
        return $this->formBuilder
            ->addFormItems([
                ['hidden', 'id'],
                ['image', 'icon', '类型图标', '类型图标'],
                ['text', 'name', '类型标识', '类型标识'],
                ['text', 'title', '类型名称', '类型名称'],
                ['radio', 'status', '启用状态', '',['0' => '禁用','1' => '启用']],
                ['textarea', 'remark', '备注'],
            ])
            ->setFormData($info)
            ->fetch();
    }

    public function delete_type()
    {
        if ($this->request->isPost())
        {
            $id= $this->request->post('id');
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if(db('users_verify_type')->whereIn('id',$ids)->delete())
                {
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }
                return json(['error' => 1, 'msg' => '删除失败']);
            }
            if(db('users_verify_type')->where('id',$id)->delete())
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return json(['error' => 1, 'msg' => '删除失败']);
        }
    }

    public function state_type()
    {
        if (request()->isPost())
        {
            $id = request()->post('id');
            $field = request()->post('field');
            $info =db('users_verify_type')->find($id);
            $status = $info[$field] == 1 ? 0 : 1;
            db('users_verify_type')->where('id',$id)->update([$field=>$status]);
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }
}