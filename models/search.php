<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/

if (!defined('IN_ANWSION'))
{
	die;
}

class search_class extends AWS_MODEL
{
	public function get_mixed_result($types, $q, $topic_ids, $page, $limit = 20, $is_recommend = false)
	{
		$types = explode(',', $types);
        $result = [];
		if (in_array('users', $types) AND !$is_recommend)
		{
			$result = array_merge((array)$result, (array)$this->search_users($q, $page, $limit));
		}

		if (in_array('titcks', $types) AND !$is_recommend)
		{
			$result = array_merge((array)$result, (array)$this->search_titcks($q, $page, $limit));
		}

		if (in_array('topics', $types) AND !$is_recommend)
		{
			$result = array_merge((array)$result, (array)$this->search_topics($q, $page, $limit));
		}

		if (in_array('questions', $types))
		{
            if(get_plugins_config('elasticsearch')['status']=='Y')
            {
                $question_content = $this->model('elasticsearch')->search($q,['question_content','question_detail'],$page,$limit,'question');
               // $question_detail = $this->model('elasticsearch')->search($q,'question_detail',$page,$limit,'question');
                $result =  $question_content ? array_merge((array)$result, (array)$question_content) : $result;
                //$result =  $question_detail ? array_merge((array)$result, (array)$question_detail) : $result;
            }else{
                $result = array_merge((array)$result, (array)$this->search_questions($q, $topic_ids, $page, $limit, $is_recommend));
            }
        }

		if (in_array('articles', $types))
		{
            if(get_plugins_config('elasticsearch')['status']=='Y')
            {
                $article_title = $this->model('elasticsearch')->search($q,['title','message'],$page,$limit,'article') ;
               // $article_message = $this->model('elasticsearch')->search($q,'message',$page,$limit,'article');
                $result =  $article_title ? array_merge((array)$result, (array)$article_title) : $result;
                //$result = $article_message ? array_merge((array)$result, (array)$article_message) : $result;

            }else{
                $result = array_merge((array)$result, (array)$this->search_articles($q, $topic_ids, $page, $limit, $is_recommend));
            }
		}
		return $result;
	}

	public function search_users($q, $page, $limit = 20)
	{
		if (is_array($q) AND sizeof($q) > 1)
		{
			$where[] = "user_name = '" . $this->quote(implode(' ', $q)) . "' OR user_name = '" . $this->quote(implode('', $q)) . "'";
		}
		else
		{
			if (is_array($q))
			{
				$q = implode('', $q);
			}

			$where[] = "user_name LIKE '%" . $this->quote($q) . "%'";
		}

		return $this->query_all('SELECT uid, last_login FROM ' . get_table('users') . ' WHERE (' . implode(' OR ', $where).') and is_del=0 order by user_name asc', calc_page_limit($page, $limit));
	}

	public function search_titcks($q, $page, $limit = 20)
	{
		if (is_array($q) AND sizeof($q) > 1)
		{
			$where[] = "t1.user_name = '" . $this->quote(implode(' ', $q)) . "' OR t1.user_name = '" . $this->quote(implode('', $q)) . "'";
		}
		else
		{
			if (is_array($q))
			{
				$q = implode('', $q);
			}

			$where[] = "t1.user_name LIKE '%" . $this->quote($q) . "%'";
		}

		$sql = 'SELECT uid, last_login FROM ' . get_table('users') . ' as t1 LEFT JOIN ' . get_table('users_group') . ' as t2 on t1.group_id = t2.group_id WHERE (' . implode(' OR ', $where).') and t1.is_del=0 and t2.type=2 and t2.custom=2 order by t1.user_name asc';
		return $this->query_all($sql, calc_page_limit($page, $limit));
	}

	public function search_topics($q, $page, $limit = 20)
	{
		if (is_array($q))
		{
			$q = implode('', $q);
		}

		if ($result = $this->fetch_all('topic', "topic_title LIKE '%" . $this->quote($q) . "%' OR topic_description LIKE '%" . $this->quote($q) . "%'", null, calc_page_limit($page, $limit)))
		{
			foreach ($result AS $key => $val)
			{
				if (!$val['url_token'])
				{
					$result[$key]['url_token'] = urlencode($val['topic_title']);
				}
			}
		}

		return $result;
	}

