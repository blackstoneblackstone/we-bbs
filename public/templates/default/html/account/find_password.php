{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}
<link rel="stylesheet" type="text/css" href="{$cdnUrl}/static/libs/captcha/css/captcha.css?v={$version}" />
<script type="text/javascript" src="{$cdnUrl}/static/libs/captcha/captcha.js?v={$version}"></script>
<style>
    {if !$_ajax_open}
    .signBox{
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        background-image: url('{$static_url}images/bg.jpg');
        background-repeat: no-repeat;
        background-color: #b8e5f8;
        background-size: cover;
        width: 100%;
        height: 100vh;
        overflow: auto;
    }
    .signBoxContent{
        -webkit-box-flex: 1;
        -ms-flex: 1 1;
        flex: 1 1;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        border-radius: 2px;
        min-height: 640px;
        height: calc(100% - 42px);
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
    }
    .content {
        box-sizing: border-box;
        margin: 0;
        min-width: 0;
        padding: 0;
        background-color: #FFFFFF;
        box-shadow: 0 1px 3px rgba(18,18,18,0.1);
        border-radius:3px;
        width: 489px;
        overflow: hidden;
    }
    {/if}
    .signBoxFooter a{color: #f6f6f6;}
    .signBox .logo {
        width: 128px;
        height: 80px;
        margin-bottom: 24px;
        line-height: 80px;
        font-weight: 600;
        text-align: center;
    }
    .SignContainer-content {
        margin: 0 auto;
        text-align: center;
    }
    .SignContainer-inner {
        position: relative;
        overflow: hidden;
    }
    .nav-tabs-block {
        background-color: #fff;
        border-bottom: none;
    }
    .nav-tabs-block .nav-link {
        border-color: transparent;
        border-radius: 0;
        color: #575757;
        font-size: 1rem;
        padding: .8rem 0;
        margin-right: 1rem;
    }
    .nav-tabs-block .nav-link:hover{
        background: none;
    }
    .nav-tabs-block .nav-link.active, .nav-tabs-block .nav-item.show .nav-link {
        color: #575757;
        background-color: #fff;
        border-color: transparent;
        border-bottom: 3px solid #06f;
    }
    .logo a{color:#ffffff !important;}
    .bline{display:flex; align-items:center; margin-bottom:1rem;}
    .bline input{ width:100%}
    .newbut .btn{ background:#563d7c; border:none; height:38px;}
    .nav-tabs{ height:64px;}
    .nav-tabs a.nav-link{ height:60px; line-height:60px;}
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{ height:60px; line-height:60px;}
</style>
<main class="appMain" style="{$theme_config['common']['fixed_navbar']=='Y' && !$_ajax_open && !$_ajax ? 'margin-top:-80px' : ''}" >
    <div class="signBox">
        <div class="signBoxContent">
            {if !$_ajax_open}
            <h1 class="logo ">
                <a href="{$baseUrl}" class="text-primary">{:get_setting('site_name')}</a>
            </h1>
            {/if}
            <div class="content" style="padding: 0" id="boxMain">
                <div class="SignContainer-content">
                    <div class="SignContainer-inner">
                        <form action="" method="POST">
                            <input type="hidden" name="type" value="{$login_type}">
                            <div class="block mb-0">
                                <ul class="nav nav-tabs nav-tabs-block px-3 mb-1 aw-pjax-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link {if $login_type!='mobile'}active{/if}" href="{:url('account/find_password?type=email')}" data-pjax="tabMain">{:L('邮箱找回')}</a>
                                    </li>
                                    {if get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N'}
                                    <li class="nav-item">
                                        <a class="nav-link {if $login_type=='mobile'}active{/if}" href="{:url('account/find_password?type=mobile')}" data-pjax="tabMain">{:L('手机号找回')}</a>
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
                                    <input type="hidden" name="type" value="mobile" />
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
                                        <input type="text" style="border-bottom: 0" class="aw-form-control flex-fill" id="code" name="code" placeholder="{:L('请输入短信验证码')}">
                                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-block btn-primary login-button aw-ajax-form">{:L('下一步')}</button>
                                    </div>
                                    {else/}
                                    <input type="hidden" name="captcha" id="captcha">
                                    <input type="hidden" name="type" value="email" />
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
                {if get_plugins_config('third', 'base') && get_plugins_config('third','base')['enable']}
                <div class="socialLogin p-3">
                    <span>{:L('社交帐号登录')}</span>
                    <span class="Login-socialButtonGroup">
                        {:hook('third_login')}
                    </span>
                </div>
                {/if}
            </div>
        </div>
        {if !$_ajax_open}
        <div class="signBoxFooter text-center text-white px-6">
            <div class="py-2">
                {volist name="footerMenu" id="v"}
                <a target="_blank" rel="noopener noreferrer" href="{$v.url}">{$v.title}</a>
                {/volist}
            </div>
            <div class="pb-3">
                <span>© {:date('Y')} {$setting.site_name}</span>
                <a target="_blank" rel="noopener noreferrer" href="https://beian.miit.gov.cn/">{$setting.icp}</a>
            </div>
        </div>
        {/if}
    </div>
</main>
{/block}
{block name="footer"}{/block}
