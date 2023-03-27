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

use app\model\Notify;
use think\helper\Str;

/**
 * 后台管理员通知处理
 * Class AdminNotifyHelper
 * @package app\common\library\helper
 */
class NotifyHelper
{
    /**
     * 获取通知数量
     * @return int
     */
    public static function getNotifyCount(): int
    {
        $notifications = self::notifyCount();
        $count = 0;
        if (!empty($notifications)) {
            foreach ($notifications as $key => $val) {
                $count += intval($val);
            }
        }
        return $count;
    }

    //返回通知数量
    public static function notifyCount(): array
    {
        return [
            'question_approval_count' => db('approval')->where(['type' => 'question', 'status' => 0])->count(),
            'article_approval_count' => db('approval')->where(['type' => 'article', 'status' => 0])->count(),
            'answer_approval_count' => db('approval')->where(['type' => 'answer', 'status' => 0])->count(),
            'modify_question_approval_count' => db('approval')->where(['type' => 'modify_question', 'status' => 0])->count(),
            'modify_answer_approval_count' => db('approval')->where(['type' => 'modify_answer', 'status' => 0])->count(),
            'modify_article_approval_count' => db('approval')->where(['type' => 'modify_article', 'status' => 0])->count(),
            'article_comment_approval_count' => db('approval')->where(['type' => 'article_comment', 'status' => 0])->count(),
            'user_report_count' => db('report')->where(['status' => 0])->count(),
            'register_approval_count' => db('users')->where(['status' => 2])->count(),
            'verify_approval_count' => db('users_verify')->where(['status' => 1])->count(),
            'column_approval_count' => db('column')->where(['verify' => 0])->count(),
        ];
    }

    /**
     * 获取通知内容
     * @return array
     */
    public static function getNotifyTextList(): array
    {
        $notifications_texts = [];
        $notifications = self::notifyCount();
        /*问题*/
        if ($notifications['question_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'question']),
                'text' => lang('有 %s 个问题待审核', [$notifications['question_approval_count']]),
            );
        }

        /*文章*/
        if ($notifications['article_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'article']),
                'text' => lang('有 %s 个文章待审核', [$notifications['article_approval_count']])
            );
        }

        /*回答*/
        if ($notifications['answer_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'answer']),
                'text' => lang('有 %s 个回答待审核', [$notifications['answer_approval_count']])
            );
        }

        /*问题修改*/
        if ($notifications['modify_question_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'modify_question']),
                'text' => lang('有 %s 个问题修改待审核', [$notifications['modify_question_approval_count']])
            );
        }

        /*文章修改*/
        if ($notifications['modify_article_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'modify_article']),
                'text' => lang('有 %s 个文章修改待审核', [$notifications['modify_article_approval_count']])
            );
        }

        /*回答修改*/
        if ($notifications['modify_answer_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'modify_answer']),
                'text' => lang('有 %s 个回答修改待审核', [$notifications['modify_answer_approval_count']])
            );
        }

        /*文章评论*/
        if ($notifications['article_comment_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Approval/index', ['status' => 0, 'type' => 'article_comment']),
                'text' => lang('有 %s 个文章评论待审核', [$notifications['article_comment_approval_count']])
            );
        }

