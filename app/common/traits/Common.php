<?php
namespace app\common\traits;

use app\common\library\helper\RenderHelper;

trait Common
{
    /**
     * javascript渲染文件列表
     * @var array
     */
    public static $scriptFile=[];

    /**
     * css渲染文件列表
     * @var array
     */
    public static $styleFile=[];

    public function TDK($title='',$keywords='',$description='')
    {
        $tdk = array(
            '_page_title' =>$title ? $title .' - '.get_setting('seo_title'): get_setting('seo_title'),
            '_page_keywords' => $keywords ?: get_setting('seo_keywords'),
            '_page_description' => $description ?: get_setting('seo_description'),
        );
        $this->assign($tdk);
    }

    /**
     * 加载js
     * @param array $script
     */
    public function script(array $script=[])
    {
        self::$scriptFile = array_merge(self::$scriptFile,$script);
        $scriptFile =$script ?  RenderHelper::script(self::$scriptFile) : '';
        $this->assign('_script',$scriptFile);
    }

    /**
     * 加载样式文件
     * @param array $style
     */
    public function style(array $style=[])
    {
        self::$styleFile = array_merge(self::$styleFile,$style);
        $styleFile = $style ? RenderHelper::style(self::$styleFile) : '';
        $this->assign('_style',$styleFile);
    }
}