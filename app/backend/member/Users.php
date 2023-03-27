<?php
namespace app\backend\member;
use app\common\controller\Backend;
use app\common\library\helper\ArrayHelper;
use app\common\library\helper\ExcelHelper;
use app\common\library\helper\LogHelper;
use app\common\library\helper\RandomHelper;
use app\model\Score as ScoreModel;
use app\model\Users as UserModel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use think\facade\Request;

class Users extends Backend
{
    protected $table='users';
    public function initialize()
    {
        parent::initialize();
        $this->model = new UserModel();
    }

    public function index()
    {
        $columns = [
            ['uid','ID'],
            ['nick_name', '昵称','link',get_url('people/index',['name'=>'__url_token__'])],
            ['avatar','头像','image'],
            //['nick_name','用户昵称'],
            ['email','邮箱'],
            ['reputation_group_id','威望组','tag','',array_column(db('users_reputation_group')->column('title,id'),'title','id')],
            ['integral_group_id','积分组','tag','',array_column(db('users_integral_group')->column('title,id'),'title','id')],
            ['group_id','系统组','tag', '',array_column(db('admin_group')->column('title,id'),'title','id')],
            ['is_valid_email','验证邮箱','bool','',[0=>'<font class="text-red">未验证</font>',1=>'<font class="text-success">已验证</font>']],
            ['is_valid_mobile','验证手机','bool','',[0=>'<font class="text-red">未验证</font>',1=>'<font class="text-success">已验证</font>']],
            ['last_login_time','最后登录','datetime'],
            ['last_login_ip','登录IP'],
            ['create_time', '注册时间','datetime'],
        ];
        $search = [
            ['datetime', 'create_time', '注册时间'],
            ['text', 'user_name', '用户名', 'LIKE'],
            ['select', 'integral_group_id', '积分组', '=','',array_column(db('users_integral_group')->column('title,id'),'title','id')],
            ['select', 'reputation_group_id', '威望组', '=','',array_column(db('users_reputation_group')->column('title,id'),'title','id')],
            ['select', 'group_id', '系统组', '=','',array_column(db('admin_group')->column('title,id'),'title','id')],
        ];
        $status = $this->request->param('status',1);
        $forbiddenIp = $this->request->param('fbip', 0);
        if ($forbiddenIp) $status = 99;

        //正常用户操作
        $right_button = [
            'edit',
            'delete',
            'forbidden' => [
                'title'       => '封禁',
                'icon'        => 'fa fa-ban',
                'class'       => 'btn btn-success btn-sm aw-ajax-open',
                'url'        => (string)url('forbidden', ['id' => '__uid__']),
                'target'      => '',
                'href' => 'javascript:;',
                'confirm' => '确定封禁该用户吗？'
            ],
            'integral' => [
                'title'       => '积分',
                'icon'        => 'fa fa-database',
                'class'       => 'btn btn-warning btn-sm aw-ajax-open',
                'url'        => (string)url('integral', ['uid' => '__uid__']),
                'target'      => '',
                'href' => 'javascript:;',
            ],
        ];
        $top_button = [
            'add',
            'delete',
            'export',
            'forbidden'=>
                [
                    'title'   => '封禁',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => 'javascript:;',
                    'onclick' => 'AWS_ADMIN.operate.topOpen("'. url('forbidden') .'","封禁用户")',
                ]
        ];

        //删除用户操作
        if(!$status)
        {
            $right_button = [
                'edit',
                'recover' => [
                    'title'       => '恢复',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-sm aw-ajax-get',
                    'url'        => (string)url('manager', ['id' => '__uid__','type'=>'recover']),
                    'target'      => '',
                    'confirm' =>'是否确定恢复？',
                    'href' => ''
                ],
                'remove' => [
                    'title'       => '彻底删除',
                    'icon'        => '',
                    'class'       => 'btn btn-danger btn-sm aw-ajax-get',
                    'url'        => (string)url('manager', ['id' => '__uid__','type'=>'remove']),
                    'target'      => '',
                    'confirm' =>'是否确定彻底删除？',
                    'href' => ''
                ],
            ];
            $top_button = [
                'add',
                'export',
                'recover'=>[
                    'title'   => '恢复删除',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => 'javascript:;',
                    'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('manager') .'","恢复删除用户","recover")',
                ],
                'remove'=>[
                    'title'   => '彻底删除',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-danger multiple disabled',
                    'href'    => 'javascript:;',
                    'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('manager') .'","恢复删除用户","remove")',
                ],
            ];
        }

        if ($status == 1) {
            $top_button['forbidden_ip'] = [
                'title'   => '封禁IP',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => 'javascript:;',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('forbidden_ip') .'","封禁用户", "list")',
            ];
            $right_button['forbidden_ip'] = [
                'title'       => '封禁IP',
                'icon'        => 'fa fa-ban',
                'class'       => 'btn btn-warning btn-sm aw-ajax-get',
                'url'        => (string) url('forbidden_ip', ['id' => '__uid__']),
                'target'      => '',
                'href' => 'javascript:;',
                'confirm' => '确认封禁该用户的IP吗？'
            ];
        }

        //待审核用户操作
        if($status==2)
        {
            $right_button = [
                'edit',
                'delete',
                'approval' => [
                    'title'       => '审核通过',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-sm aw-ajax-get',
                    'url'        => (string)url('approval', ['id' => '__uid__']),
                    'target'      => '',
                    'href'=>'javascript:;'
                ],
                'decline' => [
                    'title'       => '拒绝审核',
                    'icon'        => 'fa fa-edit',
                    'class'       => 'btn btn-success btn-sm aw-ajax-get',
                    'url'        => (string)url('decline', ['id' => '__uid__']),
                    'target'      => '',
                    'href'=>'javascript:;'
                ],
            ];
            $top_button = [
                'add',
                'delete',
                'export',
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
                    'class'   => 'btn btn-danger multiple disabled',
                    'href'    => 'javascript:;',
                    'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('decline') .'","拒绝审核","decline")',
                ]
            ];
        }

