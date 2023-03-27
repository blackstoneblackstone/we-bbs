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


namespace app\backend\content;
use app\common\controller\Backend;
use app\common\library\helper\TreeHelper;
use Pay\Exceptions\Exception;
use think\App;
use think\facade\Request;

class Column extends Backend
{
    public function initialize()
    {
        parent::initialize();
        $this->model = new \app\model\Column();
        $this->table = 'column';
    }

    public function index()
    {
        $columns = [
            ['id'  , 'ID'],
            ['cover','封面','image'],
            ['name', '标题','link',get_url('column/detail',['id'=>'__id__'])],
            ['user_name','用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['focus_count','关注','number','','','',true],
            ['view_count','浏览','number','','','',true],
            ['post_count','文章','number','','','',true],
            /*['join_count','用户数量','number','','','',true],
            ['description','专栏描述'],*/
            //['verify', '是否审核', 'tag', '0',['0' => '待审核','1'=>'已审核','2'=>'拒绝审核']],
           /* ['sort','排序'],*/
            ['create_time','申请时间','datetime'],
        ];
        $search = [
            ['text', 'name', '专栏标题', 'LIKE'],
        ];
        $status = $this->request->param('verify',1);

        $top_button = $status==0 ? ['delete',
            'approval'=>[
                'title'   => '审核通过',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('approval') .'","审核通过","approval")',
            ],
            'decline'=>[
                'title'   => '拒绝审核',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('decline') .'","拒绝审核","decline")',
            ]] : ['delete'];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db($this->table)
                ->alias('c')
                ->where($where)
                ->where('verify',$status)
                ->order([$orderByColumn => $isAsc])
                ->join('users u','c.uid=u.uid')
                ->field('c.*,u.user_name,u.url_token')
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setDataUrl(Request::baseUrl().'?_list=1&verify='.$status)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons($top_button)
            ->setLinkGroup([
                [
                    'title'=>'已审核',
                    'link'=>(string)url('index', ['verify' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['verify' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'已拒绝',
                    'link'=>(string)url('index', ['verify' => 2]),
                    'active'=> $status==2
                ]
            ])
            ->fetch();
    }

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = $this->model->create($data);
            if (!$result) {
                $this->error('添加失败');
            } else {
                $this->success('添加成功','index');
            }
        }

        return $this->formBuilder
            ->addText('name','专栏标题','填写专栏标题')
            ->addImage('cover','专栏图片','上传专栏图片')
            ->addTextarea('description','专栏简介','请填写专栏简介')
            ->addNumber('sort','专栏排序','填写专栏排序值')
            ->addRadio('recommend','是否推荐专栏','选择是否推荐专栏',['0' => '不推荐','1' => '推荐'],0)
            ->addRadio('verify','是否审核','是否审核',['0' => '待审核','1'=>'已审核','2'=>'拒绝审核'],0)
            ->fetch();
    }

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            $result = $this->model->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =$this->model->find($id)->toArray();
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','专栏标题','填写专栏标题',$info['name'])
            ->addImage('cover','专栏图片','上传专栏图片',$info['cover'])
            ->addTextarea('description','专栏简介','请填写专栏简介',$info['description'])
            ->addNumber('sort','专栏排序','填写专栏排序值',$info['sort'])
            ->addRadio('recommend','是否推荐专栏','选择是否推荐专栏',['0' => '不推荐','1' => '推荐'],$info['recommend'])
            ->addRadio('verify','是否审核','是否审核',['0' => '待审核','1'=>'已审核','2'=>'拒绝审核'],$info['verify'])
            ->fetch();
    }

    /**
     * 审核专栏
     */
    public function approval()
    {
        $id = $this->request->param('id');
        $id = is_array($id) ? $id : explode(',',$id);
        if($id)
        {
            foreach($id as $k=>$v)
            {
                try {
                    if($uid = db('column')->where(['id'=>$v,'verify'=>0])->value('uid'))
                    {
                        db('column')->where('id',$v)->update(['verify'=>1]);
                        send_notify(0,$uid,'TYPE_COLUMN_APPROVAL','column',$v);
                    }
                }catch(Exception $e)
                {

                }
            }
            return json(['error'=>0,'msg'=>'操作成功!']);
        }
        return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
    }

    /**
     * 拒绝审核
     */
    public function decline()
    {
        $id = $this->request->param('id');
        $id = is_array($id) ? $id : explode(',',$id);
        if($id)
        {
            foreach($id as $k=>$v)
            {
                try {
                    if($uid = db('column')->where(['id'=>$v,'verify'=>0])->value('uid'))
                    {
                        db('column')->where('id',$v)->update(['verify'=>2]);
                        send_notify(0,$uid,'TYPE_COLUMN_DECLINE','column',$v);
                    }
                }catch(Exception $e)
                {

                }
            }
            return json(['error'=>0,'msg'=>'操作成功!']);
        }

        return json(['error'=>1,'msg'=>'请选择要操作的数据!']);
    }
}