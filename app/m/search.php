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
if (! defined('IN_ANWSION'))
{
    die();
}
class search extends AWS_MOBILE_CONTROLLER
{
    public function get_access_rule()
    {
        $rule_action['rule_type'] = 'black'; //黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
        $rule_action['actions'] = array();
        return $rule_action;
    }

    /*搜索*/
    public function index_action()
    {
        if (!is_mobile())
        {
            HTTP::redirect('/search/');
        }

        if ($_POST['q'])
        {
            HTTP::redirect('/m/search/q-' . base64_encode($_POST['q']));
        }

        $keyword = htmlspecialchars(base64_decode($_GET['q']));

        if($keyword)
        {
            $history_words = AWS_APP::cache()->get('history_words');
            $history_words = $history_words ? $history_words : array();
            $noIn = 0;

            foreach ($history_words as $key => $va) 
            {
                if ($keyword == $va['title'])
                {
                    $history_words[$key]['num'] = $va['num']+1;
                    $noIn = 1;
                }
            }

            if(!$noIn)
            {
                $k = count($history_words)+1;
                $history_words[$k]['title'] = $keyword;
                $history_words[$k]['num'] = 1;
            }

            AWS_APP::cache()->set('history_words', $history_words, 86400);
        }

        TPL::assign('keyword', $keyword);
        TPL::assign('split_keyword', implode(' ', $this->model('system')->analysis_keyword($keyword)));

        $hot_word_list = AWS_APP::cache()->get('history_words');

        $last_names = !empty($hot_word_list) ? array_column($hot_word_list,'num') : [];
        !empty($last_names) ? array_multisort($last_names,SORT_DESC,$hot_word_list) : [];
        TPL::assign('hot_word_list', $hot_word_list);
        TPL::output('m/search/index');
    }
}