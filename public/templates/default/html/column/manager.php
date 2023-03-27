{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="aw-left col-md-9 col-sm-12 px-0">
                <div class="aw-nav-container clearfix bg-white px-3">
                    <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
                        <li class="nav-item"><a class="nav-link {if $type=='recommend'}active{/if}" data-pjax="tabMain" href="{:url('column/manager',['type'=>'recommend','column_id'=>$column_id])}">{:L('推荐收录')}</a></li>
                        <!--<li class="nav-item"><a class="nav-link {if $type=='users'}active{/if}" data-pjax="tabMain" href="{:url('column/manager',['type'=>'users','column_id'=>$column_id])}">{:L('签约用户')}</a></li>-->
                    </ul>
                </div>
                <div id="tabMain" class="bg-white">
                    {if $type=='recommend'}
                    <div class="aw-common-list">
                        {if !empty($list)}
                        {foreach $list as $key=>$v}
                        <dl>
                            <dd>
                                <div class="n-title">
                                    {:hook('article_badge')}
                                    {if $v.set_top}
                                    <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                                    {/if}
                                    <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']|raw}</a>
                                </div>
                                <div class="pcon {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                                    {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                                    <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%"></div>
                                    <div class="ov-3 col-md-9">
                                        <div class="aw-three-line">
                                            {$v.message|raw}
                                        </div>
                                        {if $v['topics']}
                                        <div class="tags">
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
                                    {if $v['topics']}
                                    <div class="tags">
                                        {volist name="$v['topics']" id="topic"}
                                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                                        {/volist}
                                    </div>
                                    {/if}
                                    {/if}
                                </div>
                            </dd>
                            <dd class="clearfix">
                                <div class="float-left">
                                    <label class="dz">
                                        <a type="button" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['agree_count']}</span></a>
                                    </label>
                                    <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {$v['view_count']?:''}{:L('浏览')}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
                                    <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
                                    <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
                                </div>

                                {if $user_id && !$v['column_id'] && (isNormalAdmin() || isSuperAdmin() || $user_id==$column_info['uid'])}
                                <div class="float-right">
                                    <a href="javascript:;" class="btn btn-outline-primary btn-sm px-3" onclick="saveColumnRecommendArticle('{$v.id}')">{:L('收录')}</a>

                                    <a href="javascript:;" class="btn btn-outline-danger btn-sm px-3 ml-2" onclick="unSaveColumnRecommendArticle('{$v.id}')">{:L('拒绝')}</a>
                                </div>
                                {/if}
                            </dd>
                        </dl>
                        {/foreach}
                        {$page|raw}
                        {else/}
                        <p class="text-center py-3 text-muted">
                            <img src="{$cdnUrl}/static/common/image/empty.svg">
                            <span class="d-block">{:L('暂无内容')}</span>
                        </p>
                        {/if}
                    </div>
                    {/if}

                    {if $type=='users'}

                    {/if}
                </div>
            </div>
            <div class="aw-right col-md-3 col-sm-12">
                <!--侧边栏顶部钩子-->
                {:hook('sidebarTop')}

                <div class="aw-mod mb-2">
                    <div class="aw-mod-body">
                        <dl class="overflow-hidden mb-0 border-bottom bg-white p-3">
                            <dt class="float-left">
                                <a href="{$user_info['url']}">
                                    <img src="{$user_info['avatar']|default='/static/common/image/default-avatar.svg'}" width="45" height="45">
                                </a>
                            </dt>
                            <dd class="float-right" style="width:calc(100% - 60px)">
                                <a href="{$user_info['url']}" class="d-block">
                                    <strong>{$user_info['name']}</strong>
                                </a>
                                <p class="mb-0 font-8 text-muted aw-one-line">{$user_info['signature']}</p>
                            </dd>
                        </dl>
                        <div class="d-flex text-center bg-white p-3 text-muted">
                            <dl class="flex-fill mb-0">
                                <dt>{$user_info['answer_count']}</dt>
                                <dd>{:L('回答')}</dd>
                            </dl>
                            <dl class="flex-fill mb-0">
                                <dt>{$user_info['article_count']}</dt>
                                <dd>{:L('文章')}</dd>
                            </dl>
                            <dl class="flex-fill mb-0">
                                <dt>{$user_info['question_count']}</dt>
                                <dd>{:L('问题')}</dd>
                            </dl>
                        </div>
                        <div class="d-flex bg-white p-3 border-top">
                            <a href="{:url('column/apply')}" class="btn btn-sm btn-primary flex-fill mr-1">{:L('申请专栏')}</a>
                            <a href="{:url('column/my')}" class="btn btn-sm btn-outline-primary flex-fill ml-1">{:L('我的专栏')}</a>
                        </div>
                    </div>
                </div>
                {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
                <!--侧边栏底部钩子-->
                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
<script>
    function saveColumnRecommendArticle(articleId)
    {
        layer.confirm("{:L('是否收录该文章')}",{
            btn: ["{:L('确认')}", "{:L('取消')}"]
        }, function () {
            AWS.api.post("{:url('ajax.column/collect_recommend')}",{
                article_id:articleId,
                column_id:"{$column_id}",
                status:1
            });
        }, function(){
            layer.closeAll();
        });

    }

    function unSaveColumnRecommendArticle(articleId)
    {
        layer.confirm("{:L('是否拒绝收录该文章')}",{
            btn: ["{:L('确认')}", "{:L('取消')}"]
        }, function () {
            AWS.api.post("{:url('ajax.column/collect_recommend')}",{
                article_id:articleId,
                column_id:"{$column_id}",
                status:2
            });
        }, function(){
            layer.closeAll();
        });

    }
</script>
{/block}
