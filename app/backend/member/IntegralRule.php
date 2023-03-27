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
use app\model\Score as ScoreModel;
use think\App;
use think\facade\Request;

/**
 * 积分规则
 * Class Score
 */
class IntegralRule extends Backend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ScoreModel();
		$this->table = 'integral_rule';
	}

	public function index()
	{
        $columns = [
            ['id','编号'],
            ['name', '行为标识'],
            ['title','行为名称'],
            ['cycle','执行周期'],
            ['cycle_type','周期类型','tag','',['month'=>'每月','week'=>'每周','day'=>'每天','hour'=>'每小时','minute'=>'每分钟','second'=>'每秒']],
            ['max','最大执行次数'],
            ['integral','操作积分'],
            ['log','日志规则'],
            ['status', '状态', 'status', '0',['0' => '禁用','1' => '启用']],
        ];

        $search = [

        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $where = $this->makeBuilder->getWhere($search);
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            return db('integral_rule')
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
            ->setSearch($search)
            ->addColumn('right_button', '操作', 'btn')
            ->addRightButtons(['edit', 'delete'])
            ->addTopButtons(['add','delete'])
            ->fetch();
	}

    public function add()
    {
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            $result = $this->model->create($data);
            if ($result) {
                $this->success('添加成功','index');
            } else {
                $this->error('添加失败');
            }
        }
        return $this->formBuilder
            ->addText('name','行为标识','输入行为标识 英文字母')
            ->addText('title','行为名称','输入行为名称')
            ->addText('cycle','执行周期','输入执行周期,代表每xx周期执行',1)
            ->addRadio('cycle_type','周期类型','选择执行类型',['month'=>'每月','week'=>'每周','day'=>'每天','hour'=>'每小时','minute'=>'每分钟','second'=>'每秒'])
            ->addNumber('max','最大执行次数','输入最大执行次数')
            ->addText('integral','操作积分','输入操作积分')
            ->addTextarea('log','日志规则','记录日志备注时按此规则来生成，支持[变量|函数],如[user|get_username]。目前变量有：user(用户uid),time(时间),record(记录值)')
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    public function edit( $id=0)
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

        $info =$this->model->where('id',$id)->find();

        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('name','行为标识','输入行为标识 英文字母',$info['name'])
            ->addText('title','行为名称','输入行为名称',$info['title'])
            ->addText('cycle','执行周期','代表每xx周期执行',$info['cycle'])
            ->addRadio('cycle_type','周期类型','选择周期类型',['month'=>'每月','week'=>'每周','day'=>'每天','hour'=>'每小时','minute'=>'每分钟','second'=>'每秒'],$info['cycle_type'])
            ->addNumber('max','最大执行次数','输入最大执行次数',$info['max'])
            ->addText('integral','操作积分','输入操作积分',$info['integral'])
            ->addTextarea('log','日志规则','记录日志备注时按此规则来生成，支持[变量|函数],如[user|get_username]。目前变量有：user(用户uid),time(时间),record(记录值)',$info['log'])
            ->addRadio('status','状态','状态',['0' => '禁用','1' => '正常'],$info['status'])
            ->fetch();
    }

    //日志列表
    public function log()
    {
        $columns = [
            ['id','编号'],
            ['nick_name','执行用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['record_id','数据id'],
            ['action_type','触发行为的类型','tag'],
            ['integral','操作积分'],
            ['remark','积分说明'],
            ['balance','积分余额'],
            ['create_time', '记录时间','datetime'],
        ];

        $search = [
            ['text', 'nick_name', '用户昵称', 'LIKE'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            $where = [];
            if($nick_name = $this->request->param('nick_name'))
            {
                $uid = db('users')->where([['nick_name','like',$nick_name]])->value('uid');
                if($uid){
                    $where[]= ['uid','like',$uid];
                }
            }
            $result = db('integral_log')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
            foreach ($result['data'] as $k=>$v)
            {
                $result['data'][$k]['nick_name'] = db('users')->where('uid',$v['uid'])->value('nick_name');
                $result['data'][$k]['url_token'] = db('users')->where('uid',$v['uid'])->value('url_token');
                $result['data'][$k]['action_type'] = db('integral_rule')->where('name',$v['action_type'])->value('title');
            }
            return $result;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->setEditUrl((string)url('detail',['id'=>'__id__']))
            ->fetch();
    }

    public function detail(string $id)
    {

    }
}