{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('消息')}</div>
    <a class="aui-header-right" href="{:url('setting/notify')}" data-pjax="pageMain"><i class="far fa-sun text-muted" style="font-size: 1rem"></i></a>
</header>
{/block}

{block name="main"}
<div class="mt-1">
    <div class="block new-block mb-0 p-3 bg-white">
        <div class="block-header">
            <b>{:L('通知中心')}</b>
            <div class="float-right notefun">
                <a class="text-muted mr-1 aw-ajax-get" href="javascript:;" data-url="{:url('notify/read_all')}"><i class="icon-eye"></i> {:L('全部已读')}</a>
            </div>
        </div>
    </div>
    <div class="mt-2 bg-white">
        <div class="swiper-container bg-white">
            <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide">
                    <a class="nav-link {if $type==''}active{/if}" href="{:url('notify/index')}" data-pjax="pageMain">{:L('全部')}</a>
                </li>
                {foreach $notify as $key => $v}
                <li class="nav-item swiper-slide">
                    <a class="nav-link {if $type == $key}active{/if}" href="{:url('notify/index', ['type' => $key])}" data-pjax="pageMain">{:L($v)}</a>
                </li>
                {/foreach}
            </ul>
        </div>
    </div>
    <div class="rounded px-3 bg-white" id="tabMain">
        {if $list}
        {volist name="list" id="v"}
        <dl class="mb-0 pt-3 pb-4 border-bottom overflow-hidden position-relative">
            <dt class="float-left">
                {if $v['recipient_user']}
                <a href="javascript:;" class="badge badge-warning text-white" style="width: 46px;height: 46px;line-height: 46px;">
                    <i class="icon-bell" style="font-size: 24px;line-height: 40px"></i>
                </a>
                {else/}
                <a href="{$v['recipient_user']['url']}">
                    <img src="{$v['recipient_user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
                </a>
                {/if}
                <span class="aw-online-status aw-notify-status {$v['read_flag'] ? 'read' : 'unread'}"></span>
            </dt>
            <dd class="float-right" style="width: calc(100% - 61px)">
                <p class="text-muted font-9 {if !$v['read_flag']}font-weight-bold{/if}">{$v['subject']|raw} · {:date_friendly($v['create_time'])}</p>
                <p class="font-9 mt-1 text-color-info">{$v.content|raw}</p>
            </dd>
            <div class="font-8 position-absolute" style="{$isMobile ? 'bottom: 0.5rem;right: 0' : 'top: 1rem;right: 0'}">
                {if !$v['read_flag']}
                <a href="javascript:;" onclick="AWS_MOBILE.User.readNotify(this,{$v.id})" class="text-color-info">{:L('标记已读')}</a>
                {/if}
                <a href="javascript:;" onclick="AWS_MOBILE.User.deleteNotify(this,{$v.id})" class="ml-2 text-color-info"><i class="icon-delete"></i> {:L('删除')}</a>
            </div>
        </dl>
        {/volist}
        {$page|raw}

        {else/}
        <p class="text-center mt-4 text-meta">
            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无消息')}">
            <span class="mt-3 d-block ">{:L('暂无消息')}</span>
        </p>
        {/if}
    </div>
</div>

{/block}

{block name="sideMenu"}{/block}
{block name="footer"}{/block}