        //拒绝审核用户操作
        if($status==4)
        {
            $right_button = [
                'edit',
                'delete',
                /*'approval' => [
                    'title'       => '审核通过',
                    'icon'        => '',
                    'class'       => 'btn btn-success btn-sm aw-ajax-get',
                    'url'        => (string)url('approval', ['id' => '__id__']),
                    'target'      => '',
                    'href'=>'javascript:;'
                ],*/
            ];
            $top_button = [
                'add',
                'delete',
                'export',
                /*'approval'=>[
                    'title'   => '审核通过',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning multiple disabled',
                    'href'    => 'javascript:;',
                    'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('approval') .'","审核通过","approval")',
                ],*/
            ];
        }

        //封禁用户操作
        if($status==3)
        {
            $right_button = [
                'edit',
                'delete',
                'un_forbidden' => [
                    'title'   => '解封',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning btn-sm aw-ajax-get',
                    'href'    => 'javascript:;',
                    'url'     => (string)url('un_forbidden', ['id' => '__uid__']),
                ],
            ];
            $top_button = [
                'add',
                'delete',
                'export',
                'un_forbidden' =>
                    [
                        'title'   => '解除封禁',
                        'icon'    => 'fa fa-times',
                        'class'   => 'btn btn-warning multiple disabled',
                        'href'    => 'javascript:;',
                        'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('un_forbidden') .'","解除封禁")',
                    ]];
        }

        // 封禁用户IP操作
        if ($forbiddenIp) {
            $top_button['forbidden_ip'] = [
                'title'   => '解封IP',
                'icon'    => 'fa fa-times',
                'class'   => 'btn btn-warning multiple disabled',
                'href'    => 'javascript:;',
                'onclick' => 'AWS_ADMIN.operate.selectAll("'. url('forbidden_ip', ['action' => 'relieve']) .'","封禁用户", "list")',
            ];
            $right_button = [
                'edit',
                'delete',
                'un_forbidden' => [
                    'title'   => '解封',
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-warning btn-sm aw-ajax-get',
                    'href'    => 'javascript:;',
                    'confirm' => '确定解封该用户IP吗？',
                    'url'     => (string) url('forbidden_ip', ['id' => '__uid__', 'action' => 'relieve']),
                ],
            ];
        }

