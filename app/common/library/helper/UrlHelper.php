<?php
namespace app\common\library\helper;

class UrlHelper
{
    public static  function getHostTopDomain()
    {
        $host = strtolower(request()->host());
        if (strpos($host, '/') !== false)
        {
            $parse = @parse_url($host);
            $host = $parse['host'];
        }
        $str = '';
        $top_level_domain_db = array('com', 'edu', 'gov', 'int', 'mil', 'net', 'org', 'biz', 'info', 'pro', 'name', 'coop', 'aero', 'xxx', 'idv', 'mobi', 'cc', 'me', 'jp', 'uk', 'ws', 'eu', 'pw', 'kr', 'io', 'us', 'cn');
        foreach ($top_level_domain_db as $v)
        {
            $str .= ($str ? '|' : '') . $v;
        }
        $matches = "[^\.]+\.(?:(" . $str . ")|\w{2}|((" . $str . ")\.\w{2}))$";
        if (preg_match('/' . $matches . '/is', $host, $match))
        {
            $domain = $match['0'];
        }else
        {
            $domain = $host;
        }
        return $domain;
    }
}