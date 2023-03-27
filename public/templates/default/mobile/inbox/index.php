{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('私信')}</div>
    <a class="aui-header-right" data-pjax="pageMain" href="{:url('setting/notify')}"><i class="far fa-sun text-muted" style="font-size: 1rem"></i></a>
</header>
{/block}

{block name="main"}
<div class="mt-1" id="wrapMain">
    <div class="block new-block p-3 bg-white ">
        <div class="block-header">
            <b>{:L('对话列表')}</b>
            <div class="float-right notefun">
                <a href="javascript:;" onclick="AWS_MOBILE.User.inbox()" class="text-muted">{:L('新私信')}</a>
            </div>
        </div>
    </div>
    <div class="mb-2 rounded mt-1 p-3 bg-white">
        {if isset($list)}
        {volist name="list" id="v"}
        <dl class="mb-0 py-3 border-bottom overflow-hidden position-relative">
            <dt class="float-left">
                <a href="{$v['user']['url']}">
                    <img src="{$v['user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
                </a>
                <span class="aw-online-status {$v['unread']  ? 'unread' : 'read'}"></span>
            </dt>
            <dd class="float-right" style="width: calc(100% - 61px)">
                <p class="text-muted font-9">{$v['user']['name']} · {:date_friendly($v['update_time'])}</p>
                <p class="mt-1 aw-one-line cursor-pointer {$v['unread']  ? 'text-primary' : 'text-muted'}">
                    <a href="{:url('inbox/detail', ['uid' => $v.user.uid])}" data-pjax="pageMain">
                        {:get_username($v['last_message_uid'])}:{$v['last_message']}
                    </a>
                </p>

                <div class="font-8 mt-1">
                    <a class="text-primary" href="{:url('inbox/detail', ['receiver' => $v.user.user_name])}" data-pjax="pageMain">{:L('共')}{$v.count}{:L('条对话')}</a>
                    <a href="javascript:;" onclick="AWS_MOBILE.User.deleteNotify(this,{$v.id})" class="ml-2 text-color-info"><i class="icon-delete"></i> {:L('已读')}</a>
                </div>
            </dd>
        </dl>
        {/volist}
        {$page}
        {else/}
        <p class="text-center mt-4 text-meta">
            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无私信记录')}">
            <span class="mt-3 d-block ">{:L('暂无私信记录')}</span>
        </p>
        {/if}
    </div>
</div>

{/block}

{block name="sideMenu"}{/block}
{block name="footer"}{/block}