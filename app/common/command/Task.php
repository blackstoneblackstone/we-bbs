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
namespace app\common\command;

use app\common\service\TaskService;
use Psr\Log\NullLogger;
use think\Collection;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

/**
 * 异步任务管理指令
 * Class Task
 * @package app\common\command
 */
class Task extends Command
{

    /**
     * 任务编号
     * @var string
     */
    protected $code;

    /**
     * 绑定数据表
     * @var string
     */
    protected $table = 'task';

    /**
     * 配置指令参数
     */
    public function configure()
    {
        $this->setName('task');
        $this->addOption('host', '-host', Option::VALUE_OPTIONAL, 'The host of WebServer.');
        $this->addOption('port', '-port', Option::VALUE_OPTIONAL, 'The port of WebServer.');
        $this->addOption('daemon', 'd', Option::VALUE_NONE, 'Run the task listen in daemon mode');
        $this->addArgument('action', Argument::OPTIONAL, 'stop|start|status|query|listen|clean|doRun|webStop|webStart|webStatus', 'listen');
        $this->addArgument('code', Argument::OPTIONAL, '任务指令');
        $this->addArgument('spt', Argument::OPTIONAL, '分隔符/Separator');
        $this->setDescription('Asynchronous Command Queue Task');
    }

    /**
     * 执行指令内容
     * @param Input $input
     * @param Output $output
     * @return mixed
     */
    public function execute(Input $input, Output $output)
    {
        $action = $this->input->hasOption('daemon') ? 'start' : $input->getArgument('action');
        if (method_exists($this, $method = "{$action}Action")) return $this->$method();
        $this->output->error(">> Wrong operation, Allow stop|start|status|query|listen|clean|doRun|webStop|webStart|webStatus");
    }

    /**
     * 停止 WebServer 调试进程
     */
    protected function webStopAction()
    {
        $root = $this->app->getRootPath() . 'public' . DIRECTORY_SEPARATOR;
        if (count($result = $this->process->query("-t {$root} {$root}router.php")) < 1) {
            $this->output->writeln(">> There are no WebServer processes to stop");
        } else {
            foreach ($result as $item) {
                $this->process->close(intval($item['pid']));
                $this->output->writeln(">> Successfully sent end signal to process {$item['pid']}");
            }
        }
    }

    /**
     * 启动 WebServer 调试进程
     */
    protected function webStartAction()
    {
        $port = $this->input->getOption('port') ?: '80';
        $host = $this->input->getOption('host') ?: '127.0.0.1';
        $root = $this->app->getRootPath() . 'public' . DIRECTORY_SEPARATOR;
        $command = "php -S {$host}:{$port} -t {$root} {$root}router.php";
        $this->output->comment("># {$command}");
        if (count($result = $this->process->query($command)) > 0) {
            if ($this->process->isWin()) $this->process->exec("start http://{$host}:{$port}");
            $this->output->writeln(">> WebServer process already exist for pid {$result[0]['pid']}");
        } else {
            [$this->process->create($command), usleep(2000)];
            if (count($result = $this->process->query($command)) > 0) {
                $this->output->writeln(">> WebServer process started successfully for pid {$result[0]['pid']}");
                if ($this->process->isWin()) $this->process->exec("start http://{$host}:{$port}");
            } else {
                $this->output->writeln('>> WebServer process failed to start');
            }
        }
    }

    /**
     * 查看 WebServer 调试进程
     */
    protected function webStatusAction()
    {
        $root = $this->app->getRootPath() . 'public' . DIRECTORY_SEPARATOR;
        if (count($result = $this->process->query("-t {$root} {$root}router.php")) > 0) {
            $this->output->comment("># {$result[0]['cmd']}");
            $this->output->writeln(">> WebServer process {$result[0]['pid']} running");
        } else {
            $this->output->writeln(">> The WebServer process is not running");
        }
    }

    /**
     * 停止所有任务
     */
    protected function stopAction()
    {
        $keyword = $this->process->think('task');
        if (count($result = $this->process->query($keyword)) < 1) {
            $this->output->writeln(">> There are no task processes to stop");
        } else foreach ($result as $item) {
            $this->process->close(intval($item['pid']));
            $this->output->writeln(">> Successfully sent end signal to process {$item['pid']}");
        }
    }

