<?php
namespace app\backend\extend;
use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;
use think\exception\ValidateException;
use think\facade\Request;

class RouteRule extends Backend
{
    protected $table = 'route_rule';

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['title', '规则名称'],
            ['url','路由地址'],
            ['rule','路由规则'],
            ['status','是否启用','status'],
        ];

        $search = [
            ['text', 'url', '路由地址', 'LIKE'],
        ];

        $entrance = $this->request->param('entrance','');

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $entrance = $this->request->param('entrance','');
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            if($entrance)
            {
                $where[] = ['entrance','=',$entrance];
            }
            // 排序处理
            return db($this->table)
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
            ->setDataUrl(Request::baseUrl().'?_list=1&entrance='.$entrance)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit','delete'])
            ->addTopButtons(['add','delete'])
            ->setLinkGroup([
                [
                    'title'=>'全部',
                    'link'=>(string)url('index'),
                    'active'=> !$entrance
                ],
                [
                    'title'=>'通用',
                    'link'=>(string)url('index', ['entrance' => 'all']),
                    'active'=> $entrance=='all'
                ],
                [
                    'title'=>'网页端',
                    'link'=>(string)url('index', ['entrance' => 'frontend']),
                    'active'=> $entrance=='frontend'
                ],
                [
                    'title'=>'手机端',
                    'link'=>(string)url('index', ['entrance' => 'mobile']),
                    'active'=> $entrance=='mobile'
                ],
                [
                    'title'=>'接口端',
                    'link'=>(string)url('index', ['entrance' => 'api']),
                    'active'=> $entrance=='api'
                ],
                [
                    'title'=>'管理端',
                    'link'=>(string)url('index', ['entrance' => 'backend']),
                    'active'=> $entrance=='backend'
                ],
            ])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            try {
                validate(\app\validate\RouteRule::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $result = db($this->table)->insert($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addSelect('entrance','入口模块','选择入口模块',['all'=>'通用','frontend'=>'网页端','mobile'=>'手机端','api'=>'接口端','backend'=>'管理端'])
            ->addSelect('method','请求方法','选择请求方法',['*'=>'所有请求','GET'=>'GET请求','POST'=>'POST请求','PUT'=>'PUT请求','DELETE'=>'DELETE请求','PATCH'=>'PATCH请求','HEAD'=>'HEAD请求'])
            ->addText('title','规则名称','填写规则名称')
            ->addText('url','路由地址','填写路由地址,如index/index或index.Test/index')
            ->addText('rule','生成规则','填写生成规则,[:变量]代表可选变量、:变量 代表具体必填变量;具体规则参数可查看<a href="https://www.kancloud.cn/manual/thinkphp6_0/1037496" target="_blank">【变量规则】</a>')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'])
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            try {
                validate(\app\validate\RouteRule::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $result = db($this->table)->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }
        $info = db($this->table)->where('id',$id)->find();
        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addSelect('entrance','入口模块','选择入口模块',['all'=>'通用','frontend'=>'网页端','mobile'=>'手机端','api'=>'接口端','backend'=>'管理端'])
            ->addSelect('method','请求方法','选择请求方法',['*'=>'所有请求','get'=>'GET请求','post'=>'POST请求','put'=>'PUT请求','delete'=>'DELETE请求','patch'=>'PATCH请求','head'=>'HEAD请求'])
            ->addText('title','规则名称','填写规则名称')
            ->addText('url','路由地址','填写路由地址,如index/index或index.Test/index')
            ->addText('rule','生成规则','填写生成规则,[:变量]代表可选变量、:变量 代表具体必填变量;具体规则参数可查看<a href="https://www.kancloud.cn/manual/thinkphp6_0/1037496" target="_blank">【变量规则】</a>')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'])
            ->fetch();
    }
}