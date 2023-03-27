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
use app\logic\common\FocusLogic;
use app\model\UsersFavorite as FavoriteModel;
use think\App;

class Favorite extends Frontend
{
	public function __construct(App $app)
	{
		parent::__construct($app);
		if(!$this->user_id)
		{
			$this->redirect('/');
		}
		$this->model = new FavoriteModel();
	}

	//收藏标签
	public function index()
	{
		$type = $this->request->param('type','my');
		$this->assign('type',$type);
		return $this->fetch();
	}

	//收藏详情
	public function detail()
	{
		$id = $this->request->param('id',0);
		$info = db('users_favorite_tag')->where(['id'=>$id])->find();
		$info['focus'] = FocusLogic::checkUserIsFocus($this->user_id,'favorite',$info['id']);
		$this->assign($info);
		return $this->fetch();
	}

	//删除标签
	public function delete()
	{
		$id = $this->request->param('id',0);
		if(db('users_favorite')->where('tag_id',$id)->delete())
		{
            db('users_favorite_tag')->where(['id'=>$id])->delete();
			$this->success('删除成功');
		}
		$this->error('删除失败');
	}

	public function save_favorite()
    {
        if($this->request->isPost())
        {
            $is_public = $this->request->post('is_public',0);
            $title = $this->request->post('title','');
            if(!FavoriteModel::saveFavoriteTag($this->user_id, $title,$is_public))
            {
                $this->error('创建失败');
            }
            $this->success('创建成功');
        }
    }

    /**
     * 收藏
     * @param $item_type
     * @param $item_id
     * @return mixed
     */
    public function dialog($item_type, $item_id) {
        if ($this->request->isPost()) {
            $tag_id = $this->request->param('tag_id');
            if ($return = FavoriteModel::saveFavorite($this->user_id, $tag_id, $item_id, $item_type)) {
                $this->result($return, 1);
            }
            $this->result([], 0);
        }

        $favorite_list = FavoriteModel::getFavoriteTags($this->user_id);
        foreach ($favorite_list['list'] as $key => $value) {
            $favorite_list['list'][$key]['is_favorite'] = FavoriteModel::where(['item_type' => $item_type, 'item_id' => (int) $item_id, 'tag_id' => $value['id'], 'uid' => $this->user_id])->value('id');
        }
        $this->assign($favorite_list);
        $this->assign('item_type', $item_type);
        $this->assign('item_id', $item_id);
        return $this->fetch();
    }

    /**
     * 收藏列表
     * @return void
     */
    public function get_favorite_tags()
    {
        $page = $this->request->param('page',1);
        $data = FavoriteModel::getFavoriteTags($this->user_id,$page);
        $data['html'] = $this->fetch('',$data);
        return $this->apiResult($data);
    }

    public function get_favorite_content()
    {
        $id = $this->request->param('id',0);
        $page = $this->request->param('page',1);

        $data = FavoriteModel::getFavoriteListByTagId($this->user_id,$id,$page);
        $data['html'] = $this->fetch('ajax/lists',$data);
        return $this->apiResult($data);
    }
}