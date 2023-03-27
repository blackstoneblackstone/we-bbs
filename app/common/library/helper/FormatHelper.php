<?php
namespace app\common\library\helper;

class FormatHelper
{
    /**
     * 是否包含外部链接
     * @param $str
     * @return bool
     */
    public static function outsideUrlExists($str): bool
    {
        $str = strtolower($str);

        if (strstr($str, 'http'))
        {
            preg_match_all('/(https?:\/\/[-a-zA-Z0-9@:;%_\+.~#?\&\/\/=!]+)/i', $str, $matches);
        }
        else
        {
            preg_match_all('/(www\.[-a-zA-Z0-9@:;%_\+\.~#?&\/\/=]+)/i', $str, $matches);
        }

        if ($matches)
        {
            foreach($matches as $key => $val)
            {
                if (!$val)
                {
                    continue;
                }

                if (!self::isInsideUrl($val[0]))
                {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 是否是内部链接
     * @param $url
     * @return bool
     */
    public static function isInsideUrl($url): bool
    {
        if (!$url)
        {
            return false;
        }

        if (preg_match('/^(?!http).*/i', $url))
        {
            $url = 'http://' . $url;
        }
        if (preg_match('/^(?!https).*/i', $url))
        {
            $url = 'https://' . $url;
        }

        $domain = UrlHelper::getHostTopDomain();
        if (preg_match('/^http[s]?:\/\/([-_a-zA-Z0-9]+[\.])*?' . $domain . '(?!\.)[-a-zA-Z0-9@:;%_\+.~#?&\/\/=]*$/i', $url))
        {
            return true;
        }
        return false;
    }

    /**
     * 自动过滤网址文本为超链接
     * @param $ret
     * @return string
     */
    public static function parseAutoLink($ret): string
    {
        $ret = ' ' . $ret;
        /*过滤a标签*/
        $ret = preg_replace_callback('#([\s>])([\w]+?://[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', function ($matches){
            $ret = '';
            $url = $matches[2];
            $url = str_replace('&nbsp;','',$url);
            if ( empty($url) )
                return $matches[0];
            if ( in_array(substr($url, -1), array('.', ',', ';', ':')) === true ) {
                $ret = substr($url, -1);
                $url = substr($url, 0, strlen($url)-1);
            }
            return $matches[1] . "<a href=\"$url\" rel=\"nofollow\" target=\"_blank\">$url</a>" . $ret;
        }, $ret);

        /*过滤ftp*/
        $ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]*)#is', function ($matches){
            $ret = '';
            $dest = $matches[2];
            $dest = str_replace('&nbsp;','',$dest);
            $dest = 'http://' . $dest;
            if ( empty($dest) )
                return $matches[0];
            if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
                $ret = substr($dest, -1);
                $dest = substr($dest, 0, strlen($dest)-1);
            }
            return $matches[1] . "<a href=\"$dest\" rel=\"nofollow\" target=\"_blank\">$dest</a>" . $ret;
        }, $ret);

        /*过滤Email*/
        $ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', function ($matches){
            $email = $matches[2] . '@' . $matches[3];
            return $matches[1] . "<a href=\"mailto:$email\">$email</a>";
        }, $ret);

        $ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
        return trim($ret);
    }
}