        /*用户举报*/
        if ($notifications['user_report_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Report/index', ['status' => 0]),
                'text' => lang('有 %s 个用户举报待查看', [$notifications['user_report_count']])
            );
        }

        /*用户注册*/
        if (get_setting('register_valid_type') == 'admin' and $notifications['register_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('member.Users/index', ['status' => 2]),
                'text' => lang('有 %s 个新用户待审核', [$notifications['register_approval_count']])
            );
        }

        /*认证申请*/
        if ($notifications['verify_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('member.Verify/index', ['status' => 1]),
                'text' => lang('有 %s 个认证申请待审核', [$notifications['verify_approval_count']])
            );
        }

        if ($notifications['column_approval_count']) {
            $notifications_texts[] = array(
                'url' => url('content.Column/index', ['verify' => 0]),
                'text' => lang('有 %s 个专栏申请待审核', [$notifications['column_approval_count']])
            );
        }

        return $notifications_texts;
    }

    /**
     * 发送通知
     * @param int $sender_uid
     * @param int $recipient_uid
     * @param string $action_type 通知类型
     * @param int|mixed $item_id 内容ID
     * @param string $item_type 内容类型 对应表名称
     * @param array $extends 附加信息 自定义信息 需包含 subject,message,和 type发送类型
     * @param int $anonymous
     * @return bool
     */
    public static function sendNotify(int $sender_uid=0, int $recipient_uid=0,string $action_type='',$item_id = 0,string $item_type='',array $extends = array(),$anonymous=0)
    {
        $guid=$sender_uid ?: intval(session('login_uid'));

        $notify_setting = db('users_extends')->where('uid',$guid)->value('notify_setting');

        if((!$sender_uid and !$recipient_uid) || $guid==0 || !$notify_setting){
            return false;
        }

        $notify_setting = json_decode($notify_setting,true);
        $settings = db('users_notify_setting')->where(['status'=>1,'name'=>$action_type])->find();

        if(!$settings && !isset($extends['subject'])) return false;

        $message = $extends['message']?? $settings['message'];
        $subject = $extends['subject']?? $settings['subject'];

        $notify_type = $settings['type'] ? explode(',',$settings['type']) : '';

        if(!$notify_type) return false;

        foreach ($notify_type as $k=>$v)
        {
            //用户是否设置允许通知
            if((!isset($notify_setting[$v]) || !in_array($action_type,$notify_setting[$v])) && $settings['user_setting'])
            {
                continue;
            }

            //是否是允许的通知类型
            if(!in_array($v,get_setting('notify_type')))
            {
                continue;
            }

            //系统通知
            if($v=='site')
            {
                Notify::send($sender_uid, $recipient_uid, $action_type, $subject, $item_id, $item_type,$message,$anonymous);
            }

            //邮件通知
            if($v=='email' && $email = db('users')->where(['uid'=>$recipient_uid,'status'=>1])->value('email'))
            {
                $title = '';
                $link = '';
                $user_name = $recipient_uid ? get_link_username($recipient_uid) : '';
                $from_name = $anonymous ? '匿名用户' :($sender_uid ? get_link_username($sender_uid) : get_setting('site_name').'站务');

                //存在自定义附属信息时 不做处理
                if($item_type && $item_id && !$extends)
                {
                    if($item_type=='question' || $item_type=='article' || $item_type=='reward') {
                        $title = db($item_type)->where(['id' => $item_id])->value('title');
                        $link = (string)get_url($item_type . '/detail', ['id' => $item_id]);
                    }
                    if($item_type=='column') {
                        $title = db('column')->where(['id' => $item_id])->value('name');
                        $link = (string)get_url('column/detail', ['id' => $item_id]);
                    }
                    if($item_type=='users')
                    {
                        $title = db('users')->where(['uid'=>$item_id])->value('nick_name');
                        $link = get_user_url($item_id);
                    }
                }

                $search = [
                    '[#site_name#]',
                    '[#title#]',
                    '[#time#]',
                    '[#user_name#]',
                    '[#from_username#]'
                ];

                if(!strstr($link,'http://') && !strstr($link,'https://'))
                {
                    $link = request()->domain().$link;
                }
                $replace = [
                    get_setting('site_name'),
                    '<a href="'.$link.'">'.$title.'</a>',
                    date('Y-m-d H:i',time()),
                    $user_name,
                    $from_name
                ];

                if($subject)
                {
                    $subject = str_replace($search,$replace,$subject);
                }

                if($message)
                {
                    $message = str_replace($search,$replace,$message);
                    $message = str_replace('[#subject#]',$subject,$message);
                }

                MailHelper::sendEmail($email,$subject,$message,$extends);
            }

            //其他通知钩子
            hook('sendNotify'.Str::title($v),['sender_uid'=>$sender_uid,'recipient_uid'=>$recipient_uid,'action_type'=>$action_type,'subject'=>$subject,'item_id'=>$item_id,'item_type'=>$item_type,'message'=>$message,'extends'=>$extends,'settings'=>$settings]);
        }
        return true;
    }

    /**
     * 获取用户可配置的选项
     */
    public static function getNotifyConfigItem()
    {
        $settings = db('users_notify_setting')->where(['status'=>1])->select()->toArray();

        if(!$settings)
        {
            return false;
        }

        $result = [];
        foreach ($settings as $k=>$v)
        {
            $notify_type = $v['type'] ? explode(',',$v['type']) : '';
            if(!$notify_type)
            {
                continue;
            }

            foreach ($notify_type as $k1=>$v1)
            {
                $result[$v1]['config'][] = $v;
            }
        }

        return $result;
    }

    /**
     * 获取用户默认配置
     * @return array|false
     */
    public static function getDefaultNotifyConfig()
    {
        $settings = db('users_notify_setting')->where(['status'=>1])->select()->toArray();

        if(!$settings)
        {
            return false;
        }

        $result = [];
        foreach ($settings as $k=>$v)
        {
            $notify_type = $v['type'] ? explode(',',$v['type']) : '';
            if(!$notify_type || !$v['user_setting'])
            {
                continue;
            }

            foreach ($notify_type as $k1=>$v1)
            {
                $result[$v1][] = $v['name'];
            }
        }

        return $result;
    }
}