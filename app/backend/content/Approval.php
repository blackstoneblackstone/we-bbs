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
use think\App;
use think\facade\Request;
use app\common\controller\Backend;

use app\model\Users as UserModel;
use app\model\Approval as ApprovalModel;

class Approval extends Backend
{
    protected $table = 'approval';
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ApprovalModel();
	}

    public function index()
    {
        $columns = [
            ['id'  , '编号'],
            ['type', '内容类型','tag', 'question',
                [
                    'question' => '问题',
                    'article' => '文章',
                    'answer' => '回答',
                    'modify_question'=>'修改问题',
                    'modify_article'=>'修改文章',
                    'modify_answer'=>'修改回答',
                    'article_comment'=>'文章评论'
                ]
            ],
            ['user_name','用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['create_time', '提交时间','datetime'],
        ];
        $type =  $this->request->param('type','');
        $search = [
            ['select', 'type', '审核类型', '=',$type,[
                'question' => '问题审核',
                'article' => '文章审核',
                'answer' => '回答审核',
                'modify_question'=>'修改问题',
                'modify_article'=>'修改文章',
                'modify_answer'=>'修改回答',
            ]]
        ];
        $status = $this->request->param('status',0);
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));

            return db('approval')
                ->alias('a')
                ->where($where)
                ->where(['a.status'=>$status])
                ->order([$orderByColumn => $isAsc])
                ->join('users u','a.uid=u.uid')
                ->field('a.*,u.user_name,u.url_token')
                ->paginate([
                'query'     => Request::get(),
                'list_rows' =>$pageSize,
            ])->toArray();
        }
        $top_button = ['delete',
            'approval'=>[
                'title'   => '通过审核',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-success multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'.(string)url('state').'","审核","approval")',
            ],
            'decline'=>[
                'title'   => '拒绝审核',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.topOpen("'.(string)url('state').'","拒绝审核")',
            ],
            'forbidden' => [
                'title'   => '封禁',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-secondary multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.topOpen("'.(string)url('forbidden').'","封禁用户")',
            ],
            'forbidden_ip' => [
                'title'   => '封禁IP',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => '',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'.(string) url('forbidden_ip').'","封禁用户", "list")',
            ]
        ];
        $right_button = [
            'edit'=>[
                'title'       => '预览',
            ],
            'delete',
            'forbidden' => [
                'title'       => '封禁',
                'icon'        => 'fa fa-ban',
                'class'       => 'btn btn-secondary btn-sm aw-ajax-open',
                'url'        => (string) url('forbidden', ['id' => '__id__']),
                'target'      => '',
                'href' => 'javascript:;',
                'confirm' => '确定封禁该用户吗？'
            ],
            'decline' => [
                'title'       => '拒绝',
                'icon'        => 'fa fa-ban',
                'class'       => 'btn btn-secondary btn-sm aw-ajax-open',
                'url'        => (string) url('state', ['id' => '__id__']),
                'target'      => '',
                'href' => 'javascript:;',
                'confirm' => '确定拒绝审核该信息吗？'
            ],
            'forbidden_ip' => [
                'title'       => '封禁IP',
                'icon'        => 'fa fa-ban',
                'class'       => 'btn btn-warning btn-sm aw-ajax-get',
                'url'        => (string) url('forbidden_ip', ['id' => '__uid__']),
                'target'      => '',
                'href' => 'javascript:;',
                'confirm' => '确认封禁该用户的IP吗？'
            ]
        ];
        if($status==1 || $status==2)
        {
            $top_button = ['delete'];
            $right_button = ['delete'];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status.'$type='.$type)
            ->setPagination('false')
            ->addRightButtons($right_button)
            ->addTopButtons($top_button)
            ->setLinkGroup([
                [
                    'title'=>'已审核',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'已拒绝',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ]
            ])
            ->setSecondLinkGroup([
                [
                    'title'=>'全部',
                    'link'=>(string)url('index', ['status' => $status]),
                    'active'=> !$type
                ],
                [
                    'title'=>'问题',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'question']),
                    'active'=> $type=='question'
                ],
                [
                    'title'=>'文章',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'article']),
                    'active'=> $type=='article'
                ],
                [
                    'title'=>'回答',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'answer']),
                    'active'=> $type=='answer'
                ],
                [
                    'title'=>'修改问题',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'modify_question']),
                    'active'=> $type=='modify_question'
                ],
                [
                    'title'=>'修改文章',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'modify_article']),
                    'active'=> $type=='modify_article'
                ],
                [
                    'title'=>'修改回答',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'modify_answer']),
                    'active'=> $type=='modify_answer'
                ],
                [
                    'title'=>'文章评论',
                    'link'=>(string)url('index', ['status' => $status,'type'=>'article_comment']),
                    'active'=> $type=='article_comment'
                ]
            ])
            ->fetch();
    }

    public function edit($id=0)
    {
        $info = db('approval')->where('id',$id)->find();
        $data = json_decode($info['data'],true);
        $category_list = \app\model\Category::getCategoryListByType();
        $category_list = $category_list ? array_column($category_list,'title','id') : [];

        //发起问题审核
        if($info['type']=='question')
        {
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addText('title','问题标题','',$data['title'],'disabled readonly')
                ->addEditor('detail','问题详情','',htmlspecialchars_decode($data['detail']),'','disabled readonly')
                ->addSelect('category_id','问题分类','',$category_list,$data['category_id'],'disabled readonly')
                ->addRadio('question_type','问题类型','',['normal' => '普通问题','reward' => '悬赏问题'],$data['question_type'],'disabled readonly')
                ->addRadio('is_anonymous','是否匿名','',[0 => '公开',1 => '匿名'],$data['is_anonymous'],'disabled readonly')
                ->hideBtn(['submit'])
                ->fetch();
        }

        //修改问题审核
        if($info['type']=='modify_question')
        {
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addText('title','问题标题','',$data['title'],'disabled readonly')
                ->addEditor('detail','问题详情','',htmlspecialchars_decode($data['detail']),'','disabled readonly')
                ->addSelect('category_id','问题分类','',$category_list,$data['category_id'],'disabled readonly')
                ->addRadio('question_type','问题类型','',['normal' => '普通问题','reward' => '悬赏问题'],$data['question_type'],'disabled readonly')
                ->addRadio('is_anonymous','是否匿名','',[0 => '公开',1 => '匿名'],$data['is_anonymous'],'disabled readonly')
                ->hideBtn(['submit'])
                ->fetch();
        }

        //发起文章审核
        if($info['type']=='article')
        {
            $column_list = \app\model\Column::getColumnByUid($info['uid']);
            $column_list = $column_list ? array_column($category_list,'name','id') : [];
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addImage('cover','文章封面','',$data['cover']??'','disabled readonly')
                ->addText('title','文章标题','',$data['title'],'disabled readonly')
                ->addEditor('message','文章详情','',htmlspecialchars_decode($data['message']),'','disabled readonly')
                ->addSelect('category_id','文章分类','',$category_list,$data['category_id'],'disabled readonly')
                ->addSelect('column_id','文章专栏','',$column_list,$data['column_id']??'','disabled readonly')
                ->hideBtn(['submit'])
                ->fetch();
        }

        //修改文章审核
        if($info['type']=='modify_article')
        {
            $column_list = \app\model\Column::getColumnByUid($info['uid']);
            $column_list = $column_list ? array_column($category_list,'name','id') : [];
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addImage('cover','文章封面','',$data['cover']??'','disabled readonly')
                ->addText('title','文章标题','',$data['title'],'disabled readonly')
                ->addEditor('message','文章详情','',htmlspecialchars_decode($data['message']),'','disabled readonly')
                ->addSelect('category_id','文章分类','',$category_list,$data['category_id'],'disabled readonly')
                ->addSelect('column_id','文章专栏','',$column_list,$data['column_id']??'','disabled readonly')
                ->hideBtn(['submit'])
                ->fetch();
        }

        if($info['type']=='answer')
        {
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addText('question_id','问题标题','',db('question')->where('id',$data['question_id'])->value('title'),'disabled readonly')
                ->addEditor('content','回答详情','',htmlspecialchars_decode($data['content']),'','disabled readonly')
                ->hideBtn(['submit'])
                ->fetch();
        }

        if($info['type']=='modify_answer')
        {
            return $this->formBuilder
                ->addHidden('approval_id',$info['id'])
                ->addText('question_id','问题标题','',db('question')->where('id',$data['question_id'])->value('title'),'disabled readonly')
                ->addEditor('content','回答详情','',htmlspecialchars_decode($data['content']),'','disabled readonly')
                ->hideBtn(['submit'])
                ->fetch();

        }
    }

	//审核状态
	public function state()
	{
        if ($this->request->isPost()) {
            $ids = $this->request->post('id');
            $type = $this->request->post('type');
            if ($type == 'approval') {
                if (ApprovalModel::approval($ids)) {
                    $this->success('审核成功');
                }
                $this->error('审核失败!'.ApprovalModel::getError());
            }

            if ($type == 'decline') {
                $reason = $this->request->param('reason','');
                if(ApprovalModel::decline($ids,$reason)) {
                    $this->success('操作成功');
                }
                $this->error('操作失败!'.ApprovalModel::getError());
            }
        }
        $id = $this->request->param('id');
        return $this->formBuilder
            ->addHidden('id',$id)
            ->addHidden('type','decline')
            ->addTextarea('reason','拒绝理由','填写拒绝理由')
            ->fetch();
	}

    public function choose()
    {
        if(request()->isPost())
        {
            $data = request()->post();
            db($this->table)->where('id',$data['id'])->update([$data['field']=>$data['value']]);
            if($data['field']=='status')
            {
                if($data['value']==1)
                {
                    ApprovalModel::approval($data['id']);
                }

                if($data['value']==2)
                {
                    ApprovalModel::decline($data['id']);
                }
            }
            return json(['error'=>0, 'msg'=>'修改成功!']);
        }
    }

    // 封禁用户
    public function forbidden()
    {
        if ($this->request->isPost()) {
            $data = $this->request->except(['file'],'post');
            $data['id'] = is_array($data['id']) ? $data['id'] : explode(',',$data['id']);
            if(!$data['forbidden_time'])
            {
                $this->error('请选择封禁时长');
            }

            if(!$data['forbidden_reason'])
            {
                $this->error('请填写封禁原因');
            }

            if ($data['id']) {
                foreach ($data['id'] as $val) {
                    if (!db('users_forbidden')->where(['uid'=>$val])->find()) {
                        db('users_forbidden')->insert([
                            'uid'=>$val,
                            'forbidden_time'=>strtotime($data['forbidden_time']),
                            'forbidden_reason'=>trim($data['forbidden_reason']),
                            'create_time'=>time(),
                            'status'=>1
                        ]);
                        db('users')->whereIn('uid',$val)->update(['status'=>3]);
                    }
                }
                $this->success('操作成功');
            }

            $this->error('请选择要操作的数据!');
        }

        $id = $this->request->param('id');
        $uid = db('approval')->whereIn('id', explode(',', $id))->column('uid');

        return $this->formBuilder
            ->addHidden('id', join(',', $uid))
            ->addDatetime('forbidden_time','封禁时长','选择封禁时长,默认15天',date('Y-m-d H:i',time()+15*ONE_DAY))
            ->addTextarea('forbidden_reason','封禁原因','填写封禁原因')
            ->fetch();
    }

    // 封禁IP
    public function forbidden_ip()
    {
        $id = $this->request->param('id', 0);
        $type = $this->request->param('type', '');

        // 批量操作
        if ($type == 'list') {
            if (!$id) $this->error('请选择要操作的数据');
            $uid = ApprovalModel::whereIn('id', explode(',', $id))->column('uid');

            if (UserModel::batchForbiddenIp($uid)) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }

        // 单条操作
        $user = UserModel::find($id);
        if (UserModel::forbiddenIp(['uid' => $id, 'ip' => $user->last_login_ip, 'time' => time()])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}