<?php
namespace app\backend\extend;

use app\common\controller\Backend;
use app\common\library\helper\DateHelper;
use app\common\service\TaskService;
use think\App;
use think\facade\Request;

class Task extends Backend
{
    protected $table='task';
    protected $taskService;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->taskService = TaskService::instance();
    }

    //队列主页
	public function index()
	{
        $columns = [
            //['id'  , '编号'],
            //['code', '任务编号'],
            ['title','任务名称','text'],
            //['command','执行指令'],
            ['exec_pid','执行进程'],
            //['exec_data','执行参数'],
            ['exec_time','下次执行时间','datetime'],
            //['exec_desc','任务描述'],
            //['enter_time', '本次开始时间','datetime'],
            //['outer_time', '本次结束时间','datetime'],
            ['loops_time','循环时间'],
            ['attempts','执行次数'],
            //['rscript','任务类型','tag','',[0=>'单例',1=>'多例']],
            ['status_text', '状态', 'tag', '0',[1=>'新任务',2=>'处理中',3=>'成功',4=>'失败']],
           // ['create_at', '创建时间','datetime'],
        ];

        if ($this->request->param('_list'))
        {
            // 排序规则
            $orderByColumn = $this->request->param('orderByColumn') ?? 'id';
            $isAsc = $this->request->param('isAsc') ?? 'desc';
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            // 排序处理
            $data = db('task')
                ->order([$orderByColumn => $isAsc])
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();

            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['status_text'] = $val['status'];
                $data['data'][$key]['loops_time'] = DateHelper::formatSeconds(intval($val['loops_time']));
            }
            return $data;
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            ->addColumn('right_button', '操作', 'btn')
            ->setPageTips('本定时任务可直接在windows、linux系统运行,使用的是tp的console控制台，运行须知：<br>
1、运行前需确认php是否允许exec命令,且php已加入环境变量<br>
2、运行前先点击“开始监听主进程”方可执行定时<br>
3、系统默认服务需要先点击“注册系统默认任务”来注册系统任务')
            ->addRightButtons(['edit','redo'=>[
                    'title' => '重置',
                    'icon'  => '',
                    'class' => 'btn btn-success btn-sm do-queue',
                    'url'  => (string)url('redo',['code'=>'__code__']),
                    'href'=>''
                ],
                'state'=>[
                    'title' => '状态',
                    'icon'  => '',
                    'class' => 'btn btn-danger btn-sm do-queue',
                    'url'  => (string)url('state',['id'=>'__code__']),
                    'href'=>''
                ],
                'log'=>[
                    'title' => '日志',
                    'icon'  => '',
                    'class' => 'btn btn-warning btn-sm aw-ajax-open',
                    'url'  => (string)url('log',['code'=>'__code__']),
                    'href'=>''
                ],
            ])
            ->addTopButtons([
                'delete',
                'start'=>[
                    'title'   => '开始监听主进程',
                    'icon'    => 'fa fa-file-code-o',
                    'class'   => 'btn btn-info aw-ajax-get',
                    'url'    => (string)url('start'),
                ],
                'stop'=>[
                    'title'   => '停止监听主进程',
                    'icon'    => 'fa fa-file-code-o',
                    'class'   => 'btn btn-info aw-ajax-get',
                    'url'    => (string)url('stop'),
                ],
                'status'=>[
                    'title' => '主进程状态',
                    'icon'  => '',
                    'class' => 'btn btn-success btn-sm aw-ajax-get',
                    'url'  => (string)url('status'),
                    'href'=>''
                ],
                'register'=>[
                    'title' => '注册系统默认任务',
                    'icon'  => '',
                    'class' => 'btn btn-success btn-sm aw-ajax-get',
                    'url'  => (string)url('register'),
                    'href'=>''
                ],
                ])
            ->fetch();
	}

    public function edit($id=0)
    {
        if ($this->request->isPost())
        {
            $data =$this->request->except(['file'],'post');
            $data['exec_time'] = strtotime($data['exec_time']);
            $result = db('task')->where(['id'=>$data['id']])->update($data);
            if ($result) {
                $this->success('修改成功', 'index');
            } else {
                $this->error('提交失败或数据无变化');
            }
        }

        $info =db('task')->where(['id'=>$id])->find();
        $info['exec_time'] = date('Y-m-d H:i:s',$info['exec_time']);
        return $this->formBuilder
            ->addHidden('id',$info['id'])
            ->addText('title','任务名称','',$info['title'])
            ->addText('loops_time','执行间隔','输入执行间隔时间 单位为秒',$info['loops_time'])
            ->addDatetime('exec_time','开始执行时间','',$info['exec_time'])
            ->addRadio('rscript','任务类型','任务类型，单例只允许同一个命令，多例可允许存在多个相同命令',['0' => '单例','1' => '多例'],$info['rscript'])
            ->addRadio('status','状态','任务状态',[1=>'新任务',2=>'处理中',3=>'成功',4=>'失败'],$info['status'])
            ->fetch();
    }

    public function start()
    {
        $message = nl2br($this->app->console->call('task', ['start'])->fetch());
        if (stripos($message, 'daemons started successfully for pid')) {
            $this->success('任务监听主进程启动成功！');
        } elseif (stripos($message, 'daemons already exist for pid')) {
            $this->success('任务监听主进程已经存在！');
        } else {
            $this->error($message);
        }
    }

    public function stop()
    {
        $message = nl2br($this->app->console->call('task', ['stop'])->fetch());
        if (stripos($message, 'sent end signal to process')) {
            $this->success('停止任务监听主进程成功！');
        } elseif (stripos($message, 'processes to stop')) {
            $this->success('没有找到需要停止的进程！');
        } else {
            $this->error($message);
        }
    }

    public function status()
    {
        $message = $this->app->console->call('task', ['status'])->fetch();
        $this->success($message);
    }

    //注册系统定时任务
    public function register()
    {
        $start_time = strtotime(date("Y-m-d",time()));
        try {
            $this->taskService->register('清除已读通知', 'we clearNotify', $start_time + (10 * 60), [], 0, ONE_DAY*30);
            $this->taskService->register('延时发布文章', 'we publish', time()+60, [], 0, 60);
            $this->taskService->register('计算用户威望', 'we calcReputation', $start_time + (20 * 60), [], 0, 60*30);
            $this->taskService->register('自动锁定问题', 'we autoLockQuestion', $start_time + (20 * 60), [], 0, ONE_DAY);
            $this->taskService->register('自动设定最佳回复', 'we autoSetBestAnswer', $start_time + (20 * 60), [], 0, ONE_DAY);
            $this->taskService->register('自动折叠回复', 'we forceFoldAnswers', $start_time + (20 * 60), [], 0, 60*5);
            $this->taskService->register('每半分钟执行', 'we halfMinute', time()+30, [], 0, 30);
            $this->taskService->register('每分钟执行', 'we minute', time()+60, [], 0, 60);
            $this->taskService->register('每5分钟执行', 'we fiveMinutes', time()+300, [], 0, 60*5);
            $this->taskService->register('每10分钟执行', 'we tenMinutes', time()+600, [], 0, 60*10);
            $this->taskService->register('每半小时执行', 'we halfHour', time()+(60*30), [], 0, 60*30);
            $this->taskService->register('每小时执行', 'we hour', time()+(60*60), [], 0, 60*60);
            $this->taskService->register('每日执行', 'we day', time()+ONE_DAY, [], 0, ONE_DAY);
            $this->taskService->register('每周执行', 'we week', time()+(ONE_DAY*7), [], 0, ONE_DAY*7);
            $this->taskService->register('每月执行', 'we month', time()+(ONE_DAY*30), [], 0, ONE_DAY*30);
        } catch (\Exception $e) {
            $this->error($e->getMessage(),'index');
        }
        $this->success('初始任务初始成功！','index');
    }

    /**
     * 重启任务
     * @auth true
     */
    public function redo()
    {
        $code = $this->request->param('code');
        $task = $this->taskService->initialize($code)->reset();
        $task->progress(1, '>>> 任务重置成功 <<<', 0.00);
        $this->success('任务重置成功！','', $task->code);
    }

    public function log($code)
    {
        $columns = [
            ['create_time', '执行时间','datetime'],
            ['status', '状态', 'tag', '0',['0' => '失败','1' => '成功']],
        ];
        if ($this->request->param('_list'))
        {
            // 排序规则
            $code = $this->request->param('code');
            $pageSize = $this->request->param('pageSize',get_setting("contents_per_page",15));
            return db('task_log')
                ->where(['code'=>$code])
                ->order('create_time','DESC')
                ->paginate([
                    'query'     => Request::get(),
                    'list_rows' =>$pageSize,
                ])
                ->toArray();
        }

        return $this->tableBuilder
            ->setUniqueId('id')
            ->addColumns($columns)
            /*->addTopButtons(['delete'])*/
            ->setDataUrl((string)url('log',['code'=>$code,'_list'=>1]))
            ->fetch();
    }

    //获取任务状态
    public function state($id='')
    {
        $task = $this->taskService->initialize($id);
        $task->progress(1, '>>> 获取任务状态成功 <<<', 0.00);
        $this->success('获取任务状态成功！','', $task->code);
    }

    //获取任务进度
    public function progress()
    {
        $code=$this->request->param('code');
        $task = $this->taskService->initialize($code);
        $this->success('获取任务进度成功！','',  $task->progress());
    }
}