{if isset($list)}
{volist name="list" id="v"}
<div class="mb-0 py-2 px-3 header-inbox-item overflow-hidden position-relative cursor-pointer text-left">
    <p class="font-9 mb-1">{$v['subject']}</p>
    <p class="text-color-info font-9 mt-1 aw-two-line" >
        {$v.content|raw}
    </p>
    <p class="font-8 mt-2">
        {if !$v['read_flag']}
        <a href="javascript:;" onclick="AWS.User.readNotify(this,{$v.id})" class="text-muted"><i class="fa fa-check-circle"></i> {:L('标记已读')}</a>
        {/if}
        <a href="javascript:;" onclick="AWS.User.deleteNotify(this,{$v.id})" class="ml-2 text-muted"><i class="fa fa-trash-alt"></i> {:L('删除')}</a>
    </p>
</div>
{/volist}
{else/}
<p class="text-center text-muted py-3 font-9">{:L('暂无最新通知')}</p>
{/if}