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

namespace app\common\library\helper;

class HtmlHelper
{
	/**
	 * 替换内容中的图片地址
	 * @param string $content 内容原始html
	 * @return string 返回替换后的内容
	 */
	public static function parseImgUrl(string $content,$url=''): string
    {
        $images = ImageHelper::srcList($content);
        $url = $url ?: request()->domain();
		if($images)
		{
            foreach ($images as $v) {
                if(!strstr( $v,'http') && !strstr( $v,'https'))
                {
                    $image_url = $url.$v;
                    $content = str_replace($v, $image_url, $content);
                }
            }
		}
		return $content;
	}

    public static function replaceVideo(string $content,string $url=''): mixed
    {
        preg_match_all('/<iframe.*?\/iframe>/i', $content, $match2);
        $url = $url ?: request()->domain();
        $video = '';
        if(!empty($match2))
        {

            foreach ($match2[0] as $v)
            {
                preg_match('/[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i', $v, $match);
                if(!empty($match))
                {
                    $video = $match[1];
                }
                if(!strstr( $video,'http') && !strstr( $video,'https'))
                {
                    str_replace($video,$url.$video,$content);
                }
            }
        }
        return str_replace('iframe','video',$content);
    }

    /**
     * 解析WEditor编辑器内容中单个视频地址
     * @param string $content
     * @param string $url
     * @return mixed|string
     */
    public static function parseVideoUrl(string $content,string $url=''): mixed
    {
        preg_match('/<iframe.*?\/iframe>/i', $content, $match2);
        $url = $url ?: request()->domain();
        $video = '';
        if(!empty($match2))
        {
            preg_match('/[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i', $match2[0], $match);
            if(!empty($match))
            {
                $video = $match[1];
            }
        }

        if(!strstr( $video,'http') && !strstr( $video,'https'))
        {
            $video = $url.$video;
        }
        return $video;
    }

    /**
     * 获取内容中的视频
     * @param $content
     * @return array|mixed
     */
    public static function parseVideo($content)
    {
        preg_match_all('/<video[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',$content,$matches);
        $video = [];
        if(!empty($matches))
        {
            $video = $matches[1];
        }
        return !empty($video) ? $video : [];
    }

    public static function replaceVideoUrl($content,$url='')
    {
        preg_match_all('/<video[^>]*src=[\'"]?([^>\'"\s]*)[\'"]?[^>]*>/i',$content,$matches);
        if(empty($matches))
        {
            return $content;
        }

        $video = $matches[1];
        $url = $url ?: request()->domain();
        if($video)
        {
            foreach ($video as $v) {
                if(!strstr( $v,'http') && !strstr( $v,'https'))
                {
                    $image_url = $url.$v;
                    $content = str_replace($v, $image_url, $content);
                }
            }
        }
        return $content;
    }
    /**
     * 提取内容中的图片并本地化处理
     * @param $content
     * @param $type
     * @param $uid
     * @param bool $convert 是否解码html
     * @return array|mixed|string|string[]
     */
    public static function fetchContentImagesToLocal($content,$type,$uid,bool $convert=false)
    {
        if(get_setting('download_local_enable')=='N')
        {
            return $content;
        }

        //远程图片下载到本地逻辑处理钩子
        hook('fetch_content_image_local',['content'=>$content,'type'=>$type,'uid'=>$uid]);

        $content = htmlspecialchars_decode($content);

        $images = ImageHelper::srcList($content);
        if(!is_array($images) )return $content;

        foreach ($images as  $v)
        {
            if((strpos($v,'http://')!==false || strpos($v,'https://')!==false) && strpos($v,request()->domain())===false)
            {
                $fileName = explode('/',$v);
                $fileName = end($fileName);
                $imgByte = HttpHelper::get($v);
                if($imgByte['code'])
                {
                    $file = $imgByte['data'];
                    $result = UploadHelper::instance()->setAccessKey(md5($uid.time()))->setUploadPath($type)->upload($uid,$fileName,$file);
                    if($result['code']) {
                        $content = str_replace($v, $result['url'], $content);
                    }
                }
            }
        }
        return htmlspecialchars($content);
    }

    /**
     * 远程下载图片到本地
     * @param $url
     * @param string|null $save_dir 目录名称
     * @param int $uid
     * @return string
     */
    public static function downloadImageToLocal($url,string $save_dir='common',int $uid=0)
    {
        if($url !="" )
        {
            $url=str_replace(['&amp;'],['&'],$url); //url中特定字符替换
            $ext = strrchr($url, '.');
            $mimes = array('.gif', '.jpg', '.png','.ico');
            if (!in_array($ext, $mimes)) {
                $ext = '.jpg';
            }

            $save_path = config('filesystem.disks.public.root').DS.$save_dir .DS.date('Ymd',time()).DS;

            if (!file_exists($save_path)) {
                FileHelper::mkDirs($save_path);
            }

            $filename_r = md5(date('YmdHis',time())).$ext;	//给图片命名
            $filename = $save_path.$filename_r;
            $file_url = config('filesystem.disks.public.url').'/'.$save_dir.'/'.date('Ymd',time()).'/'.$filename_r;
            $imgByte = HttpHelper::get($url);
            if($imgByte['data'])
            {
                FileHelper::createFile($filename,$imgByte['data']);
            }

            return is_file($filename) ? $file_url : '';
        }else{
            return false;
        }
    }
}