<?php
namespace app\widget;
use app\common\controller\Widget;
use app\common\library\helper\ImageHelper;
use app\common\paginator\AWS;
use app\logic\common\FocusLogic;
use app\model\Answer;
use app\model\Category;
use app\model\PostRelation;
use app\model\Topic;
use app\model\Users;
use app\model\Vote;

//渲染小组件（可用于自定义内容调用公共样式）
class Render extends Widget
{
    //问答列表小组件
    public function questions($data=[],$sort = null, $topic_ids = null, $category_id = null,$per_page=10,$relation_uid=0,$theme='render/questions',$pjax='tabMain',$ids=[])
    {
        $page = $this->request->param('page',1);
        //已有数据进行渲染
        if($data){
            //已分页数据
            if(isset($data['list']) || isset($data['data']))
            {
                if(isset($data['data'])) $data['list'] = $data['data'];
                $this->assign($data);
            }else{
                //未进行分页的数据
                $total = count($data['list']);
                $result = AWS::make($data, intval($per_page), intval($page), $total, false, [
                    'path'=>$this->request->baseUrl(),
                    'list_rows'=> intval($per_page),
                    'query'=>$this->request->param(),
                    'category_id'=>$category_id,
                    'page' => intval($page),
                    'pjax'=>$pjax,
                ]);
                $this->assign([
                    'topic_id'=>$topic_ids,
                    'sort'=>$sort,
                    'category_id'=>$category_id,
                    'list'=>$data,
                    'total'=>$total,
                    'page'=>$result->render(),
                ]);
            }
            return $this->fetch($theme);
        }

        //根据条件查询渲染
        $uid = session('login_uid')?:0;

        //推荐内容
        if($sort=='recommend' && !$ids)
        {
            $data_list= PostRelation::getRecommendPost($uid,'question', $topic_ids, $category_id,$page, $per_page,$relation_uid,$pjax);
            $this->assign($data_list);
            return $this->fetch($theme);
        }

        $data_list = [];

        $order = $where = array();

        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];

        if($ids)
        {
            $where[] = ['id','IN',is_array($ids)?implode(',',$ids):$ids];
        }

        if($relation_uid)
        {
            $where[] = ['uid','=',$relation_uid];
        }

        if($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
        }

        if($sort=='hot')
        {
            $order['popular_value'] = 'desc';
            $where[] = ['popular_value','>',1];
        }

        if($sort=='new'){
            $order['set_top_time'] = 'DESC';
            $order['update_time'] = 'DESC';
        }

        $order['create_time'] = 'DESC';

        if ($topic_ids)
        {
            $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
            $topic_where =  'item_type="question" AND status=1' ;
            $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
            $relationInfo = db('topic_relation')
                ->whereRaw($topic_where.$topicIdsWhere)
                ->column('item_id,item_type');
            $item_ids = array_column($relationInfo,'item_id');
            $where[] = ['id','in',implode(',', $item_ids)];
        }

        if ($category_id)
        {
            $category_ids = Category::getCategoryWithChildIds($category_id,true);
            if($category_ids)
            {
                $where[] = ['category_id','in', implode(',',$category_ids )];
            }else{
                $where[] = ['category_id','=', $category_id];
            }
        }

        $list = db('question')->where($where)->order($order)->paginate(
            [
                'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                'page' => $page,
                'pjax'=>$pjax
            ]
        );
        $pageVar = $list->render();
        $allList = $list->toArray();
        $users_info = Users::getUserInfoByIds(array_column($allList['data'],'uid'),'user_name,avatar,nick_name,uid');
        $last_answers = Answer::getLastAnswerByIds(array_column($allList['data'],'id'));
        $topic_infos = Topic::getTopicByItemIds(array_column($allList['data'],'id'), 'question');
        $result_list = [];

