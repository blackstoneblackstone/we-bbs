{foreach $list as $val}
<li class="aui-list">
    <div class="aui-list-left">
        {:L('邀请时间')}：{:date('Y-m-d H:i', $val.create_time)}
        <span class="aui-tag aui-tag-linear aui-tag-success aui-tag-small" onclick="AWS_MOBILE.common.copyText('{$val.invitation_link}')">{:L('复制链接')}</span>
    </div>
    {if $val.invitation_email}
    <div class="aui-list-left-desc">
        {:L('邮箱')}：{$val.invitation_email}
    </div>
    {/if}
    <div class="aui-list-left-desc">
        {:L('链接')}：{$val.invitation_link}
    </div>
    <div class="aui-list-left">
        {:L('过期时间')}：{:date('Y-m-d H:i', $val.active_expire)}
        {if $val.active_expire<=time()}
        <span class="badge badge-danger">{:L('已过期')}</span>
        {else/}
        {if ($val.active_status == 1 && $val.active_expire) }
        <span class="badge badge-success">{$val.active_status_label}</span>
        {else/}
        <span class="badge badge-danger">{$val.active_status_label}</span>
        {/if}
        {/if}
    </div>
</li>
{/foreach}