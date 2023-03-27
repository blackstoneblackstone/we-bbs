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
use app\common\library\helper\ImageHelper;
use app\common\library\helper\LogHelper;
use app\logic\common\FocusLogic;
use app\model\Answer;
use app\model\Article as ArticleModel;
use app\model\Column as ColumnModel;
use app\model\Draft;
use app\model\PostRelation;
use app\model\Question as QuestionModel;
use app\model\QuestionInvite;
use app\model\Report;
use app\model\Topic;
use app\model\Topic as TopicModel;
use app\model\Users;
use app\model\Users as UsersModel;
use app\model\UsersFavorite;
use app\model\Verify;
use app\model\Vote;
use think\exception\ValidateException;

class Ajax extends Frontend
{
    public function lists()
    {
        $item_type = $this->request->post('item_type');
        $sort = $this->request->post('sort','new');
        $category_id = $this->request->post('category_id',0,'intval');
        $topic_ids = $this->request->post('topic_ids',0);
        $data = PostRelation::getPostRelationList($this->user_id,$item_type,$sort,$topic_ids,$category_id,$this->request->param('page',1));
        $this->assign($data);
        if($sort=='focus')
        {
            $data['html'] = $this->fetch('ajax/focus');
            $this->apiResult($data);
        }

        $data['html'] = $this->fetch('ajax/lists');
        $this->apiResult($data);
    }

    //专栏列表
    public function columns()
    {
        $page = $this->request->param('page',1);
        $sort = $this->request->param('sort','new');
        $data = ColumnModel::getColumnListByPage($this->user_id,$sort,$page,get_setting('contents_per_page',15));
        $this->assign($data);
        $data['html'] = $this->fetch('ajax/columns');
        return $this->apiResult($data);
    }

    //投票操作
    public function set_vote()
    {
        $item_id = $this->request->post('item_id',0,'intval');
        $item_type = $this->request->post('item_type');
        $vote_value = intval($this->request->post('vote_value'));
        $result = Vote::saveVote($this->user_id, $item_id, $item_type, $vote_value);
        if (!$result) {
            $this->result([], 0, Vote::getError());
        }
        $this->result($result, 1, '操作成功');
    }

    /**
     * 收藏
     * @param $item_type
     * @param $item_id
     * @return mixed
     */
    public function favorite($item_type, $item_id) {
        if ($this->request->isPost()) {
            $tag_id = $this->request->param('tag_id');
            if ($return = UsersFavorite::saveFavorite($this->user_id, $tag_id, $item_id, $item_type)) {
                $this->result($return, 1);
            }
            $this->result([], 0);
        }

        $favorite_list = UsersFavorite::getFavoriteTags($this->user_id);
        foreach ($favorite_list['list'] as $key => $value) {
            $favorite_list['list'][$key]['is_favorite'] = UsersFavorite::where(['item_type' => $item_type, 'item_id' => (int) $item_id, 'tag_id' => $value['id'], 'uid' => $this->user_id])->value('id');
        }
        $this->assign($favorite_list);
        $this->assign('item_type', $item_type);
        $this->assign('item_id', $item_id);
        return $this->fetch();
    }

    /**
     * 不感兴趣
     */
    public function uninterested()
    {
        if(!$this->user_id){
            $this->error('请先登录');
        }

        $item_id = $this->request->post('id');
        $item_type = $this->request->post('type');
        if(db('uninterested')->where(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$this->user_id])->value('id'))
        {
            $this->error('您已进行过此操作');
        }

