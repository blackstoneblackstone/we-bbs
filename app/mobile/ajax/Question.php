<?php
namespace app\mobile\ajax;
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
        'remove_question',
        'manager',
        'delete_answer',
        'delete_comment',
        'save_answer',
        'delete_answer',
        'comment_vote',
        'remove_question',
        'set_answer_best',
        'thanks'
    ];
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

    /**
     * 保存回答
     */
    public function save_answer()
    {
        if($this->request->isPost())
        {
            $data = $this->request->post();
            $access_key = $data['access_key'];

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

            if(!$this->request->checkToken('__token__',$data))
            {
                $this->error('请不要重复提交');
            }

            session('__token__',null);

            unset($data['__token__'],$data['access_key']);

            $uid = $data['uid']??$this->user_id;

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

            if ($ret = Answer::saveAnswer($data,$access_key))
            {
                $ret['info']['vote_value'] = Vote::getVoteByType($ret['info']['id'],'answer',$this->user_id);
                $ret['info']['has_thanks'] = db('answer_thanks')->where(['answer_id'=>$ret['info']['id'],'uid'=>$this->user_id])->value('id') ? 1 : 0;
                $ret['info']['has_uninterested'] = db('uninterested')->where(['item_id'=>$ret['info']['id'],'item_type'=>'answer','uid'=>$this->user_id])->value('id')  ? 1 : 0;
                if($data['id'])
                {
                    $this->result(['answer_count'=>$ret['answer_count'],'id'=>$ret['info']['id']],1,'更新成功');
                }
                $this->result(['answer_count'=>$ret['answer_count'],'id'=>$ret['info']['id']],2,'回复成功');
            }
            $this->result([],0,Answer::getError());
        }
    }

    //删除回复
    public function delete_answer()
    {
        $answer_id = $this->request->param('answer_id',0);
        $answer_info = Answer::getAnswerInfoById($answer_id);

        if(!$answer_info){
            $this->error('回答不存在');
        }

        if(!$answer_info['status']){
            $this->error('回答已被删除');
        }

        if ($answer_info['uid']!=$this->user_id && $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->error('您没有删除回答的权限');
        }

        if(!Answer::deleteAnswer($answer_id))
        {
            $this->error(Answer::getError());
        }

        $this->success('删除成功');
    }


    /**
     * 喜欢回答
     */
    public function thanks()
    {
        if(!$this->user_id){
            $this->error('请先登录');
        }

        $id = $this->request->param('id',0);
        $answer_uid = db('answer')->where(['id'=>$id,'status'=>1])->value('uid');

        if(!$answer_uid)
        {
            $this->error('回答不存在');
        }

        if($answer_uid==$this->user_id)
        {
            $this->error('自己不能感谢自己');
        }

        if(db('answer_thanks')->where(['answer_id'=>$id,'uid'=>$this->user_id])->value('id'))
        {
            $this->error('您已感谢该回答');
        }

        if(db('answer_thanks')->insert(['uid'=>$this->user_id,'answer_id'=>$id,'create_time'=>time()]))
        {
            LogHelper::addIntegralLog('THANKS_ANSWER',$id,'answer',$answer_uid);
            LogHelper::addIntegralLog('ANSWER_THANKS',$id,'answer',$this->user_id);
            $this->success('感谢成功');
        }

        $this->error('感谢失败');
    }

    /**
     * 设置最佳回答
     */
    public function set_answer_best()
    {
        if(get_user_permission('set_best_answer')!='Y')
        {
            $this->error('您没有操作权限');
        }

        $answer_id = $this->request->param('answer_id',0);
        $answer_info = Answer::getAnswerInfoById($answer_id);

        if(!$answer_info)
        {
            $this->error('回答不存在');
        }

        if($answer_info['uid']==$this->user_id &&  $this->user_info['group_id']!=1 && $this->user_info['group_id']!=2)
        {
            $this->error('不可设置自己的回答为最佳答案');
        }

        if(db('answer')->where(['question_id'=>$answer_info['question_id'],'is_best'=>1])->count())
        {
            $this->error('最多只可设置一个最佳答案');
        }

        if(db('question')->where(['id'=>$answer_info['question_id']])->update(['best_answer'=>$answer_info['id']]))
        {
            db('answer')->where(['id'=>$answer_info['id']])->update(['is_best'=>1,'best_uid'=>$this->user_id,'best_time'=>time()]);
            //添加积分记录
            LogHelper::addIntegralLog('BEST_ANSWER',$answer_id,'answer',$answer_info['uid']);

            //$question_info = QuestionModel::getQuestionInfo($answer_info['question_id'],'title');

            //系统通知用户
            send_notify(0,$answer_info['uid'],'BEST_ANSWER','question',$answer_info['question_id']);

            $this->success('设置最佳答案成功',(string)url('question/detail',['id'=>$answer_info['question_id']]));
        }
        $this->error('设置最佳答案失败');
    }
}