        if ($this->request->param('_list'))
        {
            $where = $this->makeBuilder->getWhere($search);
            if ($forbiddenIp) {
                $where[] = ['forbidden_ip', '=', 1];
            } else {
                $where = [
                    ['status', '=', $status],
                    ['forbidden_ip', '=', 0]
                ];
            }

            if($user_name = $this->request->post('user_name'))
            {
                $where[] = ['user_name','LIKE',$user_name];
            }

            // 排序规则
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            return db('users')
                ->order('uid',$isAsc)
                ->where($where)
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('uid')
            ->addColumns($columns)
            ->setSearch($search)
            ->setDataUrl(Request::baseUrl()."?_list=1&status={$status}&fbip={$forbiddenIp}")
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons($right_button)
            ->addTopButtons($top_button)
            ->setLinkGroup([
                [
                    'title'=>'用户列表',
                    'link'=>(string)url('index', ['status' => 1]),
                    'active'=> $status==1
                ],
                [
                    'title'=>'删除列表',
                    'link'=>(string)url('index', ['status' => 0]),
                    'active'=> $status==0
                ],
                [
                    'title'=>'待审核',
                    'link'=>(string)url('index', ['status' => 2]),
                    'active'=> $status==2
                ],
                [
                    'title'=>'拒绝审核',
                    'link'=>(string)url('index', ['status' => 4]),
                    'active'=> $status==4
                ],
                [
                    'title'=>'封禁用户',
                    'link'=>(string)url('index', ['status' => 3]),
                    'active'=> $status==3
                ],
                [
                    'title' => '封禁IP用户',
                    'link' => (string) url('index', ['fbip' => 1]),
                    'active' => $forbiddenIp == 1
                ]
            ])
            ->fetch();
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $data = $this->request->except(['file'],'post');
            $result = UserModel::registerUser($data['user_name'],$data['password'],$data,false,true);
            if (!$result) {
                $this->error(UserModel::getError());
            } else {
                $this->success('添加成功');
            }
        }

