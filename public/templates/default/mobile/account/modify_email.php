{extend name="$theme_block" /}
{block name="main"}
<form class="p-3" action="{:url('account/modify_email')}">
    <div class="form-group">
        <input type="password" class="aw-form-control" name="password" placeholder="{:L('输入用户密码')}" value="">
    </div>

    <div class="form-group">
        <input type="text" class="aw-form-control" name="new_email" placeholder="{:L('输入新的邮箱')}" value="">
    </div>

    <div class="overflow-hidden">
        <button class="aw-ajax-form btn btn-primary px-4" type="button">{:L('保存')}</button>
    </div>
</form>
{/block}