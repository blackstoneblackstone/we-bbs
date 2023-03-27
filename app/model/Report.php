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

namespace app\model;

class Report extends BaseModel
{
    /**
     * 保存举报信息
     * @param $item_id
     * @param $item_type
     * @param $report_type
     * @param $reason
     * @param $uid
     * @return array
     */
	public static function saveReport($item_id,$item_type,$report_type,$reason,$uid)
	{
        $url= '';

        if (self::getReportInfo($item_id,$item_type,$uid))
        {
            return ['code' => 0, 'msg' => '您已经举报过了！'];
        }

        if($item_type == 'question')
        {
            $url = (string) url('question/detail', ['id' => $item_id]);
        }elseif($item_type == 'answer')
        {
            if (strpos($item_id, '-')) {
                $item_id = explode('-', $item_id);
                $item_id = $item_id[1];
                $url = (string) url('question/detail', ['id' => $item_id[0]]) . '#comment-id-' . $item_id[1];
            }
        }elseif($item_type=='article')
        {
            $url = (string) url('article/detail', ['id' => $item_id]);
        }elseif($item_type=='article_comment')
        {
            $url = (string) url('article/detail', ['id' => $item_id]);
        }else{
            //保存举报信息处理钩子
            hook('saveReport',['item_id'=>$item_id,'item_type'=>$item_type,'report_type'=>$report_type,'reason'=>$reason,'uid'=>$uid]);
        }

        $insertData = [
            'item_id'=>$item_id,
            'item_type'=>$item_type,
            'report_type'=>$report_type,
            'reason'=>$reason,
            'url'=>$url,
            'uid'=>$uid,
            'create_time'=>time()
        ];
		self::create($insertData);

        hook('saveReportAfter',$insertData);

		return ['code' => 1, 'msg' => '举报成功'];
	}

	//获取举报信息
	public static function getReportInfo($item_id,$item_type,$uid)
	{
		return db('report')->where(['item_id' => $item_id, 'uid' => $uid, 'item_type' => $item_type])->find();
	}