        return $this->formBuilder
            ->addText('user_name','用户名','填写用户名')
            ->addText('nick_name','用户昵称','填写用户昵称')
            ->addPassword('password','用户密码','填写用户密码')
            ->addText('email','用户邮箱','填写用户邮箱')
            ->addText('mobile','用户手机','填写用户手机')
            ->addImage('avatar','用户头像')
            ->addTextarea('signature','个人签名','填写个人签名')
            ->addSelect('group_id','系统组','',array_column(db('admin_group')->column('title,id'),'title','id'),4)
            ->addSelect('reputation_group_id','威望组','',array_column(db('users_reputation_group')->column('title,id'),'title','id'),1)
            ->addSelect('integral_group_id','积分组','',array_column(db('users_integral_group')->column('title,id'),'title','id'),1)
            //->addRadio('sex','用户性别','选择用户性别',['0' => '保密','1' => '男',2=>'女'],0)
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常',2=>'待审核',3=>'已封禁',4=>'拒绝审核'],1)
            ->fetch();
    }

    public function edit($id = '')
    {
        $info = $this->model->where('uid', $id ?: $this->request->post('uid'))->find();
        if ($this->request->isPost()) {
            $data = $this->request->except(['file'],'post');
            if (isset($data['password'])) {
                if ($data['password']) {
                    $data['salt'] = RandomHelper::alnum();
                    $data['password'] = compile_password($data['password'], $data['salt']);
                } else {
                    unset($data['password']);
                }
            }

            if (isset($data['deal_password'])) {
                if ($data['deal_password']) {
                    $data['deal_password'] = password_hash($data['deal_password'], 1);
                } else {
                    unset($data['deal_password']);
                }
            }

            unset($data['create_time'], $data['last_login_time'], $data['last_login_time']);

            $data['avatar'] = $data['avatar'] ? : null;
            if (isset($data['birthday'])) {
                if ($data['birthday']) {
                    $data['birthday'] = strtotime($data['birthday']);
                } else {
                    unset($data['birthday']);
                }
            }

            if(isset($data['verified']))
            {
                $data['verified'] = $data['verified']=='normal' ? '' : $data['verified'];
            }

            $result = $this->model->where(['uid'=>$data['uid']])->update($data);
            if ($result) {
                $this->success('提交成功');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $verify_type = db('users_verify_type')->where(['status'=>1])->column('title','name');
        $verify_type['normal'] = '无';
        $builder = $this->formBuilder
            ->addHidden('uid',$info['uid'])
            ->addText('nick_name','用户昵称','填写用户昵称',$info['nick_name'])
            ->addPassword('password','用户密码','填写用户密码,如不需要更改密码请留空','')
            ->addPassword('deal_password','交易密码','用户交易密码,如不需要更改密码请留空','')
            ->addText('email','用户邮箱','填写用户邮箱',$info['email'])
            ->addText('mobile','用户手机','填写用户手机',$info['mobile'])
            ->addImage('avatar','用户头像','',$info['avatar'])
            ->addText('create_time','注册时间','',date('Y-m-d H:i:s',$info['create_time']),'readonly disabled')
            ->addText('last_login_time','最后登录时间','',date('Y-m-d H:i:s',$info['last_login_time']),'readonly disabled')
            ->addText('last_login_ip','最后登录IP','',$info['last_login_ip'],'readonly disabled')
            ->addTextarea('signature','个人签名','填写个人签名',$info['signature'])
            ->addSelect('group_id','系统组','',array_column(db('admin_group')->column('title,id'),'title','id'),$info['group_id'])
            ->addSelect('reputation_group_id','威望组','',array_column(db('users_reputation_group')->column('title,id'),'title','id'),$info['reputation_group_id'])
            ->addSelect('integral_group_id','积分组','',array_column(db('users_integral_group')->column('title,id'),'title','id'),$info['integral_group_id'])
            ->addRadio('verified','认证类型','选择用户认证类型', $verify_type,$info['verified'])
            ->addRadio('sex','用户性别','选择用户性别',['0' => '保密','1' => '男',2=>'女'],$info['sex'])
            ->addDate('birthday', '生日', '选择生日', $info['birthday'] ? date('Y-m-d', $info['birthday']) : '')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常',2=>'待审核',3=>'已封禁',4=>'拒绝审核'],$info['status']);

        return $builder->fetch();
    }

    //删除用户
    public function delete()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            if (UserModel::removeUser($id)) $this->success('删除成功');

            $this->error(UserModel::getError());
        }

        $this->error('错误的请求！');
    }

    /**
     * 封禁操作
     */
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

        return $this->formBuilder
            ->addHidden('id',$id)
            ->addDatetime('forbidden_time','封禁时长','选择封禁时长,默认15天',date('Y-m-d H:i',time()+15*ONE_DAY))
            ->addTextarea('forbidden_reason','封禁原因','填写封禁原因')
            ->fetch();
    }

    /**
     * 解除封禁
     */
    public function un_forbidden()
    {
        $data = $this->request->param();
        $data['id'] = is_array($data['id']) ? $data['id'] : explode(',',$data['id']);
        if ($data['id']) {
            foreach ($data['id'] as $key => $val) {
                if ($id = db('users_forbidden')->where(['uid'=>$val])->value('id')) {
                    db('users_forbidden')->where(['id'=>$id])->update([
                        'status'=>0
                    ]);
                }
                db('users')->whereIn('uid',$data['id'])->update(['status'=>1]);
            }

            $this->success('操作成功');
        }

        $this->error('请选择要操作的数据!');
    }

    /**
     * 审核用户
     */
    public function approval()
    {
        $id = $this->request->param('id');
        $id = is_array($id) ? $id : explode(',',$id);
        if ($id) {
            db('users')->whereIn('uid', $id)->update(['status' => 1]);
            $this->success('操作成功');
        }

        $this->error('请选择要操作的数据!');
    }

    /**
     * 拒绝审核
     */
    public function decline()
    {
        $id = $this->request->param('id');
        $id = is_array($id) ? $id : explode(',',$id);
        if ($id) {
            db('users')->whereIn('uid', $id)->update(['status' => 4]);
            $this->success('操作成功');
        }

        $this->error('请选择要操作的数据!');
    }

    /**
     * 通用管理
     */
    public function manager()
    {
        $id = $this->request->param('id');
        $type = $this->request->param('type');
        $id = is_array($id) ? $id : explode(',',$id);

        if(empty($id))
        {
            $this->error('请选择要操作的数据');
        }

        switch ($type)
        {
            //恢复
            case 'recover':
                if(UserModel::recoverUsers($id))
                {
                    $this->success('恢复成功!');
                }
                $this->error('恢复失败:'.UserModel::getError());
                break;
            //删除
            case 'remove':

                if(UserModel::removeUser($id,1))
                {
                    $this->success('删除成功!');
                }
                $this->error(UserModel::getError());
                break;

        }

        $this->error('操作类型不正确');
    }

    /**
     * 积分记录
     */
    public function integral()
    {
        if($this->request->isPost())
        {
            $uid = $this->request->param('uid',0,'intval');
            $integral = $this->request->post('integral');
            if(!$integral)
            {
                $this->error('请输入操作'.get_setting("score_unit"));
            }
            if(LogHelper::addIntegralLog('AWARD',intval($uid),'users',intval($uid),$integral))
            {
                $this->success(get_setting("score_unit").'操作成功');
            }

            $this->error(get_setting("score_unit").'操作失败');
        }

        $uid = $this->request->param('uid',0,'intval');
        $page = $this->request->param('page',1);
        $where=['uid'=>$uid];
        $data = ScoreModel::getScoreList($where,$page,10);
        $this->assign($data);
        $this->assign('uid',$uid);
        return $this->fetch();
    }

    // 封禁IP操作
    public function forbidden_ip()
    {
        $type = $this->request->param('type', '');
        $uid = $this->request->param('id', 0);
        $action = $this->request->param('action', 'forbidden', 'trim');

        // 批量操作
        if ($type == 'list') {
            if (!$uid) $this->error('请选择要操作的数据');
            if ($action == 'relieve') {
                if (UserModel::batchLiftIp($uid)) {
                    $this->success('操作成功');
                } else {
                    $this->error('操作失败');
                }
            }

            if (UserModel::batchForbiddenIp($uid)) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }

        // 单条操作
        if ($action == 'relieve') {
            if (UserModel::liftIp($uid)) {
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }

        $user = UserModel::find($uid);
        if (UserModel::forbiddenIp(['uid' => $uid, 'ip' => $user->last_login_ip, 'time' => time()])) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function export($type='')
    {
        $columns = $this->makeBuilder->getListColumns($this->table);
        $tableInfo = $this->table();
        $search = $this->makeBuilder->getSearchColumn($this->table);
        $where = $this->makeBuilder->getWhere($search);
        $param = $this->request->param();
        $isAsc = $param['isAsc'] ?? 'desc';
        $page = $tableInfo['page']??1;
        $title = $tableInfo['title']??'数据导出';
        $orderByColumn = $param['orderByColumn'] ?? 'uid';
        unset($param['type'],$param['pageSize'],$param['page'],$param['searchValue'],$param['orderByColumn'],$param['isAsc']);
        if(!$page || $type=='all')
        {
            $list = db($this->table)
                ->where($where)
                ->where($param)
                ->order([$orderByColumn => $isAsc])
                ->select()
                ->toArray();
        }else{
            $param = ArrayHelper::arrayFilter($param);
            $where = ArrayHelper::arrayFilter($where);
            $list = db($this->table)
                ->where($where)
                ->where($param)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'page'=>$this->request->param('page',1),
                    'query'     => Request::get(),
                    'list_rows' =>$this->request->param('pageSize',get_setting("contents_per_page",15))
                ])
                ->toArray()['data'];
        }
        try {
            ExcelHelper::exportData($list, $columns, $title);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}