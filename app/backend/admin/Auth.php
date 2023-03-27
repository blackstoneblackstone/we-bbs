<?php
namespace app\backend\admin;
use app\common\library\helper\TreeHelper;
use app\common\controller\Backend;
use app\model\admin\AdminAuth;
use think\App;

class Auth extends Backend
{
    protected $table = 'admin_auth';
    protected $pk = 'id';
    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->model = new AdminAuth();
    }

    // 列表
    public function index(){
        // 获取列表数据
        $columns = [
            ['id'  , lang('编号')],
            ['title', lang('菜单名称')],
            ['icon', lang('图标'),'icon'],
            ['name',lang('控制器/方法')],
            ['auth_open', lang('权限验证'), 'tag', '0',['0' => '否','1' => '是']],
            ['status', lang('状态'), 'status', '1', ['0' => '否','1' => '是']],
            ['sort', lang('排序'), 'sort'],
        ];
        if ($this->request->param('_list'))
        {
            $orderByColumn ='id';
            $isAsc = $this->request->param('isAsc')=='asc' ? 'DESC': 'ASC';
            $list = db('admin_auth')->order([$orderByColumn => $isAsc])
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
        // 构建页面
        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->addColumn('right_button', lang('操作'), 'btn')
            ->addRightButton('info', [
                'title' => lang('add'),
                'icon'  => 'fa fa-plus',
                'class' => 'btn btn-success btn-sm aw-ajax-open',
                'href'=>'',
                'url'  => (string)url('add', ['pid' => '__id__'])
            ])
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete','export'])
            ->addTopButton('default', [
                'title'       => '展开/折叠',
                'icon'        => 'fas fa-exchange-alt',
                'class'       => 'btn btn-info treeStatus',
                'href'        => '',
                'onclick'     => 'AWS_ADMIN.operate.treeStatus()'
            ])
            ->setPagination('false')
            ->setParentIdField('pid')
            ->fetch();
    }

    // 添加
    public function add()
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
            $result = AdminAuth::create($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        $pid = $this->request->param('pid',0,'intval');
        $result = AdminAuth::getPidOptions();
        return $this->formBuilder
            ->addSelect('pid','父级标题','选择父级标题',$result,$pid)
            ->addIcon('icon','选择图标','选择图标')
            ->addText('name','控制器/方法','控制器/方法,如system.AuthRule/index')
            ->addText('title','菜单名称','请输入菜单名称')
            ->addText('param','附加参数','URL地址后的参数，如 type=button&name=my')
            ->addRadio('auth_open','验证权限','选择验证权限',['1' => '是','0' => '否'],1)
            ->addRadio('status','菜单状态','选择菜单状态',['0' => '禁用','1' => '启用'],1)
            ->addText('sort','排序值','',50)
            ->fetch();
    }

    // 修改
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
            $result = db('admin_auth')->update($data);
            if ($result) {
                $this->success('提交成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =db('admin_auth')->find($id);
        $result =AdminAuth::getPidOptions();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addSelect('pid','父级标题','选择父级标题',$result,$info['pid'])
            ->addIcon('icon','选择图标','选择图标',$info['icon'])
            ->addText('name','控制器/方法','控制器/方法,如system.AuthRule/index',$info['name'])
            ->addText('title','菜单名称','请输入菜单名称',$info['title'])
            ->addText('param','附加参数','URL地址后的参数，如 type=button&name=my',$info['param'])
            ->addRadio('auth_open','验证权限','选择验证权限',['1' => '是','0' => '否'],$info['auth_open'])
            ->addRadio('status','菜单状态','选择菜单状态',['0' => '禁用','1' => '启用'],$info['status'])
            ->addText('sort','排序值','',$info['sort'])
            ->fetch();
    }
}