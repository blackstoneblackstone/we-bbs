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

namespace app\logic\search\driver;
use app\common\library\helper\ImageHelper;
use app\logic\common\FocusLogic;
use app\model\UsersFavorite;
use app\model\Users;
use app\model\Answer;
use app\model\Article;
use app\model\Column;
use app\model\Question;
use app\model\Report;
use app\model\Topic;
use app\model\Vote;
use think\helper\Str;

class RegexpSearch
{
    /**
     * 聚合搜索
     * @param $keywords
     * @param string $type
     * @param int $uid
     * @param array $sort
     * @param int $page
     * @param int $per_page
     * @return array|false
     */
    public function search($keywords,string $type='',int $uid=0,array $sort=[],$page=1,$per_page=10)
    {
        $keywords = implode('|', $keywords);
        $page = $page>1 ? intval(intval($page)-1) : 0;
        $limit = $page * $per_page;
        if(!$type)
        {
            $search_type_list = db('search_engine')->where(['status'=>1,'search_engine'=>get_setting('search_handle','regexp')])->column('name,search_field,pk,union_sql');
            $sql = [];
            foreach ($search_type_list as $k=>$v) {
                $search_field = explode('|',$v['search_field']);
                $sql1 = [];
                foreach ($search_field as $k1=>$v1)
                {
                    $sql1[$k1] = $v1." regexp '".$keywords."'";
                }

                $sql[$k] = '('.db($v['name'])
                        ->whereRaw('status=1 AND ('.implode(' OR ',$sql1).')')
                        ->field($v['pk']." as id,".$v['union_sql'].",'".$v['name']."' as search_type")
                        ->order('create_time','DESC')
                        ->fetchSql()
                        ->select().')';
            }
            $union_sql =implode( ' UNION ',$sql);
            $searchResult = db()->query($union_sql.' LIMIT '.intval($limit).','.intval($per_page));
            $totalCount =  db()->query($union_sql);
        }else{
            $search_type = db('search_engine')->where(['name'=>trim($type),'status'=>1,'search_engine'=>get_setting('search_handle','regexp')])->field('name,search_field,pk,union_sql')->find();
            $search_field = explode('|',$search_type['search_field']);
            $sql1 = [];
            foreach ($search_field as $k1=>$v1)
            {
                $sql1[$k1] = $v1." regexp '".$keywords."'";
            }

            $sql = db($search_type['name'])
                    ->whereRaw('status=1 AND ('.implode(' OR ',$sql1).')')
                    ->field($search_type['pk']." as id,".$search_type['union_sql'].",'".$search_type['name']."' as search_type")
                    ->order('create_time','DESC')
                    ->fetchSql()
                    ->select();
            $searchResult = db()->query($sql.' LIMIT '.intval($limit).','.intval($per_page));
            $totalCount =  db()->query($sql);
        }
        $totalCount = count($totalCount);
        return $this->parseMixResult($keywords,$searchResult,$uid,$totalCount);
    }

