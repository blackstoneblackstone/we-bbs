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
use think\facade\Db;

class StatisticLogic
{
    // 单个类别统计数据
    public static function singleTagData($tag, $start_time = null, $end_time = null): array
    {
        if (!$start_time)
        {
            $start_time = strtotime('-6 months');
        }

        if (!$end_time)
        {
            $end_time = strtotime('Today');
        }

        switch ($tag)
        {
            case 'new_user':
                $query = "SELECT COUNT(uid) AS count, FROM_UNIXTIME(create_time, '%y-%m') AS statistic_date FROM " . get_table('users') . " WHERE create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'user_valid':
                $query = "SELECT COUNT(uid) AS count, FROM_UNIXTIME(create_time , '%y-%m') AS statistic_date FROM " . get_table('users') . " WHERE is_valid_email = 1 AND create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_question':
                $query = "SELECT COUNT(*) AS count, FROM_UNIXTIME(create_time, '%y-%m') AS statistic_date FROM " . get_table('question') . " WHERE create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_answer':
                $query = "SELECT COUNT(*) AS count, FROM_UNIXTIME(create_time, '%y-%m') AS statistic_date FROM " . get_table('answer') . " WHERE create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_topic':
                $query = "SELECT COUNT(*) AS count, FROM_UNIXTIME(create_time, '%y-%m') AS statistic_date FROM " . get_table('topic') . " WHERE create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_answer_vote':
                $query = "SELECT COUNT(*) AS count, FROM_UNIXTIME(create_time, '%y-%m') AS statistic_date FROM " . get_table('answer_vote') . " WHERE create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_question_thanks':
                $query = "SELECT COUNT(*) AS count, FROM_UNIXTIME(time, '%y-%m') AS statistic_date FROM " . get_table('question_thanks') . " WHERE time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_answer_thanks':
                $query = "SELECT COUNT(id) AS count, FROM_UNIXTIME(time, '%y-%m') AS statistic_date FROM " . get_table('answer_thanks') . " WHERE time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            case 'new_article':
                $query = "SELECT COUNT(*) AS count, FROM_UNIXTIME(create_time, '%y-%m') AS statistic_date FROM " . get_table('article') . " WHERE create_time BETWEEN " . intval($start_time) . " AND " . intval($end_time) . " GROUP BY statistic_date ASC";
                break;
            default:
                $query = '';
        }

        $data = [];
        if ($query)
        {
            if ($result = Db::query($query))
            {
                foreach ($result AS $key => $val)
                {
                    $data[] = array(
                        'date' => $val['statistic_date'],
                        'count' => $val['count']
                    );
                }
            }
        }
        return $data;
    }
}