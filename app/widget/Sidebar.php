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

namespace app\widget;
use app\common\controller\Widget;
use app\model\Users;
use app\model\Topic;

/**
 * 侧边栏小部件
 * Class Sidebar
 */
class Sidebar extends Widget
{
    /**
     * 用户信息
     * @param $user_info
     * @param $uid
     * @return mixed
     */
	public function profile($user_info,$uid)
	{
		$this->assign('user_info',$user_info);
		$this->assign('uid',$uid);
		return $this->fetch('sidebar/profile');
	}

    /**
     * 当前登录用户信息
     * @return mixed
     */
	public function loginUser()
	{
		return $this->fetch('sidebar/login_user');
	}

    /**
     * 我感兴趣的话题
     * @param $uid
     * @return mixed
     */
    public function focusTopic($uid)
    {
        $topic_list = Topic::getFocusTopicByRand($uid);
        $this->assign('topic_list',$topic_list);
        return $this->fetch('sidebar/focus_topic');
    }

    /**
     * 热门话题
     * @param $uid
     * @param array $where
     * @param array $order
     * @param int $limit
     * @return mixed
     */
	public function hotTopic($uid,array $where=[],array $order=[],int $limit=5)
	{
		$topic_list = Topic::getHotTopics($uid,$where,$order,$limit);
		$this->assign('topic_list',$topic_list['data']);
		return $this->fetch('sidebar/hot_topic');
	}

    /**
     * 热门用户
     * @param int $uid
     * @param array $where
     * @param array $order
     * @param int $limit
     * @return mixed
     */
	public function hotUsers(int $uid=0, $where=[], $order=[], $limit=5)
    {
        $people_list = Users::getHotUsers($uid,$where,$order,$limit);
        $this->assign('people_list',$people_list);
        return $this->fetch('sidebar/hot_users');
    }

    /**
     * 快捷菜单
     * @return mixed
     */
    public function writeNav()
    {
        return $this->fetch('sidebar/write_nav');
    }

	//侧边分类列表
    public function category($sort='',$category='')
    {
        $where =  ['status'=>1,'type'=>'all','pid'=>0];
        $list = db('category')->where($where)->order('sort','DESC')->column('id,title,icon');
        if($list)
        {
            /*foreach ($list as $key=>$val)
            {
                $list[$key]['post_count'] = db('post_relation')->where(['category_id'=>$val['id'],'status'=>1])->count();
            }*/

            $this->assign([
                'list'=>$list,
                'sort'=>$sort,
                'category'=>$category,
                //'total'=>db('post_relation')->where(['status'=>1])->count()
            ]);
            return $this->fetch('sidebar/category');
        }
    }

    /**
     * 侧边热门专栏
     * @param $uid
     * @param string $sort
     * @param int $page
     * @param int $per_page
     * @return mixed
     */
    public function hotColumn($uid,string $sort='new',int $page=1,int $per_page=5)
    {
        $order = array();
        $where[] = ['verify','=',1];
        switch ($sort)
        {
            case 'new':
                $order['create_time'] = 'DESC';
                break;
            case 'hot':
                $order['view_count'] = 'DESC';
                break;
            case 'recommend':
                $order['view_count'] = 'DESC';
                $where[]=['recommend','=',1];
                break;
        }
        $list =  db('column')->where($where)->orderRaw('RAND()')->order($order)->page($page,$per_page)->select()->toArray();
        foreach ($list as $key => $value)
        {
            $list[$key]['description'] = str_cut(strip_tags(htmlspecialchars_decode($value['description'])),0,50) ;
        }
        $this->assign([
            'list'=>$list,
        ]);
        return $this->fetch('sidebar/column');
    }

    //公告
    public function announce($page=1,$per_page=3)
    {
        $announce_list =  db('announce')
            ->where(['status'=>1])
            ->order(['set_top_time'=>'DESC','sort'=>'DESC'])
            ->page($page,$per_page)
            ->select()
            ->toArray();

        if($announce_list)
        {
            foreach ($announce_list as $k=>$item) {
                $announce_list[$k]['message'] = str_cut(strip_tags(htmlspecialchars_decode($item['message'])),0,150);
            }
            $this->assign('announce_list',$announce_list);
            return $this->fetch('sidebar/announce');
        }
    }
}