        if(db('uninterested')->insert(['item_id'=>$item_id,'item_type'=>$item_type,'uid'=>$this->user_id,'create_time'=>time()]))
        {
            $this->success('操作成功');
        }
        $this->error('操作失败');
    }

    /**
     * 举报
     * @param $item_type
     * @param $item_id
     * @return mixed
     */
    public function report($item_type, $item_id) {
        if ($this->request->isPost()) {
            $reason = $this->request->post('reason');
            $report_type = $this->request->post('report_type');
            if(!$reason || removeEmpty($reason)=='')
            {
                $this->error('请填写举报理由');
            }
            $result = Report::saveReport($item_id, $item_type, $report_type, $reason, $this->user_id);
            $this->success($result['msg']);
        }
        $this->assign('item_id', $item_id);
        $this->assign('item_type', $item_type);
        return $this->fetch();
    }

    //错误反馈
    public function report_bug()
    {
        if($this->request->isPost())
        {
            $msg = $this->request->post('msg');
            $from = $_SERVER['HTTP_REFERER'];
            $message = '来源页面：'.$from.'<br>错误信息：'.$msg;
            send_email(get_setting('report_bug_email'),'网站BUG反馈',$message);
            $this->success();
        }
    }

    /**
     * 更新关注
     */
    public function update_focus()
    {
        $item_id = $this->request->post('id',0,'intval');
        $item_type = $this->request->post('type');
        if (!$data = FocusLogic::updateFocusAction($item_id, $item_type, $this->user_id)) {
            $this->result([], 0, FocusLogic::getError());
        }
        $this->result($data, 1, '操作成功');
    }

    //用户列表
    public function get_people_list()
    {
        $page = $this->request->param('page',1);
        $sort = $this->request->param('sort','integral');
        if($page>5)
        {
            $page=5;
        }
        $order = [];
        $where[] =['status','=',1];
        switch ($sort)
        {
            //威望榜
            case 'reputation':
                $order['reputation']='DESC';
                $where[] = ['reputation','>',3];
                break;
            //活跃榜
            default :
                $order['integral']='DESC';
                $where[] = ['integral','>',500];
                break;
        }
        $data = UsersModel::getUserList($where,$order,$page,20,$this->user_id);
        $data['html'] = $this->fetch('',$data);
        return $this->apiResult($data);
    }

    /*获取话题列表*/
    public function get_topic_list()
    {
        $type = $this->request->param('type','new','trim');
        $pid = $this->request->param('pid',0,'intval');
        if($type=='discuss')
        {
            $order['discuss'] ='desc';
        }else if($type=='focus'){
            $order['focus'] ='desc';
        }else{
            $order['discuss_update'] ='desc';
        }

        $where[] = ['status','=',1];

        if($pid){
            $child_ids = TopicModel::getTopicWithChildIds($pid);
            $where[] = ['id','IN',$child_ids];
        }

        $list = db('topic')->where($where)->order($order)->paginate(
            [
                'list_rows'=> get_setting('contents_per_page',15),
                'page' => $this->request->param('page',1),
                'query'=>request()->param(),
            ]
        )->toArray();

        $data = $list;
        foreach ($list['data'] as $key => $value)
        {
            $list['data'][$key]['has_focus'] = FocusLogic::checkUserIsFocus($this->user_id, 'topic', $value['id']) ? 1 : 0;
            $list['data'][$key]['description'] = $value['description'] ? str_cut(strip_tags(htmlspecialchars_decode($value['description'])), 0, 45) : '';
        }
        $data['list'] = $list['data'];
        $data['html'] = $this->fetch('',$data);
        $this->apiResult($data);
    }

    /**
     * 获取邀请
     * @param $question_id
     * @return mixed
     */
    public function invite($question_id)
    {
        $data = QuestionInvite::getRecommendInviteUsers($this->user_id,$question_id);
        $data['question_id'] = $question_id;
        $this->assign($data);
        $this->assign('invite_list',QuestionInvite::getInvitedUsers($question_id));
        return $this->fetch();
    }

    public function invite_users()
    {
        if($this->request->isPost())
        {
            $question_id = $this->request->param('question_id');
            $name = $this->request->post('name', '');
            $page = $this->request->post('page');
            $where[] = ['uid', '<>', $this->user_id];

            $where[] = ['nick_name', 'like', "%" . $name . "%"];
            $where[] = ['status', '=', 1];
            $data = db('users')
                ->where($where)
                ->order(['reputation'=>'DESC'])
                ->field('nick_name,user_name,avatar,reputation,answer_count,question_count,uid')
                ->paginate([
                    'list_rows'=> 10,
                    'page' => $page,
                ])->toArray();

            foreach ($data['data'] as $key=>$val)
            {
                $data['data'][$key]['has_invite'] = 0;
                $data['data'][$key]['remark'] = '回答:'.$val['answer_count'].'  提问:'.$val['question_count'].'  威望:'.$val['reputation'];
                if( db('question_invite')->where(['sender_uid'=>$this->user_id,'recipient_uid'=>$val['uid'],'question_id'=>intval($question_id)])->value('id'))
                {
                    $data['data'][$key]['has_invite'] = 1;
                }
            }
            $data['question_id'] = $question_id;
            $this->assign($data);
            $data['html'] = $this->fetch('invite_users');
            $this->apiResult($data);
        }

    }

    /**
     * 保存问题邀请
     * @param $question_id
     */
    public function save_question_invite($question_id)
    {
        $invite_uid = $this->request->post('uid',0,'intval');
        $has_invite = $this->request->post('has_invite');

        if($invite_uid==$this->user_id)
        {
            $this->error('不可以邀请自己回答问题');
        }

        if(db('answer')->where(['question_id'=> $question_id, 'uid'=> $invite_uid])->value('id'))
        {
            $this->error('该用户已回答过该问题,不能继续邀请');
        }

        //验证用户积分是否满足积分操作条件
        if(!LogHelper::checkUserIntegral('INVITE_ANSWER',$this->user_id))
        {
            $this->error('您的积分不足,无法邀请用户回答问题');
        }

        if(!$question_info=QuestionModel::getQuestionInfo($question_id,'id,uid,title'))
        {
            $this->error('问题不存在或已被删除');
        }
        if(!QuestionModel::saveQuestionInvite($question_info,$this->user_id,$invite_uid))
        {
            $this->error('该用户已邀请过啦','',[]);
        }
        $this->success('操作成功','',['invite'=> (int)!$has_invite]);
    }

    /**
     * 认证类型
     * @return mixed
     */
    public function verify_type()
    {
        $type = $this->request->param('type');
        $where = ['verify_type' => $type];
        $info = db('users_verify')->where(['uid' => $this->user_id])->find();
        $result = $info ? json_decode($info['data'],true) : [];
        $result['type'] = $info ? $info['type'] : $type;
        $data = array(
            'keyList' => Verify::getConfigList($where),
            'info'=>$result
        );
        $this->assign('verify_info',$info);
        $this->assign($data);
        return $this->fetch();
    }

    /*私信弹窗*/
    public function inbox()
    {
        $user_name = $this->request->param('user_name','');
        $this->assign(['user_name'=>$user_name]);
        return $this->fetch();
    }

    /**
     * 回答编辑
     * @return mixed
     */
    public function editor()
    {
        $question_id = $this->request->param('question_id',0,'intval');
        $answer_id = $this->request->param('answer_id',0,'intval');
        $captcha_enable = 0;
        $error  = '';
        $answer_info =[];
        if($answer_id)
        {
            $answer_info = Answer::getAnswerInfoById($answer_id);
            $question_id = $answer_info['question_id'];
        }else{
            // 判断是否已回复过问题
            if ((get_setting('answer_unique') == 'Y') && db('answer')->where(['uid'=>$this->user_id,'question_id'=>intval($question_id),'status'=>1])->count())
            {
                $error = '一个问题只能回复一次，你可以编辑回复过的回复';
            }

        }
        $draft_info = Draft::getDraftByItemID($this->user_id,'answer');
        if(isset($draft_info['data']))
        {
            $answer_info['content'] = htmlspecialchars_decode($draft_info['data']['content']);
        }

        if(get_setting('publish_content_verify_time') && !Users::checkUserPublishTimeAndCount($this->user_id,'answer'))
        {
            $captcha_enable = 1;
        }

        $this->assign([
            'question_id'=>$question_id,
            'is_focus'=>FocusLogic::checkUserIsFocus($this->user_id,'question',$question_id),
            'access_key'=>md5($this->user_id.time()),
            'answer_id'=>$answer_id,
            'error'=>$error,
            'answer_captcha_enable'=>$captcha_enable,
            'answer_info'=>$answer_info
        ]);
        return $this->fetch();
    }

    //获取相关推荐
    public function get_relation_posts()
    {
        $type = $this->request->param('type');
        $id = $this->request->param('id',0,'intval');
        $page = $this->request->param('page',1,'intval');
        if($type=='article')
        {
            //获取相关文章
            $relation_posts = ArticleModel::getRelationArticleListByMobile($id,$page,get_setting('contents_per_page',15));
        }else{
            //获取相关问题
            $relation_posts = QuestionModel::getRelationQuestionByMobile($id,$page,get_setting('contents_per_page',15));
        }

        $relation_posts['html'] = $this->fetch('',['relation_posts'=>$relation_posts['data'],'type'=>$type]);
        $this->apiResult($relation_posts);
    }

    //获取专栏文章
    public function get_column_article()
    {
        $column_id = $this->request->param('id');
        $column_info = db('column')->where(['id'=>$column_id])->find();
        $page = $this->request->param('page');
        $sort = $this->request->param('sort','column');
        $order = $where = array();
        $where[] = ['status','=',1];
        $where[] = ['uid','=',$column_info['uid']];

        $order['set_top_time'] = 'DESC';
        $order['update_time'] = 'DESC';
        $order['create_time'] = 'DESC';

        if($sort=='column')
        {
            $where[] = ['column_id','=',$column_id];
        }

        if($sort=='other'){
            $where[] = ['column_id','<>',$column_id];
        }
        $_list = db('article')->where($where)->order($order)->paginate(
            [
                'list_rows'=> get_setting('contents_per_page'),
                'page' => $page,
            ]
        )->toArray();

        if($_list['data'])
        {
            $topic_infos = Topic::getTopicByItemIds(array_column($_list,'id'), 'article');
            foreach ($_list['data'] as $key => $val)
            {
                $_list['data'][$key]['user_info'] = Users::getUserInfo($val['uid'], true);
                $_list['data'][$key]['message'] = str_cut(strip_tags(htmlspecialchars_decode($val['message'])), 0, 120);
                $_list['data'][$key]['img_list'] =ImageHelper::srcList(htmlspecialchars_decode($val['message']));
                $_list['data'][$key]['vote_value'] = Vote::getVoteByType($val['id'],'article',$this->user_id);
                $_list['data'][$key]['topics'] = $topic_infos ? $topic_infos[$val['id']] : [];
            }
        }

        $_list['html'] = $this->fetch('',$_list);
        $this->apiResult($_list);
    }

    /**
     * 锁定话题
     */
    public function lock()
    {
        $id = $this->request->param('id');
        if($this->user_id && ($this->user_info['group_id']===1 || $this->user_info['group_id']===2))
        {
            if(Topic::lockTopic((int)$id,$this->user_id)){
                $this->success('操作成功');
            }
        }
        $this->error('您没有锁定话题权限');
    }

    /**
     * 获取话题列表
     */
    public function get_topic()
    {
        $item_type = $this->request->param('item_type');
        $item_id = $this->request->param('item_id',0,'intval');
        $keywords = $this->request->param('keywords');
        $cache_key = 'get_topic_q'.md5(trim($keywords).'_search_type'.$item_type.'_id'.$item_id);
        $topic_list = cache($cache_key);
        if(!$keywords)
        {
            $topic_list = [];
        }

        if(!$topic_list)
        {
            $where = 'status=1';
            if ($keywords) {
                $where .= " AND title regexp'".$keywords."'";
            }
            $topic_list = TopicModel::getTopic($where);
            foreach ($topic_list as $key=>$val)
            {
                $topic_list[$key]['is_checked'] = 0;
                if(TopicModel::checkTopicRelation($val['id'], $item_id, $item_type))
                {
                    $topic_list[$key]['is_checked'] = 1;
                }
            }
            cache($cache_key,$topic_list,60);
        }

        $this->apiResult($topic_list);
    }

    /**
     * 创建话题
     * @return mixed
     */
    public function create()
    {
        if(!$this->user_id)
        {
            $this->error('请先登录','account/login');
        }
        if ($this->user_info['permission']['create_topic_enable'] == 'N') $this->error('你没有创建话题权限');
        if($this->request->isPost())
        {
            $data = $this->request->except(['file'],'post');
            try {
                validate(\app\validate\Topic::class)->check($data);
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
            $result = TopicModel::saveTopic($data['title'],$this->user_id);
            if (!$result) {
                $this->error(TopicModel::getError());
            } else {
                $this->success('添加成功',url('index'),['id'=>$result,'title'=>$data['title']]);
            }
        }
    }


    /**
     * 发送短信
     */
    public function sms()
    {
        $mobile = $this->request->param('mobile','','intval');
        if(!$mobile)
        {
            return ['code'=>0,'msg'=>'请输入正确的手机号'];
        }
        $result = hook('sms',['mobile'=>$mobile]);
        if($result=='')
        {
            return ['code'=>0,'msg'=>'短信功能未启用'];
        }
        return json_decode($result,true);
    }

    /**
     * 保存草稿
     */
    public function save_draft()
    {
        if ($this->request->isPost()) {
            $item_id = $this->request->post('item_id',0,'intval');
            $item_type = $this->request->post('item_type');
            $data = $this->request->post('data');

            if($item_type!='answer')
            {
                if (empty($data) || !$data['title']) {
                    $this->error('保存草稿失败');
                }
            }else{
                if (empty($data) || !$data['content']) {
                    $this->error('保存草稿失败');
                }
            }

            $data['is_anonymous'] = isset($data['is_anonymous']) ? intval( $data['is_anonymous']) : 0;
            unset($data['__token__']);
            if (Draft::saveDraft($this->user_id, $item_type, $data, $item_id)) {
                $this->success('保存草稿成功');
            }
            $this->error('保存草稿失败');
        }
    }

}