	public function search_questions($q, $topic_ids = null, $page = 1, $limit = 20, $is_recommend = false)
	{
        if(!$result = $this->model('question')->get_by_like($q, $page, $limit, $is_recommend))
        {
            $result = get_setting('enable_search_answer')=='Y' ? $this->search_question_answers($q, $limit) : [];
        }

        return $result;
	}

	public function search_articles($q, $topic_ids = null, $page = 1, $limit = 20, $is_recommend = false)
	{
        return $this->model('article')->get_by_like($q, $page, $limit, $is_recommend);
	}

    public function search_question_answers($q, $limit = 10)
    {
        return $this->model('question')->get_by_answer_like($q, $limit);
    }

	public function search($q, $search_type, $page = 1, $limit = 20, $topic_ids = null, $is_recommend = false)
	{
		if (!$q)
		{
			return false;
		}

		$q = (array)explode(' ', str_replace('  ', ' ', trim($q)));

		foreach ($q AS $key => $val)
		{
			if (strlen($val) == 1)
			{
				unset($q[$key]);
			}
		}

		if (!$q)
		{
			return false;
		}

		if (!$search_type)
		{
			$search_type = 'users,topics,questions,articles';
		}

		$result_list = $this->get_mixed_result($search_type, $q, $topic_ids, $page, $limit, $is_recommend);
		if ($result_list)
		{
			foreach ($result_list as $result_info)
			{
				$result = $this->prase_result_info($result_info,$q);
				if (is_array($result))
				{
					$data[] = $result;
				}
			}
		}

		return $data;
	}
  
