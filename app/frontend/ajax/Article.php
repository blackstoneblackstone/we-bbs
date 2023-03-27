<?php
namespace app\frontend\ajax;
use app\common\controller\Frontend;
use app\model\Article as ArticleModel;
use app\model\PostRelation;
use app\model\Vote;

class Article extends Frontend
{
    protected $needLogin = [
        'remove_article',
        'action'
    ];

    /*删除文章*/
    public function remove_article()
    {
        $id = $this->request->param('id');
        $article_info = ArticleModel::getArticleInfo($id);

        if ($this->user_id != $article_info['uid'] && get_user_permission('remove_article')!='Y') {
            $this->error('您没有删除文章的权限');
        }

        if (!ArticleModel::removeArticle($id)) {
            $this->error('删除文章失败');
        }

        $this->success('删除文章成功');
    }

    /*文章操作*/
    public function action()
    {
        $action=input('type');
        $article_id=input('article_id',0,'intval');
        switch ($action) {
            case 'recommend':
                $is_recommend=input('is_recommend');
                $msg=$is_recommend ? '取消成功' : '推荐成功';
                $ret = db('article')->where('id',$article_id)->update(['is_recommend'=>$is_recommend ? 0 : 1]);
                if($ret != false){
                    PostRelation::updatePostRelation($article_id,'article',['is_recommend'=>$is_recommend?0:1]);
                    $this->success($msg);
                }
                break;
            case 'set_top':
                $set_top=input('set_top');
                $msg=$set_top ?  '取消成功' : '置顶成功';
                $ret= db('article')->where(['id'=>$article_id])->update(['set_top'=>$set_top ? 0 : 1,'set_top_time'=>time()]);
                if($ret != false){
                    PostRelation::updatePostRelation($article_id,'article',['set_top'=>$set_top ? 0 : 1,'set_top_time'=>$set_top ? 0 : time()]);
                    $this->success($msg);
                }
                break;
        }
    }

    //ajax加载文章评论
    public function get_ajax_comment()
    {
        $article_id = $this->request->param('article_id', 0,'intval');
        $page = $this->request->param('page', 1,'intval');

        $comment_list = ArticleModel::getArticleCommentList($article_id, ['create_time' => 'DESC'], intval($page));
        foreach ($comment_list['data'] as $key => $val)
        {
            $comment_list['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'], 'article_comment', $this->user_id);
        }
        $this->assign('comment_list', $comment_list['data']);
        $this->assign('total', $comment_list['last_page']);
        return $this->fetch();
    }
}