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

use app\model\PostRelation;
use app\model\Article;
use app\model\Question;

/**
 * 规则核心类
 * Class RuleHelper
 * @package app\common\library\helper
 */
class PopularHelper
{
    /**
     * 计算问题热度
     * @param $question_id
     * @return bool
     */
    public static function calcQuestionPopularValue($question_id): bool
    {
        if(!$question_info = Question::getQuestionInfo($question_id))
        {
            return false;
        }

        $qualityInitValue = get_setting('popular_quality_init_value'); //内容初始质量
        $gravity = get_setting('popular_gravity'); //内容变得不再热门的速度，重力越大，一个内容刷新的就越快
        $hotParamValue = $question_info['agree_count'] * intval(get_setting('popular_agree_ratio')) + $question_info['against_count'] * intval(get_setting('popular_against_ratio')) + $question_info['view_count'] * intval(get_setting('popular_view_ratio')) + $question_info['answer_count'] * intval(get_setting('popular_comment_ratio')); //内容质量（点赞，反对，浏览，评论）
        $publishTime = $question_info['create_time'];//内容发布的时间
        $hotValue = (($hotParamValue + $qualityInitValue)/pow((time()-$publishTime)+1,$gravity))*100000; //热度值

        if(db('question')->where('id',$question_id)->update(['popular_value'=>$hotValue,'popular_value_update'=>time()]))
        {
            PostRelation::updatePostRelation($question_id,'question',['popular_value'=>$hotValue]);
        }
        return true;
    }

    /**
     * 计算文章热度
     * @param $article_id
     * @return bool
     */
    public static function calcArticlePopularValue($article_id): bool
    {
        if(!$article_info = Article::getArticleInfo($article_id))
        {
            return false;
        }

        $qualityInitValue = get_setting('popular_quality_init_value'); //内容初始质量
        $gravity = get_setting('popular_gravity'); //内容变得不再热门的速度，重力越大，一个内容刷新的就越快
        $hotParamValue = $article_info['agree_count'] * intval(get_setting('popular_agree_ratio')) + $article_info['against_count'] * intval(get_setting('popular_against_ratio')) + $article_info['view_count'] * intval(get_setting('popular_view_ratio')) + $article_info['comment_count'] * intval(get_setting('popular_comment_ratio')); //内容质量（点赞，反对，浏览，评论）
        $hotValue = (($hotParamValue + $qualityInitValue)/pow((time()-$article_info['create_time'])+1,$gravity))*100000; //热度值

        if(db('article')->where('id',$article_id)->update(['popular_value'=>$hotValue,'popular_value_update'=>time()]))
        {
            PostRelation::updatePostRelation($article_id,'article',['popular_value'=>$hotValue]);
        }
        return true;
    }
}