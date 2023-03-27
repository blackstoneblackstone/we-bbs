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
use think\template\TagLib;

class We extends Taglib
{
    // 标签定义
    protected $tags = [
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'question'            =>['attr' => 'name,uid,limit,sort,topic_ids,category_id,pjax,per_page', 'close' => 1],
        'article'            =>['attr' => 'name,uid,limit,sort,topic_ids,category_id,pjax,per_page', 'close' => 1],
        'sql'      => ['attr' => 'name,limit,sort,table,field,per_page,where,sql','close' => 1],
        'nav'      => ['attr' => 'name,type','close' => 1],
        'relation'=>['attr' => 'name,type,uid,limit,sort,topic_ids,category_id,pjax,per_page','close' => 1],
        'topic'=>['attr' => 'name,uid,limit,sort,pjax,per_page', 'close' => 1],
        'link'      => ['attr' => 'name','close' => 1],// 获取友情链接
        'focus'      => ['attr' => 'name,type,uid,limit,per_page','close' => 1],// 获取友情链接
    ];

    // 获取导航
    public function tagNav($tag, $content): string
    {
        $name = $tag['name'] ?: 'nav';
        $type = $tag['type']?:'nav';
        $key = $tag['key'] ?? 'key';
        $parse = '<?php ';
        $parse .= '$type="'.$type.'"';
        $parse .= '$__LIST__ = \app\common\library\helper\UserAuthHelper::instance()->getNav("",$type);';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 获取友情链接
    public function tagLink($tag, $content): string
    {
        $name = $tag['name'] ?: 'link';
        $parse = '<?php ';
        $parse .= '$__LIST__ = db(\'link\')->where(\'status\',1)->order(\'sort asc,id desc\')->select()->toArray();';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 获取问题列表
    public function tagQuestion($tag, $content): string
    {
        $uid = $tag['uid']??(getLoginUid()??0);
        $sort = $tag['sort'] ?? 'new';
        $limit    = $tag['limit'] ?? '0';
        $per_page    = $tag['per_page'] ?? '15';
        $name = $tag['name'] ?? 'v';
        $cache = $tag['cache'] ?? '0';
        $key = $tag['key'] ?? 'key';
        $topic_ids = $tag['topic_ids'] ?? 0;
        $pjax = $tag['pjax'] ?? 'wrapMain';
        $type = $tag['type'] ?? 'normal';
        $category_id = $tag['category_id'] ?? 0;
        $empty = $tag['empty'] ?? "<p class='text-center py-3 text-muted'><img src='/static/common/image/empty.svg'><span class='d-block'>".L('暂无内容')."</span></p>";
        $parse = '<?php ';
        $parse .= '$uid='.$uid.';';
        $parse .= '$type="'.$type.'";';
        $parse .= '$cache='.$cache.';';
        $parse .= '$pjax="'.$pjax.'";';
        $parse .= '$sort="'.$sort.'";';
        $parse .= '$limit='.$limit.';';
        $parse .= '$per_page='.$per_page.';';
        $parse .= '$topic_ids='.$topic_ids.';';
        $parse .= '$category_id='.$category_id.';';
        $page = $tag['page']??'page';
        $parse .= '$__DATA__ = \app\logic\common\TemplateTag::getQuestionList($uid,$sort, $topic_ids, $category_id,$limit,$per_page,$cache,$pjax,$type);';
        $parse .= '$__LIST__ = $__DATA__[\'list\'];';
        $parse.='$'.$page.' = $__DATA__[\'page\']??\'\';';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 获取文章列表
    public function tagArticle($tag, $content): string
    {
        $uid = $tag['uid']??(getLoginUid()??0);
        $sort = $tag['sort'] ?? 'new';
        $limit    = $tag['limit'] ?? '0';
        $per_page    = $tag['per_page'] ?? '15';
        $name = $tag['name'] ?? 'v';
        $cache = $tag['cache'] ?? '0';
        $topic_ids = $tag['topic_ids'] ?? "0";
        $key = $tag['key'] ?? 'key';
        $pjax = $tag['pjax'] ?? 'wrapMain';
        $category_id = $tag['category_id'] ?? "0";
        $page = $tag['page']??'page';
        $type = $tag['type'] ?? 'normal';
        $empty = $tag['empty'] ?? "<p class='text-center py-3 text-muted'><img src='/static/common/image/empty.svg'><span class='d-block'>".L('暂无内容')."</span></p>";
        $parse = '<?php ';
        $parse .= '$uid='.$uid.';';
        $parse .= '$cache='.$cache.';';
        $parse .= '$pjax="'.$pjax.'";';
        $parse .= '$type="'.$type.'";';
        $parse .= '$sort="'.$sort.'";';
        $parse .= '$limit='.$limit.';';
        $parse .= '$per_page='.$per_page.';';
        $parse .= '$topic_ids='.$topic_ids.';';
        $parse .= '$category_id='.$category_id.';';
        $parse .= '$__DATA__ = \app\logic\common\TemplateTag::getArticleList($uid,$sort, $topic_ids, $category_id,$limit,$per_page,$cache,$pjax,$type);';
        $parse .= '$__LIST__ = $__DATA__[\'list\'];';
        $parse.='$'.$page.' = $__DATA__[\'page\']??\'\';';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 获取话题列表
    public function tagTopic($tag, $content): string
    {
        $uid = $tag['uid']??(getLoginUid()??0);
        $sort = $tag['sort'] ?? 'new';
        $where = $tag['where'] ?? 'status=1';
        $limit    = $tag['limit'] ?? '0';
        $per_page    = $tag['per_page'] ?? '15';
        $name = $tag['name'] ?? 'v';
        $key = $tag['key'] ?? 'key';
        $pjax = $tag['pjax'] ?? 'wrapMain';
        $empty = $tag['empty'] ?? "<p class='text-center py-3 text-muted'><img src='/static/common/image/empty.svg'><span class='d-block'>".L('暂无内容')."</span></p>";
        $page = $tag['page']??'page';
        $parse = '<?php ';
        $parse .= '$uid='.$uid.';';
        $parse .= '$where="'.$where.'";';
        $parse .= '$pjax="'.$pjax.'";';
        $parse .= '$sort="'.$sort.'";';
        $parse .= '$limit='.$limit.';';
        $parse .= '$per_page='.$per_page.';';
        $parse .= '$__DATA__ = \app\logic\common\TemplateTag::getTopicList($uid,$where,$sort,$limit,$per_page,$pjax);';
        $parse .= '$__LIST__ = $__DATA__[\'list\'];';
        $parse.='$'.$page.' = $__DATA__[\'page\']??\'\';';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 获取聚合数据列表
    public function tagRelation($tag, $content): string
    {
        $uid = $tag['uid']??(getLoginUid()??0);
        $sort = $tag['sort'] ?? 'new';
        $limit    = $tag['limit'] ?? '0';
        $per_page    = $tag['per_page'] ?? '15';
        $type    = $tag['type'] ?? null;//内容类型
        $cache = $tag['cache'] ?? '0';
        $key = $tag['key'] ?? 'key';
        $name = $tag['name'] ?? 'v';
        $page = $tag['page']??'page';
        $topic_ids = $tag['topic_ids'] ?? 0;
        $pjax = $tag['pjax'] ?? 'wrapMain';
        $category_id = $tag['category_id'] ?? 0;
        $empty = $tag['empty'] ?? "<p class='text-center py-3 text-muted'><img src='/static/common/image/empty.svg'><span class='d-block'>".L('暂无内容')."</span></p>";

        $parse = '<?php ';
        $parse .= '$uid='.$uid.';';
        $parse .= '$cache='.$cache.';';
        $parse .= '$type="'.$type.'";';
        $parse .= '$sort="'.$sort.'";';
        $parse .= '$limit='.$limit.';';
        $parse .= '$per_page='.$per_page.';';
        $parse .= '$pjax="'.$pjax.'";';
        $parse .= '$topic_ids='.$topic_ids.';';
        $parse .= '$category_id='.$category_id.';';

        $parse .= '$__DATA__ = \app\logic\common\TemplateTag::getPostRelationList($uid,$type,$sort,$topic_ids,$category_id,$limit,$per_page,$cache,$pjax);';
        $parse .= '$__LIST__ = $__DATA__[\'list\'];';
        $parse.='$'.$page.' = $__DATA__[\'page\']??\'\';';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'" empty="'.$empty.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    // 万能标签
    public function tagSql($tag, $content): string
    {
        $sort = $tag['sort'] ?? '';
        $limit    = $tag['limit'] ?? '0';
        $per_page    = $tag['per_page'] ?? '15';
        $table    = $tag['table'] ?? '';
        $where    = $tag['where'] ?? '';
        $field    = $tag['field'] ?? '*';
        $sql    = $tag['sql'] ?? '';
        $key = $tag['key'] ?? 'key';
        $name = $tag['name'] ?? 'v';
        $parse = '<?php ';
        $parse .= '$table="'.$table.'";';
        $parse .= '$where="'.$where.'";';
        $parse .= '$sort="'.$sort.'";';
        $parse .= '$limit='.$limit.';';
        $parse .= '$per_page='.$per_page.';';
        $parse .= '$field="'.$field.'";';
        $parse .= '$sql="'.$sql.'";';
        $parse .= '$__DATA__ = \app\logic\common\TemplateTag::sqlFetch($table,$where,$sort,$sql,$per_page,$limit,$field);';
        $parse .= '$__LIST__ = $__DATA__[\'list\'];';
        $parse.='$page = $__DATA__[\'page\']??\'\';';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }

    //用户关注
    public function tagFocus($tag,$content): string
    {
        $uid = $tag['uid']??(getLoginUid()??0);
        $limit    = $tag['limit'] ?? '0';
        $per_page    = $tag['per_page'] ?? '15';
        $type    = $tag['type'] ?? '';
        $key = $tag['key'] ?? 'key';
        $name = $tag['name'] ?? 'v';
        $parse = '<?php ';
        $parse .= '$type="'.$type.'";';
        $parse .= '$limit='.$limit.';';
        $parse .= '$per_page='.$per_page.';';
        $parse .= '$uid='.$uid.';';
        $parse .= '$__DATA__ = \app\logic\common\TemplateTag::getUserFocus($uid,$type,$limit,$per_page);';
        $parse .= '$__LIST__ = $__DATA__[\'list\'];';
        $parse.='$page = $__DATA__[\'page\']??\'\';';
        $parse .= ' ?>';
        $parse .= '{volist name="__LIST__" id="' . $name . '" key="'.$key.'"}';
        $parse .= $content;
        $parse .= '{/volist}';
        return $parse;
    }
}