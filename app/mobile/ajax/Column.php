<?php
namespace app\mobile\ajax;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\model\Article;
use app\model\Column as ColumnModel;

class Column extends Frontend
{
    protected $needLogin=['collect','columns'];

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

    // 我的专栏列表
    public function columns()
    {
        $page = $this->request->param('page',1);
        $sort = $this->request->param('sort','new');
        $verify =  $this->request->param('verify',1);
        $data = ColumnModel::getMyColumnList($this->user_id, $sort, $verify, $page);
        $data['html'] = $this->fetch('', $data);
        $this->apiResult($data);
    }
}