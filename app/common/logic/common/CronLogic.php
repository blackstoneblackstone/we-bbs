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

namespace app\common\logic\common;

use app\common\library\helper\ReputationHelper;

class CronLogic
{
    // 每半分钟执行
    public static function half_minute()
    {
        hook('cronHalfMinute');
    }

    // 每分钟执行
    public static function minute()
    {
        //计算用户威望
        $reputation_calculate_start = intval(cache('reputation_calculate_start'));
        if (ReputationHelper::calculate($reputation_calculate_start))
        {
            cache('reputation_calculate_start', (intval($reputation_calculate_start) + intval(get_setting('reputation_calc_limit',200))), intval(get_setting('reputation_calc_time',3)*86400));
        }
        else
        {
            cache('reputation_calculate_start', 0, intval(get_setting('reputation_calc_time',3)*86400));
        }

        hook('cronMinute');
    }

    // 每五分钟执行
    public static function five_minutes()
    {
        hook('cronFiveMinutes');
    }

    // 每十分钟执行
    public static function ten_minutes()
    {
        hook('cronTenMinutes');
    }

    // 每半小时执行
    public static function half_hour()
    {
        hook('cronHalfHour');
    }

    // 每小时执行
    public static function hour()
    {
        hook('cronHour');
    }

    // 每日执行
    public static function day()
    {
        hook('cronDay');
    }

    // 每周执行
    public static function week()
    {
        hook('cronWeek');
    }

    // 每月执行
    public static function month()
    {
        hook('cronMonth');
    }
}