<?php

namespace app\backend\content;

use think\facade\Request;
use app\common\controller\Backend;

use app\model\Users as UserModel;
use app\model\Report as ReportModel;

class Report extends Backend
{
    protected $table = 'report';

    public function index($question_id=0)
    {
        $status = $this->request->param('status',0);

        if($status)
        {
            $columns = [
                ['id'  , 'ID'],
                ['item_type', '内容类型','tag','',['question'=>'问题','article'=>'文章','answer'=>'回答']],
                ['user_name', '举报用户','link',get_url('people/index',['name'=>'__url_token__'])],
                ['item_id','举报内容','number'],
                ['reason','举报理由','text'],
                ['url','举报地址','link','__url__'],
                ['handle_type','处理方式','tag',0,[0=>'不做处理','1' => '删除内容','2' => '要求修改内容']],
                ['handle_reason','处理理由'],
                ['status', '是否处理', 'tag', '0',['1' => '已处理','0' => '未处理']],
            ];
        }else{
            $columns = [
                ['id'  , 'ID'],
                ['item_type', '内容类型','tag','',['question'=>'问题','article'=>'文章','answer'=>'回答']],
                ['user_name', '举报用户','link',get_url('people/index',['name'=>'__url_token__'])],
                ['item_id','举报内容','number'],
                ['reason','举报理由','text'],
                ['url','举报地址','link','__url__'],
                ['status', '是否处理', 'tag', '0',['1' => '已处理','0' => '未处理']],
            ];
        }
        $search = [];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            return db($this->table)
                ->alias('a')
                ->order([$orderByColumn => $isAsc])
                ->where(['a.status'=>$status])
                ->join('users u','a.uid=u.uid')
                ->field('a.*,u.user_name,u.url_token')
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        $topButtons = [
            'forbidden' => [
                'title'   => '封禁',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
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

        if ($status) {
            $rightButtons = [
                'forbidden' => [
                    'title'       => '封禁',
                    'icon'        => 'fa fa-ban',
                    'class'       => 'btn btn-success btn-sm aw-ajax-open',
                    'url'        => (string)url('forbidden', ['id' => '__id__']),
                    'target'      => '',
                    'href' => 'javascript:;',
                    'confirm' => '确定封禁该用户吗？'
                ],
                'forbidden_ip' => [
                    'title'       => '封禁IP',
                    'icon'        => 'fa fa-ban',
                    'class'       => 'btn btn-warning btn-sm aw-ajax-get',
                    'url'        => (string) url('forbidden_ip', ['id' => '__id__']),
                    'target'      => '',
                    'href' => 'javascript:;',
                    'confirm' => '确认封禁该用户的IP吗？'
                ]
            ];
        } else {
            $rightButtons = [
                'edit' => [
                    'title'   => '处理',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-primary btn-sm',
                    'href'    => '',
                ],
                'forbidden' => [
                    'title'       => '封禁',
                    'icon'        => 'fa fa-ban',
                    'class'       => 'btn btn-warning btn-sm aw-ajax-open',
                    'url'        => (string) url('forbidden', ['id' => '__id__']),
                    'target'      => '',
                    'href' => 'javascript:;',
                    'confirm' => '确定封禁该用户吗？'
                ],
                'forbidden_ip' => [
                    'title'       => '封禁IP',
                    'icon'        => 'fa fa-ban',
                    'class'       => 'btn btn-warning btn-sm aw-ajax-get',
                    'url'        => (string) url('forbidden_ip', ['id' => '__id__']),
                    'target'      => '',
                    'href' => 'javascript:;',
                    'confirm' => '确认封禁该用户的IP吗？'
                ]
            ];
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->setDataUrl(Request::baseUrl().'?_list=1&status='.$status)
            ->addTopButtons($topButtons)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons($rightButtons)
            ->setLinkGroup([
                [
                    'title'=>'未处理',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'已处理',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
            ])->fetch();
    }

    //举报处理
    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            if(ReportModel::reportHandle($data['id'],$data['handle_type'],$data['handle_reason']))
            {
                $this->success('处理成功', 'index');
            }
            $this->error('处理失败');
        }

        $info =db($this->table)->find($id);
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addRadio('handle_type','处理方式','选择处理方式,删除内容时会直接删除信息,其他选项均会给举报人和内容发起者发送通知',[0=>'不处理',1=>'删除内容',2=>'修改内容'],$info['handle_type'])
            ->addTextarea('handle_reason','处理理由','填写处理理由',$info['handle_reason'])
            ->fetch();
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
        $uid = ReportModel::reportedUid(explode(',', $id));

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
            if (UserModel::batchForbiddenIp(ReportModel::reportedUid(explode(',', $id)))) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }
        // 单条操作
        $user = UserModel::find(ReportModel::reportedUid($id));
        if (UserModel::forbiddenIp(['uid' => $user->uid, 'ip' => $user->last_login_ip, 'time' => time()])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}