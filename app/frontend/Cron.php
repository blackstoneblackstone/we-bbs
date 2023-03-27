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

namespace app\frontend;
use app\common\controller\Frontend;
use app\common\logic\common\CronLogic;

/**
 * 前台定时任务
 */
class Cron extends Frontend
{
    public function run()
    {
        if(get_setting('cron_enable')!='Y') return;

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');             // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: no-cache, must-revalidate');           // HTTP/1.1
        header('Pragma: no-cache');                                   // HTTP/1.0
        @set_time_limit(0);

        $call_actions = [];
        if (!cache('cron_timer_half_minute'))
        {
            $call_actions[] = 'half_minute';
            cache('cron_timer_half_minute', time(), 30, 'cron');
        }

        if (!cache('cron_timer_minute'))
        {
            $call_actions[] = 'minute';
            cache('cron_timer_minute', time(), 60, 'cron');
        }

        if (!cache('cron_timer_five_minutes'))
        {
            $call_actions[] = 'five_minutes';
            cache('cron_timer_five_minutes', time(), 300, 'cron');
        }

        if (!cache('cron_timer_ten_minutes'))
        {
            $call_actions[] = 'ten_minutes';
            cache('cron_timer_ten_minutes', time(), 600, 'cron');
        }
        
        if (gmdate('YW', cache('cron_timer_week')) != gmdate('YW', time()))
        {
            $call_actions[] = 'week';
            cache('cron_timer_week', time(), 259200, 'cron');
        }
        else if (gmdate('Y-m-d', cache('cron_timer_day')) != gmdate('Y-m-d', time()))
        {
            $call_actions[] = 'day';
            cache('cron_timer_day', time(), 86400, 'cron');
        }
        else if (!cache('cron_timer_hour'))
        {
            $call_actions[] = 'hour';
            cache('cron_timer_hour', time(), 3600, 'cron');
        }
        else if (!cache('cron_timer_half_hour'))
        {
            $call_actions[] = 'half_hour';
            cache('cron_timer_half_hour', time(), 1800, 'cron');
        }
        else if (!cache('cron_timer_month'))
        {
            $call_actions[] = 'month';
            cache('cron_timer_month', time(), ONE_DAY*30, 'cron');
        }

        if ($call_actions)
        {
            foreach ($call_actions AS $call_action)
            {
                CronLogic::$call_action();
            }
        }
    }
}