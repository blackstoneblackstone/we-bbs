<?php

namespace app\common\command;
use app\common\library\helper\ReputationHelper;
use app\model\Answer;
use app\model\Question;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

/**
 *系统内置定时任务
 */
class WeCenter extends Command
{
    public function configure()
    {
        $this->setName('we');
        $this->addArgument('action', Argument::OPTIONAL, 'clearNotify', 'clearNotify');
        $this->setDescription('系统默认定时任务');
    }

    /**
     * @param Input $input
     * @param Output $output
     * @return void
     */
    public function execute(Input $input, Output $output)
    {
        $action = $input->getArgument('action');
        if (method_exists($this, $method = "{$action}Action")) return $this->$method();
        $this->output->error(">> Wrong operation, Allow stop|start|status|query|listen|clean|doRun|webStop|webStart|webStatus");
    }

    /**
     * 清除已读通知
     * 每日凌晨10分执行
     * @return string
     */
    public function clearNotifyAction(): string
    {
        $notify_list = db('users_notify')->where([
            ['read_flag','=',1],
            ['create_time','<',(time() - 2592000)]
        ])->order('id','ASC')->select()->toArray();

        $count=sizeof($notify_list);

        foreach ($notify_list AS $k => $v)
        {
            db('users_notify')->where(['id'=>$v['id']])->delete();
            $this->setTaskProgress('正在处理【'.($k+1).'条数据】',  sprintf("%.2f",(($k+1)/$count)*100));
        }

        $this->setTaskProgress("数据处理完成", 100);
        return "success";
    }

    /**
     * 定时计算用户威望
     * @return string
     */
    public function calcReputationAction(): string
    {
        $users_list = db('users')->where([
            ['reputation_update_time','<',(time() - (get_setting('reputation_calc_time',3)*86400))]
        ])->limit(intval(get_setting('reputation_calc_limit',200)))->order('uid','ASC')->select()->toArray();

        $count=sizeof($users_list);

        foreach ($users_list AS $k => $v)
        {
            ReputationHelper::calcUserReputationByUid($v['uid']);
            $this->setTaskProgress('正在处理【'.($k+1).'条数据】',  sprintf("%.2f",(($k+1)/$count)*100));
        }

        $this->setTaskProgress("数据处理完成", 100);
        return "success";
    }

    //定时发布文章
    public function publishAction(): string
    {
        $publish_article_list = db('article')->where([['wait_time','>',0],['wait_time','<=',time()]])->select()->toArray();
        $count=sizeof($publish_article_list);
        foreach ($publish_article_list AS $k => $v)
        {
            db('article')->where(['id'=>$v['id']])->update([
                'status'=>1,
                'wait_time'=>0
            ]);
            $this->setTaskProgress('正在处理【'.($k+1).'条数据】',  sprintf("%.2f",(($k+1)/$count)*100));
        }
        $this->setTaskProgress("数据处理完成", 100);
        return "success";
    }

    /**
     * 自动锁定问题
     * @return string
     */
    public function autoLockQuestionAction(): string
    {
        Question::autoLockQuestion();
        $this->setTaskProgress("数据处理完成", 100);
        return "success";
    }

    /**
     * 自动设定最佳回答
     * @return string
     */
    public function autoSetBestAnswerAction(): string
    {
        Answer::autoSetBestAnswer();
        $this->setTaskProgress("数据处理完成", 100);
        return "success";
    }

    /**
     * 自动折叠不感兴趣的回复
     * @return string
     */
    public function forceFoldAnswersAction(): string
    {
        if(get_setting('uninterested_fold'))
        {
            $answer_ids = db('answer')->where([['uninterested_count','>=',get_setting('uninterested_fold')]])->column('id');
            if($answer_ids)
            {
                db('answer')->whereIN('id',$answer_ids)->update(['force_fold'=>1]);
            }
        }
        return "success";
    }

    // 每半分钟执行
    public function halfMinuteAction()
    {
        hook('cronHalfMinute');
    }

    // 每分钟执行
    public function minuteAction()
    {
        hook('cronMinute');
    }

    // 每五分钟执行
    public function fiveMinutesAction()
    {
        hook('cronFiveMinutes');
    }

    // 每十分钟执行
    public function tenMinutesAction()
    {
        hook('cronTenMinutes');
    }

    // 每半小时执行
    public function halfHourAction()
    {
        hook('cronHalfHour');
    }

    // 每小时执行
    public function hourAction()
    {
        hook('cronHour');
    }

    // 每日执行
    public function dayAction()
    {
        hook('cronDay');
    }

    // 每周执行
    public function weekAction()
    {
        hook('cronWeek');
    }

    // 每月执行
    public function monthAction()
    {
        hook('cronMonth');
    }
}