	public function prase_result_info($result_info,$q)
	{
        if (isset($result_info['last_login']))
        {
            $result_type = 'users';

            $search_id = $result_info['uid'];

            $user_info = $this->model('account')->get_user_info_by_uid($result_info['uid'], true);

            $name = $user_info['user_name'];

            $url = get_js_url('/people/' . $user_info['uid']);

            $detail = array(
                'avatar_file' => htmlspecialchars_decode(get_avatar_url($user_info['uid'], 'mid')),	// 头像
                'signature' => $user_info['signature'],	// 签名
                'reputation' =>  $user_info['reputation'],	// 威望
                'agree_count' =>  $user_info['agree_count'],	// 赞同
                'thanks_count' =>  $user_info['thanks_count'],	// 感谢
                'fans_count' =>  $user_info['fans_count'],	// 关注数
            );
        }
        else if ($result_info['topic_id'])
        {
            $result_type = 'topics';
            $search_id = $result_info['topic_id'];
            $url = get_js_url('/topic/' . $result_info['topic_id']);
            $name = $result_info['topic_title'];
            $result_info['topic_description']=strip_tags(html_entity_decode(FORMAT::parse_bbcode($result_info['topic_description'])));
            $info = mb_chunk_split($q,$result_info['topic_description'],50);
            $detail = array(
                'topic_pic'=> get_topic_pic_url('max', $result_info['topic_pic']),
                'topic_id' => $result_info['topic_id'],	// 话题 ID
                'focus_count' => $result_info['focus_count'],
                'discuss_count' => $result_info['discuss_count'],	// 讨论数量
                'topic_description' => $info
            );
        }
        else if ($result_info['question_id'])
        {
            $result_type = 'questions';
            $question_info = $this->model('question')->get_question_info_by_id($result_info['question_id']);
            $result_info = array_merge($result_info,$question_info);
            if($result_info['answer_content'])
            {
                $result_info['answer_content'] = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", " ", strip_tags($result_info['answer_content']));
                $result_info['question_detail'] = $result_info['answer_content'];
            }

            $user_info = $this->model('account')->get_user_info_by_uid($result_info['published_uid'], true);
            $userinfo['user_name'] = $result_type == 'questions' ? $user_info['user_name'] : '';
            $userinfo['user_pic'] = $result_type == 'questions' ? get_avatar_url($user_info['uid'], 'mid') : '';
            $search_id = $result_info['question_id'];

            $url = get_js_url('/question/' . $result_info['question_id']);

            $name = $result_info['question_content'];
            $result_info['question_detail']=strip_tags(html_entity_decode(FORMAT::parse_bbcode($result_info['question_detail'])));
            $info = mb_chunk_split($q,$result_info['question_detail'],150);
            $detail = array(
                'best_answer' => $result_info['best_answer'],	// 最佳回复 ID
                'answer_count' => $result_info['answer_count'],	// 回复数
                'comment_count' => $result_info['comment_count'],
                'content' => $info,
                'focus_count' => $result_info['focus_count'],
                'agree_count' => $result_info['agree_count'],
                'add_time' => date_friendly($result_info['add_time']),
                'anonymous' => $result_info['anonymous']
            );
        }
        else if ($result_info['id'])
        {
            $result_type = 'articles';

            $search_id = $result_info['id'];
            $user_info = $this->model('account')->get_user_info_by_uid($result_info['uid'], true);
            $search_id = $result_info['id'];
            $userinfo['user_name'] = $result_type == 'articles' ? $user_info['user_name'] : '';
            $userinfo['user_pic'] = $result_type == 'articles' ? get_avatar_url($user_info['uid'], 'mid') : '';
            $url = get_js_url('/article/' . $result_info['id']);

            $name = $result_info['title'];
            $result_info['message']=strip_tags(html_entity_decode(FORMAT::parse_bbcode($result_info['message'])));
            $info=mb_chunk_split($q[0],$result_info['message'],150);
            $detail = array(
                'comments' => $result_info['comments'],
                'content' =>$info,
                'add_time' =>date_friendly($result_info['add_time']),
                'votes' =>$result_info['votes'],
                'views' => $result_info['views']
            );
        }
        else if ($result_info['_source']['type'] == 'questions')
        {

            $result_type = $result_info['_source']['type'];
            $search_id = $result_info['_source']['question_id'];
            $url = get_js_url('/question/' . $search_id);
            $name = $result_info['highlight']['question_content'][0]? :$result_info['_source']['question_content'];
            $detail = array(
                'uid' => $result_info['_source']['uid'],
                'add_time' => $result_info['_source']['add_time'],
                'avatar_file' => get_avatar_url($result_info['_source']['uid'], 'mid'),	// 头像
                'user_name' => $this->fetch_one('users','user_name','uid='.$result_info['_source']['uid']),
                'question_detail' => $result_info['_source']['question_detail'],
                'answer_count' => $result_info['_source']['answer_count'],	// 回复数
                'focus_count' => $result_info['_source']['focus_count'],
                'topic_info' => $this->model('topic')->get_topics_by_item_id($search_id, 'question'),
                'anonymous' => $this->fetch_one('question','anonymous','question_id = '.$search_id)
            );

            if ($result_type)
            {
                return array(
                    'uid' => $result_info['_source']['uid'],
                    'score' => 0,
                    'type' => $result_type,
                    'url' => $url,
                    'search_id' => $search_id,
                    'name' => $name,
                    'detail' => $detail
                );
            }
        }
        else if ($result_info['_source']['type'] == 'articles')
        {
            $result_type = $result_info['_source']['type'];
            $search_id = $result_info['_source']['article_id'];
            $url = get_js_url('/article/' . $search_id);
            $name = $result_info['highlight']['title'][0]? :$result_info['_source']['title'];
            $detail = array(
                'uid' => $result_info['_source']['uid'],
                'add_time' => $result_info['_source']['add_time'],
                'message' => $result_info['_source']['message'],
                'avatar_file' => get_avatar_url($result_info['_source']['uid'], 'mid'),	// 头像
                'user_name' => $this->fetch_one('users','user_name','uid='.$result_info['_source']['uid']),
                'comments' => $result_info['_source']['comments'],
                'views' => $result_info['_source']['views']
            );
            if ($result_type)
            {
                return array(
                    'uid' => $result_info['_source']['uid'],
                    'score' => 0,
                    'type' => $result_type,
                    'url' => $url,
                    'search_id' => $search_id,
                    'name' => $name,
                    'detail' => $detail
                );
            }
        }

        if ($result_type)
        {
            return array(
                'uid' => $result_info['uid'],
                'score' => $result_info['score'],
                'type' => $result_type,
                'url' => $url,
                'search_id' => $search_id,
                'name' => $name,
                'detail' => $detail
            );
        }
	}
}