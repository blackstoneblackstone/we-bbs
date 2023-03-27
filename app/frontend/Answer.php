<?php
namespace app\frontend;
use app\common\controller\Frontend;
use app\common\library\helper\LogHelper;
use app\model\Answer as AnswerModel;
use think\facade\Db;

class Answer extends Frontend
{
    protected $needLogin = [
        'set_answer_best',
        'thanks',
        'force',
        'delete_answer'
    ];

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
        $answer_info = \app\model\Answer::getAnswerInfoById($answer_id);

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
            $thanks_count = db('answer_thanks')->where(['answer_id'=>$id])->count();
            db('answer')->where(['id'=>$id])->update(['thanks_count'=>$thanks_count]);
            LogHelper::addIntegralLog('THANKS_ANSWER',$id,'answer',$answer_uid);
            LogHelper::addIntegralLog('ANSWER_THANKS',$id,'answer',$this->user_id);
            $this->success('感谢成功');
        }
        $this->error('感谢失败');
    }

    /**
     * 折叠回答
     * @return void
     */
    public function force()
    {
        if(!$this->user_id){
            $this->error('请先登录');
        }

        $answer_id = $this->request->param('answer_id',0,'intval');
        if(AnswerModel::forceAnswer($answer_id,$this->user_id))
        {
            $this->success('操作成功');
        }
        $this->error('操作失败:'.AnswerModel::getError());
    }

    //删除回复
    public function delete_answer()
    {
        $answer_id = $this->request->param('answer_id',0);
        $answer_info = \app\model\Answer::getAnswerInfoById($answer_id);

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

        if(!AnswerModel::deleteAnswer($answer_id))
        {
            $this->error(AnswerModel::getError());
        }

        $this->success('删除成功','question/detail?id='.$answer_info['question_id']);
    }

}