    /**
     * 启动后台任务
     */
    protected function startAction()
    {
        $command = $this->process->think('task listen');
        $this->output->comment("># {$command}");
        if (count($result = $this->process->query($command)) > 0) {
            $this->output->writeln(">> Asynchronous daemons already exist for pid {$result[0]['pid']}");
        } else {
                $ret=$this->process->create($command);
                usleep(1000);
            $this->output->writeln(">> $command");
            if (count($result = $this->process->query($command)) > 0) {
                $this->output->writeln(">> Asynchronous daemons started successfully for pid {$result[0]['pid']}");
            } else {
                $this->output->writeln(">> Asynchronous daemons failed to start");
            }
        }
    }

    /**
     * 查询所有任务
     */
    protected function queryAction()
    {
        $list = $this->process->query($this->process->think("task"));
        if (count($list) > 0) foreach ($list as $item) {
            $this->output->writeln(">> {$item['pid']}\t{$item['cmd']}");
        } else {
            $this->output->writeln('>> No related task process found');
        }
    }

    /**
     * 清理所有任务
     */
    protected function cleanAction()
    {
        // 清理 7 天前的历史任务记录
        $map = [['exec_time', '<', time() - 7 * 24 * 3600]];
        $clear = db($this->table)->where($map)->delete();
        // 标记超过 1 小时未完成的任务为失败状态，循环任务失败重置
        $map1 = [['loops_time', '>', 0], ['status', '=', 4]]; // 执行失败的循环任务
        $map2 = [['exec_time', '<', time() - 3600], ['status', '=', 2]]; // 执行超时的任务
        [$timeout, $loops, $total] = [0, 0, db($this->table)->whereOr([$map1, $map2])->count()];
        db($this->table)->whereOr([$map1, $map2])->chunk(100, function (Collection $result) use ($total, &$loops, &$timeout) {
            foreach ($result->toArray() as $item) {
                $item['loops_time'] > 0 ? $loops++ : $timeout++;
                if ($item['loops_time'] > 0) {
                    $this->task->message($total, $timeout + $loops, "正在重置任务 {$item['code']} 为运行");
                    [$status, $message] = [1, intval($item['status']) === 4 ? '任务执行失败，已自动重置任务！' : '任务执行超时，已自动重置任务！'];
                } else {
                    $this->task->message($total, $timeout + $loops, "正在标记任务 {$item['code']} 为超时");
                    [$status, $message] = [4, '任务执行超时，已自动标识为失败！'];
                }
                db($this->table)->where(['id' => $item['id']])->update(['status' => $status, 'exec_desc' => $message]);
            }
        });
        $this->setTaskSuccess("清理 {$clear} 条历史任务，关闭 {$timeout} 条超时任务，重置 {$loops} 条循环任务");
    }

    /**
     * 查询兼听状态
     */
    protected function statusAction()
    {
        $command = $this->process->think('task listen');
        if (count($result = $this->process->query($command)) > 0) {
            $this->output->writeln("监听主进程 {$result[0]['pid']} 在运行");
        } else {
            $this->output->writeln("监听主进程没有启动");
        }
    }

    /**
     * 立即监听任务
     */
    protected function listenAction()
    {
        set_time_limit(0);
        $this->app->db->setLog(new NullLogger());

        if ($this->process->isWin()) {
            $this->setProcessTitle("Task Listen");
        }
        $this->output->writeln("\tYou can exit with <info>`CTRL-C`</info>");
        $this->output->writeln('============== LISTENING ==============');
        while (true) {
            [$start, $where] = [microtime(true), [['status', '=', 1], ['exec_time', '<=', time()]]];
            foreach (db($this->table)->where($where)->order('exec_time asc')->select()->toArray() as $vo) try {
                $command = $this->process->think("task doRun {$vo['code']} -");
                $this->output->comment("># {$command}");
                if (count($this->process->query($command)) > 0) {
                    $this->output->writeln(">> Already in progress -> [{$vo['code']}] {$vo['title']}");
                } else {
                    $this->process->create($command);
                    $this->output->writeln(">> Created new process -> [{$vo['code']}] {$vo['title']}");
                }
            } catch (\Exception $exception) {
                db($this->table)->where(['code' => $vo['code']])->update([
                    'status' => 4, 'outer_time' => time(), 'exec_desc' => $exception->getMessage(),
                ]);
                $this->output->error(">> Execution failed -> [{$vo['code']}] {$vo['title']}，{$exception->getMessage()}");
            }
            if (microtime(true) - $start < 0.5000) usleep(500000);
        }
    }

