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
use app\common\library\helper\IpLocation;
use think\App;
use app\model\Action as ActionModel;
use think\facade\Request;

/**
 * @title 行为管理
 * @description 行为管理
 */
class Action extends Backend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ActionModel();
		$this->table = 'action';
	}

    public function index()
    {
        $columns = [
            ['id','编号'],
            ['name', '行为标识'],
            ['title','行为名称'],
            ['remark','行为描述'],
            ['log_rule','日志规则'],
            ['status', '状态', 'status', '0',['0' => '禁用','1' => '启用']],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            return db('action')
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
            ->addTextarea('remark','行为描述','输入行为描述')
            ->addTextarea('log_rule','日志规则','记录日志备注时按此规则来生成，支持[变量|函数] 默认user,time变量已进行解析，无需配置函数。目前变量有：user-当前行为用户,time-行为触发时间,name-执行类型标题;如:[user|get_username]在[time|formatTime]]登录了系统')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],1)
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

        $info =db('action')->where('id',$id)->find();

        return $this->formBuilder
            ->setFormData($info)
            ->addHidden('id')
            ->addText('name','行为标识','输入行为标识 英文字母')
            ->addText('title','行为名称','输入行为名称')
            ->addTextarea('remark','行为描述','输入行为描述')
            ->addTextarea('log_rule','日志规则','记录日志备注时按此规则来生成，支持[变量|函数] 默认user,time变量已进行解析，无需配置函数。目前变量有：user-当前行为用户,time-行为触发时间,name-执行类型标题;如:[user|get_username]在[time|formatTime]]登录了系统')
            ->addRadio('status','状态','用户状态',['0' => '禁用','1' => '正常'],1)
            ->fetch();
    }

    //行为日志列表
    public function log()
    {
        $columns = [
            ['id','编号'],
            ['action_title', '行为'],
            ['nick_name','执行用户','link',get_url('people/index',['name'=>'__url_token__'])],
            ['action_ip','ip'],
            ['action_local','真实地址'],
            ['record_type','行为表'],
            ['record_id','数据id'],
            ['anonymous', '匿名', 'tag', '0',['0' => '否','1' => '是']],
            ['status', '状态', 'status', '0',['0' => '禁用','1' => '启用']],
            ['create_time', '执行时间','datetime'],
        ];

        $action_list = db('action')->column('title,id');

        $search = [
            ['text', 'nick_name', '用户昵称', 'LIKE'],
            ['select', 'action_id', '行为类型', '=','',array_column($action_list,'title','id')],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            $where = $this->makeBuilder->getWhere($search);
            if($nick_name = $this->request->param('nick_name'))
            {
                $uid = db('users')->where([['nick_name','like',$nick_name]])->value('uid');
                if($uid){
                    $where[]= ['uid','like',$uid];
                }
            }
            $data = db('action_log')
                ->where($where)
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();

            $ip = new IpLocation();
            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['nick_name'] = db('users')->where('uid',$val['uid'])->value('nick_name');
                $data['data'][$key]['url_token'] = db('users')->where('uid',$val['uid'])->value('url_token');
                $data['data'][$key]['action_title'] = db('action')->where('id',$val['action_id'])->value('title');
                $data['data'][$key]['action_local'] = $ip->getLocation($val['action_ip'])['country'];
            }
            return $data;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->setSearch($search)
            ->addTopButtons(['delete'])
            ->setDelUrl((string)url('deleteLog'))
            ->setEditUrl((string)url('detail',['id'=>'__id__']))
            ->fetch();
    }

    public function deleteLog(string $id)
    {
        if ($this->request->isPost()) {
            if (strpos($id, ',') !== false)
            {
                $ids = explode(',',$id);
                if(db('action_log')->delete($ids)){
                    return json(['error'=>0, 'msg'=>'删除成功!']);
                }else{
                    return ['error' => 1, 'msg' => '删除失败'];
                }
            }

            if(db('action_log')->delete())
            {
                return json(['error'=>0,'msg'=>'删除成功!']);
            }
            return ['error' => 1, 'msg' => '删除失败'];
        }
    }

    public function detail(string $id)
    {

    }
}