        foreach ($allList['data'] as $key=>$data)
        {
            $result_list[$key] = $data;
            $result_list[$key]['title'] = htmlspecialchars_decode($result_list[$key]['title']);
            $result_list[$key]['answer_info'] = $last_answers[$data['id']] ?? false;
            if($result_list[$key]['answer_info']){
                $result_list[$key]['answer_info']['user_info'] = $users_info[$last_answers[$data['id']]['uid']] ?? '';
                $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($last_answers[$data['id']]['id'],'answer',$uid);
            }else{
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid,'question',$data['id']);
            }

            $detail = $result_list[$key]['answer_info'] ? $result_list[$key]['answer_info']['content'] : $result_list[$key]['detail'];
            $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
            $result_list[$key]['detail'] = $result_list[$key]['answer_info'] && isset($users_info[$last_answers[$data['id']]['uid']]) ?  ($result_list[$key]['answer_info']['is_anonymous'] ? '<a href="javascript:;" class="aw-username" >匿名用户</a> :' : '<a href="'.$result_list[$key]['answer_info']['user_info']['url'].'" class="aw-username" >'.$result_list[$key]['answer_info']['user_info']['nick_name'].'</a> :').str_cut(strip_tags($result_list[$key]['answer_info']['content']),0,150) : str_cut(strip_tags(htmlspecialchars_decode($result_list[$key]['detail'])),0,150);
            if($result_list[$key]['answer_info'] && !isset($users_info[$last_answers[$data['id']]['uid']]))
            {
                $result_list[$key]['answer_info'] = [];
            }
            $cover  = ImageHelper::srcList(htmlspecialchars_decode($detail));
            $result_list[$key]['img_list'] = $cover;
            $result_list[$key]['topics'] = $topic_infos[$data['id']] ?? [];
            $result_list[$key]['user_info'] = $users_info[$data['uid']];
        }
        $data_list['list'] = $result_list;
        $data_list['page'] = $pageVar;
        $data_list['total'] = $allList['last_page'];
        $this->assign($data_list);
        return $this->fetch($theme);
    }

    //文章列表小组件
    public function articles($data=[],$sort = null, $topic_ids = null, $category_id = null,$per_page=10,$relation_uid=0,$theme='render/articles',$pjax='tabMain',$ids=[])
    {
        $page = $this->request->param('page',1);
        //已有数据进行渲染
        if($data){
            //已分页数据
            if(isset($data['list']) || isset($data['data']))
            {
                if(isset($data['data'])) $data['list'] = $data['data'];
                $this->assign($data);
            }else{
                //未进行分页的数据
                $total = count($data['list']);
                $result = AWS::make($data, intval($per_page), intval($page), $total, false, [
                    'path'=>$this->request->baseUrl(),
                    'list_rows'=> intval($per_page),
                    'query'=>$this->request->param(),
                    'category_id'=>$category_id,
                    'page' => intval($page),
                    'pjax'=>$pjax,
                ]);
                $this->assign([
                    'topic_id'=>$topic_ids,
                    'sort'=>$sort,
                    'category_id'=>$category_id,
                    'list'=>$data,
                    'total'=>$total,
                    'page'=>$result->render(),
                ]);
            }
            return $this->fetch($theme);
        }

        $uid = session('login_uid')?:0;
        $order = $where = array();
        $order['set_top_time'] = 'DESC';
        $where[] = ['status','=',1];
        if($relation_uid)
        {
            $where[] = ['uid','=',$relation_uid];
        }

        //推荐内容
        if($sort=='recommend' && !$ids)
        {
            $data_list= PostRelation::getRecommendPost($uid,'article', $topic_ids, $category_id,$page, $per_page,$relation_uid,$pjax);
            $this->assign($data_list);
            return $this->fetch($theme);
        }

        if($ids)
        {
            $where[] = ['id','IN',is_array($ids)?implode(',',$ids):$ids];
        }

        if($sort=='unresponsive')
        {
            $where[] = ['answer_count','=',0];
        }

        if($sort=='hot')
        {
            $order['popular_value'] = 'desc';
            $where[] = ['popular_value','>',1];
        }

        if($sort=='new'){
            $order['set_top_time'] = 'DESC';
            $order['update_time'] = 'DESC';
        }

        $order['create_time'] = 'DESC';

        if ($topic_ids)
        {
            $topic_ids = is_array($topic_ids) ? $topic_ids : explode(',',$topic_ids);
            $topic_where =  'item_type="article" AND status=1' ;
            $topicIdsWhere = ' AND topic_id IN('.implode(',',array_unique($topic_ids)).')';
            $relationInfo = db('topic_relation')
                ->whereRaw($topic_where.$topicIdsWhere)
                ->column('item_id,item_type');
            $item_ids = array_column($relationInfo,'item_id');
            $where[] = ['id','in',implode(',', $item_ids)];
        }

        if ($category_id)
        {
            $category_ids = Category::getCategoryWithChildIds($category_id,true);
            if($category_ids)
            {
                $where[] = ['category_id','in', implode(',',$category_ids )];
            }else{
                $where[] = ['category_id','=', $category_id];
            }
        }

        $list = db('article')->where($where)->order($order)->paginate(
            [
                'list_rows'=> $per_page ? : get_setting('contents_per_page'),
                'page' => $page,
                'query'=>request()->param(),
                'pjax'=>$pjax
            ]
        );
        $pageVar = $list->render();
        $allList = $list->toArray();
        $users_info = Users::getUserInfoByIds(array_column($allList['data'],'uid'));
        $topic_infos = Topic::getTopicByItemIds(array_column($allList['data'],'id'), 'article');
        $result_list = [];
        foreach ($allList['data'] as $key=>$data)
        {
            $result_list[$key] = $data;
            $result_list[$key]['title'] = htmlspecialchars_decode($data['title']);
            $result_list[$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($data['message'])),0,120);
            $cover  = ImageHelper::srcList(htmlspecialchars_decode($data['message']));
            $result_list[$key]['img_list'] = $cover;
            $result_list[$key]['topics'] = $topic_infos[$data['id']] ?? [];
            $result_list[$key]['user_info'] = $users_info[$data['uid']] ?? ['url'=>'javascript:;','uid'=>0,'name'=>'未知用户'];
            $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'article',$uid);
        }

        $data_list['list'] = $result_list;
        $data_list['page'] = $pageVar;
        $data_list['total'] = $allList['last_page'];
        $this->assign($data_list);
        return $this->fetch($theme);
    }

    //万能自定义渲染
    public function render($table='',$where=[],$order=[],$sql='',$page=1,$limit=10,$field='*',$alias='a',$joinTable='',$joinWhere='',$union=[],$theme='render/render')
    {
        if(!$table && !$sql) return '';

        if($sql)
        {
            $data = db()->query($sql);
            $this->assign($data);
            return $this->fetch($theme);
        }

        if(!$table) return '';

        $db = db($table);

        if($alias)
        {
            $db->alias($alias);
        }

        if($joinTable && $joinWhere)
        {
            $db->join($joinTable,$joinWhere);
        }
        if($union)
        {
            $db->union($union);
        }

        if(is_string($where))
        {
            $db->whereRaw($where);
        }else{
            $db->where($where);
        }

        if(is_string($order))
        {
            $db->orderRaw($order);
        }else{
            $db->order($order);
        }

        $data = $db->field($field)->paginate(
            [
                'list_rows'=> $limit ? : get_setting('contents_per_page'),
                'page' => $page,
                'query'=>request()->param(),
            ]
        );

        $pageVar = $data->render();
        $allList = $data->toArray();
        $data_list['list'] = $allList['data'];
        $data_list['page'] = $pageVar;
        $data_list['total'] = $allList['last_page'];
        $this->assign($data_list);
        return $this->fetch($theme);
    }
}