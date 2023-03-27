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

use app\common\service\ProcessService;
use app\common\service\TaskService;
use Exception;
use think\console\Command as ThinkCommand;
use think\console\Input;
use think\console\Output;
use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;

/**
 * 自定义指令基类
 * Class Command
 * @package think\admin
 */
abstract class Command extends ThinkCommand
{
    /**
     * 任务控制服务
     * @var TaskService
     */
    protected $task;

    /**
     * 进程控制服务
     * @var ProcessService
     */
    protected $process;

    /**
     * 初始化指令变量
     * @param Input $input
     * @param Output $output
     * @return static
     * @throws Exception
     * @throws DataNotFoundException
     * @throws DbException
     * @throws ModelNotFoundException
     */
    protected function initialize(Input $input, Output $output): Command
    {
        $this->task = TaskService::instance();
        $this->process = ProcessService::instance();

        if (defined('WorkTaskCode')) {
            if (!$this->task instanceof TaskService) {
                $this->task = TaskService::instance();
            }
            if ($this->task->code !== WorkTaskCode) {
                $this->task->initialize(WorkTaskCode);
            }
        }
        return $this;
    }

    /**
     * 设置进度消息并继续执行
     * @param null|string $message 进度消息
     * @param mixed $progress 进度数值
     * @return static
     */
    protected function setTaskProgress(?string $message = null, $progress = null): Command
    {
        if (defined('WorkTaskCode')) {
            $this->task->progress(2, $message, $progress);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }

    /**
     * 设置失败消息并结束进程
     * @param $message
     * @return static
     */
    protected function setTaskError($message): Command
    {
        if (defined('WorkTaskCode')) {
            $this->task->error($message);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }

    /**
     * 设置成功消息并结束进程
     * @param $message
     * @return static
     */
    protected function setTaskSuccess($message): Command
    {
        if (defined('WorkTaskCode')) {
            $this->task->success($message);
        } elseif (is_string($message)) {
            $this->output->writeln($message);
        }
        return $this;
    }
}