    /**
     * 执行任务内容
     */
    protected function doRunAction()
    {
        set_time_limit(0);
        $this->code = trim($this->input->getArgument('code'));
        if (empty($this->code)) {
            $this->output->error('Task number needs to be specified for task execution');
        } else try {
            $this->task->initialize($this->code);
            if (empty($this->task->record) || intval($this->task->record['status']) !== 1) {
                $this->output->warning("The or status of task {$this->code} is abnormal");
            } else {
                db($this->table)->strict(false)->where(['code' => $this->code])->update([
                    'enter_time' => microtime(true), 'attempts' => $this->app->db->raw('attempts+1'),
                    'outer_time' => 0, 'exec_pid' => getmypid(), 'exec_desc' => '', 'status' => 2,
                ]);
                $this->task->progress(2, '>>> 任务处理开始 <<<', 0);
                if ($this->process->isWin()) {
                    $this->setProcessTitle(" {$this->process->version()} Task - {$this->task->title}");
                }
                defined('WorkTaskCall') or define('WorkTaskCall', true);
                defined('WorkTaskCode') or define('WorkTaskCode', $this->code);
                if (class_exists($command = $this->task->record['command'])) {
                    $class = $this->app->make($command, [], true);
                    if ($class instanceof Task) {
                        $this->updateTask(3, $class->initialize($this->task,null)->execute($this->task->data,null) ?: '');
                    } elseif ($class instanceof TaskService) {
                        $this->updateTask(3, $class->initialize($this->task->code)->execute($this->task->data) ?: '');
                    } else {
                        throw new \think\Exception("自定义 {$command} 未继承 Task");
                    }
                } else {
                    $attr = explode(' ', trim(preg_replace('|\s+|', ' ', $this->task->record['command'])));
                    $this->updateTask(3, $this->app->console->call(array_shift($attr), $attr)->fetch(), false);
                }
            }
        } catch (\Exception|\Error|\Throwable $exception) {
            $code = $exception->getCode();
            if (intval($code) !== 3) $code = 4;
            $this->updateTask($code, $exception->getMessage());
        }
    }

    /**
     * 修改当前任务状态
     * @param integer $status 任务状态
     * @param $message
     * @param boolean $isSplit 是否分隔
     */
    protected function updateTask(int $status, $message, bool $isSplit = true)
    {
        // 更新当前任务
        $info = trim(is_string($message) ? $message : '');
        $desc = $isSplit ? explode("\n", $info) : [$message];
        db($this->table)->strict(false)->where(['code' => $this->code])->update([
            'status' => $status, 'outer_time' => microtime(true), 'exec_pid' => getmypid(), 'exec_desc' => $desc[0],
            //'exec_time'=>$this->app->db->raw('exec_time+'.intval($this->task->record['loops_time']))
        ]);

        //记录日志
        db('task_log')->insert([
            'status' => $status==3?1:0,
            'code'=>$this->code,
            'create_time'=>time(),
            'remark'=>$status==3 ? '任务处理完成' : '任务处理失败',
        ]);

        $this->output->writeln(is_string($message) ? $message : '');
        // 任务进度标记
        if (!empty($desc[0])) {
            $this->task->progress($status, ">>> {$desc[0]} <<<");
        }
        if ($status == 3) {
            $this->task->progress($status, '>>> 任务处理完成 <<<', 100);
        } elseif ($status == 4) {
            $this->task->progress($status, '>>> 任务处理失败 <<<');
        }

        // 注册循环任务
        if (isset($this->task->record['loops_time']) && $this->task->record['loops_time'] > 0) {
            try {
                $this->task->initialize($this->code)->reset($this->task->record['loops_time']);
            } catch (\Exception|\Error|\Throwable $exception) {
                $this->app->log->error("task {$this->task->record['code']} Loops Failed. {$exception->getMessage()}");
            }
        }
    }
}