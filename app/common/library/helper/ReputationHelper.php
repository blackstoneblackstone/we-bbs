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
namespace app\common\library\helper;
use app\model\Users;
use app\model\Vote;

/**
 * 威望计算
 * Class PowerHelper
 */
class ReputationHelper
{
    //计算用户威望，$calcType 0 用户全部内容的总威望,其他数字代表xx天内的用户威望，如 30 代表一个月内的威望
    public static function calcUserReputationByUid($uid,int $calcType=0)
    {
        //用户在文章内的威望计算：$user_article_power = 不同威望组用户威望系数 * （赞同数-反对数） + 认证用户威望系数*（赞同数-反对数）
        if (!$user_info = Users::getUserInfoByUid($uid))
        {
            return false;
        }

        $user_reputation = 0;

        $articles_ids = $articles_vote_agree_users = $articles_vote_against_users = [];

        //认证用户威望系数
        $verify_user_power_factor = intval(get_setting('verify_user_power_factor'));
        //赞同威望系数
        $power_agree_factor = intval(get_setting('power_agree_factor'));
        //反对威望系数
        $power_against_factor = intval(get_setting('power_against_factor'));
        //发起者威望系数
        $publish_user_power_factor = intval(get_setting('publish_user_power_factor'));
        //最佳回复威望系数
        $power_best_answer_factor = intval(get_setting('power_best_answer_factor'));
        //感谢威望系数
        $thanks_power_factor = intval(get_setting('thanks_power_factor'));
        //不感兴趣威望系数
        $uninterested_power_factor = intval(get_setting('uninterested_power_factor'));

        //总威望系数
        $reputation_log_factor = intval(get_setting('power_log_factor'));

        $user_articles = db('article')->where('uid',$uid)->select()->toArray();

        //计算文章威望
        if ($user_articles)
        {
            foreach ($user_articles as $key => $val)
            {
                $articles_ids[] = $val['id'];
            }

            if ($articles_ids)
            {
                //文章点赞
                $articles_vote_agree_users = Vote::getVoteByItemIds('article',$articles_ids,1);
                //文章反对
                $articles_vote_against_users = Vote::getVoteByItemIds('article',$articles_ids,-1);
            }

            $s_agree_value = $s_against_value = $verify_user_agree_value = $verify_user_against_value =$article_agree_reputation = $article_against_reputation=  0 ;

            foreach ($user_articles as $key => $val)
            {
                // 赞同的用户
                if (isset($articles_vote_agree_users[$val['id']]) && $articles_vote_agree_users && $articles_vote_agree_users[$val['id']])
                {
                    $voteUserInfo =  Users::getUserInfo($articles_vote_agree_users[$val['id']]['uid']);
                    if(isset($voteUserInfo['verified']) && $voteUserInfo['verified']!='')
                    {
                        $verify_user_agree_value += $verify_user_power_factor;
                    }else{
                        $s_agree_value += $power_agree_factor;
                    }
                }

                // 反对的用户
                if (isset($articles_vote_against_users[$val['id']]) && $articles_vote_against_users && $articles_vote_against_users[$val['id']])
                {
                    $voteUserInfo =  Users::getUserInfoByUid($articles_vote_against_users[$val['id']]['uid']);
                    if($voteUserInfo['verified'])
                    {
                        $verify_user_against_value += $verify_user_power_factor;
                    }else{
                        $s_against_value += $power_against_factor;
                    }
                }

                //文章威望权重
                $article_agree_reputation = $s_agree_value + $verify_user_against_value;
                $article_against_reputation = $s_against_value + $verify_user_against_value;
            }

            //赞同
            if ($article_agree_reputation < 0)
            {
                $article_agree_reputation = (0 - $article_agree_reputation) - 0.5;
                if ($power_agree_factor > 1)
                {
                    $article_agree_reputation = (0 - log($article_agree_reputation, $power_agree_factor));
                }
            }

            if ($article_agree_reputation > 0)
            {
                $article_agree_reputation = $article_agree_reputation + 0.5;
                if ($power_agree_factor > 1)
                {
                    $article_agree_reputation = log($article_agree_reputation, $power_agree_factor);
                }
            }

            //反对
            if ($article_against_reputation < 0)
            {
                $article_against_reputation = (0 - $article_against_reputation) - 0.5;
                if ($power_agree_factor > 1)
                {
                    $article_against_reputation = (0 - log($article_against_reputation, $power_against_factor));
                }
            }

            if ($article_against_reputation > 0)
            {
                $article_against_reputation = $article_against_reputation + 0.5;
                if ($power_agree_factor > 1)
                {
                    $article_against_reputation = log($article_against_reputation, $power_against_factor);
                }
            }

            $user_reputation += $user_reputation + $article_against_reputation + $article_agree_reputation;
        }

        //用户在问题回答中的威望计算（不包含问题发起者的回答）：
        //$user_answer_power =
        //（不同威望组用户威望系数 * （赞同数-反对数）） + （最佳回复数 * 最佳回复威望系数） + （感谢数 * 感谢威望系数） + （问题发起者威望系数*（赞同数-反对数）） + 认证用户威望系数*（赞同数-反对数）
        $user_answers = db('answer')->where('uid',$uid)->column('id, question_id, agree_count, thanks_count,uid');
        if ($user_answers)
        {
            $question_ids = $answer_ids = $questions_info = $vote_agree_users = $vote_against_users =  [];

            $answer_against_reputation = $answer_agree_reputation = 0;

            foreach ($user_answers as $key => $val)
            {
                $answer_ids[] = $val['id'];
                $question_ids[] = $val['question_id'];
            }

            if ($question_ids)
            {
                if ($questions_info_query = db('question')->whereIn('id',$question_ids)->column('id, best_answer, uid, category_id'))
                {
                    foreach ($questions_info_query AS $key => $val)
                    {
                        $questions_info[$val['id']] = $val;
                    }
                    unset($questions_info_query);
                }
            }
            if ($answer_ids)
            {
                $vote_agree_users = Vote::getVoteByItemIds('answer',$answer_ids,1);
                $vote_against_users = Vote::getVoteByItemIds('answer',$answer_ids,-1);
            }

            $s_publisher_agree = 0;	// 得到发起者赞同
            $s_publisher_against = 0;	// 得到发起者反对
            $s_verify_user_agree = 0;	// 得到发起者赞同
            $s_verify_user_against = 0;	// 得到发起者反对
            $s_agree_value = 0;	// 赞同威望系数
            $s_against_value = 0;	// 反对威望系数
            $s_best_answer = 0;

            foreach ($user_answers as $key => $val)
            {
                if (!isset($questions_info[$val['question_id']]))
                {
                    continue;
                }

                // 是否最佳回复
                if ($questions_info && $questions_info[$val['question_id']]['best_answer'] == $val['id'])
                {
                    $s_best_answer+= $power_best_answer_factor;
                }

                // 赞同的用户
                if (isset($vote_agree_users[$val['id']]) && $vote_agree_users && $vote_agree_users[$val['id']])
                {
                    // 排除发起者
                    if (isset($val['uid']) && $questions_info[$val['question_id']]['uid'] != $val['uid'])
                    {
                        $voteUserInfo =  Users::getUserInfoByUid($vote_agree_users[$val['id']]['uid']);
                        if($voteUserInfo['verified'])
                        {
                            $s_verify_user_agree += $verify_user_power_factor;
                        }else if ($questions_info[$val['question_id']]['uid'] == $vote_agree_users[$val['id']]['uid'] AND!$s_publisher_agree)
                        {
                            $s_publisher_agree += $publish_user_power_factor;
                        }else{
                            $s_agree_value += $power_agree_factor;
                        }
                    }
                }

                // 反对的用户
                if (isset($vote_against_users[$val['id']]) && $vote_against_users && $vote_against_users[$val['id']])
                {
                    //排除发起者
                    if ($questions_info[$val['question_id']]['uid'] != $val['uid'])
                    {
                        $voteUserInfo =  Users::getUserInfoByUid($vote_against_users[$val['id']]['uid']);
                        if($voteUserInfo['verified'])
                        {
                            $s_verify_user_against += $verify_user_power_factor;
                        }else if ($questions_info[$val['question_id']]['uid'] == $vote_against_users[$val['id']]['uid'] AND !$s_publisher_agree)
                        {
                            $s_publisher_against += $publish_user_power_factor;
                        }else{
                            $s_against_value += $power_against_factor;
                        }
                    }
                }

                $answer_agree_reputation = intval($s_agree_value + $s_publisher_agree  + $s_best_answer + $s_verify_user_agree);
                $answer_against_reputation = intval($s_against_value + $s_publisher_against + $s_verify_user_against);
            }

            if ($answer_agree_reputation < 0)
            {
                $answer_agree_reputation = (0 - $answer_agree_reputation) - 0.5;
                if ($answer_agree_reputation >0 && $power_agree_factor > 1)
                {
                    $answer_agree_reputation = (0 - log($answer_agree_reputation, $power_agree_factor));
                }
            }
            if ($answer_agree_reputation > 0)
            {
                $answer_agree_reputation = $answer_agree_reputation + 0.5;

                if ($answer_agree_reputation >0 && $power_agree_factor > 1)
                {
                    $answer_agree_reputation = log($answer_agree_reputation, $power_agree_factor);
                }
            }

            if ($answer_against_reputation < 0)
            {
                $answer_against_reputation = (0 - $answer_against_reputation) - 0.5;
                if ($power_against_factor > 1)
                {
                    $answer_against_reputation = (0 - log($answer_against_reputation, $power_against_factor));
                }
            }
            if ($answer_against_reputation > 0)
            {
                $answer_against_reputation = $answer_against_reputation + 0.5;

                if ($power_against_factor > 1)
                {
                    $answer_against_reputation = log($answer_against_reputation, $power_against_factor);
                }
            }

            $user_reputation += $answer_against_reputation + $answer_agree_reputation;
        }

        //计算用户总威望
        if ($user_reputation < 0)
        {
            $user_reputation = (0 - $user_reputation) - 0.5;
            if ($user_reputation > 1)
            {
                $user_reputation = (0 - log($user_reputation, $reputation_log_factor));
            }
        }

        if ($user_reputation > 0)
        {
            $user_reputation = $user_reputation + 0.5;
            if ($user_reputation > 1)
            {
                $user_reputation = log($user_reputation, $reputation_log_factor);
            }
        }

        Users::updateUserFiled($uid,array(
            'reputation' => round($user_reputation),
            'reputation_update_time' => time(),
        ));

        return Users::updateUsersReputationGroup($uid);

        //最终威望计算： $user_power = $user_article_power + $user_answer_power;
        //$user_power > 0 时 $user_power = (0 - $user_power) - 0.5; 对数底数 $log_factor ,  $user_power = (0 - log($user_power, $log_factor));
        //$user_power < 0 时 $user_power =$user_power + 0.5; 对数底数 $log_factor , $user_power = log($user_power, $log_factor);
    }

    /**
     * 计算用户威望
     */
    public static function calculate($start = 0): bool
    {
        $users_list = db('users')->where([
            ['reputation_update_time','<',(time() - (get_setting('reputation_calc_time',3)*86400))]
        ])->order('uid','ASC')->limit($start,intval(get_setting('reputation_calc_limit')))->select()->toArray();

        if ($users_list)
        {
            foreach ($users_list as $val)
            {
                self::calcUserReputationByUid($val['uid']);
            }
            return true;
        }
        return false;
    }
}