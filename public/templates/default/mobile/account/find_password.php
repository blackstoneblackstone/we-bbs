{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title">{:L('找回密码')}</div>
</header>
{/block}
{block name="main"}
<div class="main-container mt-1">
    <div class="bg-white mb-1">
        <form action="" method="POST">
            <input type="hidden" name="type" value="{$login_type}">
            <div class="block mb-0">
                <ul class="nav nav-tabs px-2 mb-1 aw-pjax-tabs border-0">
                    <li class="nav-item">
                        <a class="nav-link {if $login_type!='mobile'}active{/if}" href="{:url('account/find_password?type=email')}" data-pjax="pageMain">{:L('邮箱找回')}</a>
                    </li>
                    {if get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N'}
                    <li class="nav-item">
                        <a class="nav-link {if $login_type=='mobile'}active{/if}" href="{:url('account/find_password?type=mobile')}" data-pjax="pageMain">{:L('手机号找回')}</a>
                    </li>
                    {/if}
                    {if $setting.register_type=='open'}
                    <li class="nav-item ml-auto" style=" float:right">
                        <a class="nav-link text-primary"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
                    </li>
                    {/if}
                </ul>
                <div class="block-content tab-content p-3 bg-white" id="tabMain">
                    <input type="hidden" name="token" value="{:token()}" />
                    {if $login_type=='mobile'}
                    <div class="form-group bline">
                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                    </div>
                    <div class="form-group bline">
                        <input type="password" class="aw-form-control" name="password" placeholder="{:L('请输入新密码')}">
                    </div>
                    <div class="form-group bline">
                        <input type="password" class="aw-form-control" name="re_password" placeholder="{:L('请再次确认密码')}">
                    </div>
                    <div class="form-group bline d-flex" style="border-bottom: 1px solid #ebebeb">
                        <input type="text" style="border-bottom: 0" class="aw-form-control flex-fill" id="code" name="code" placeholder="{:L('请输入')}6{:L('位短信验证码')}">
                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary login-button aw-ajax-form">{:L('下一步')}</button>
                    </div>
                    {else/}
                    <input type="hidden" name="captcha" id="captcha">
                    <div class="form-group bline">
                        <input type="text" class="aw-form-control" name="email" placeholder="{:L('请输入您绑定的邮箱')}">
                    </div>

                    <div class="form-group newbut" style=" margin-top:30px;">
                        <button type="button" class="btn btn-block btn-primary login-button aw-ajax-form">{:L('下一步')}</button>
                    </div>

                    <div class="form-group clearfix font-size-sm" style=" margin-top:30px;">
                        {if $setting.register_type=='open'}
                        <a class="nav-link text-primary float-right"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
                        {/if}
                    </div>
                    {/if}
                </div>
            </div>
        </form>
    </div>
</div>
{/block}
{block name="footer"}{/block}
