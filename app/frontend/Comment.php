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
use app\model\Answer;
use app\model\Article as ArticleModel;
use app\model\Question as QuestionModel;
use app\model\Users;
use app\model\Vote;

/**
 * 评论控制器
 * Class Comment
 * @package app\ask\controller
 */
class Comment extends Frontend
{
    protected $needLogin=[
        'delete_comment',
        'delete_answer_comment',
    ];

    /**
     * 问题评论
     */
    public function question()
    {
        if($this->request->isPost())
        {
            $question_id = $this->request->param('id', 0);
            $order = $this->request->param('sort', 'new');
            $page = $this->request->param('page', 0);
            $sort = ['create_time' => 'desc'];
            if ($order == 'hot') {
                $sort = ['create_time' => 'desc'];
            }
            $data = QuestionModel::getQuestionComments($question_id, $page, $sort);
            $this->assign(['question_id' => $question_id, 'sort' => $sort,'list'=>$data['data']]);
            $data['html'] = $this->fetch();
            return json($data);
        }
    }

    /**
     * 问题回答评论
     */
    public function answer()
    {
        if($this->request->isPost())
        {
            $answer_id = $this->request->param('id',0);
            if(!$answer_info = Answer::getAnswerInfoById($answer_id))
            {
                $this->error('回答不存在');
            }
            $list = Answer::getAnswerComments($answer_id,$this->request->post('page'));
            $this->assign('answer_id',$answer_id);
            $this->assign([
                'list'=>$list['data'],
                'answer_info'=>$answer_info
            ]);
            $list['html'] = $this->fetch();
            return json($list);
        }
    }

    /**
     * 保存问题评论
     */
    public function save_question_comment()
    {
        if($this->request->isPost())
        {
            hook('save_question_comment_post_before',$this->request->post());

            $question_id = $this->request->post('question_id','','intval');
            if (!$message = $this->request->post('message','','sqlFilter')) $this->error('请填写评论内容');

            $question_info = QuestionModel::getQuestionInfo(intval($question_id),'id,uid,title');

            if (!$question_info) {
                $this->result([],0,'问题不存在或已被删除');
            }

            if(htmlspecialchars_decode($message)=='' || removeEmpty($message)=='')
            {
                $this->error('请填写评论内容');
            }

            $ret = QuestionModel::saveComments($this->user_id, $question_id,$message);

            hook('save_question_comment_post_after',$ret);

            if ($ret) {
                $ret['user_info'] = $this->user_info;
                $html = $this->fetch('comment/ajax_question_comment', ['comment' => $ret]);
                $this->result(['html' => $html],1,'评论成功');
            }

            $this->result([],0,'评论失败');
        }
    }

    /**
     * 保存回答评论
     */
    public function save_answer_comment()
    {
        if($this->request->isPost())
        {
            $data = $this->request->post();
            hook('save_answer_comment_post_before',$data);

            if (!$data['message']) $this->error('请填写评论内容');
            $data['uid'] = $this->user_id;
            $data['user_name'] = $this->user_info['user_name'];
            $answer=Answer::getAnswerInfoById($data['answer_id']);
            if (!$answer) {
                $this->result([],0,'回答不存在');
            }

            if(htmlspecialchars_decode($data['message'])=='' || removeEmpty($data['message'])=='')
            {
                $this->error('请填写评论内容');
            }

            $data['question_info'] = QuestionModel::getQuestionInfo($answer['question_id'],'id,uid,title');
            $ret = Answer::saveComments($data);

            hook('save_answer_comment_post_after',$ret);

            if ($ret) {
                $html = $this->fetch('comment/ajax_answer_comment',$ret);
                $this->result(['html'=>$html],1,'评论成功');
            }
            $this->result([],0,'评论失败');
        }
    }

    /**
     * 保存文章评论
     */
    public function save_article_comment()
    {
        $this->view->engine()->layout(false);
        if ($this->request->isPost())
        {
            hook('save_article_comment_before',$this->request->post());

            $article_id = $this->request->post('article_id');
            $article=ArticleModel::getArticleInfoField($article_id,'id,title,uid');
            $message = remove_xss($this->request->post('message','','sqlFilter'));
            $at_info = $this->request->post('at_info','','intval');
            $pid = $this->request->post('pid',0,'intval');

            if(session('__token__')!=$this->request->post('token'))
            {
                $this->error('请不要重复提交');
            }

            if (!$article) {
                $this->error('文章不存在');
            }
            if(htmlspecialchars_decode($message)=='' || removeEmpty($message)=='')
            {
                $this->error('请填写评论内容');
            }

            $result = ArticleModel::saveArticleComment($article, $message, $this->user_info,intval($at_info),$pid);

            hook('save_article_comment_after',$result);

            if (!$result)
            {
                $this->result([],0,'评论失败');
            }

            $comment_info = db('article_comment')->find($result['comment_id']);
            $comment_info['user_info'] = Users::getUserInfo($comment_info['uid'],'user_name,nick_name,avatar,uid');
            $comment_info['vote_value'] = Vote::getVoteByType($comment_info['id'], 'article_comment', $this->user_id);
            $this->assign('comment_info',$comment_info);
            $this->result(['html'=>$this->fetch('single_comment'),'comment_count'=>$result['comment_count']],1,'评论成功');
        }
    }

    /**
     * 删除文章评论
     */
    public function remove_article_comment()
    {
        $id= intval(input('id'));
        $comment_info = db('article_comment')->find($id);
        if($this->user_id!=$comment_info['uid'] && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->result([], 0, '您没有删除评论的权限');
        }

        if (ArticleModel::deleteComment($id)) $this->success('删除成功', (string) url('article/detail', ['id' => $comment_info['article_id']]));

        $this->result([], 0, '删除失败');
    }

    /**
     * 删除回答评论
     */
    public function delete_answer_comment()
    {
        $comment_id = $this->request->param('id');
        $ret = Answer::deleteComment($comment_id, $this->user_info);
        if ($ret) {
            $this->success('删除成功');
        }
        $this->error(Answer::getError());
    }

    /**
     * 删除问题评论
     */
    public function delete_comment()
    {
        $comment_id = $this->request->param('id');
        $ret = QuestionModel::deleteComment($comment_id, $this->user_info);
        if ($ret) {
            $this->success('删除成功');
        }
        $this->error(QuestionModel::getError());
    }

    //评论赞踩
    public function comment_vote()
    {
        $item_id=$this->request->param('item_id', 0);
        if(!$item_id){
            $this->result([], 0, '参数错误');
        }
        $comment=QuestionModel::comment($item_id);
        if(!$comment){
            $this->result([], 0, '数据错误');
        }

        $ret=QuestionModel::comment_vote($item_id,$this->user_id,$comment);

        if($ret){
            $this->result([], 1, '操作成功');
        }else{
            $this->result([], 0, '操作失败');
        }
    }
}