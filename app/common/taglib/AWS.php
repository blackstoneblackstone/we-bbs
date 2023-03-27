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
namespace app\common\taglib;
use app\model\Question;
use think\template\TagLib;

class    AWS extends Taglib
{

	// 标签定义
	protected $tags = [
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'question'            =>['attr' => 'name,uid,limit,sort,topic_ids,category_id,pjax', 'close' => 0],
        'link'      => ['attr' => 'name','close' => 1],                                      // 获取友情链接
	];

    // 获取友情链接
    public function tagLink($tag, $content)
    {
        $name = $tag['name'] ? $tag['name'] : 'link';
        $parse = '<?php ';
        $parse .= '$__LIST__ = db(\'link\')->where(\'status\',1)->order(\'sort asc,id desc\')->select()->toArray();';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    public function tagQuestion($tag, $content)
    {
        $uid = $tag['uid']??session('login_uid');
        $sort = $tag['sort'] ?? 'new';
        $limit    = $tag['limit'] ?? '15';
        $name = $tag['name'] ?? 'v';
        $topic_ids = $tag['topic_ids'] ?? null;
        $pjax = $tag['pjax'] ?? 'wrapMain';
        $category_id = $tag['category_id'] ?? null;
        $parse = '<?php ';
        $parse .= '$__DATA__ = Question::getQuestionList('.$uid.','.$sort.', '.$topic_ids.', '.$category_id.',1,'.$limit.',0,'.$pjax.');';
        $parse .= '$__LIST__ = $__DATA__["list"];';
        $parse.='$page = $__LIST__["page"];';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

}

