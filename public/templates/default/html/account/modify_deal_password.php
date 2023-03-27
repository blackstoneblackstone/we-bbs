{extend name="$theme_block" /}
{block name="main"}
<form class="p-3" action="" id="verify" style="min-height: 186px">
    {if $user_info.mobile}
    <div class="form-group">
        <input type="text" class="form-control border-0" id="mobile" disabled placeholder="{:L('使用手机')} {:substr_replace($user_info.mobile,'****',3,4)} {:L('验证')}" value="{$user_info.mobile}">
    </div>
    <div class="form-group">
        <label class="mb-0 mr-2">
            <input type="text" class="form-control border-0 border-bottom verify-text" name="code" placeholder="{:L('输入您的短信验证码')}">
        </label><button class="btn btn-primary px-4 send-sms" data-sms='#mobile' type="button">{:L('获取验证码')}</button>
    </div>
    {else/}
    <div class="form-group">
        <input type="password" class="form-control border-0 border-bottom verify-text" name="old_password" placeholder="{:L('输入当前账号密码')}">
    </div>
    {/if}
    <div class="form-group">
        <input type="password" class="form-control border-0 border-bottom" name="password" placeholder="{:L('输入新的密码')}" value="">
    </div>
    <div class="form-group">
        <input type="password" class="form-control border-0 border-bottom" name="re_password" placeholder="{:L('再次输入新的密码')}" value="">
    </div>
    <div class="overflow-hidden">
        <button class="aw-ajax-form btn btn-primary w-100" type="button">{:L('提交修改')}</button>
    </div>
</form>
{/block}
