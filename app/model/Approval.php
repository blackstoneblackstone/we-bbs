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

class Approval extends BaseModel
{
    /**
     * 添加审核数据
     * @param $type
     * @param $data
     * @param $uid
     * @param string $access_key
     * @return int|string
     */
	public static function saveApproval($type, $data, $uid,string $access_key='')
	{
		$insertData = array(
			'type' => $type,
			'data' => json_encode($data,JSON_UNESCAPED_UNICODE),
			'uid' => intval($uid),
            'access_key'=>$access_key,
			'create_time' => time()
		);
        //保存审核信息钩子
        hook('saveApprovalHook',$insertData);
		return db('approval')->insertGetId($insertData);
	}

    /**
     * 审核通过
     * @param $id
     * @return bool
     */
	public static function approval($id): bool
    {
		$id = is_array($id) ? $id : explode(',',$id);

		if (!$approval_list = db('approval')->whereIn('id',$id)->select()->toArray())
		{
			return false;
		}

		foreach ($approval_list as $key=>$val)
		{
            $dataId = 0;
            //更新审核
            $val['data'] = json_decode($val['data'],true);
            $access_key = $val['access_key'];
            if($val['type']=='question')
            {
                $val['data']['from']='approval';
                $dataId = Question::saveQuestion($val['uid'],$val['data'],$access_key);
                if($dataId)
                {
                    db('approval')->where(['id'=>$val['id']])->update(['status'=>1,'item_id'=>$dataId]);
                    send_notify(0,$val['uid'],'TYPE_QUESTION_APPROVAL','question',$dataId);
                }else{
                    self::setError(Question::getError());
                }
            }elseif($val['type']=='modify_question'){
                $val['data']['from']='approval';
                $dataId = Question::saveQuestion($val['uid'],$val['data'],$access_key);
                if($dataId) {
                    db('approval')->where(['id'=>$val['id']])->update(['status'=>1,'item_id'=>$dataId]);
                    send_notify(0, $val['uid'], 'TYPE_QUESTION_MODIFY_APPROVAL', 'question', $dataId);
                }else{
                    self::setError(Question::getError());
                }
            }elseif($val['type']=='answer'){
                $val['data']['uid'] = $val['uid'];
                $answerInfos = Answer::saveAnswer($val['data'],$access_key);
                $dataId = $answerInfos['info']['id']??0;
                if($dataId) {
                    db('approval')->where(['id'=>$val['id']])->update(['status'=>1,'item_id'=>$dataId]);
                    send_notify(0, $val['uid'], 'TYPE_ANSWER_APPROVAL', 'question', $val['data']['question_id']);
                }else{
                    self::setError(Answer::getError());
                }
            }elseif($val['type']=='modify_answer')
            {
                $val['data']['uid'] = $val['uid'];
                $answerInfos = Answer::saveAnswer($val['data'],$access_key);
                $dataId = $answerInfos['info']['id']??0;
                if($dataId) {
                    db('approval')->where(['id'=>$val['id']])->update(['status'=>1,'item_id'=>$dataId]);
                    send_notify(0, $val['uid'], 'TYPE_ANSWER_MODIFY_APPROVAL', 'question', $val['data']['question_id']);
                }else{
                    self::setError(Answer::getError());
                }
            }elseif($val['type']=='article'){
                $dataId = Article::saveArticle($val['uid'],$val['data'],$access_key);
                if($dataId) {
                    db('approval')->where(['id'=>$val['id']])->update(['status'=>1,'item_id'=>$dataId]);
                    send_notify(0, $val['uid'], 'TYPE_ARTICLE_APPROVAL', 'article', $dataId);
                }else{
                    self::setError(Article::getError());
                }
            }elseif($val['type']=='modify_article'){
                $dataId = Article::updateArticle($val['uid'],$val['data'],$access_key);
                if($dataId) {
                    db('approval')->where(['id'=>$val['id']])->update(['status'=>1,'item_id'=>$dataId]);
                    send_notify(0, $val['uid'], 'TYPE_ARTICLE_MODIFY_APPROVAL', 'article', $dataId);
                }else{
                    self::setError(Article::getError());
                }
            }else{
                //审核钩子
                hook('approval_'.$val['type'],$val);
            }
            if(!$dataId) return false;
		}
		return true;
	}

