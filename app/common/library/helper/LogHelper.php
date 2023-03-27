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
use app\logic\common\FocusLogic;
use app\model\Answer;
use app\model\Article;
use app\model\Question;
use app\model\Topic;
use app\model\Users;
use app\model\Vote;
use think\helper\Str;

/**
 * 日志记录
 */
class LogHelper
{
    public static $error;
    /**
     * 添加积分记录
     * @param $action
     * @param $record_id
     * @param string $record_db
     * @param int $uid
     * @param int $integral
     * @param int $create_time
     * @return bool
     */
    public static function addIntegralLog($action, $record_id, string $record_db='', int $uid=0, int $integral=0,int $create_time=0): bool
    {
        $uid = $uid ? : session('login_uid');
        //参数检查
        if (empty($action) || empty($record_id) || empty($uid)) {
            self::setError('参数不正确');
            return false;
        }
        //查询行为,判断是否执行
        $rule_info = db('integral_rule')->where(['name'=>$action,'status'=>1])->find();

        if (!$rule_info) {
            self::setError('积分规则不存在');
            return false;
        }

        if($integral!=0)
        {
            $rule_info['integral'] = $integral;
        }
        $user_score = db('users')->where(['uid'=>$uid])->value('integral');

        if($rule_info['integral']<0 && $user_score<abs($rule_info['integral']))
        {
            self::setError('当前积分不足');
            return false;
        }

        $balance = db('integral_log')->where('uid',$uid)->sum('integral');

        $balance = $balance + $rule_info['integral'];
        //插入行为日志
        $data['action_type']   = $action;
        $data['uid']     = $uid;
        $data['record_id']   = $record_id;
        $data['record_db']   = $record_db;
        $data['create_time'] = $create_time?:time();
        $data['integral'] = $rule_info['integral'];
        $data['balance'] = $balance;
        //解析日志规则,生成日志备注
        if (!empty($rule_info['log']))
        {
            if (preg_match_all('/\[(\S+?)\]/', $rule_info['log'], $match)) {
                $log['user']   = $uid;
                $log['record'] = $record_id;
                $log['time']   = formatTime($data['create_time']);
                $replace = [];
                foreach ($match[1] as $value) {
                    $param = explode('|', $value);
                    if (isset($param[1])) {
                        $replace[] = call_user_func($param[1], $log[$param[0]]);
                    } else {
                        $replace[] = $log[$param[0]];
                    }
                }
                $data['remark'] = str_replace($match[0], $replace, $rule_info['log']);
            } else {
                $data['remark'] = $rule_info['log'];
            }
        } else {
            //未定义日志规则，记录操作url
            $data['remark'] = '';
        }

        if($rule_info['cycle'])
        {
            $cycle_time = 0 ;
            switch($rule_info['cycle_type'])
            {
                case 'month':
                    $cycle_time = $rule_info['cycle']*365*24*60*60;
                    break;
                case 'week':
                    $cycle_time = $rule_info['cycle']*7*24*60*60;
                    break;
                case 'day':
                    $cycle_time = $rule_info['cycle']*24*60*60;
                    break;
                case 'hour':
                    $cycle_time = $rule_info['cycle']*60*60;
                    break;
                case 'minute':
                    $cycle_time = $rule_info['cycle']*60;
                    break;
                case 'second':
                    $cycle_time = $rule_info['cycle'];
                    break;
            }

            $map[] = ['uid','=',$uid];
            $map[] = ['action_type','=',$action];
            if(in_array($rule_info['cycle_type'],['month','week','day']))
            {
                $map[] = ['create_time','>', strtotime(date('Y-m-d',time()).' 23:59:59') - (int)$cycle_time];
            }else{
                $map[] = ['create_time','>', time() - (int)$cycle_time];
            }
            $exec_count = db('integral_log')->where($map)->count();
            if ($rule_info['max']!=0 and $exec_count >= $rule_info['max']) {
                return true;
            }
        }

        db('integral_log')->insert($data);

        $res = Users::updateUserFiled($uid,['integral'=>$balance]);
        //更新积分组
        Users::updateUsersIntegralGroup($uid);
        if (!$res) {
            self::setError('更新用户积分记录失败');
            return false;
        }
        return true;
    }

