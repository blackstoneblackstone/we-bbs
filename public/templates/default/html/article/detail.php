{extend name="$theme_block" /}
{block name="meta_script"}
{if $theme_config['common']['enable_mathjax']=='Y'}
<style>
    .MathJax{outline:0;text-align: unset !important;}
</style>
<script async src="{$cdnUrl}/static/common/js/tex-mml-chtml.js"></script>
{/if}
{/block}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="col-md-1 text-center d-xs-none actions">
                <label class="px-1 py-2 bg-white rounded d-block mb-2">
                    <a href="javascript:;" class="aw-ajax-agree {$article_info['vote_value']==1 ? 'active' : ''}" onclick="AWS.User.agree(this,'article','{$article_info.id}');">
                        <i class="icon-thumb_up font-12"></i>
                        <span class="d-block">{$article_info['agree_count']}</span>
                    </a>
                </label>
                {if $user_id}
                {if $user_id!=$article_info['uid']}
                <label class="px-1 py-2 bg-white rounded d-block">
                    <a href="javascript:;"  style="color: #76839b" class="font-weight-bold aw-ajax-against {$article_info['vote_value']==-1 ? 'active' : ''}" onclick="AWS.User.against(this,'article','{$article_info.id}');">
                        <i class="icon-thumb_down font-12"></i>
                        <span class="d-block">{:L('反对')}</span>
                    </a>
                </label>
                <label class="px-1 py-2 bg-white rounded d-block"  onclick="AWS.User.report(this,'article','{$article_info.id}');">
                    <a href="javascript:;"  style="color: #76839b" class="font-weight-bold">
                        <i class="icon-warning font-12"></i>
                        <span class="d-block">{if $article_info['is_report']}{:L('已举报')}{else/}{:L('举报')}{/if}</span>
                    </a>
                </label>
                {/if}
                <label class="px-1 py-2 bg-white rounded d-block" onclick="AWS.User.favorite(this,'article','{$article_info.id}');">
                    <a href="javascript:;"  style="color: #76839b" class="font-weight-bold">
                        <i class="icon-star-outlined font-12"></i>
                        <span class="d-block">{if $article_info['is_favorite']}{:L('已收藏')}{else/}{:L('收藏')}{/if}</span>
                    </a>
                </label>
                {/if}

                <div class="dropdown px-1 py-2 bg-white rounded d-block mb-2">
                    <a href="javascript:;" style="color: #76839b" class="font-weight-bold" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-share"></i>
                        <span class="d-block">{:L('分享')}</span>
                    </a>
                    <div class="dropdown-menu p-0 border-0 font-size-sm">
                        <div class="text-center d-block py-2" style="min-width: 100px">
                            <a href="javascript:;"  class="dropdown-item aw-clipboard" data-clipboard-text="{:url('article/detail',['id'=>$article_info.id],true,true)}"><i class="icon-link"></i>
                                {:L('复制链接')}</a>
                            <a href="javascript:;" onclick="AWS.User.share('{$article_info.title}','{:url('article/detail',['id'=>$article_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="fab fa-weibo text-warning"></i>
                                {:L('新浪微博')}</a>
                            <a href="javascript:;" onclick="AWS.User.share('{$article_info.title}','{:url('article/detail',['id'=>$article_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="fab fa-qq text-primary"></i>
                                {:L('腾讯空间')}</a>
                            <div class="aw-qrcode-container" data-share="{:url('article/detail',['id'=>$article_info.id],true,true)}">
                                <a href="javascript:;" class="dropdown-item "><i class="fab fa-weixin text-success"></i>
                                    {:L('微信扫一扫')}</a>
                                <div class="aw-qrcode text-center py-2"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {if $user_id && ($user_id==$article_info['uid'] || isSuperAdmin() || isNormalAdmin())}
                <div class="dropdown px-1 py-2 bg-white rounded d-block mb-2">
                    <a href="javascript:;" class="font-weight-bold" style="color: #76839b" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon-more-horizontal"></i>
                        <span class="d-block">{:L('更多')}</span>
                    </a>
                    <div class="dropdown-menu p-0 border-0 font-size-sm">
                        <div class="text-center d-block py-2" style="min-width: 100px">
                            {if get_user_permission('recommend_post')=='Y'}
                            <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ajax.article/action',['type'=>'recommend','is_recommend'=>$article_info['is_recommend'],'article_id'=>$article_info['id']])}">
                                <span>{$article_info['is_recommend'] ? L('取消推荐') : L('推荐文章')}</span>
                            </a>
                            {/if}
                            {if get_user_permission('set_top_post')=='Y'}
                            <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ajax.article/action',['type'=>'set_top','set_top'=>$article_info['set_top'],'article_id'=>$article_info['id']])}">
                                <span>{$article_info['set_top'] ? L('取消置顶') : L('置顶文章')}</span>
                            </a>
                            {/if}

                            {if $user_id && (get_user_permission('modify_article')=='Y' || $user_info['uid']==$article_info['uid'])}
                            <a href="{:url('article/publish',['id'=>$article_info['id']])}" class="py-1 text-muted dropdown-item" target="_blank">
                                <span>{:L('编辑文章')}</span>
                            </a>
                            {/if}
                            {if $user_id && (get_user_permission('remove_article')=='Y' || $user_info['uid']==$article_info['uid'])}
                            <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-confirm="{:L('确定要删除吗')}？" data-url="{:url('ajax.article/remove_article',['id'=>$article_info['id']])}">
                                <span>{:L('删除文章')}</span>
                            </a>

                            {if !$article_info['column_id'] && ($user_info['uid']==$article_info['uid'] || isNormalAdmin() || isSuperAdmin())}
                            <a href="javascript:;" class="aw-ajax-open py-1 text-muted dropdown-item" data-title="{:L('收录至专栏')}" data-url="{:url('ajax.column/collect',['id'=>$article_info['id']])}">
                                <span>{:L('收录至专栏')}</span>
                            </a>
                            {/if}

                            {if isSuperAdmin() || isNormalAdmin()}
                            <a class="aw-ajax-open py-1 text-muted dropdown-item" data-title="{:L('添加到帮助')}" href="javascript:;" data-url="{:url('ajax.Help/select_chapter',['item_id'=>$article_info['id'],'item_type'=>'article'])}">
                                <span>{:L('添加到帮助')}</span>
                            </a>
                            {/if}

                            {/if}
                        </div>
                    </div>
                </div>
                {/if}

                <!--自定义文章页左侧拓展钩子-->
                {:hook('article_detail_page_left',['article_info'=>$article_info])}

            </div>
            <div class="col-md-8 px-0 col-sm-12 mb-1">
                <div class="bg-white p-3 aw-article-wrap rounded">
                    <!--文章内容主页面 详情顶部钩子-->
                    {:hook('article_detail_page_main_top',['article_info'=>$article_info])}
                    {:hook('pageDetailTop',['info'=>$article_info])}
                    <article class="aw-article">
                        <h2 class="font-14 mb-3">{if $article_info.set_top}
                            <i class="iconfont icon-zhiding text-warning font-14"></i>{/if}{:htmlspecialchars_decode($article_info.title)}
                            {:hook('extend_title_label',['area'=>'article_detail','info'=>$article_info])}
                        </h2>
                        <div class="aw-author-info mb-3">
                            <div class="aw-user overflow-hidden">
                                <dl class="overflow-hidden float-left mb-0">
                                    <dt class="float-left mr-2 mb-0">
                                        <a href="{$article_info['user_info']['url']}" class="aw-username" data-id="{$article_info.uid}">
                                            <img alt="{$article_info['user_info']['nick_name']}" src="{$article_info['user_info']['avatar']}" class="circle" style="width: 45px;height: 45px">
                                        </a>
                                    </dt>
                                    <dd class="float-left mb-0">
                                        <h6 class="mb-0">
                                            <a href="{$article_info['user_info']['url']}" class="aw-username" data-id="{$article_info.uid}">{$article_info['user_info']['nick_name']}</a>
                                            <small class="text-muted d-block mt-2">{$article_info['user_info']['signature']|default=L('这家伙很懒，还没有设置简介')}</small>
                                        </h6>
                                    </dd>
                                </dl>
                                <p class="float-right text-muted "><span>{$article_info.agree_count}</span>&nbsp;{:L('人点赞了该文章')} · {:L('%s 浏览',$article_info.view_count)}</p>
                            </div>
                        </div>
                        <div class="aw-content mt-3">
                            {$article_info.message|raw}
                        </div>
                    </article>

                    {if $attach_list}
                    {if get_plugins_config('paid_attach','enable')=='Y'}
                    {:hook('attachDetail',['info'=>$article_info,'page'=>'article','attach_list'=>$attach_list??[]])}
                    {else/}
                    <div class="bg-info-light aw-attach-list my-3 p-3">
                        {volist name="attach_list" id="v"}
                        <dl class="clearfix mb-0 py-1">
                            <dt class="float-left">
                                <p class="mb-1">{$v.name}</p>
                                <p class="text-muted font-8 mb-0">{:formatBytes($v.size)}</p>
                            </dt>
                            <dd class="float-right mb-0">
                                <a href="javascript:;" class="btn btn-primary btn-sm attach-download" data-name="{$v.auth_key}">{:L('下载')}</a>
                            </dd>
                        </dl>
                        {/volist}
                    </div>
                    {/if}
                    {/if}

                    <!--文章内容主页面 详情底部钩子-->
                    {:hook('article_detail_page_main_bottom',['article_info'=>$article_info])}

                    {:hook('pageDetailBottom',['info'=>$article_info])}

                    <div class="aw-article-bottom overflow-hidden mt-2 mb-2 text-muted py-1">
                        <p class="float-left publish-info ">{:L('发布于')} {:date_friendly($article_info['create_time'])}</p>
                        <div class="float-right">
                            <ul class="page-detail-topic d-inline" id="awTopicList">
                                {if !empty($article_info['topics'])}
                                {volist name="article_info['topics']" id="v"}
                                <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                                {/volist}
                                {/if}
                            </ul>
                            {if $user_id && (isSuperAdmin() || isNormalAdmin() || get_user_permission('edit_content_topic')=='Y' || $user_id == $article_info.uid)}
                            <a href="javascript:;" data-width="600px" class="aw-ajax-open d-inline" data-url="{:url('topic/select',['item_type'=>'article','item_id'=>$article_info['id']])}" data-title="{:L('编辑话题')}"><i class="icon-edit1"> </i></a>
                            {/if}
                        </div>
                    </div>
                    <div class="actions d-sm-none">
                        <label class="mr-3 mb-0">
                            <a href="javascript:;" class="{$article_info['vote_value']==1 ? 'active' : ''} aw-ajax-agree" onclick="AWS.User.agree(this,'article','{$article_info.id}');">
                                <i class="icon-thumb_up"></i> {:L('赞同')} <span class="agree-count">{$article_info['agree_count'] ? $article_info['agree_count'] : ''}</span>
                            </a>
                        </label>

                        <div class="dropdown d-inline-block mr-3">
                            <a href="javascript:;"  class="font-weight-bold" style="color: #76839b" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-share"></i> {:L('分享')}
                            </a>
                            <div class="dropdown-menu p-0 border-0 font-size-sm">
                                <div class="text-left d-block py-2" style="min-width: 100px">
                                    <a href="javascript:;"  class="dropdown-item aw-clipboard" data-clipboard-text="{:url('article/detail',['id'=>$article_info.id],true,true)}"><i class="icon-link"></i>
                                        {:L('复制链接')}</a>
                                    <a href="javascript:;" onclick="AWS.User.share('{$article_info.title}','{:url('article/detail',['id'=>$article_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="fab fa-weibo text-warning"></i>
                                        {:L('新浪微博')}</a>
                                    <a href="javascript:;" onclick="AWS.User.share('{$article_info.title}','{:url('article/detail',['id'=>$article_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="fab fa-qq text-primary"></i>
                                        {:L('腾讯空间')}</a>
                                    <div class="aw-qrcode-container" data-share="{:url('article/detail',['id'=>$article_info.id],true,true)}">
                                        <a href="javascript:;" class="dropdown-item "><i class="fab fa-weixin text-success"></i>
                                            {:L('微信扫一扫')}</a>
                                        <div class="aw-qrcode text-center py-2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {if $user_id}
                        {if $user_id!=$article_info['uid']}
                        <label class="mr-3 mb-0">
                            <a href="javascript:;" class="aw-ajax-against {$article_info['vote_value']==-1 ? 'active' : ''}" onclick="AWS.User.against(this,'article','{$article_info.id}');">
                                <i class="icon-thumb_down"></i> <span>{:L('反对')}</span>
                            </a>
                        </label>
                        <label class="mr-3 mb-0"  onclick="AWS.User.report(this,'article','{$article_info.id}');">
                            <a href="javascript:;">
                                <i class="icon-warning"></i> <span>{if $article_info['is_report']}{:L('已举报')}{else/}{:L('举报')}{/if}</span>
                            </a>
                        </label>
                        {/if}
                        <label class="mr-3 mb-0" onclick="AWS.User.favorite(this,'article','{$article_info.id}');">
                            <a href="javascript:;">
                                <i class="icon-star-outlined"></i> <span>{if $article_info['is_favorite']}{:L('已收藏')}{else/}{:L('收藏')}{/if}</span>
                            </a>
                        </label>
                        {/if}
                        {if $user_id && ($user_info['uid']==$article_info['uid'] || isSuperAdmin() || isNormalAdmin())}
                        <div class="dropdown px-1 py-2 bg-white rounded d-inline-block mb-2">
                            <a href="javascript:;" class="font-weight-bold" style="color: #76839b" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon-more-horizontal"></i> <span class="d-inline-block">{:L('更多')}</span>
                            </a>
                            <div class="dropdown-menu p-0 border-0 font-size-sm">
                                <div class="text-center d-block py-2" style="min-width: 100px">
                                    {if get_user_permission('recommend_post')=='Y'}
                                    <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ajax.article/action',['type'=>'recommend','is_recommend'=>$article_info['is_recommend'],'article_id'=>$article_info['id']])}">
                                        <span>{$article_info['is_recommend'] ? L('取消推荐') : L('推荐文章')}</span>
                                    </a>
                                    {/if}
                                    {if get_user_permission('set_top_post')=='Y'}
                                    <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-url="{:url('ajax.article/action',['type'=>'set_top','set_top'=>$article_info['set_top'],'article_id'=>$article_info['id']])}">
                                        <span>{$article_info['set_top'] ? L('取消置顶') : L('置顶文章')}</span>
                                    </a>
                                    {/if}

                                    {if $user_id && (get_user_permission('modify_article') || $user_info['uid']==$article_info['uid'])}
                                    <a href="{:url('article/publish',['id'=>$article_info['id']])}" class=" py-1 text-muted dropdown-item" target="_blank">
                                        <span>{:L('编辑文章')}</span>
                                    </a>
                                    {/if}
                                    {if $user_id && (get_user_permission('remove_article') || $user_info['uid']==$article_info['uid'])}
                                    <a href="javascript:;" class="ajax-get py-1 text-muted dropdown-item" data-confirm="{:L('确定要删除吗')}?" data-url="{:url('ajax.article/remove_article',['id'=>$article_info['id']])}">
                                        <span>{:L('删除文章')}</span>
                                    </a>
                                    {/if}

                                    {if !$article_info['column_id'] && ($user_info['uid']==$article_info['uid'] || isNormalAdmin() || isSuperAdmin())}
                                    <a href="javascript:;" class="aw-ajax-open py-1 text-muted dropdown-item" data-title="{:L('收录至专栏')}" data-url="{:url('ajax.column/collect',['id'=>$article_info['id']])}">
                                        <span>{:L('收录至专栏')}</span>
                                    </a>
                                    {/if}

                                    {if isSuperAdmin() || isNormalAdmin()}
                                    <a class="aw-ajax-open py-1 text-muted dropdown-item" data-title="{:L('添加到帮助')}" href="javascript:;" data-url="{:url('ajax.Help/select_chapter',['item_id'=>$article_info['id'],'item_type'=>'article'])}">
                                        <span>{:L('添加到帮助')}</span>
                                    </a>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        {/if}
                    </div>
                    <div class="font-9 mt-2 p-3 bg-light">
                        <h6 class="text-danger font-weight-bold font-11">{:L('免责声明')}:</h6>
                        <p class="text-muted">{:L('本文由')} <a href="{$article_info['user_info']['url']}">{$article_info['user_info']['nick_name']}</a>
                            {:L('原创发布于')} <a href="{$baseUrl}">{$setting['site_name']}</a> ，{:L('著作权归作者所有')}。</p>
                    </div>
                </div>
                <div class="bg-white mt-2 p-3 aw-article-comment-editor rounded">
                    {if $user_id}
                    <form method="post" action="{:url('comment/save_article_comment')}">
                        <input type="hidden" name="article_id" value="{$article_info.id}">
                        <input type="hidden" name="token" value="{:token()}" />
                        <input type="hidden" name="at_info" value="">
                        <input type="hidden" name="pid" value="0">
                        <textarea type="text" name="message" class="form-control commentMessage" rows="6" placeholder="{:L('写下您的评论吧')}..."></textarea>
                        <div class="overflow-hidden mt-3">
                            <div class="float-left aw-username" data-id="{$user_info.uid}">
                                <img src="{$user_info['avatar']|default='/static/plugin/aw-home/images/avatars/avatar-2.jpg'}" alt="{$user_info['nick_name']}" class="rounded" style="width: 16px;height: 16px">
                                <span>{$user_info['nick_name']}</span>
                            </div>
                            <div class="float-right">
                                <button type="button" class="aw-article-comment-submit btn btn-primary btn-sm">{:L('发布')}</button>
                            </div>
                        </div>
                    </form>
                    {else/}
                    <p class="text-center">{:L('登录一下,更多精彩内容等你发现，贡献精彩回答，参与评论互动')}</p>
                    <p class="text-center mt-2">{:L('去')} <a href="javascript:;" onclick="AWS.User.login();" class="mr-1 text-primary">{:L('登录')}</a>!
                        {:L('还没有账号?')}{:L('去')}<a <a href="javascript:;" onclick="AWS.User.login();" class="text-primary">{:L('注册')}</a></p>
                    {/if}
                </div>
                <div id="comment-container" class="rounded">
                    <div class="aw-mod bg-white px-3 mt-2 pt-3">
                        <div class="aw-mod-head mb-0 border-bottom clearfix pb-3">
                            <p class="float-left mod-head-title">{:L('全部')} <span class="aw-comment-count">{$article_info.comment_count}</span>{:L('条评论')}</p>
                            <div class="dropdown float-right">
                                <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block"></i><span>{$sort=='hot' ? L('热门排序'):L('默认排序')}</span>
                                </a>
                                <div class="dropdown-menu p-0 border-0 font-size-sm">
                                    <div class="text-center d-block py-2 aw-nav aw-dropdown-nav text-center aw-answer-sort" style="min-width: 100px">
                                        <div class="{$sort=='new' ? 'active':''} py-1"><a href="{:url('article/detail',['id'=>$article_info['id'],'sort'=>'new'])}" data-pjax="comment-container">{:L('默认排序')}</a> </div>
                                        <div class="{$sort=='hot' ? 'active':''} py-1"><a href="{:url('article/detail',['id'=>$article_info['id'],'sort'=>'hot'])}" data-pjax="comment-container">{:L('热门排序')} </a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="post-comments post" style="padding: 0;box-shadow: none">
                        <div id="article-comment-list" class="bg-white">
                            {if $comment_list}
                            {volist name="comment_list" id="v"}
                            <div class="p-3 mb-1 aw-article-comment-item" id="article-comment-{$v.id}">
                                <div class="clearfix pt-0 pb-2">
                                    <div class="user-details-card-avatar float-left" style="position: relative">
                                        <a href="{:get_user_url($v['uid'])}">
                                            <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
                                        </a>
                                    </div>
                                    <div class="user-details-card-name float-left ml-2">
                                        <a href="{$v['user_info']['url']}">{$v['user_info']['name']}</a> <br><span class="ml-0"> {:date_friendly($v['create_time'])} </span>
                                    </div>
                                </div>
                                <div class="articleCommentContent" style="padding-left: 45px;">
                                    <div class="aw-content">{$v.message|raw}</div>
                                    <div class="actions">
                                        <div class="font-8 mt-2 text-muted">
                                            <a href="javascript:;" class="text-muted aw-ajax-agree mr-3 {if $v['vote_value']==1}active{/if}" onclick="AWS.User.agree(this,'article_comment','{$v.id}')"><i class="icon-thumb_up"></i>{:L('点赞')} <span>{$v.agree_count}</span></a>
                                            {if $user_id}
                                            <a href="javascript:;" class="mr-3 text-muted article-comment-reply"  data-comment-id="{$v.id}" data-username="{$v['user_info']['nick_name']}" data-uid='{$v["uid"]}'> <i class="icon-reply"></i> {:L('回复')}</a>
                                            {if $user_id==$v['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                                            <a href="javascript:;" class="text-muted aw-ajax-get" data-confirm="{:L('确定要删除吗')}?" data-url="{:url('comment/remove_article_comment',['id'=>$v.id])}"> <i class="icon-delete mr-1"></i> {:L('删除')} </a>
                                            {/if}
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="replay-editor mt-2" style="display: none"></div>
                                    {if isset($v['childs']) && $v['childs']}
                                    <div class="article-comment-child px-3 bg-light rounded mt-2">
                                        {volist name="$v['childs']" id="v1"}
                                        <div class="p-3 mb-1 aw-article-comment-item border-bottom" style="border-color: #eee !important;" id="article-comment-{$v1.id}">
                                            <div class="user-details-card pt-0 pb-2">
                                                <div class="user-details-card-avatar" style="position: relative">
                                                    <a href="{:get_user_url($v['uid'])}">
                                                        <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['nick_name']}" style="width: 40px;height: 40px">
                                                    </a>
                                                </div>
                                                <div class="user-details-card-name">
                                                    <a href="{$v['user_info']['url']}">{$v['user_info']['nick_name']}</a> <span class="ml-0"> {:date('Y-m-d H:i',$v['create_time'])} </span>
                                                </div>
                                            </div>
                                            <p>{$v1.message|raw}</p>
                                            <div class="actions">
                                                <div class="font-9 mt-2">
                                                    <a href="javascript:;" class="text-muted aw-ajax-agree mr-3 {if $v1['vote_value']==1}active{/if}" onclick="AWS.User.agree(this,'article_comment','{$v1.id}')"><i class="icon-thumb_up"></i>
                                                        {:L('点赞')} <span>{$v1.agree_count}</span></a>
                                                    {if $user_id}
                                                    <a href="javascript:;" class="mr-3 text-muted article-comment-reply" data-username="{$v1['user_info']['user_name']}" data-comment-id="{$v1.id}" data-info='{:json_encode(["uid"=>$v1["uid"],"user_name"=>$v1["user_info"]["user_name"]])}'> <i class="icon-reply"></i>
                                                        {:L('回复')} </a>
                                                    {if $user_id==$v1['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                                                    <a href="javascript:;" class="text-muted aw-ajax-get" data-confirm="{:L('确定要删除吗')}?" data-url="{:url('comment/remove_article_comment',['id'=>$v1.id])}"> <i class="icon-delete mr-1"></i>{:L('删除')} </a>
                                                    {/if}
                                                    {/if}
                                                </div>
                                            </div>
                                            <div class="replay-editor mt-2" style="display: none"></div>
                                        </div>
                                        {/volist}
                                    </div>
                                    {/if}
                                </div>
                            </div>
                            {/volist}
                            {if $page_render}
                            <div class="p-3">{$page_render|raw}</div>
                            {/if}
                            {else/}
                            <p class="text-center py-3 text-muted ">
                                <img src="{$cdnUrl}/static/common/image/empty.svg">
                                <span class="d-block  ">{:L('暂无评论')}</span>
                            </p>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 px-xs-0">
                {:hook('sidebarTop')}
                
                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('write_nav',get_theme_setting('article_detail.sidebar_show_items'))}
                {:widget('sidebar/writeNav')}
                {/if}
                
                {if $article_info['column_id'] && $article_info['column_info']}
                <div class="r-box mb-2">
                    <div class="r-title">
                        <h4>{:L('已收录至专栏')}</h4>
                    </div>
                    <div class="block-content">
                        <dl class="overflow-hidden mb-0 pb-2 border-bottom">
                            <dt class="float-left">
                                <a href="{:url('column/detail',['id'=>$article_info['column_id']])}" data-pjax="pageMain">
                                    <img src="{$article_info['column_info']['cover']|default='/static/common/image/default-avatar.svg'}" class="rounded" width="40" height="40">
                                </a>
                            </dt>
                            <dd class="float-right" style="width:calc(100% - 50px)">
                                <a href="{:url('column/detail',['id'=>$article_info['column_id']])}" class="d-block aw-one-line" target="_blank">
                                    <strong>{$article_info['column_info']['name']}</strong>
                                </a>
                                <p class="mb-0 font-8 text-muted aw-one-line">{$article_info['description']['name']|default=L("暂无专栏介绍")}</p>
                            </dd>
                        </dl>
                        <div class="d-flex text-center pt-3 pb-3 text-muted">
                            <a href="javascript:;" class="flex-fill mb-0">
                                <dl class="mb-0">
                                    <dt>{$article_info['column_info']['post_count']}</dt>
                                    <dd>{:L('文章')}</dd>
                                </dl>
                            </a>
                            <a href="javascript:;" class="flex-fill mb-0">
                                <dl class="mb-0">
                                    <dt>{$article_info['column_info']['view_count']}</dt>
                                    <dd>{:L('浏览')}</dd>
                                </dl>
                            </a>
                            <a href="javascript:;" class="flex-fill mb-0">
                                <dl class="mb-0">
                                    <dt>{$article_info['column_info']['focus_count']}</dt>
                                    <dd>{:L('关注')}</dd>
                                </dl>
                            </a>
                        </div>
                    </div>
                </div>
                {/if}

                {if $relation_article && $theme_config['article_detail']['sidebar_show_relation_article']=='Y'}
                <div class="r-box mb-2 hot-topic">
                    <div class="r-title">
                        <h4>{:L('相关文章')}</h4>
                        <a href="{:url('article/index',['sort'=>'hot'])}" target="_blank"><label class="iconfont">&#xe660;</label></a>
                    </div>
                    <div>
                        {volist name="relation_article" id="v"}
                        <dl class="mb-0 py-2">
                            <dt class="d-block aw-one-line font-weight-normal">
                                <a href="{:url('article/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{:L('%s 浏览',$v.view_count)}</label>
                                <label class="mr-2 mb-0">{:L('%s 评论',$v['comment_count'])}</label>
                            </dd>
                        </dl>
                        {/volist}
                    </div>
                </div>
                {/if}

                {if $recommend_post && $theme_config['article_detail']['sidebar_show_recommend_post']=='Y'}
                <div class="r-box mb-2 hot-topic">
                    <div class="r-title">
                        <h4>{:L('推荐内容')}</h4>
                    </div>
                    <div >
                        {volist name="recommend_post" id="v"}
                        {if $v['item_type']=='article'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block aw-one-line font-weight-normal font-9">
                                <span class="bg-primary text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">{:L('文')}</span> <a href="{:url('article/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{:L('%s 浏览',$v.view_count)}</label>
                                <label class="mr-2 mb-0">{:L('%s 评论',$v['comment_count'])}</label>
                            </dd>
                        </dl>
                        {/if}
                        {if $v['item_type']=='question'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block aw-one-line font-weight-normal font-9">
                                <span class="bg-warning text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">{:L('问')}</span> <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{:L('%s 浏览',$v.view_count)}</label>
                                <label class="mr-2 mb-0">{:L('%s 关注',$v.focus_count)}</label>
                                <label class="mr-2 mb-0">{:L('%s 评论',$v['comment_count'])}</label>
                            </dd>
                        </dl>
                        {/if}
                        {/volist}
                    </div>
                </div>
                {/if}
                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('announce',get_theme_setting('article_detail.sidebar_show_items'))}
                {:widget('sidebar/announce')}
                {/if}

                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('focus_topic',get_theme_setting('article_detail.sidebar_show_items'))}
                {:widget('sidebar/focusTopic',['uid'=>$user_id])}
                {/if}

                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('hot_topic',get_theme_setting('article_detail.sidebar_show_items'))}
                {:widget('sidebar/hotTopic',['uid'=>$user_id])}
                {/if}

                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('column',get_theme_setting('article_detail.sidebar_show_items'))}
                {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
                {/if}

                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('hot_users',get_theme_setting('article_detail.sidebar_show_items'))}
                {:widget('sidebar/hotUsers',['uid'=>$user_id])}
                {/if}

                {if get_theme_setting('article_detail.sidebar_show_items') && in_array('diy_content',get_theme_setting('article_detail.sidebar_show_items'))}
                {$theme_config['home']['sidebar_diy_content']|raw|htmlspecialchars_decode}
                {/if}
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
{/block}