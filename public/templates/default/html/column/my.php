{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            <div class="aw-left col-md-9 col-sm-12 px-0">
                <div class="aw-nav-container clearfix bg-white px-3">
                    <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
                        <li class="nav-item"><a class="nav-link {if $verify==1}active{/if}" data-pjax="tabMain" href="{:url('column/my',['verify'=>1])}">{:L('已审核')}</a></li>
                        <li class="nav-item"><a class="nav-link {if $verify==0}active{/if}" data-pjax="tabMain" href="{:url('column/my',['verify'=>0])}">{:L('待审核')}</a></li>
                        <li class="nav-item"><a class="nav-link {if $verify==2}active{/if}" data-pjax="tabMain" href="{:url('column/my',['verify'=>2])}">{:L('已拒绝')}</a></li>
                    </ul>
                </div>
                <div id="tabMain" class="p-3 bg-white">
                    {if !empty($list)}
                    <div class="my-column-list">
                        {volist name="list" id="v"}
                        <div class="column-item border-bottom">
                            <dl class="mb-0 clearfix py-2">
                                <dt class="float-left">
                                    <a href="{:url('column/detail',['id'=>$v['id']])}">
                                        <img src="{$v.cover}" alt="" width="70" height="70" class="rounded">
                                    </a>
                                </dt>
                                <dd class="float-right" style="width: calc(100% - 80px)">
                                    <h3 class="aw-one-line font-10 mb-0"> <a href="{:url('column/detail',['id'=>$v['id']])}">{$v.name}</a></h3>
                                    <p class="text-color-info my-1 font-9 aw-one-line">{$v.description}</p>
                                    <div class="font-9 clearfix">
                                        <div class="float-left">
                                            <a href="javascript:;" class="text-color-info"> {$v.post_count|num2string}
                                                {:L('内容')} </a><span class="text-color-info"> | </span>
                                            <a href="javascript:;" class="text-color-info"> {$v.focus_count|num2string}
                                                {:L('关注')} </a>
                                        </div>

                                        <div class="float-right font-9">
                                            <div class="dropdown show d-inline-block">
                                                <a href="javascript:;" class="d-none-arrow text-color-info" data-toggle="dropdown" ><i class="icon-more-horizontal"></i></a>
                                                <div class="dropdown-menu detail-more-dropdown text-center">
                                                    <span class="arrow"></span>
                                                    {if $verify==1}
                                                    <a href="{:url('article/publish',['column_id'=>$v.id])}" target="_blank" class="dropdown-item">{:L('发文')}</a>
                                                    {/if}
                                                    <a href="{:url('column/apply',['id'=>$v.id])}" class="dropdown-item">{:L('编辑')}</a>
                                                    <a href="{:url('column/manager',['column_id'=>$v.id])}" class="dropdown-item">{:L('管理')}</a>
                                                    <a href="javascript:;" data-url="{:url('ajax.column/delete',['id'=>$v.id])}" class="dropdown-item">{:L('删除')}</a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </dd>
                            </dl>
                        </div>
                        {/volist}
                    </div>
                    {$page|raw}
                    {else/}
                    <p class="text-center p-3 text-color-info">
                        <img src="{$cdnUrl}/static/common/image/empty.svg">
                        <span class="mt-3 d-block ">{:L('暂无记录')}</span>
                    </p>
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
                                <p class="mb-0 font-8 text-muted aw-one-line">{$user_info['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
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
{/block}
