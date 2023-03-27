{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('安全设置')}</div>
</header>
{/block}

{block name="main"}
<div class="p-3 bg-white mt-1">
    <div class="form-group d-flex">
        <label class="flex-fill col-form-label"> {:L('账号密码')}: </label>
        <div class="flex-fill">
            <button class="btn btn-primary px-4 btn-sm aw-ajax-open" data-url="{:url('account/modify_password')}">{:L('修改')}</button>
        </div>
    </div>

    <div class="form-group d-flex">
        <label class="flex-fill col-form-label"> {:L('交易密码')}: </label>
        <div class="flex-fill">
            <button class="btn btn-primary px-4 btn-sm aw-ajax-open" data-url="{:url('account/modify_deal_password')}">{:L('修改')}</button>
        </div>
    </div>
</div>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}{/block}