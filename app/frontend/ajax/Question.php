<?php
namespace app\frontend\ajax;

use app\common\controller\Frontend;
use app\common\library\helper\FormatHelper;
use app\common\library\helper\IpHelper;
use app\common\library\helper\IpLocation;
use app\common\library\helper\LogHelper;
use app\model\Answer;
use app\model\Approval;
use app\model\Question as QuestionModel;
use app\model\Vote;

class Question extends Frontend
{
    protected $needLogin=[
        'lock_question',
        'redirect_content',
        'redirect_search',
        'cancel_redirect',
        'remove_question',
        'manager',
        'force_fold_answers',
        'save_answer'
    ];

    //锁定问题
    public function lock_question()
    {
        $question_id = $this->request->param('question_id',0,'intval');
        $question_info = \app\model\Question::getQuestionInfo($question_id);
        if(!$question_info) $this->error('问题不存在');
        if (!isSuperAdmin() && !isNormalAdmin() && get_user_permission('lock_question')!='Y') $this->error('您没有锁定问题的权限');

        if(db('question')->where(['id'=>$question_id])->update(['is_lock'=>1])){
            $this->success('问题已被锁定');
        }
        $this->error('问题锁定失败');
    }

    //内容重定向
    public function redirect_content()
    {
        if($this->request->isPost()){
            $item_id = $this->request->param('item_id',0,'intval');
            $target_id = $this->request->param('target_id',0,'intval');
            if(!$item_id || !$target_id) $this->error('请求参数不正确');
            if($item_id==$target_id) $this->error('不可重定向问题本身');

            if(db('question_redirect')->where([
                'item_id'=>$item_id,
                'target_id'=>$target_id,
            ])->value('id')) $this->error('该问题已被重定向啦...');

            db('question_redirect')->insert([
                'item_id'=>$item_id,
                'target_id'=>$target_id,
                'uid'=>$this->user_id,
                'create_time'=>time()
            ]);
            $this->success('重定向成功',url('question/detail',['id'=>$target_id,'rf'=>$item_id]));
        }

        $item_id = $this->request->param('item_id',0,'intval');
        $this->assign([
            'item_id'=>$item_id,
        ]);
        return $this->fetch();
    }

    //搜索重定向问题
    public function redirect_search()
    {
        $item_id = $this->request->param('item_id',0,'intval');
        $limit = $this->request->param('limit',5,'intval');
        $keywords = $this->request->param('keywords');
        $keywords=preg_replace('/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/', '', trim($keywords));
        $keywords = is_array($keywords) ? $keywords : explode(' ',$keywords);
        $sql[] = "title regexp '".implode('|', $keywords)."'";
        $sql[] = "id regexp '".implode('|', $keywords)."'";
        $questions = db('question')
            ->whereRaw('status=1 AND ('.implode(' OR ',$sql).')')
            ->where([['id','<>',$item_id]])
            ->limit($limit)
            ->field('title,id')
            ->select()
            ->toArray();

        $this->assign([
            'questions'=>$questions,
            'item_id'=>$item_id
        ]);
        return $this->fetch();
    }

    //取消重定向
    public function cancel_redirect()
    {
        $item_id = $this->request->param('item_id',0,'intval');
        if(db('question_redirect')->where([
            'item_id'=>$item_id,
        ])->delete())
        {
            $this->success('重定向撤销成功');
        }
        $this->error('重定向撤销失败');
    }

    //删除问题
    public function remove_question()
    {
        $id = $this->request->param('id');
        $question_info = QuestionModel::getQuestionInfo($id,'uid');

        if ($this->user_id !== $question_info['uid'] && get_user_permission('remove_question')!='Y')
        {
            $this->error('您没有删除问题的权限');
        }

        if (!QuestionModel::removeQuestion($id)) {
            $this->error('删除问题失败');
        }
        $this->success('删除问题成功',url('question/index'));
    }

    //问题管理操作
    public function manager()
    {
        $question_id = $this->request->param('id');
        $type=$this->request->param('type');

        if(!$question_id && !$type)
        {
            $this->error('请求参数不正确');
        }

        if(QuestionModel::manger($question_id,$type))
        {
            $this->success('操作成功');
        }

        $this->error(QuestionModel::getError());
    }

