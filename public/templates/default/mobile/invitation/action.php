<form action="{:url('invitation/operate')}" method="post" class="form-horizontal" id="invitation-form">
    <input type="hidden" name="active_type" value="{$type}">
    <input type="hidden" name="invitation_code" value="{$invitation_code}">
    {if ($type == 'link')}
    <div class="form-group">
        <label>{:L('链接')}</label>
        <div class="mt-2">
            <input type="text" name="link" value="{$link}" class="form-control" readonly>
        </div>
    </div>
    {else/}
    <div class="form-group">
        <label>{:L('邮箱')}</label>
        <div class="mt-2">
            <input type="email" name="invitation_email" value="" placeholder="{:L('请输入被邀请者邮箱')}" class="form-control">
        </div>
    </div>
    {/if}

    <div class="form-group text-center">
        <button class="aui-btn aui-btn-success aui-btn-small generate-invitation" type="button">{:L('生成邀请')}</button>
    </div>
</form>