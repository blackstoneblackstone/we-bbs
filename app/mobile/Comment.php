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

namespace app\mobile;

use app\common\controller\Frontend;
use app\model\Answer;
use app\model\Article as ArticleModel;
use app\model\Attach;
use app\model\Question as QuestionModel;
use app\model\Users;
use app\model\Vote;
use think\response\Json;
use tools\Tree;

/**
 * 评论控制器
 */
class Comment extends Frontend
{
    protected $needLogin=[
        'delete_comment',
        'delete_answer_comment',
    ];

    public function question()
    {
        $question_id = $this->request->param('id', 0);
        $this->assign('id',$question_id);
        return $this->fetch();
    }

    /**
     * 获取问题评论
     */
    public function get_question_comments()
    {
        $question_id = $this->request->param('id', 0);
        $page = $this->request->param('page', 0);
        $sort = ['create_time' => 'desc'];
        $data = QuestionModel::getQuestionComments($question_id, $page, $sort,get_setting('contents_per_page',15));
        $data['question_id'] = $question_id;
        $data['list']=$data['data'];
        $data['html'] = $this->fetch('',$data);
        return $this->apiResult($data);;
    }

    /**
     * 问题回答评论
     */
    public function answer()
    {
        $answer_id = $this->request->param('answer_id', 0);
        $this->assign('id',$answer_id);
        return $this->fetch();
    }

    /**
     * 获取问题评论
     */
    public function get_answer_comments()
    {
        $answer_id = $this->request->param('id',0);
        $page = $this->request->param('page', 0);
        if(!$answer_info = Answer::getAnswerInfoById($answer_id))
        {
            $this->error('回答不存在');
        }
        $data = Answer::getAnswerComments($answer_id,$page,get_setting('contents_per_page',15));
        $data['list']=$data['data'];
        $data['html'] = $this->fetch('',$data);
        $this->apiResult($data);;
    }

    /**
     * 保存问题评论
     */
    public function save_question_comment()
    {
        if($this->request->isPost())
        {
            $question_id = $this->request->post('question_id','','intval');
            if (!$message = $this->request->post('message','','sqlFilter')) $this->error('请填写评论内容');
            $question_info = QuestionModel::getQuestionInfo(intval($question_id),'id,uid,title');

            if(htmlspecialchars_decode($message)=='' || removeEmpty($message)=='')
            {
                $this->error('请填写评论内容');
            }

            if (!$question_info) {
                $this->result([],0,'问题不存在或已被删除');
            }

            $ret = QuestionModel::saveComments($this->user_id, $question_id,$message);
            if ($ret) {
                $ret['user_info'] = $this->user_info;
                $this->result([],1,'评论成功');
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
            if (!$data['message']) $this->error('请填写评论内容');
            $data['uid'] = $this->user_id;
            $data['user_name'] = $this->user_info['user_name'];

            if(htmlspecialchars_decode($data['message'])=='' || removeEmpty($data['message'])=='')
            {
                $this->error('请填写评论内容');
            }

            $answer=Answer::getAnswerInfoById($data['answer_id']);
            if (!$answer) {
                $this->result([],0,'回答不存在');
            }
            $data['question_info'] = QuestionModel::getQuestionInfo($answer['question_id'],'id,uid,title');
            $ret = Answer::saveComments($data);
            if ($ret) {
                $this->result([],1,'评论成功');
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
            $article_id = $this->request->post('article_id');
            $article=ArticleModel::getArticleInfoField($article_id,'id,title,uid');
            $message = remove_xss($this->request->post('message','','sqlFilter'));
            $at_uid = $this->request->post('at_uid',0,'intval');
            $pid = $this->request->post('pid',0,'intval');

            if (!$article) {
                $this->error('文章不存在');
            }
            if(htmlspecialchars_decode($message)=='' || removeEmpty($message)=='')
            {
                $this->error('请填写评论内容');
            }

            if (!$result = ArticleModel::saveArticleComment($article, $message, $this->user_info,intval($at_uid),$pid))
            {
                $this->result([],0,'评论失败');
            }

            $this->result(['comment_count'=>$result['comment_count']],1,'评论成功');
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

    //获取文章评论
    public function get_article_comments()
    {
        $page = $this->request->param('page', 1);
        $id = $this->request->param('id',0,'intval');
        $sort = $this->request->param('sort', 'new');
        $comment_list = ArticleModel::getArticleCommentList($id, $sort, intval($page),get_setting('contents_per_page',15));
        foreach ($comment_list['data'] as $key => $val)
        {
            $comment_list['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'], 'article_comment', $this->user_id);
        }

        $comment_list['data'] = Tree::toTree($comment_list['data']);
        $comment_list['html'] = $this->fetch('',$comment_list);
        return $this->apiResult($comment_list);;
    }

    //评论编辑器
    public function comment_editor()
    {
        $id = $this->request->param('id',0,'intval');
        $at_uid = $this->request->param('at_uid',0,'intval');
        $pid = $this->request->param('pid',0,'intval');
        $at_user_info = $at_uid ? Users::getUserInfoByUid($at_uid,'nick_name,uid') : [];
        $this->assign([
            'id'=>$id,
            'pid'=>$pid,
            'at_uid'=>$at_uid,
            'at_user_info'=>$at_user_info
        ]);
        return $this->fetch();
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
}