{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2" id="wrapMain">
    {if $setting.enable_category=='Y'}
    {:widget('common/category',['type'=>'article','category'=>$category,'show_type'=>'list'])}
    {/if}
    <div class="container">
        <div class="row justify-content-between">
            <div class="aw-left radius col-md-9 bg-white mb-2">
                <div class="nav nav-tabs aw-pjax-a px-4" role="tablist">
                    <a class="nav-item nav-link {if $sort=='recommend'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'recommend','category_id'=>$category])}">{:L('推荐')}</a>
                    <a class="nav-item nav-link {if $sort=='new'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'new','category_id'=>$category])}">{:L('最新')}</a>
                    <a class="nav-item nav-link {if $sort=='hot'}active{/if}" data-pjax="wrapMain" href="{:url('article/index',['sort'=>'hot','category_id'=>$category])}">{:L('热门')}</a>
                </div>

                <div id="tabMain" class="tab-content" >
                    <div class="tab-pane fade show active">
                        <div class="aw-common-list">
                            {we:article sort="$sort" category_id="$category"}
                            <!--自定义内容列表页拓展钩子,可自定义内容页插入内如，如每多少条内容显示一条广告-->
                            {:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'article'])}
                            <dl>
                                <dd>
                                    <div class="n-title">
                                        <span class="tip-s2 badge badge-secondary">{:L('文章')}</span>
                                        {:hook('article_badge',$v)}
                                        {if $v.set_top}
                                        <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                        {/if}
                                        <a href="{:url('article/detail',['id'=>$v['id']])}" target="_blank">{$v['title']|raw}</a>
                                        {:hook('extend_title_label',['area'=>'article_list','info'=>$v])}
                                    </div>
                                    <div class="pcon {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                        {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                                        <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%"></div>
                                        <div class="ov-3 col-md-9">
                                            <div class="aw-three-line">
                                                {$v.message|raw}
                                            </div>
                                            {if isset($v['topics']) && !empty($v['topics'])}
                                            <div class="tags mt-1">
                                                {volist name="$v['topics']" id="topic"}
                                                <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                                {/volist}
                                            </div>
                                            {/if}
                                        </div>
                                        {else/}
                                        <div class="aw-three-line">
                                            {$v.message|raw}
                                        </div>
                                        {if isset($v['topics']) && !empty($v['topics'])}
                                        <div class="tags mt-1">
                                            {volist name="$v['topics']" id="topic"}
                                            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                            {/volist}
                                        </div>
                                        {/if}
                                        {/if}
                                    </div>
                                </dd>
                                <dd>
                                    <label class="dz">
                                        <a type="button" href="javascript:;" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['agree_count']}</span></a>
                                    </label>
                                    <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {:L('%s 浏览',$v.view_count)}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe601;</i> {:L('%s 评论',$v.comment_count)}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
                                    <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
                                </dd>
                            </dl>
                            {/we:article}
                        </div>
                        {$page|raw}
                    </div>
                </div>
            </div>
            <div class="aw-right radius col-md-3 px-xs-0">

                <!--侧边栏顶部钩子-->
                {:hook('sidebarTop')}

                {if $theme_config['article']['sidebar_show_items'] && in_array('write_nav',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/writeNav')}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('announce',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/announce')}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('focus_topic',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/focusTopic',['uid'=>$user_id])}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('hot_topic',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/hotTopic',['uid'=>$user_id])}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('column',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
                {/if}

                {if $theme_config['article']['sidebar_show_items'] && in_array('hot_users',$theme_config['article']['sidebar_show_items'])}
                {:widget('sidebar/hotUsers',['uid'=>$user_id])}
                {/if}

                {if in_array('diy_content',$theme_config['article']['sidebar_show_items'])}
                {$theme_config['article']['sidebar_diy_content']|raw|htmlspecialchars_decode}
                {/if}

                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
{/block}