    /**
     * 检查用户积分是否足够操作
     * @param $action
     * @param int $uid
     * @param int $integral
     * @return bool
     */
    public static function checkUserIntegral($action,int $uid=0,int $integral=0): bool
    {
        $rule_info = db('integral_rule')->where(['name'=>$action,'status'=>1])->find();
        if($integral!=0)
        {
            $rule_info['integral'] = $integral;
        }
        if ($rule_info) {
            $user_score = db('users')->where(['uid'=>$uid])->value('integral');
            if($rule_info['integral']<0 && $user_score<abs($rule_info['integral']))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * 获取用户的动态概况
     * @param $action
     * @param $uid
     * @param $current_uid
     * @param int $page
     * @param int $per_page
     * @param string $pjax_page
     * @return array
     */
    public static function getUserActionLogList($action,$uid,$current_uid,int $page=1,int $per_page=10,string $pjax_page='wrapMain'): array
    {
        $action = is_array($action) ? $action : explode(',', $action);
        $action_ids = [];
        if ($action) {
            $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
        }
        $where[] = ['status', '=', 1];
        $where[] = ['uid', '=', $uid];

        if (!empty($action_ids)) {
            $where[] = ['action_id', 'IN', $action_ids];
        }
        if ($current_uid!=$uid) {
            $where[] = ['anonymous', '=', 0];
        }
        $param =request()->param();
        $action_log_list = db('action_log')->where($where)->order('create_time','DESC')->paginate(
            [
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>$param,
                'pjax'=>$pjax_page
            ]
        );
        $pageVar = $action_log_list->render();
        $action_log_list = $action_log_list->toArray();
        $data = $action_log_list['data'];
        $result_list = self::parseActionLog($current_uid,$data,$uid)?:[];
        $action_log_list['data'] = $result_list?:[];
        return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
    }

    /**
     * 解析行为
     * @param int $current_uid 被解析的用户
     * @param $data
     * @param int $uid 当前用户
     * @return array|false
     */
    public static function parseActionLog($current_uid,$data,$uid=0)
    {
        if(!$data) return false;
        $question_ids = $article_ids = $answer_ids = $data_list_uid = $topic_infos = $last_answers =  [];

        foreach ($data as $val)
        {
            if($val['record_type']=='question' || $val['relation_type']=='question')
            {
                if($val['record_type']=='question' && $val['record_id'])
                {
                    $question_ids[] = $val['record_id'];
                }
                if($val['relation_type']=='question' && $val['relation_id'])
                {
                    $question_ids[] = $val['relation_id'];
                }
            }

            if($val['record_type']=='article' || $val['relation_type']=='article')
            {
                if($val['record_type']=='article' && $val['record_id'])
                {
                    $article_ids[] = $val['record_id'];
                }
                if($val['relation_type']=='article' && $val['relation_id'])
                {
                    $article_ids[] = $val['relation_id'];
                }
            }

            if($val['record_type']=='answer' || $val['relation_type']=='answer')
            {
                if($val['record_type']=='answer' && $val['record_id'])
                {
                    $answer_ids[] = $val['record_id'];
                }
                if($val['relation_type']=='answer' && $val['relation_id'])
                {
                    $answer_ids[] = $val['relation_id'];
                }
            }

            $data_list_uid[$val['uid']] = $val['uid'];
        }

        $question_infos = $article_infos = $answer_infos = array();

        if (array_unique($question_ids))
        {
            if ($last_answers = Answer::getHotAnswerByIds($question_ids))
            {
                foreach ($last_answers as $val)
                {
                    $data_list_uid[$val['uid']] = $val['uid'];
                }
            }
            $topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
            $question_infos = Question::getQuestionByIds($question_ids);
        }

        if (array_unique($article_ids))
        {
            $topic_infos['article'] = Topic::getTopicByItemIds($article_ids, 'article');
            $article_infos = Article::getArticleByIds($article_ids);
        }

        if(array_unique($answer_ids))
        {
            $answer_infos = Answer::getAnswerInfoByIds($answer_ids);
        }

        $users_info = Users::getUserInfoByIds($data_list_uid,'uid,nick_name,avatar',99);

        $result_list = array();

        foreach ($data as $key => $val)
        {
            //查询行为,判断是否存在
            $action_info = db('action')->where('id',$val['action_id'])->find();
            if (!$action_info || $action_info['status'] != 1) {
                continue;
            }

            //解析日志规则
            $val['remark'] = '';
            if (!empty($action_info['log_rule']))
            {
                if (preg_match_all('/\[(\S+?)\]/', $action_info['log_rule'], $match)) {
                    $log['user']   = get_link_username($val['uid']);
                    $log['record'] = $val['record_type'];
                    $log['time']   = date_friendly($val['create_time']);

                    if($val['record_type']=='column')
                    {
                        $column_name = db('column')->where(['id'=>intval($val['record_id'])])->value('name');
                        $log['name']   = '<a href="'.(string)url('column/detail',['id'=>intval($val['record_id'])]).'" target="_blank"><em class="tag mr-0">'.$column_name.'</em></a>' ? : '<a href="javascript:;"><em class="tag mr-0">未知专栏</em></a>';
                    }else{
                        $log['name'] = '';
                    }

                    $replace = [];
                    foreach ($match[1] as $value) {
                        $param = explode('|', $value);
                        if (isset($param[1])) {
                            $replace[] = call_user_func($param[1], $log[$param[0]]);
                        } else {
                            $replace[] = $log[$param[0]];
                        }
                    }
                    $val['remark'] = str_replace($match[0], $replace, $action_info['log_rule']);
                } else {
                    $val['remark'] = $action_info['log_rule'];
                }
            } else {
                //未定义日志规则，记录操作url
                $val['remark'] = $val['data']['remark']??'';
            }

            //回答操作记录
            if($val['record_type']=='answer' && $val['relation_type']=='question')
            {
                if($answer_infos && isset($answer_infos[$val['record_id']]) && $answer_infos[$val['record_id']] && isset($question_infos[$answer_infos[$val['record_id']]['question_id']]))
                {
                    $question_id = $answer_infos[$val['record_id']]['question_id'];
                    $result_list[$key] = $question_infos[$question_id];

                    //是否已回答
                    $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$question_id,'status'=>1])->value('id') : 0;

                    //回答用户
                    $answerUidLists = db('answer')->where(['question_id'=>$question_id,'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
                    $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];

                    $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($current_uid,'question',$question_id);

                    $result_list[$key]['answer_info'] = $answer_infos[$val['record_id']];
                    $result_list[$key]['answer_info']['user_info'] = $users_info[$val['uid']];
                    $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($val['record_id'],'answer',$uid);
                    $result_list[$key]['answer_info']['content'] = str_cut(strip_tags(htmlspecialchars_decode($answer_infos[$val['record_id']]['content'])),0,150);
                    $result_list[$key]['answer_info']['img_list'] = ImageHelper::srcList($result_list[$key]['answer_info']['content']);

                    $result_list[$key]['vote_value'] = Vote::getVoteByType($question_id,'question',$current_uid);

                    $result_list[$key]['img_list'] =  ImageHelper::srcList($result_list[$key]['detail']);

                    $result_list[$key]['detail'] = str_cut(strip_tags($result_list[$key]['detail']),0,150);

                    $result_list[$key]['topics'] = $topic_infos['question'][$question_id] ?? [];
                    if(!isset($users_info[$result_list[$key]['uid']]) && $result_list[$key]['uid'])
                    {
                        $user_info = Users::getUserInfo($result_list[$key]['uid']);
                    }else{
                        $user_info = $users_info[$result_list[$key]['uid']];
                    }
                    $result_list[$key]['user_info'] = $user_info;

                    $result_list[$key]['item_type'] = 'answer';

                    $result_list[$key]['relation_type'] = $val['relation_type'];
                    if($result_list[$key]['is_anonymous'] && $current_uid!=$val['uid'])
                    {
                        $result_list[$key]['remark'] = str_replace(get_link_username($val['uid']),'<a href="javascript:;">匿名用户</a>',$val['remark']);
                    }else{
                        $result_list[$key]['remark'] = $val['remark'];
                    }
                }
            }

            //问题类型操作记录
            elseif(($val['record_type']=='question' || $val['relation_type']=='question') && $val['record_type']!='answer')
            {
                if($question_infos && (isset($question_infos[$val['record_id']]) || isset($question_infos[$val['relation_id']])))
                {
                    $question_id = $val['record_id']??($val['relation_id']??0);
                    $result_list[$key] = $question_infos[$question_id];
                    $result_list[$key]['answer_info'] = $last_answers[$question_id] ?? false;

                    if($result_list[$key]['answer_info']){
                        $result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$question_id]['uid']]??['url'=>'javascript:;','uid'=>0,'name'=>'未知用户','avatar'=>'/static/common/image/default-avatar.svg'];
                        $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($last_answers[$question_id]['id'],'answer',$uid);
                        $result_list[$key]['answer_info']['content'] = str_cut(strip_tags($result_list[$key]['answer_info']['content']),0,150);
                        $result_list[$key]['answer_info']['img_list'] = ImageHelper::srcList($result_list[$key]['answer_info']['content']);
                    }

                    $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($current_uid,'question',$question_id);
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($question_id,'question',$current_uid);
                    $result_list[$key]['img_list'] = ImageHelper::srcList($result_list[$key]['detail']);

                    $result_list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);
                    $result_list[$key]['topics'] = $topic_infos['question'][$question_id] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$val['uid']]??[];
                    $result_list[$key]['item_type'] = $val['record_type'];
                    $result_list[$key]['relation_type'] = $val['relation_type'];

                    //是否已回答
                    $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$question_id,'status'=>1])->value('id') : 0;

                    //回答用户
                    $answerUidLists = db('answer')->where(['question_id'=>$question_id,'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
                    $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];

                    if($result_list[$key]['is_anonymous'] && $current_uid!=$val['uid'])
                    {
                        $result_list[$key]['remark'] = str_replace(get_link_username($val['uid']),'<a href="javascript:;">匿名用户</a>',$val['remark']);
                    }else{
                        $result_list[$key]['remark'] = $val['remark'];
                    }
                }
            }
            //文章类型操作记录
            elseif($val['record_type']=='article' || $val['relation_type']=='article')
            {
                if($article_infos && (isset($article_infos[$val['record_id']]) || isset($article_infos[$val['relation_id']])))
                {
                    $result_list[$key] = $article_infos[$val['record_id']] ?? $article_infos[$val['relation_id']];
                    $result_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['message'])),0,100);
                    $result_list[$key]['topics'] = $topic_infos['article'][$val['record_id']] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$val ['uid']];
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($val['record_id'],'article',$current_uid);
                    $result_list[$key]['item_type'] = $val['record_type'];
                    $result_list[$key]['relation_type'] = $val['relation_type'];
                    $result_list[$key]['remark'] = $val['remark'];
                }
            }
            else{
                $result_list[$key] = $val;
                $result_list[$key]['item_type'] = $val['record_type'];
                $result_list[$key]['relation_type'] = $val['relation_type'];
            }

            /**
             * 解析操作记录
             */
            hook('parseActionLog'.Str::title($val['record_type']),$val);
        }

        return $result_list;
    }

    /**
     * 获取操作记录
     * @param $action
     * @param int $uid
     * @param int $current_uid
     * @param int $page
     * @param int $per_page
     * @param string $pjax_page
     * @return array
     */
    public static function getActionLogList($action,int $uid=0,int $current_uid=0,int $page=1,int $per_page=10,string $pjax_page='wrapMain'): array
    {
        $where = self::getWhere($action, $uid, $current_uid);
        $param =request()->param();
        $action_log_list = db('action_log')->where($where)->order('create_time','DESC')->paginate(
            [
                'list_rows'=> $per_page,
                'page' => $page,
                'query'=>$param,
                'pjax'=>$pjax_page
            ]
        );
        $pageVar = $action_log_list->render();
        $question_ids = $topic_ids = $topic_log_list = $article_ids = $answer_ids = $column_ids = $article_comment_ids = $answer_comment_ids = $data_list_uid = $topic_infos = [];
        $action_log_list = $action_log_list->toArray();
        $data = $action_log_list['data'];

        foreach ($data as $key => $val)
        {
            switch ($val['record_type'])
            {
                case 'question':
                    $question_ids[] = $val['record_id'];
                    break;
                case 'article':
                    $article_ids[] = $val['record_id'];
                    break;
                case 'answer':
                    $answer_ids[] = $val['record_id'];
                    break;
                case 'column':
                    $column_ids[] = $val['record_id'];
                    break;
                case 'article_comment':
                    $article_comment_ids[] = $val['record_id'];
                    break;
                case 'answer_comment':
                    $answer_comment_ids[] = $val['record_id'];
                    break;
                case 'topic':
                    $topic_ids[] = $val['record_id'];
                    break;
            }
            $data_list_uid[$val['uid']] = $val['uid'];
        }

        $question_infos = $article_infos = $answer_infos = array();

        if (array_unique($question_ids))
        {
            if ($last_answers = Answer::getLastAnswerByIds($question_ids))
            {
                foreach ($last_answers as $key => $val)
                {
                    $data_list_uid[$val['uid']] = $val['uid'];
                }
            }
            $topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
            $question_infos = Question::getQuestionByIds($question_ids);
        }

        if (array_unique($article_ids))
        {
            $topic_infos['article'] = Topic::getTopicByItemIds($article_ids, 'article');
            $article_infos = Article::getArticleByIds($article_ids);
        }

        if(array_unique($answer_ids))
        {
            $answer_infos = Answer::getAnswerInfoByIds($answer_ids);
        }

        $users_info = Users::getUserInfoByIds($data_list_uid,'uid,nick_name,avatar');

        $result_list = array();

        foreach ($data as $key => $val)
        {
            if($val['anonymous'] && $val['uid']!=$current_uid)
            {
                unset($data[$key]);
                continue;
            }
            //查询行为,判断是否执行
            $action_info = db('action')->where('id',$val['action_id'])->find();

            if (!$action_info || $action_info['status'] != 1) {
                continue;
            }

            //解析日志规则,生成日志备注
            if (!empty($action_info['log_rule']))
            {
                if (preg_match_all('/\[(\S+?)\]/', $action_info['log_rule'], $match)) {
                    $log['[user]']   = get_link_username($val['uid']);
                    $log['[record]'] = $val['record_type'];
                    $log['[model]']  = $val['record_type'];
                    $log['[time]']   = date_friendly($val['create_time']);
                    $replaces = array();
                    foreach ($match[0] as $k=> $v)
                    {
                        $replaces[] = $log[$v];
                    }
                    $val['remark'] = str_replace($match[0], $replaces, $action_info['log_rule']);
                } else {
                    $val['remark'] = $action_info['log_rule'];
                }
            } else {
                //未定义日志规则，记录操作url
                $val['remark'] = '操作url：' . $_SERVER['REQUEST_URI'];
            }

            if($val['record_type']=='question')
            {
                if($question_infos && isset($question_infos[$val['record_id']]))
                {
                    $result_list[$key] = $question_infos[$val['record_id']];
                    $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($current_uid,'question',$val['record_id']);
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($val['record_id'],'question',$current_uid);
                    $result_list[$key]['detail'] = str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);
                    $result_list[$key]['topics'] = $topic_infos['question'][$val['record_id']] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$val['uid']];
                    $result_list[$key]['item_type'] = $val['record_type'];
                    $result_list[$key]['remark'] = $val['remark'];
                }
            }

            if($val['record_type']=='article')
            {
                if($article_infos && isset($article_infos[$val['record_id']]))
                {
                    $result_list[$key] = $article_infos[$val['record_id']];
                    $result_list[$key]['message'] = str_cut(strip_tags($result_list[$key]['message']),0,100);
                    $result_list[$key]['topics'] = $topic_infos['article'][$val['record_id']] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$val ['uid']];
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($val['record_id'],'article',$current_uid);
                    $result_list[$key]['item_type'] = $val['record_type'];
                    $result_list[$key]['remark'] = $val['remark'];
                }
            }

            if($val['record_type']=='answer')
            {
                if($answer_infos && isset($answer_infos[$val['record_id']]))
                {
                    $question_id = $answer_infos[$val['record_id']]['question_id'];
                    $result_list[$key] = Question::getQuestionInfo($question_id);
                    $result_list[$key]['answer_info'] = $answer_infos[$val['record_id']];
                    $result_list[$key]['vote_value'] = Vote::getVoteByType($val['record_id'],'answer',$current_uid);
                    $result_list[$key]['detail'] = '<a href="'.$users_info[$val['uid']]['url'].'" class="aw-username" >'.$users_info[$val['uid']]['nick_name'].'</a> :'.str_cut(strip_tags(htmlspecialchars_decode($answer_infos[$val['record_id']]['content'])),0,150);
                    $result_list[$key]['topics'] = $topic_infos['question'][$question_id] ?? [];
                    $result_list[$key]['user_info'] = $users_info[$val['uid']];
                    $result_list[$key]['item_type'] = $val['record_type'];
                    $result_list[$key]['remark'] = $val['remark'];
                }
            }

            /**
             * 解析操作记录
             */
            hook('parseActionLog'.Str::title($val['record_type']),$val);
        }
        $action_log_list['data'] = $result_list;
        return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
    }

    /**
     * 获取用户关注内容动态
     * 关注的人，关注的问题，关注的话题，关注的专栏，关注的收藏夹
     */
    public static function getUserFocusLogList($uid,$focus_type='all',$page=1,$per_page=10,string $pjax_page='wrapMain')
    {
        if(!in_array($focus_type,['all','user','column','topic','question']))
        {
            return false;
        }

        //关注聚合
        if($focus_type=='all')
        {
            //关注的用户
            $friend_uid = db('users_follow')->where(['status'=>1,'fans_uid'=>intval($uid)])->column('friend_uid');

            //关注的话题
            $topic_ids = db('topic_focus')->where(['uid'=>intval($uid)])->column('topic_id');

            //关注的问题
            $question_ids = db('question_focus')->where(['uid'=>intval($uid)])->column('question_id');

            //关注的专栏
            $column_ids = db('column_focus')->where(['uid'=>intval($uid)])->column('column_id');

            $sqlWhere = [];

            if($friend_uid)
            {
                $action = [
                    'publish_question',
                    'publish_article',
                    'publish_answer',
                    'agree_question',
                    'agree_article',
                    'agree_answer',
                    /*'focus_question',*/
                ];
                $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                $sqlWhere[] = '(uid IN('.implode(',',$friend_uid).') AND action_id IN('.implode(',',$action_ids).') AND anonymous=0)';
            }

            if($question_ids)
            {
                $sql[] = '(action_id=6 AND record_id IN('.implode(',',$question_ids).') AND record_type="question")';
                $sql[] = '(relation_id IN('.implode(',',$question_ids).') AND record_type="answer" AND action_id IN(4,8))';
                $sqlWhere[] = '('.implode(' OR ',$sql).')';
            }

            if($column_ids)
            {
                $action = [
                    'create_column_article',
                    'modify_column_article',
                ];
                $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                $sqlWhere[] = 'record_id IN('.implode(',',$column_ids).') AND record_type="column" AND action_id IN('.implode(',',$action_ids).')';
            }

            if($topic_ids)
            {
                $action = [
                    'modify_question_topic',
                    'modify_article_topic',
                ];
                $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                $sqlWhere[] = 'record_type="topic" AND record_id IN('.implode(',',$topic_ids).') AND action_id IN('.implode(',',$action_ids).')';
            }

            if(!$sqlWhere)
            {
                return ['list'=>[],'page'=>'','total'=>0];
            }

            $result =db('action_log')
                ->whereRaw(implode(' OR ',$sqlWhere))
                ->order('create_time','DESC')
                ->paginate(
                    [
                        'list_rows'=> $per_page,
                        'page' => $page,
                        'pjax'=>$pjax_page
                    ]
                );

            $data = $result->toArray();
            $result_list = self::parseActionLog($uid,$data['data']) ?: [];
            return ['list'=>$result_list,'page'=>$result->render(),'total'=>$data['last_page']];
        }

        //关注的用户
        if($focus_type=='user')
        {
            $friend_uid = db('users_follow')->where(['status'=>1,'fans_uid'=>intval($uid)])->column('friend_uid');
            if($friend_uid)
            {
                $action = [
                    'publish_question',
                    'publish_article',
                    'publish_answer',
                    'agree_question',
                    'agree_article',
                    'agree_answer',
                    /*'focus_question',*/
                ];
                $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                $where[] = ['status', '=', 1];
                $where[] = ['uid', 'IN', $friend_uid];
                if (!empty($action_ids)) {
                    $where[] = ['action_id', 'IN', $action_ids];
                }
                $where[] = ['anonymous', '=', 0];
                $action_log_list = db('action_log')
                    ->where($where)
                    ->paginate(
                        [
                            'list_rows'=> $per_page,
                            'page' => $page,
                            'pjax'=>$pjax_page
                        ]
                    );

                $pageVar = $action_log_list->render();
                $action_log_list = $action_log_list->toArray();
                $data = $action_log_list['data'];
                $result_list = self::parseActionLog($uid,$data);
                $action_log_list['data'] = $result_list?:[];

                return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
            }
        }

        //关注的话题
        if($focus_type=='topic')
        {
            $topic_ids = db('topic_focus')->where(['uid'=>intval($uid)])->column('topic_id');
            if($topic_ids)
            {
                $action = [
                    'modify_question_topic',
                    'modify_article_topic',
                ];
                $action_ids = [];
                if ($action) {
                    $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                }
                if (!empty($action_ids)) {
                    $where3[] = ['action_id', 'IN', $action_ids];
                }

                $where3[] = ['status', '=', 1];
                $where3[] = ['record_id', 'IN', $topic_ids];
                $where3[] = ['record_type', '=', 'topic'];
                $action_log_list = db('action_log')
                    ->where($where3)
                    ->paginate(
                        [
                            'list_rows'=> $per_page,
                            'page' => $page,
                            'pjax'=>$pjax_page
                        ]
                    );
                $pageVar = $action_log_list->render();
                $action_log_list = $action_log_list->toArray();
                $data = $action_log_list['data'];
                $result_list = self::parseActionLog($uid,$data);
                $action_log_list['data'] = $result_list?:[];
                return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
            }
        }

        //关注的问题
        if($focus_type=='question')
        {
            $question_ids = db('question_focus')->where(['uid'=>intval($uid)])->column('question_id');
            if($question_ids)
            {
                $action = [
                    'publish_answer',
                    'agree_question',
                    'agree_answer',
                    'publish_answer'
                ];
                $action_ids = [];
                if ($action) {
                    $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                }
                if (!empty($action_ids)) {
                    $where1[] = ['action_id', 'IN', $action_ids];
                }

                $where1[] = ['status', '=', 1];
                $where1[] = ['record_id', 'IN', $question_ids];
                $where1[] = ['record_type', 'IN', ['question','answer']];
                $action_log_list = db('action_log')
                    ->where($where1)
                    ->paginate(
                        [
                            'list_rows'=> $per_page,
                            'page' => $page,
                            'pjax'=>$pjax_page
                        ]
                    );
                $pageVar = $action_log_list->render();
                $action_log_list = $action_log_list->toArray();
                $data = $action_log_list['data'];
                $result_list = self::parseActionLog($uid,$data);
                $action_log_list['data'] = $result_list?:[];
                return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
            }
        }

        //关注的专栏
        if($focus_type=='column')
        {
            $column_ids = db('column_focus')->where(['uid'=>intval($uid)])->column('column_id');
            if($column_ids)
            {
                $article_ids = db('article')->whereIN('column_id',$column_ids)->where(['status'=>1])->column('id');
                $action = [
                    'create_column_article',
                    'modify_column_article',
                ];
                $action_ids = [];
                if ($action) {
                    $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
                }
                if (!empty($action_ids)) {
                    $where2[] = ['action_id', 'IN', $action_ids];
                }

                $where2[] = ['status', '=', 1];
                $where2[] = ['relation_id', 'IN', $article_ids];
                $where2[] = ['relation_type', '=', 'article'];
                $action_log_list = db('action_log')
                    ->where($where2)
                    ->paginate(
                        [
                            'list_rows'=> $per_page,
                            'page' => $page,
                            'pjax'=>$pjax_page
                        ]
                    );
                $pageVar = $action_log_list->render();
                $action_log_list = $action_log_list->toArray();
                $data = $action_log_list['data'];
                $result_list = self::parseActionLog($uid,$data);
                $action_log_list['data'] = $result_list?:[];
                return ['list'=>$result_list,'page'=>$pageVar,'total'=>$action_log_list['last_page']];
            }

        }
    }

    /**
     * 获取操作记录数量
     * @param $action
     * @param $uid
     * @param $current_uid
     * @return mixed
     */
    public static function getActionLogCount($action,$uid,$current_uid)
    {
        $where = self::getWhere($action, $uid, $current_uid);
        return db('action_log')->where($where)->count();
    }

    /**
     * 记录行为日志
     * @param null $action 行为标识
     * @param null $record_type 触发行为的模型名
     * @param null $record_id 触发行为的记录id
     * @param int $uid 执行行为的用户id
     * @param int $anonymous 是否匿名
     * @param int $create_time 操作时间
     * @param mixed $relation_type 关联数据类型
     * @param int $relation_id 关联数据id
     * @param array $extends 附加信息
     * @return boolean
     */
    public static function addActionLog($action = null, $record_type = null, $record_id = null, int $uid = 0,int $anonymous=0, int $create_time=0,string $relation_type = null, int $relation_id = 0,array $extends=[]): bool
    {
        //参数检查
        if (empty($action) || empty($record_type) || !intval($record_id)) {
            return false;
        }

        //查询行为,判断是否存在
        $action_info = db('action')->where('name',$action)->field('status,id')->find();

        if (!$action_info || $action_info['status'] != 1) {
            return false;
        }

        //相同的操作进行删除重新插入
        db('action_log')
            ->where(['uid'=>intval($uid),'record_id'=>$record_id,'record_type'=>$record_type,'action_id'=>$action_info['id']])
            ->order('create_time','ASC')
            ->delete();

        $data['action_id']   = $action_info['id'];
        $data['uid']     = $uid;
        $data['action_ip']   = IpHelper::getRealIp();
        $data['record_type']  = $record_type;
        $data['record_id']   = $record_id;
        $data['anonymous']   = $anonymous;
        $data['relation_type']   = $relation_type ?: '';
        $data['relation_id']   = $relation_id ?: 0;
        $data['create_time'] = $create_time ?:time();

        //存储最新数据
        $log_id =  db('action_log')->insertGetId($data);

        //存储全部数据
        $data['data'] = wc_serialize($extends);
        db('action_log_all')->insert($data);

        if($extends)
        {
            db('action_log_data')->insert([
                'log_id'=>$log_id,
                'data'=>wc_serialize($extends),
                'status'=>1
            ]);
        }
        return $log_id;
    }

    /**
     * 删除行为记录
     * @param null $record_type
     * @param null $record_id
     * @param null $action
     * @param false $realRemove
     * @return bool
     */
    public static function removeActionLog($record_type = null, $record_id = null,$action=null,bool $realRemove=false): bool
    {
        $where = ['record_id'=>intval($record_id),'record_type'=>$record_type];
        if($action)
        {
            $action_type = db('action')->where(['status'=>1])->column('name');
            if(in_array($action,$action_type))
            {
                $action_id = db('action')->where(['name'=>$action,'status'=>1])->value('id');
                $where['action_id'] =intval($action_id);
            }
        }
        if($realRemove)
        {
            db('action_log')->where($where)->delete();
        }else{
            db('action_log')->where($where)->update(['status'=>0]);
        }
        return true;
    }

    /**
     * 恢复行为记录
     * @param null $record_type
     * @param null $record_id
     * @return bool
     */
    public static function recordActionLog($record_type = null, $record_id = null): bool
    {
        db('action_log')->where(['record_id'=>intval($record_id),'record_type'=>$record_type])->update(['status'=>1]);
        return true;
    }

    /**
     * @param $action
     * @param $uid
     * @param $current_uid
     * @return array
     */
    protected static function getWhere($action, $uid, $current_uid): array
    {
        $action = is_array($action) ? $action : explode(',', $action);
        $uid = is_array($uid) ? $uid : explode(',', $uid);
        $action_ids = [];
        if ($action) {
            $action_ids = db('action')->whereIn('name', $action)->where(['status' => 1])->column('id');
        }
        $where[] = ['status', '=', 1];
        $where[] = ['uid', 'IN', $uid];
        if (!empty($action_ids)) {
            $where[] = ['action_id', 'IN', $action_ids];
        }

        if (!in_array($current_uid, $uid)) {
            $where[] = ['anonymous', '=', 0];
        }
        return $where;
    }

    /**
     * 解析修改记录数据
     * @param string $item_type
     * @param int $item_id
     * @return array|void
     */
    public static function parseModifyLog(string $item_type='', int $item_id=0)
    {
        if(!$item_type && !$item_id) return [];
        $where[] = 'status=1';
        $action_id = db('action')->where('name','modify_log')->value('id');
        $where[] = '`action_id` ='.$action_id;
        if(!$action_id) return [];
        $where[] = '`action_id` ='.$action_id;
        if($item_type)
        {
            $where[] = '`record_type`="'.$item_type.'"';
        }
        if($item_id)
        {
            $where[] = '`record_id`='.$item_id;
        }
        $list = db('action_log_all')->whereRaw(implode(' AND ',$where))->select()->toArray();
        if($list)
        {
            foreach($list as $k=>$v)
            {
                $list[$k]['data'] = wc_unserialize($v['data']);
            }
            return $list;
        }
        return [];
    }

    /**
     * 解析行为记录
     * @param $actions
     * @param string $item_type
     * @param int $item_id
     * @return array
     */
    public static function parseActionLogList($actions,string $item_type='', int $item_id=0): array
    {
        if(!$actions && !$item_type && !$item_id) return [];
        $where[] = 'status=1';
        $action_ids = db('action')->whereIN('name',$actions)->column('id');
        if($action_ids){
            $where[] = '`action_id` IN('.implode(',',$action_ids).')';
        }
        if($item_type)
        {
            $where[] = '(`record_type`="'.$item_type.'" OR `relation_type`="'.$item_type.'")';
        }
        if($item_id)
        {
            $where[] = '(`record_id`='.$item_id.' OR `relation_id`='.$item_id.')';
        }

        $list = db('action_log_all')->order('id','DESC')->whereRaw(implode(' AND ',$where))->select()->toArray();

        $result = [];
        if($list)
        {
            foreach($list as $k=>$v)
            {
                $data = wc_unserialize($v['data']);
                //发布问题
                /*if($v['action_id']==2)
                {
                    $result[$k]['label'] = get_link_username($v['uid']).' '.L('添加了该问题');
                    $result[$k]['content'] = htmlspecialchars_decode($data['content']);
                    $result[$k]['create_time'] = date_friendly($v['create_time']);
                }*/

                //修改问题标题
                if($v['action_id']==12)
                {
                    $new = isset($data['content']) ? strip_tags(htmlspecialchars_decode($data['content'])) : '';
                    $old = isset($data['old_content']) ? strip_tags(htmlspecialchars_decode($data['old_content'])) : '';
                    $diff = TextDiffHelper::compare($new,$old);
                    $result[$k]['label'] = get_link_username($v['uid']).' '.L('修改了该问题标题');
                    $result[$k]['content'] = TextDiffHelper::toHTML($diff);
                    $result[$k]['create_time'] = date_friendly($v['create_time']);
                }

                //修改问题描述
                if($v['action_id']==14)
                {
                    $new = isset($data['content']) ? htmlspecialchars_decode($data['content']) : '';
                    $old = isset($data['old_content']) ? htmlspecialchars_decode($data['old_content']) : '';
                    $diff = TextDiffHelper::compare($new,$old);
                    $result[$k]['label'] = get_link_username($v['uid']).' '.L('修改了该问题描述');
                    $result[$k]['content'] = TextDiffHelper::toHTML($diff);
                    $result[$k]['create_time'] = date_friendly($v['create_time']);
                }

                //向问题添加话题
                if($v['action_id']==16)
                {
                    $topic_title = db('topic')->where(['id'=>$v['record_id']])->value('title');
                    $result[$k]['label'] = get_link_username($v['uid']).' '.L('向问题添加话题');
                    $result[$k]['content'] = '<a href="'.(string)url('topic/detail',['id'=>$v['record_id']]).'" class="aw-topic" data-id="'.$v['record_id'].'"><em class="tag">'.$topic_title.'</em></a>';
                    $result[$k]['create_time'] = date_friendly($v['create_time']);
                }
            }
        }
        return $result;
    }

    public static function parseActionRemark($action_id,$uid,$create_time,$record_type,$record_id)
    {
        //查询行为,判断是否存在
        $action_info = db('action')->where('id',$action_id)->find();
        if (!$action_info || $action_info['status'] != 1) {
            return false;
        }
        //解析日志规则
        $remark = '';
        if (!empty($action_info['log_rule']))
        {
            if (preg_match_all('/\[(\S+?)\]/', $action_info['log_rule'], $match)) {
                $log['user']   = get_link_username($uid['uid']);
                $log['time']   = date_friendly($create_time);

                if($record_type=='column')
                {
                    $column_name = db('column')->where(['id'=>intval($record_id)])->value('name');
                    $log['name']   = '<a href="'.(string)url('column/detail',['id'=>intval($record_id)]).'" target="_blank"><em class="tag mr-0">'.$column_name.'</em></a>' ? : '<a href="javascript:;"><em class="tag mr-0">未知专栏</em></a>';
                }else{
                    $log['name'] = '';
                }
                $replace = [];
                $rules  = $action_info['log_rule'];
                foreach ($rules as $key => $rule) {
                    $rule = explode('&', $rule);
                    foreach ($rule as $k => $fields) {
                        $field = empty($fields) ? array() : explode(':', $fields);
                        if (!empty($field)) {
                            $return[$key][$field[0]] = $field[1];
                        }
                    }
                }

                foreach ($return as $rule) {
                    //执行数据库操作
                    $field = $rule['field'];
                    $table_name = config('database.connections.mysql.prefix').$rule['table'];
                    $sql = "update ".$table_name.' set '.$field.' = '.$rule['rule'] . ' where '.$rule['condition'];
                    $res = Db::execute($sql);
                    if (!$res) {
                        $return = false;
                    }
                }

                foreach ($match[1] as $value) {
                    $param = explode('|', $value);
                    if (isset($param[1])) {
                        $replace[] = call_user_func($param[1], $log[$param[0]]);
                    } else {
                        $replace[] = $log[$param[0]];
                    }
                }
                $remark = str_replace($match[0], $replace, $action_info['log_rule']);
            } else {
                $remark = $action_info['log_rule'];
            }
        }
        return $remark;
    }

    /**
     * 设置错误信息
     * @param $error
     * @return mixed
     */
    public static function setError($error) {
        return self::$error = L($error);
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public static function getError() {
        return self::$error;
    }
}
