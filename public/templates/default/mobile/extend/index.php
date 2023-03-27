{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mt-1">
    <!--公告-->
    {:widget('sidebar/announce')}

    <!--手机拓展页面顶部钩子-->
    {:hook('mobileExtendTop')}

    <!--热门话题-->
    {if !empty($topic_list)}
    <div class="aui-card mb-1">
        <div class="aui-card-main" style="padding: 0;">
            <div class="aui-lists">
                <div class="aui-list">
                    <a href="{:url('topic/index')}" data-pjax="pageMain">
                        <div class="aui-list-left font-weight-bold">{:L('热门话题')}</div>
                        <div class="aui-list-right">
                            <span class="font-8 text-muted">{:L('去话题广场')}</span>
                            <i class="iconfont aui-btn-right font-8  iconright1"></i>
                        </div>
                    </a>
                </div>
                <div class="topicList swiper-container pt-3">
                    <div class="swiper-wrapper">
                        {volist name="topic_list" id="v"}
                        <div class="col-12 rounded mb-1 topic-item swiper-slide">
                            <dl>
                                <dt class="d-flex position-relative">
                                    <a href="{:url('topic/detail',['id'=>$v['id']])}" class="flex-fill mr-2" style="max-width: 60px">
                                        <img src="{$v['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'"  class="rounded"  width="60" height="60">
                                    </a>
                                    <div class="flex-fill" style="max-width: calc(100% - 80px)">
                                        <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
                                        <p class="mb-0 font-8 aw-one-line mt-1">{$v.description|raw}</p>
                                        <p class="font-8 text-muted position-absolute" style="bottom: 0.2rem">
                                            <span class="mr-3">{:L('讨论')}：{$v.discuss}</span>
                                            <span class="mr-3"><span class="aw-global-focus-count">{:L('关注')}：{$v.focus}</span></span>
                                        </p>
                                    </div>
                                </dt>
                                {if $v['relation_list']}
                                <dd class="info position-relative mt-2">
                                    {volist name="$v['relation_list']" id="v"}
                                    {if $v['item_type']=='article'}
                                    <dl class="mb-0 py-2">
                                        <dt class="d-block aw-one-line font-weight-normal font-9">
                                            <span class="bg-primary text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">{:L('文')}</span> <a href="{:url('article/detail',['id'=>$v['id']])}">{$v.title}</a>
                                        </dt>
                                        <dd class="mt-2 font-9 text-muted mb-0">
                                            <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
                                            <label class="mb-0">{$v['comment_count']} {:L('评论')}</label>
                                        </dd>
                                    </dl>
                                    {/if}
                                    {if $v['item_type']=='question'}
                                    <dl class="mb-0 py-2">
                                        <dt class="d-block aw-one-line font-weight-normal font-9">
                                            <span class="bg-warning text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">{:L('问')}</span> <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                                        </dt>
                                        <dd class="mt-2 font-9 text-muted mb-0">
                                            <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
                                            <label class="mr-2 mb-0">{$v.focus_count} {:L('关注')}</label>
                                            <label class="mr-2 mb-0">{$v['answer_count']} {:L('回答')}</label>
                                            <label class="mb-0">{$v['comment_count']} {:L('评论')}</label>
                                        </dd>
                                    </dl>
                                    {/if}
                                    {/volist}
                                </dd>
                                {/if}
                            </dl>
                        </div>
                        {/volist}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}
    <!--热门专栏-->
    {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot','per_page'=>get_setting('contents_per_page',15)])}

    <!--热门用户-->
    {:widget('sidebar/hotUsers',['uid'=>$user_id,'limit'=>get_setting('contents_per_page',15)])}

    <!--手机拓展页面底部钩子-->
    {:hook('mobileExtendBottom')}
</div>
{/block}