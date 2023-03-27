<?php

namespace app\logic\search\driver;

use app\common\library\helper\ImageHelper;
use app\logic\common\FocusLogic;
use app\model\Column;
use app\model\Question;
use app\model\Report;
use app\model\Users;
use app\model\UsersFavorite;
use app\model\Vote;
use think\helper\Str;

class ElasticSearch
{
    /**
     * 聚合搜索
     * @param $keywords
     * @param string $type
     * @param int $uid
     * @param array $sort
     * @param int $page
     * @param int $per_page
     */
    public function search($keywords,string $type='',int $uid=0,array $sort=[],$page=1,$per_page=10)
    {
        $keywords = $keywords[0];
        $ElasticSearch = new \app\logic\search\libs\ElasticSearch();
        if(!$type)
        {
            $search_field = '';
            $search_type_list = db('search_engine')->where(['status'=>1,'search_engine'=>get_setting('search_handle','ElasticSearch')])->column('name,search_field,pk');
            foreach ($search_type_list as $k=>$v) {
                $search_field.= $v['search_field'].'|';
            }
            $searchResult = $ElasticSearch->page($page)
                ->limit($per_page)
                ->search('',$keywords,array_unique(explode('|',rtrim($search_field,'|'))));
        }else{
            $search_type = db('search_engine')->where(['name'=>trim($type),'status'=>1,'search_engine'=>get_setting('search_handle','ElasticSearch')])->field('name,search_field,pk')->find();
            $search_field = explode('|',$search_type['search_field']);
            $searchResult = $ElasticSearch->page($page)
                ->limit($per_page)
                ->search($type,$keywords,array_unique($search_field));
        }
        $totalCount = $ElasticSearch->getCount();
        return $this->parseMixResult($keywords,$searchResult,$uid,$totalCount);
    }

    /**
     * 解析搜索数据
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
        $result_list = [];
        foreach ($contents as $key => $data)
        {
            if($data['search_type']=='question')
            {
                $result_list[$key] =$data;
                $detail = $result_list[$key]['detail'];
                $detail = htmlspecialchars_decode($detail);
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
                foreach ($keyword as $q)
                {
                    $detail = mb_chunk_split($q,strip_tags($detail),200);
                    $result_list[$key]['title'] = mb_chunk_split($q,$result_list[$key]['title'],100);
                }
                $result_list[$key]['detail'] = $detail;
                $cover = ImageHelper::srcList($detail);
                $result_list[$key]['img_list'] = $cover;
                $result_list[$key]['is_favorite'] = UsersFavorite::checkFavorite($uid,'question',$data['id']);
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'question',$uid);
                $result_list[$key]['search_type'] = 'question';
                $result_list[$key]['topics'] = $topic_infos['question'][$data['id']] ?? [];
                $result_list[$key]['user_info'] = Users::getUserInfo($data['uid']);
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, 'question', $data['id']) ? 1 : 0;
            }
            elseif($data['search_type']=='article'){
                $result_list[$key] = $data;
                $result_list[$key]['img_list'] = ImageHelper::srcList(htmlspecialchars_decode($data['message']));
                foreach ($keyword as $q)
                {
                    $result_list[$key]['message'] = mb_chunk_split($q,strip_tags(htmlspecialchars_decode($result_list[$key]['message'])),100);
                    $result_list[$key]['title'] = mb_chunk_split($q,$result_list[$key]['title'],100);
                }
                $result_list[$key]['search_type'] = 'article';
                $result_list[$key]['is_favorite'] = UsersFavorite::checkFavorite($uid,'article',$data['id']);
                $result_list[$key]['is_report'] = Report::getReportInfo($data['id'],'article',$uid);
                $result_list[$key]['topics'] = $topic_infos['article'][$data['id']] ?? [];
                $result_list[$key]['user_info'] = Users::getUserInfo($data['uid']);
                $result_list[$key]['column_info'] = $data['column_id'] ? Column::where(['verify'=>1])->column('name,cover,uid,post_count,join_count') : false;
                $result_list[$key]['vote_value'] = Vote::getVoteByType($data['id'],'article',$uid);
            }
            elseif($data['search_type'] =='users' && $user_info = Users::getUserInfo($data['id']))
            {
                $result_list[$key] = $data;
                foreach ($keyword as $q)
                {
                    $user_info['name'] = mb_chunk_split($q,$user_info['name'],100);
                }
                $result_list[$key]['name'] = $user_info['name'];
                $result_list[$key]['search_type'] = 'users';
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, 'user', $data['id']) ? 1 : 0;
            }
            elseif($data['search_type'] =='topic' && $topic_info = db('topic')->where(['id' => $data['id']])->find()){
                $result_list[$key] = $data;
                foreach ($keyword as $q)
                {
                    $topic_info['title'] = mb_chunk_split($q,$topic_info['title'],100);
                    $topic_info['description'] = mb_chunk_split($q,strip_tags(htmlspecialchars_decode($topic_info['description'])),100);
                }

                $result_list[$key]['title'] = $topic_info['title'];
                $result_list[$key]['description'] = $topic_info['description'];
                $result_list[$key]['has_focus'] = FocusLogic::checkUserIsFocus($uid, 'topic', $topic_info['id']) ? 1 : 0;
                $result_list[$key]['search_type'] = 'topic';
            }elseif($data['search_type']=='answer')
            {
                $question_id = $data['question_id'];
                $user_info = Users::getUserInfo($data['uid']);
                $result_list[$key] = Question::getQuestionInfo($question_id,'title,id,detail,answer_count,set_top');
                $result_list[$key]['answer_info'] = $data;
                $result_list[$key]['answer_info']['user_info'] = $user_info;
                $result_list[$key]['answer_info']['vote_value'] = Vote::getVoteByType($data['id'],'answer',$uid);
                $result_list[$key]['topics'] = $topic_infos['question'][$question_id] ?? [];
                $result_list[$key]['detail'] = '<a href="'.$user_info['url'].'" class="aw-username" >'.$user_info['nick_name'].'</a> :'.mb_chunk_split($keyword,strip_tags(htmlspecialchars_decode($data['content'])),150);
                $result_list[$key]['search_type'] = 'answer';
                $result_list[$key]['img_list'] = ImageHelper::srcList(htmlspecialchars_decode($data['content']));
            }else{
                /*其他搜索类型从鼓励*/
                hook('searchParseActionList'.Str::title($data['search_type']),$data);
            }
        }
        return ['list'=>$result_list,'total'=>$totalRow];
    }
}