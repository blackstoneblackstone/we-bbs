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
use app\common\library\helper\HtmlHelper;
use app\common\library\helper\ImageHelper;
use app\common\library\helper\LogHelper;
use app\model\BrowseRecords;
use app\model\Topic;
use app\model\Vote;
use app\model\Users;
use think\App;
use app\model\Column as ColumnModel;
use app\model\Article;

class Column extends Frontend
{
    protected $needLogin = [
        'apply','my','manager'
    ];

	public function __construct(App $app)
	{
		parent::__construct($app);
		$this->model = new ColumnModel();
	}

    /**
     * 专栏首页
     * @return mixed
     */
	public function index()
	{
		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','new');
		$data = ColumnModel::getColumnListByPage($this->user_id,$sort,$page);
		$this->assign($data);
		$this->assign('sort',$sort);
		return $this->fetch();
	}

	/**
	 * 申请专栏
	 */
	public function apply()
	{
		if($this->request->isPost())
		{
			$name = $this->request->post('name');
			$description = $this->request->post('description');
			$cover = $this->request->post('cover');
			$id = $this->request->post('id',0,'intval');

			if(!$name){
				$this->error('专栏标题不能为空');
			}

			if(!$description){
				$this->error('专栏描述不能为空');
			}

            if(!$id && db('column')->where(['name'=>$name])->value('id'))
            {
                $this->error('专栏已存在');
            }

            if(!$this->request->checkToken())
            {
                $this->error('请勿重复提交');
            }

            $verify = (isSuperAdmin() || isNormalAdmin()) ? 1 :0;
            $column_id= ColumnModel::applyColumn($this->user_id,$name,$description,$cover,intval($id),$verify);
            $this->success($verify ? '申请成功' : '申请成功,请等待管理员审核','column/detail?id='.$column_id);
		}

		if($id = $this->request->param('id'))
        {
            $column_info = db('column')->where(['uid'=>$this->user_id,'id'=>$id])->find();
            if(!$column_info)
            {
                $this->error('专栏信息不存在');
            }
            $this->assign('info',$column_info);
        }
		return $this->fetch();
	}

	/**
	 * 专栏详情
	 */
	public function detail()
	{
		$column_id = $this->request->param('id');
		$column_info = db('column')->where(['id'=>$column_id])->find();
		$column_info['user_info'] = Users::getUserInfo($column_info['uid']);
		$focus = ColumnModel::checkFocus($column_id,$this->user_id);
        $page = $this->request->param('page');
		$sort = $this->request->param('sort','column');
		if(!$column_info['verify'] && $this->user_id!=$column_info['uid'])
		{
			$this->error('该专栏不存在或审核中，暂时无法访问');
		}

        //记录用户浏览记录
        BrowseRecords::recordViewLog($this->user_id,$column_info['id'],'column');

        $order = $where = array();
        $where[] = ['status','=',1];

        $order['set_top_time'] = 'DESC';
        $order['update_time'] = 'DESC';
        $order['create_time'] = 'DESC';
        if($sort=='column')
        {
            $where[] = ['column_id','=',$column_id];
        }

        if($sort=='other'){
            $where[] = ['uid','=',$column_info['uid']];
            $where[] = ['column_id','<>',$column_id];
        }
		$_list = db('article')->where($where)->order($order)->paginate(
            [
                'list_rows'=> get_setting('contents_per_page'),
                'page' => $page,
                'query'=>request()->param(),
                'pjax'=>'tabMain'
            ]
        );
        $pageVar = $_list->render();
        $_list = $_list->all();

        if($_list)
        {
            $topic_infos = Topic::getTopicByItemIds(array_column($_list,'id'), 'article');
            foreach ($_list as $key => $val)
            {
                $_list[$key]['user_info'] = Users::getUserInfo($val['uid'], true);
                $_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($val['message'])), 0, 120);
                $_list[$key]['img_list'] =ImageHelper::srcList(htmlspecialchars_decode($val['message']));
                $_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'article',$this->user_id);
                $_list[$key]['topics'] = ($topic_infos && isset($topic_infos[$val['id']])) ? $topic_infos[$val['id']] : [];
            }
        }

		$this->assign('column_info', $column_info);
		$this->assign('list', $_list);
		$this->assign('focus', $focus);
		$this->assign('page',$pageVar);
		$this->assign('sort',$sort);
		return $this->fetch();
	}

	/**
	 * 管理专栏
	 * @return mixed
	 */
	public function manager()
	{
        $type = $this->request->param('type','recommend');
        $column_id = $this->request->param('column_id',0,'intval');
        $page = $this->request->param('page',1,'intval');
        $column_info = db('column')->where(['id'=>$column_id])->find();

        if(!isSuperAdmin() && !isNormalAdmin() && $this->user_id!=$column_info['uid'])
        {
            $this->error('您没有该专栏管理权限');
        }

        if($type=='recommend')
        {
            $article_ids = db('column_recommend_article')->where(['column_id'=>$column_id,'status'=>0])->column('article_id');
            $where[] = ['id','in',$article_ids];

            $_list = db('article')
                ->where($where)
                ->paginate(
                [
                    'list_rows'=> get_setting('contents_per_page'),
                    'page' => $page,
                    'query'=>request()->param(),
                    'pjax'=>'tabMain'
                ]
            );
            $pageVar = $_list->render();
            $_list = $_list->all();

            if($_list)
            {
                $topic_infos = Topic::getTopicByItemIds(array_column($_list,'id'), 'article');
                foreach ($_list as $key => $val)
                {
                    $_list[$key]['user_info'] = Users::getUserInfo($val['uid'], true);
                    $_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($val['message'])), 0, 120);
                    $_list[$key]['img_list'] =ImageHelper::srcList(htmlspecialchars_decode($val['message']));
                    $_list[$key]['vote_value'] = Vote::getVoteByType($val['id'],'article',$this->user_id);
                    $_list[$key]['topics'] = ($topic_infos && isset($topic_infos[$val['id']])) ? $topic_infos[$val['id']] : [];
                }
            }

            $this->assign('list', $_list);
            $this->assign('page',$pageVar);
        }
        $this->assign([
            'type'=>$type,
            'column_id'=>$column_id,
            'column_info'=>$column_info
        ]);
		return $this->fetch();
	}

	/**
	 * 我的专栏
	 * @return mixed
	 */
	public function my()
	{
		if($this->isMobile)
		{
			$this->redirect(url('wap/column/my'));
		}

		$page = $this->request->param('page',1);
		$sort = $this->request->param('sort','new');
		$verify =  $this->request->param('verify',1);
		$data = ColumnModel::getMyColumnList($this->user_id,$sort,$verify,$page);
		$this->assign($data);
		$this->assign([
		    'sort'=>$sort,
            'verify'=>$verify
        ]);
		return $this->fetch();
	}
}