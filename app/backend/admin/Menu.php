<?php
namespace app\backend\admin;
use app\common\library\helper\TreeHelper;
use app\common\controller\Backend;
use app\model\admin\MenuRule;

class Menu extends Backend
{
    protected $model;
    protected $table = 'menu_rule';
    public function initialize()
    {
        parent::initialize();
        $this->model = new MenuRule();
    }

    public function index(){
        $group= $this->request->param('group','nav');
        $columns = [
            ['id'  , '编号'],
            ['title', '导航名称'],
            ['icon', '图标','icon'],
            ['name','导航链接'],
            ['is_home', '默认首页', 'status', '1', ['0' => '否','1' => '是']],
            ['status', '状态', 'status', '1', ['0' => '否','1' => '是']],
            ['sort', '排序', 'sort'],
        ];
        if ($this->request->param('_list'))
        {
            $group= $this->request->param('group','nav');
            $orderByColumn ='id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $list = db('menu_rule')
                ->where('group',$group)
                ->order(['sort'=>'ASC',$orderByColumn => $isAsc])
                ->select()
                ->toArray();
            $list = TreeHelper::tree($list);
            foreach ($list as $k => $v) {
                $list[$k]['title'] = $v['left_title'];
                $list[$k]['icon'] =  $v['icon'] ? "<i class=\"{$v['icon']}\"></i>" : '';
            }

            // 渲染输出
            return [
                'total' => count($list),
                'per_page' => 10000,
                'current_page' => 1,
                'last_page' => 1,
                'data' => $list,
            ];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButton('info', [
                'title' => '添加',
                'icon'  => 'fa fa-plus',
                'class' => 'btn btn-success btn-sm aw-ajax-open',
                'href'=>'',
                'url'  => (string)url('add', ['pid' => '__id__','group'=>$group])
            ])
            ->setAddUrl((string)url('add?group='.$group))
            ->setDataUrl((string)url('index?_list=1&group='.$group))
            ->addRightButtons(['edit','delete'])        // 设置右侧操作列
            ->addTopButtons(['add','delete','export'])            // 设置顶部按钮组
            ->addTopButton('default', [
                'title'       => '展开/折叠',
                'icon'        => 'fas fa-exchange-alt',
                'class'       => 'btn btn-info treeStatus',
                'href'        => '',
                'onclick'     => 'AWS_ADMIN.operate.treeStatus()'
            ]) // 自定义按钮
            ->setPagination('false')
            ->setParentIdField('pid')
            ->setLinkGroup([
                [
                    'title'=>'主导航',
                    'link'=>(string)url('index?group=nav'),
                    'active'=> $group=='nav'
                ],
                [
                    'title'=>'底部导航',
                    'link'=>(string)url('index?group=footer'),
                    'active'=> $group=='footer'
                ],
            ])
            ->fetch();
    }

    public function add($group='nav',$pid=0)
    {
        if ($this->request->isPost())
        {
            $data = $this->request->except(['file'], 'post');
            if ($data) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        $data[$k] = implode(',', $v);
                    }
                }
            }

            if($data['is_home'] && $data['status'] && $data['type']==1)
            {
                db($this->table)->where(['is_home'=>1])->update(['is_home'=>0]);
            }

            if($data['is_home'] && $data['type']!=1)
            {
                $data['is_home']=0;
            }

            $result = $this->model->create($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }

        $result = MenuRule::getPidOptions($group);
        return $this->formBuilder
            ->addHidden('group',$group)
            ->addSelect('pid','父级标题','选择父级标题',$result,$pid)
            ->addIcon('icon','选择图标','选择图标')
            ->addText('name','导航链接','模块/控制器/方法,如member/MenuRule/index 或 外部链接完整url')
            ->addRadio('type','链接类型','选择链接类型',['1' => '站内','2' => '站外'],1)
            ->addRadio('is_home','默认首页','作为默认首页',['1' => '是','0' => '否'],0)
            ->addText('title','导航名称','请输入导航名称')
            ->addText('param','附加参数','URL地址后的参数，如 type=button&name=my')
            ->addRadio('auth_open','登录显示','选择是否登录显示',['1' => '是','0' => '否'],0)
            ->addRadio('status','菜单状态','选择菜单状态',['0' => '禁用','1' => '启用'],1)
            ->addText('sort','排序值','',50)
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost()) {
            $data = $this->request->except(['file'], 'post');
            if ($data) {
                foreach ($data as $k => $v) {
                    if (is_array($v)) {
                        $data[$k] = implode(',', $v);
                    }
                }
            }

            if($data['is_home'] && $data['status'] && $data['type']==1)
            {
                db($this->table)->where(['is_home'=>1])->update(['is_home'=>0]);
            }

            if($data['is_home'] && $data['type']!=1)
            {
                $data['is_home']=0;
            }

            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =$this->model->find($id);
        $result = MenuRule::getPidOptions();
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('pid','父级标题','选择父级标题',$result,$info['pid'])
            ->addIcon('icon','选择图标','选择图标',$info['icon'])
            ->addText('name','导航链接','模块/控制器/方法,如member/MenuRule/index 或 外部链接完整url',$info['name'])
            ->addRadio('type','链接类型','选择链接类型',['1' => '站内','2' => '站外'],$info['type'])
            ->addRadio('is_home','默认首页','作为默认首页',['1' => '是','0' => '否'],$info['is_home'])
            ->addText('title','导航名称','请输入导航名称',$info['title'])
            ->addText('param','附加参数','URL地址后的参数，如 type=button&name=my',$info['param'])
            ->addRadio('auth_open','登录显示','选择是否登录显示',['1' => '是','0' => '否'],$info['auth_open'])
            ->addRadio('status','菜单状态','选择菜单状态',['0' => '禁用','1' => '启用'],$info['status'])
            ->addText('sort','排序值','',$info['sort'])
            ->fetch();
    }

    public function state()
    {
        if (request()->isPost())
        {
            $id = request()->post('id');
            $field = request()->post('field');
            $info =db($this->table)->find($id);
            $status = $info[$field] == 1 ? 0 : 1;

            if($field=='is_home' && $status && $info['type']!=1)
            {
                return json(['error'=>1, 'msg'=>'仅可把站内链接设为默认首页!']);
            }

            if($field=='is_home' && $status && $info['type']==1)
            {
                db($this->table)->where(['is_home'=>1])->update(['is_home'=>0]);
            }

            db($this->table)->where($this->getPk(),$id)->update([$field=>$status]);
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }
}