    //获取折叠回答列表
    public function force_fold_answers()
    {
        $question_id = $this->request->param('question_id',0,'intval');
        $question_info = QuestionModel::getQuestionInfo($question_id);
        if(!$question_info)  $this->result(['html'=>'']);
        $list = Answer::getForceFoldByQuestionId($question_id,intval($this->user_id));
        $this->assign([
            'list'=>$list,
            'question_info'=>$question_info
        ]);
        $this->result(['html'=>$this->fetch()]);
    }

    /**
     * 保存回答
     */
    public function save_answer()
    {
        if($this->request->isPost())
        {
            $data = $this->request->post();
            $access_key = $data['access_key'];
            /*回答提交前钩子*/
            hook('question_answer_post_before',$data);

            // 判断是否已回复过问题
            if ((get_setting('answer_unique') == 'Y') && db('answer')->where(['uid'=>$this->user_id,'question_id'=>intval($data['question_id'])])->count() && !$data['id'])
            {
                $this->error('一个问题只能回复一次，你可以编辑回复过的回复');
            }

            if(!$data['id'] && $this->user_info['permission']['publish_answer_enable']=='N')
            {
                $this->error('您没有发布回答的权限');
            }

            if(htmlspecialchars_decode($data['content'])=='' || removeEmpty($data['content'])=='')
            {
                $this->error('请输入回答内容');
            }

            if ($this->user_info['permission']['publish_url']=='N' && FormatHelper::outsideUrlExists($data['content'])) {
                $this->error('你所在的用户组不允许发布站外链接');
            }

            $answer_info = [];

            if($data['id'])
            {
                $answer_info = Answer::getAnswerInfoById($data['id']);
                if(!$answer_info)
                {
                    $this->error('回答不存在');
                }
            }

            if(!$this->request->checkToken())
            {
                $this->error('请不要重复提交');
            }

            unset($data['__token__']);
            unset($data['access_key']);
            $uid = $data['uid'] ?? $this->user_id;

            //验证用户积分是否满足积分操作条件
            if(!LogHelper::checkUserIntegral('ANSWER_QUESTION',$uid) && !$data['id'])
            {
                $this->error('您的积分不足,无法回答问题');
            }

            $question_info = QuestionModel::getQuestionInfo(intval($data['question_id']));
            if(!$question_info)
            {
                $this->error('问题不存在', '/');
            }

            $Ip = new IpLocation(); // 实例化类 参数表示IP地址库文件
            $data['uid'] = $data['id'] ? $answer_info['uid'] : $uid;
            $data['answer_user_ip'] = IpHelper::getRealIp();
            $data['answer_user_local'] = $Ip->getLocation(IpHelper::getRealIp())['country'];
            //发起回答审核
            if($this->publish_approval_valid(htmlspecialchars_decode($data['content']),'publish_answer_approval') && !$data['id'])
            {
                Approval::saveApproval('answer',$data,$uid,$access_key);
                $this->error('发起成功,请等待管理员审核', 'question/detail?id=' . $data['question_id']);
            }

            //修改回答审核
            if($this->publish_approval_valid(htmlspecialchars_decode($data['content']),'modify_answer_approval') && $data['id'])
            {
                Approval::saveApproval('modify_answer',$data,$uid,$access_key);
                $this->error('修改成功,请等待管理员审核', 'question/detail?id=' . $data['question_id']);
            }

            $data['is_anonymous'] = $data['is_anonymous'] ?? 0;
            $ret = Answer::saveAnswer($data,$access_key);

            if ($ret)
            {
                $ret['update'] = 0;
                $ret['question_info'] = $question_info;
                $ret['best_answer_count']=db('answer')->where(['question_id'=>$data['question_id'],'is_best'=>1])->count() ? 1 : 0;
                $ret['info']['vote_value'] = Vote::getVoteByType($ret['info']['id'],'answer',$this->user_id);
                $ret['info']['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$ret['info']['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
                $ret['info']['has_uninterested'] = db('uninterested')->where(['item_id'=>$ret['info']['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
                if($data['id'])
                {
                    $ret['update'] =1;
                    $this->result(['answer_count'=>$ret['answer_count'],'id'=>$ret['info']['id'],'html'=>$this->fetch('single_answer',$ret)],1,'更新成功');
                }
            }
            hook('question_answer_post_after',['post_data'=>$data,'result'=>$ret]);
            if($ret)
            {
                $this->result(['answer_count'=>$ret['answer_count'],'id'=>$ret['info']['id'],'html'=>$this->fetch('single_answer',$ret)],2,'回复成功');
            }
            $this->result([],0,Answer::getError());
        }
    }
}