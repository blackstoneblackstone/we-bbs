{extend name="$theme_block" /}
{block name="main"}
<style>
    .swiper-slide {width: auto!important}
    .nav-pills .nav-link {
        background: #eee;
        padding: 8px 14px;
        margin-right: 10px;
        font-size: 14px;
    }
</style>
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 bg-white mb-2">
            <nav>
                <div class="nav nav-tabs pl-4 pr-4 aw-pjax-a" role="tablist">
                    <a class="nav-item nav-link {if !$type || $type=='new'}active{/if}" data-pjax="pageMain" href="{:url('topic/index',['type'=>'new','pid'=>$pid])}">
                        {:L('最新')} </a>
                    <a class="nav-item nav-link {if $type=='focus'}active{/if}" data-pjax="pageMain" href="{:url('topic/index',['type'=>'focus','pid'=>$pid])}" >
                        {:L('关注最多')} </a>
                    <a class="nav-item nav-link {if $type=='discuss'}active{/if}" data-pjax="pageMain" href="{:url('topic/index',['type'=>'discuss','pid'=>$pid])}">
                        {:L('讨论最多')} </a>
                </div>
            </nav>
            <div id="tabMain">
                <div class="swiper-container mx-3 my-3" style="margin: 0">
                    <ul class="nav nav-pills n-nav swiper-wrapper" style="flex-wrap: nowrap;">
                        <li class="nav-item swiper-slide">
                            <a class="nav-link mb-0 {if !$pid}active c-active{/if}" data-k="0" data-pjax="pageMain" href="{:url('topic/index',['type'=>$type,'pid'=>0])}">{:L('全部话题')}</a>
                        </li>
                        {foreach $parent_list as $k => $v}
                        <li class="nav-item swiper-slide">
                            <a class="nav-link {if $pid==$v.id} active c-active{/if}" data-k="{$k}" data-pjax="pageMain" href="{:url('topic/index',['type'=>$type,'pid'=>$v.id])}">{$v.title}</a>
                        </li>
                        {/foreach}
                    </ul>
                </div>

                <div class="aw-overflow-hidden rounded">
                    <div class="aw-mod aw-topic-list">
                        <div class="row">
                            {volist name="list" id="v"}
                            <div class="col-md-6 mb-3">
                                <div class="px-3 py-2 rounded topic-item">
                                    <dl class="position-relative">
                                        <dt>
                                            <a href="{:url('topic/detail',['id'=>$v['id']])}">
                                                <div style='background-image:url("{$v.pic|default='/static/common/image/topic.svg'}") ;background-size: cover;width:64px;height:64px' class="rounded"></div>
                                            </a>
                                        </dt>
                                        <dd class="info position-relative">
                                            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
                                            <p class="mb-0 font-8 aw-one-line">{$v.description|raw}</p>
                                            <p class="font-8 text-muted position-absolute" style="bottom: 0">
                                                <span class="mr-3">{:L('正在讨论')}：{$v.discuss}</span>
                                                <span class="mr-3"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
                                                {if $user_id}
                                                <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> '.L('已关注').'</span>' : '<span><i class="icon-plus-circle text-primary"></i> '.L('关注').'</span>'}</a>
                                                {/if}
                                            </p>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                            {/volist}
                        </div>
                    </div>
                </div>
                {$page|raw}
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            {:widget('sidebar/writeNav')}
            {:widget('sidebar/focusTopic',['uid'=>$user_id])}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}
        </div>
    </div>
</div>
<script>
    let c_a = $('.c-active'),
        k = parseInt(c_a.data('k'))
    let navSwiper = new Swiper('.swiper-container', {
        speed: 600,
        grabCursor: true,
        slidesPerView: "auto",
        initialSlide: k,
        slidesPerGroup: 3
    })
</script>
{/block}