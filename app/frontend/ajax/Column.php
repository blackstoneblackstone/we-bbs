<?php
namespace app\frontend\ajax;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\model\Article;
use app\model\Column as ColumnModel;

class Column extends Frontend
{
    protected $needLogin=[
        'recommend_article',
        'delete',
        'collect_recommend',
        'collect'
    ];

    //向专栏推荐文章
    public function recommend_article()
    {
        if($this->request->isPost())
        {
            $column_id = $this->request->post('column_id',0,'intval');
            $article_id = $this->request->post('article_id',0,'intval');
            $res = ColumnModel::saveColumnArticleRecommend($article_id,$column_id,$this->user_id);
            if(!$res) $this->error('推荐失败：'.ColumnModel::getError());
            $this->success('推荐成功,请等待管理员审核');
        }
        $column_id = $this->request->param('column_id',0,'intval');
        $page = $this->request->param('page',1,'intval');
        $where[] = ['uid','=',$this->user_id];
        $where[]=['status','=',1];
        //$whereOr[]=['column_id','<>',$column_id];
        $where[]=['column_id','=',0];
        $article_ids = db('column_recommend_article')->where(['uid'=>$this->user_id,'column_id'=>$column_id])->column('article_id');
        if($article_ids)
        {
            $where[]=['id','not in',$article_ids];
        }

        $_list = db('article')
            ->where($where)
            //->whereOr($whereOr)
            ->paginate(
                [
                    'list_rows'=> 5,
                    'page' => $page,
                    'query'=>request()->param(),
                    'pjax'=>'tabMain'
                ]
            );

        $pageVar = $_list->render();
        $_list = $_list->all();

        $this->assign([
            'list'=> $_list,
            'page'=>$pageVar,
            'column_id'=>$column_id
        ]);
        return $this->fetch();
    }

    //删除专栏
    public function delete()
    {
        $id = $this->request->param('id',0,'intval');
        $column_info = db('column')->where('id',$id)->find();
        if(!$column_info || !$id) $this->error('专栏不存在');

        if($this->user_id != $column_info['uid']) $this->error('您没有删除专栏的权限');

        if(db('column')->where(['id'=>$id])->delete())
        {
            db('article')->where(['column_id'=>$id])->update(['column_id'=>0]);
            $this->success('删除成功');
        }

        $this->error('删除失败');
    }

    //收录推荐文章
    public function collect_recommend()
    {
        if($this->request->isPost())
        {
            $article_id = $this->request->post('article_id',0,'intval');
            $column_id  = $this->request->post('column_id',0,'intval');
            $status = $this->request->post('status',1,'intval');
            $reason  = $this->request->post('reason','','trim');
            if(!$article_id || !$column_id)
            {
                $this->error('请求参数不正确');
            }

            $column_uid = db('column')->where(['id'=>$column_id])->value('uid');

            if($this->user_id!=$column_uid && !isSuperAdmin() && !isNormalAdmin())
            {
                $this->error('没有操作权限');
            }

            db('column')->startTrans();

            $update_result = db('column_recommend_article')
                ->where(['column_id'=>$column_id,'article_id'=>$article_id])
                ->update(['status'=>$status,'reason'=>$reason]);

            if($update_result)
            {
                $uid = db('column_recommend_article')->where(['column_id'=>$column_id,'article_id'=>$article_id])->value('uid');
                $column_name= db('column')->where(['id'=>$column_id])->value('name');
                if($status==1)
                {
                    //更新文章所属专栏
                    db('article')->where(['id'=>$article_id])->update(['column_id'=>$column_id]);
                    db('column')->where(['id'=>$column_id])->inc('post_count')->update();
                    send_site_diy_notify(0,$uid,'您推荐到专栏的文章已被收录','您推荐到专栏【<a href="'.(string)url('column/detail',['id'=>$column_id]).'" target="_blank">'.$column_name.'</a>】的文章已被收录');
                }
                if($status==2){
                    send_site_diy_notify(0,$uid,'您推荐到专栏的文章已被拒绝收录','您推荐到专栏【<a href="'.(string)url('column/detail',['id'=>$column_id]).'" target="_blank">'.$column_name.'</a>】的文章已被拒绝收录'.($reason?'拒绝理由：'.$reason:''));
                }
                db('column')->commit();
                $this->success($status==1?'收录成功':'已拒绝收录');
            }else{
                db('column')->rollback();
                $this->error(($status==1?'收录失败':'拒绝收录失败'));
            }
        }
    }

    /**
     * 专栏收录
     * @return void
     */
    public function collect()
    {
        if($this->request->isPost())
        {
            $article_id = $this->request->post('article_id',0,'intval');
            $column_id  = $this->request->post('column_id',0,'intval');
            if(!$article_id || !$column_id)
            {
                $this->error('请求参数不正确');
            }

            $article_uid = db('article')->where(['id'=>$article_id])->value('uid');

            if($this->user_id!=$article_uid)
            {
                $this->error('没有操作权限');
            }

            if(db('article')->where(['id'=>$article_id])->update(['column_id'=>$column_id]))
            {
                $column_post_count = Article::where(['column_id'=>$column_id,'status'=>1])->count();
                ColumnModel::update(['post_count'=>$column_post_count],['id'=>$column_id]);

                //添加行为日志
                LogHelper::addActionLog('create_column_article','column',$column_id,$article_uid,'0',0,'article',$article_id);
                $this->success('收录成功');
            }
            $this->error('收录失败');

        }else{
            $article_id = $this->request->param('id',0,'intval');
            $article_uid = db('article')->where(['id'=>$article_id])->value('uid');
            $column_list = db('column')->where(['uid'=>$article_uid,'verify'=>1])->select()->toArray();
            $this->assign([
                'column_list'=>$column_list,
                'article_id'=>$article_id
            ]);

            return $this->fetch();
        }
    }
}