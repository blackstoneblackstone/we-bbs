{if isset($list)}
{volist name="list" id="v"}
<dl class="mb-0 p-2 header-inbox-item overflow-hidden position-relative cursor-pointer">
    <dt class="float-left">
        <a href="{$v['user']['url']}">
            <img src="{$v['user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
        </a>
    </dt>
    <dd class="float-right mb-0" style="width: calc(100% - 56px)">
        <p class="text-muted text-left">{$v['user']['name']}</p>
        <p class="aw-one-line font-9 cursor-pointer text-left text-muted" onclick="AWS.User.inbox('{$v.nick.name}')">{:get_username($v['last_message_uid'])}:{$v['last_message']}</p>
    </dd>
</dl>
{/volist}
{else/}
<p class="text-center text-muted py-3 font-9">{:L('暂无最新私信')}</p>
{/if}