    /**
     * 拒绝审核
     * @param $id
     * @param string $reason
     * @return bool
     */
	public static function decline($id,string $reason=''): bool
    {
		if (!db('approval')->where('id',$id)->find())
		{
			return false;
		}

        $id = is_array($id) ? $id : explode(',',$id);

        if (!$approval_list = db('approval')->whereIn('id',$id)->select()->toArray())
        {
            return false;
        }

        foreach ($approval_list as $key=>$val)
        {
            self::update(['status'=>2,'reason'=>$reason],['id'=>$val['id']]);
            $val['data'] = json_decode($val['data'],true);
            if($val['type']=='question'){
                $message = '亲爱的用户您好,您发表的问题 ['.$val['data']['title'].'] 未审核通过！';
                if($reason) $message = '亲爱的用户您好,您发表的问题 ['.$val['data']['title'].'] 未审核通过！拒绝原因：【'.$reason.'】';
                send_notify(0,$val['uid'],'TYPE_QUESTION_DECLINE','question',0,['message'=>$message]);
            }elseif($val['type']=='modify_question') {
                $message = '亲爱的用户您好,您修改的问题 ['.$val['data']['title'].'] 未审核通过！';
                if($reason) $message = '亲爱的用户您好,您修改的问题 ['.$val['data']['title'].'] 未审核通过！拒绝原因：【'.$reason.'】';
                send_notify(0, $val['uid'], 'TYPE_QUESTION_MODIFY_DECLINE', 'question', $val['data']['id'],['message'=>$message]);
            }elseif($val['type']=='answer') {
                $question_title = db('question')->where(['id'=>intval($val['data']['question_id'])])->value('title');
                $message = '亲爱的用户您好,您在问题 ['.$question_title.'] 发表的回答未审核通过！';
                if($reason) $message = '亲爱的用户您好,您在问题['.$question_title.'] 发表的回答未审核通过！拒绝原因：【'.$reason.'】';
                send_notify(0, $val['uid'], 'TYPE_ANSWER_DECLINE', 'question', $val['data']['question_id'],['message'=>$message]);
            }elseif($val['type']=='modify_answer')
            {
                $question_title = db('question')->where(['id'=>intval($val['data']['question_id'])])->value('title');
                $message = '亲爱的用户您好,您在问题 ['.$question_title.'] 修改的回答未审核通过！';
                if($reason) $message = '亲爱的用户您好,您在问题['.$question_title.'] 修改的回答未审核通过！拒绝原因：【'.$reason.'】';
                send_notify(0,$val['uid'],'TYPE_ANSWER_MODIFY_DECLINE','question',$val['data']['question_id'],['message'=>$message]);
            }elseif($val['type']=='article'){
                $message = '亲爱的用户您好,您发表的文章 ['.$val['data']['title'].'] 未审核通过！';
                if($reason) $message = '亲爱的用户您好,您发表的文章 ['.$val['data']['title'].'] 未审核通过！拒绝原因：【'.$reason.'】';
                send_notify(0,$val['uid'],'TYPE_ARTICLE_DECLINE','article',0,['message'=>$message]);
            }elseif($val['type']=='modify_article'){
                $message = '亲爱的用户您好,您修改的文章 ['.$val['data']['title'].'] 未审核通过！';
                if($reason) $message = '亲爱的用户您好,您修改的文章 ['.$val['data']['title'].'] 未审核通过！拒绝原因：【'.$reason.'】';
                send_notify(0,$val['uid'],'TYPE_ARTICLE_MODIFY_DECLINE','article',$val['data']['id'],['message'=>$message]);
            }else{
                //拒绝审核钩子
                hook('decline_approval_'.$val['type'],$val);
            }
        }
		return true;
	}

    /**
     * 获取审核列表
     * @param $where
     * @param int $page
     * @param int $per_page
     * @param string $pjax
     * @return array
     */
	public static function getApprovalListByType($where,$page=1,$per_page=10,$pjax='wrapMain'): array
    {
		$list = db('approval')
            ->where($where)
            ->order('create_time','DESC')
            ->paginate(
			[
				'list_rows'=> $per_page,
				'page' => $page,
				'query'=>request()->param(),
                'pjax'=>$pjax
			]
		);
		$pageVar = $list->render();
		$list = $list->all();

		foreach ($list as $key=>$val)
		{
			$list[$key]['data'] = json_decode($val['data'],true);
            if($val['type']=='answer' || $val['type']=='modify_answer')
            {
                $list[$key]['data']['title'] = db('question')->where(['status'=>1,'id'=>$list[$key]['data']['question_id']])->value('title');
            }
		}
		return ['list'=>$list,'page'=>$pageVar];
	}
}