    /**
     * 处理举报
     * @param $id
     * @param $handle_type
     * @param $handle_reason
     * @return bool|false
     */
    public static function reportHandle($id,$handle_type,$handle_reason='')
    {
        if(!$id) return false;

        if(!$info = db('report')->find(intval($id)))
        {
            self::setError('举报内容不存在');
            return false;
        }

        //删除内容
        if($handle_type==1)
        {
            if($info['item_type']=='question')
            {
                Question::removeQuestion($info['item_id']);
                $item_info = db('question')->where('id',$info['item_id'])->find();

                //给举报人发送通知
                send_notify(0,$info['uid'],'REPORT_QUESTION_HANDLE_SUCCESS','question',$info['item_id']);

                //给发起人发送通知
                send_notify(0,$item_info['uid'],'QUESTION_REPORT_HANDLE_SUCCESS','question',$info['item_id']);

            }elseif($info['item_type']=='answer') {
                Answer::deleteAnswer($info['item_id']);
                $item_info = db('answer')->where('id', $info['item_id'])->find();
                //给举报人发送通知
                send_notify(0, $info['uid'], 'REPORT_ANSWER_HANDLE_SUCCESS', 'question', $item_info['question_id']);
                //给发起人发送通知
                send_notify(0, $item_info['uid'], 'ANSWER_REPORT_HANDLE_SUCCESS', 'question', $item_info['question_id']);

            }elseif($info['item_type']=='article') {
                Article::removeArticle($info['item_id']);

                $item_info = db('article')->where('id', $info['item_id'])->find();

                //给举报人发送通知
                send_notify(0, $info['uid'], 'REPORT_ARTICLE_HANDLE_SUCCESS', 'article', $info['item_id']);

                //给发起人发送通知
                send_notify(0, $item_info['uid'], 'ARTICLE_REPORT_HANDLE_SUCCESS', 'article', $info['item_id']);
            }elseif($info['item_type']=='article_comment'){
                Article::deleteComment($info['item_id']);
                $item_info = db('article_comment')->where('id',$info['item_id'])->find();
                //给举报人发送通知
                send_notify(0,$info['uid'],'REPORT_ARTICLE_COMMENT_HANDLE_SUCCESS','article',$info['item_id']);
                //给发起人发送通知
                send_notify(0,$item_info['uid'],'ARTICLE_REPORT_COMMENT_HANDLE_SUCCESS','article',$info['item_id']);
            }else {
                hook($info['item_type'].'ReportHandleRemove',['info'=>$info,'handle_type'=>$handle_type,'handle_reason'=>$handle_reason]);
            }
        }elseif ($handle_type==2)//要求整改内容
        {
            if($info['item_type']=='question') {
                $item_info = db('question')->where('id', $info['item_id'])->find();
                //给举报人发送通知
                send_notify(0, $info['uid'], 'REPORT_QUESTION_HANDLE_MODIFY', 'question', $info['item_id']);
                //给发起人发送通知
                send_notify(0, $item_info['uid'], 'QUESTION_REPORT_HANDLE_MODIFY', 'question', $info['item_id']);
            }elseif($info['item_type']=='answer') {
                $item_info = db('answer')->where('id',$info['item_id'])->find();
                //给举报人发送通知
                send_notify(0,$info['uid'],'REPORT_ANSWER_HANDLE_MODIFY','question',$item_info['question_id']);
                //给发起人发送通知
                send_notify(0,$item_info['uid'],'ANSWER_REPORT_HANDLE_MODIFY','question',$item_info['question_id']);

            }elseif($info['item_type']=='article') {
                $item_info = db('article')->where('id', $info['item_id'])->find();
                //给举报人发送通知
                send_notify(0, $info['uid'], 'REPORT_ARTICLE_HANDLE_MODIFY', 'article', $info['item_id']);
                //给发起人发送通知
                send_notify(0, $item_info['uid'], 'ARTICLE_REPORT_HANDLE_MODIFY', 'article', $info['item_id']);
            }elseif($info['item_type']=='article_comment')
            {
                $item_info = db('article_comment')->where('id',$info['item_id'])->find();
                //给举报人发送通知
                send_notify(0,$info['uid'],'REPORT_ARTICLE_COMMENT_HANDLE_MODIFY','article',$info['item_id']);
                //给发起人发送通知
                send_notify(0,$item_info['uid'],'ARTICLE_COMMENT_REPORT_HANDLE_MODIFY','article',$info['item_id']);
            }else{
                //举报修改处理钩子
                hook($info['item_type'].'reportHandleModify',['info'=>$info,'handle_type'=>$handle_type,'handle_reason'=>$handle_reason]);
            }
        }else{
            $content1 = '亲爱的用户您好,您举报的内容 <a href="'.$info['url'].'" target="_blank">'.$info['url'].'</a> 经审核确认不存在违规行为！ '.get_setting('site_name').' 非常感谢您的反馈！';
            send_notify(0,$info['uid'],'REPORT_HANDLE_DECLINE',$info['item_type'],$info['item_id'],['message'=>$content1]);
        }

        self::update(['handle_type'=>$handle_type,'handle_reason'=>$handle_reason,'status'=>1],['id'=>$id]);
        return true;
    }
    
    // 获取被举报uid
    public static function reportedUid($id)
    {
        if (is_array($id)) {
            $uid = [];
            $reports = self::whereIn('id', $id)->select();
            foreach ($reports as $report) {
                $uid[] = db($report->item_type)->where('id', $report->item_id)->value('uid');
            }
            $uid = array_unique($uid);
        } else {
            $report = self::find($id);
            $uid = db($report->item_type)->where('id', $report->item_id)->value('uid');
        }

        return $uid;
    }
}