    /**
     * 聚合解析
     * @param $keyword
     * @param $contents
     * @param int $uid
     * @param int $totalRow
     * @return array|false
     */
    public function parseMixResult($keyword,$contents,$uid=0,$totalRow=0)
    {
        if (empty($contents)) {
            return false;
        }

        $question_ids = $article_ids = $answer_ids = $question_infos = $article_infos = $answer_infos =  array();

        foreach ($contents as $key => $data)
        {
            switch ($data['search_type'])
            {
                case 'question':
                    $question_ids[] = $data['id'];
                    break;

                case 'article':
                    $article_ids[] = $data['id'];
                    break;

                case 'answer':
                    $answer_ids[] = $data['id'];
                    break;
            }
        }

        $topic_infos = array();

        if ($question_ids)
        {
            //$last_answers = Answer::getLastAnswerByIds($question_ids);
            $topic_infos['question'] = Topic::getTopicByItemIds($question_ids, 'question');
            $question_infos = Question::getQuestionByIds($question_ids);
        }

        if ($article_ids)
        {
            $topic_infos['article'] = Topic::getTopicByItemIds($article_ids, 'article');
            $article_infos = Article::getArticleByIds($article_ids);
        }

        if(array_unique($answer_ids))
        {
            $answer_infos = Answer::getAnswerInfoByIds($answer_ids);
        }

        $result_list = array();

        foreach ($contents as $key => $data)
        {
            if($data['search_type']=='question' && $question_infos && isset($question_infos[$data['id']]))
            {
                $result_list[$key] = $question_infos[$data['id']];
                $detail = $result_list[$key]['detail'];
                $detail = htmlspecialchars_decode($detail);
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
                if(is_array($keyword))
                {
                    foreach ($keyword as $q)
                    {
                        $detail = mb_chunk_split($q,strip_tags($detail),200);
                        $result_list[$key]['title'] = mb_chunk_split($q,$result_list[$key]['title'],100);
                    }
                }else{
                    $detail = mb_chunk_split($keyword,strip_tags($detail),200);
                    $result_list[$key]['title'] = mb_chunk_split($keyword,$result_list[$key]['title'],100);
                }

                //是否已回答
                $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$data['id'],'status'=>1])->value('id') : 0;

                //回答用户
                $answerUidLists = db('answer')->where(['question_id'=>$data['id'],'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
                $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];


                $result_list[$key]['detail'] = $detail;
                $cover = ImageHelper::srcList($detail);
                $result_list[$key]['img_list'] = $cover;
                $result_list[$key]['url'] = (string)url('question/detail',['id'=>$data['id']]);
                $result_list[$key]['is_favorite'] = UsersFavorite::checkFavorite($uid,'question',$data['id']);
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
                $result_list[$key]['search_type'] = 'question';
                $result_list[$key]['topics'] = $topic_infos['question'][$data['id']] ?? [];
                $result_list[$key]['user_info'] = Users::getUserInfoByUid($question_infos[$data['id']]['uid']);
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, 'question', $data['id']) ? 1 : 0;
            }
            elseif($data['search_type']=='article' && $article_infos  && isset($article_infos[$data['id']])){
                $result_list[$key] = $article_infos[$data['id']];
                $result_list[$key]['img_list'] = ImageHelper::srcList(htmlspecialchars_decode($article_infos[$data['id']]['message']));
                if(is_array($keyword)) {
                    foreach ($keyword as $q) {
                        $result_list[$key]['message'] = mb_chunk_split($q, strip_tags(htmlspecialchars_decode($result_list[$key]['message'])), 100);
                        $result_list[$key]['title'] = mb_chunk_split($q, $result_list[$key]['title'], 100);
                    }
                }else{
                    $result_list[$key]['message'] = mb_chunk_split($keyword, strip_tags(htmlspecialchars_decode($result_list[$key]['message'])), 100);
                    $result_list[$key]['title'] = mb_chunk_split($keyword, $result_list[$key]['title'], 100);
                }

                $result_list[$key]['search_type'] = 'article';
                $result_list[$key]['url'] = (string)url('article/detail',['id'=>$data['id']]);
                $result_list[$key]['is_favorite'] = UsersFavorite::checkFavorite($uid,'article',$data['id']);
                $result_list[$key]['is_report'] = Report::getReportInfo($data['id'],'article',$uid);
                $result_list[$key]['topics'] = $topic_infos['article'][$data['id']] ?? [];
                $result_list[$key]['user_info'] = Users::getUserInfoByUid($article_infos[$data['id']]['uid']);
                $result_list[$key]['column_info'] = $article_infos[$data['id']]['column_id'] ? Column::where(['verify'=>1])->column('name,cover,uid,post_count,join_count') : false;
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'article',$uid);
            }
            elseif($data['search_type'] =='users' && $user_info = Users::getUserInfoByUid($data['id']))
            {
                $result_list[$key] = $user_info;
                if(is_array($keyword)) {
                    foreach ($keyword as $q) {
                        $user_info['name'] = mb_chunk_split($q, $user_info['name'], 100);
                    }
                }else{
                    $user_info['name'] = mb_chunk_split($keyword, $user_info['name'], 100);
                }
                $result_list[$key]['name'] = $user_info['name'];
                $result_list[$key]['url'] =  get_user_url($user_info['uid']);
                $result_list[$key]['search_type'] = 'users';
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, 'user', $data['id']) ? 1 : 0;
            }
            elseif($data['search_type'] =='topic' && $topic_info = db('topic')->where(['id' => $data['id']])->find()){
                $result_list[$key] = $topic_info;
                if(is_array($keyword)) {
                    foreach ($keyword as $q) {
                        $topic_info['title'] = mb_chunk_split($q, $topic_info['title'], 100);
                        $topic_info['description'] = mb_chunk_split($q, strip_tags(htmlspecialchars_decode($topic_info['description'])), 100);
                    }
                }else{
                    $topic_info['title'] = mb_chunk_split($keyword, $topic_info['title'], 100);
                    $topic_info['description'] = mb_chunk_split($keyword, strip_tags(htmlspecialchars_decode($topic_info['description'])), 100);
                }

                $result_list[$key]['title'] = $topic_info['title'];
                $result_list[$key]['url'] = (string)url('topic/detail',['id'=>$data['id']]);
                $result_list[$key]['description'] = $topic_info['description'];
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, 'topic', $topic_info['id']) ? 1 : 0;
                $result_list[$key]['search_type'] = 'topic';
            }elseif($data['search_type']=='answer')
            {
                if($answer_infos && isset($answer_infos[$data['id']]))
                {
                    $answer_info = $answer_infos[$data['id']];
                    $question_id = $answer_info['question_id'];
                    $user_info = Users::getUserInfoByUid($answer_info['uid']);
                    $result_list[$key] = Question::getQuestionInfo($question_id,'title,id,detail,answer_count,set_top');

                    //是否已回答
                    $result_list[$key]['is_answer'] = $uid ? db('answer')->where(['uid'=>$uid,'question_id'=>$question_id,'status'=>1])->value('id') : 0;

                    //回答用户
                    $answerUidLists = db('answer')->where(['question_id'=>$question_id,'status'=>1])->order('agree_count','DESC')->limit(3)->column('uid');
                    $result_list[$key]['answer_users'] = $answerUidLists ? Users::getUserInfoByIds($answerUidLists,'user_name,avatar,nick_name,uid'):[];

                    $result_list[$key]['answer_info'] = $answer_infos[$data['id']];
                    $result_list[$key]['answer_info']['user_info'] = $user_info;
                    $result_list[$key]['url'] = (string)url('question/detail',['id'=>$result_list[$key]['id'],'answer'=>$result_list[$key]['answer_info']['id']]);
                    $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($data['id'],'answer',$uid);
                    $result_list[$key]['topics'] = $topic_infos['question'][$question_id] ?? [];

                    if(is_array($keyword)) {
                        foreach ($keyword as $q) {
                            $result_list[$key]['detail'] = '<a href="'.$user_info['url'].'" class="aw-username" >'.$user_info['nick_name'].'</a> :'.mb_chunk_split($q,strip_tags(htmlspecialchars_decode($answer_infos[$data['id']]['content'])),150);
                        }
                    }else{
                        $result_list[$key]['detail'] = '<a href="'.$user_info['url'].'" class="aw-username" >'.$user_info['nick_name'].'</a> :'.mb_chunk_split($keyword,strip_tags(htmlspecialchars_decode($answer_infos[$data['id']]['content'])),150);
                    }
                    $result_list[$key]['search_type'] = 'answer';
                    $result_list[$key]['img_list'] = ImageHelper::srcList(htmlspecialchars_decode($answer_infos[$data['id']]['content']));
                }
            }else{
                /*其他搜索类型从鼓励*/
                hook('searchParseActionList'.Str::title($data['search_type']),$data);
            }
        }
        return ['list'=>$result_list,'total'=>$totalRow];
    }
}