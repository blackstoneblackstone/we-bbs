{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}
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
        min-width: 400px;
        padding: 0;
        background-color: #FFFFFF;
        box-shadow: 0 1px 3px rgba(18,18,18,0.1);
        border-radius:3px;
        max-width: 489px;
        overflow: hidden;
    }
    {/if}
    .signBoxFooter a{color: #f6f6f6;}
    .signBox .logo {
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
    .logo a{color:#ffffff !important;}
    .bline{display:flex; align-items:center; margin-bottom:1rem;}
    .bline input{ width:100%}
</style>
<div class="appMain" style="{$theme_config['common']['fixed_navbar']=='Y' && !$_ajax_open && !$_ajax ? 'margin-top:-80px' : ''}" >
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
                            <div class="block mb-0">
                                <ul class="nav nav-tabs nav-tabs-block px-3 mb-1 aw-pjax-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link {if $login_type!='mobile'}active{/if}" href="{:url('account/login?type=email')}" data-pjax="tabMain">{:L('密码登录')}</a>
                                    </li>
                                    {if get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N'}
                                    <li class="nav-item">
                                        <a class="nav-link {if $login_type=='mobile' || get_setting('register_valid_type')=='mobile'}active{/if}" href="{:url('account/login?type=mobile')}" data-pjax="tabMain">{:L('手机登录')}</a>
                                    </li>
                                    {/if}
                                    {if $setting.register_type!='close'}
                                    <li class="nav-item ml-auto" style=" float:right">
                                        <a class="nav-link text-primary"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
                                    </li>
                                    {/if}
                                </ul>
                                <div class="block-content tab-content p-3 bg-white" id="tabMain">
                                    <input type="hidden" name="token" value="{:token()}" />
                                    <input name="return_url" id="returnUrl" type="hidden" value="{:base64_encode($return_url)}">
                                    {if $login_type=='mobile'}
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                                    </div>
                                    <div class="form-group bline d-flex" style="border-bottom: 1px solid #ebebeb">
                                        <input type="text" style="border-bottom: 0" class="aw-form-control flex-fill" id="code" name="code" placeholder="{:L('请输入短信验证码')}">
                                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                                    </div>
                                    <div class="form-group">
                                        <button type="button" class="btn btn-block btn-primary login-button">{:L('登录')}</button>
                                    </div>
                                    <div class="form-group clearfix font-size-sm" style=" margin-top:30px;">
                                        {if $setting.remember_login_enable=="Y"}
                                        <div class="float-left">
                                            <input type="checkbox" class="remember" name="remember"> {:L('记住我')}
                                        </div>
                                        {/if}
                                        {if $setting.register_type!='close'}
                                        <a class="nav-link text-primary float-right"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
                                        {/if}
                                    </div>

                                    <script>
                                        $(".login-button").click(function () {
                                            var token = $("input[name='token']").val();
                                            var name = $("#mobile").val();
                                            var code = $("#code").val();
                                            var remember = $(".remember").is(':checked') ? 1 : 0;
                                            var returnUrl = $('#returnUrl').val();
                                            if ((name == null) || (name === "")) {
                                                AWS.api.error("手机号不能为空！");
                                                return false;
                                            }
                                            else if ((code == null) || (code === "")) {
                                                AWS.api.error("手机验证码不能为空！");
                                                return false;
                                            }
                                            else {
                                                $.ajax({
                                                    url: "{:url('account/login')}",
                                                    type: "POST",
                                                    data: { mobile: name, code: code,token:token,remember:remember,return_url:returnUrl},
                                                    dataType:"json",
                                                    success: function (result) {
                                                        if (result.code > 0) {
                                                            if (isAjaxOpen){
                                                                parent.window.location.reload();
                                                            }
                                                            layer.msg(result.msg,function (){
                                                                window.location.href = result.url;
                                                            })
                                                        } else {
                                                            AWS.api.error(result.msg, result.url)
                                                        }
                                                    },
                                                    error: function (error) {
                                                        if ($.trim(error.responseText) !== '') {
                                                            AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                                        }
                                                    }
                                                })
                                            }
                                        })
                                    </script>
                                    {else/}
                                    <input type="hidden" name="captcha" id="captcha">
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="username" name="username" placeholder="{:L('请输入手机号/邮箱/用户名')}">
                                    </div>
                                    <div class="form-group bline">
                                        <input type="password" class="aw-form-control" id="password" name="password" placeholder="{:L('请输入密码')}">
                                    </div>

                                    <div class="form-group newbut" style=" margin-top:30px;">
                                        <button type="button" class="btn btn-block btn-primary login-button">{:L('登录')}</button>
                                    </div>

                                    <div class="form-group clearfix font-size-sm" style=" margin-top:30px;">
                                        <div class="float-left">
                                            {if $setting.remember_login_enable=="Y"}
                                            <label class="mr-3">
                                                <input type="checkbox" class="remember" name="remember"> {:L('记住我')}
                                            </label>
                                            {/if}
                                            <a href="{:url('account/find_password')}" target="_blank" class="text-muted font-9">{:L('忘记密码')}？</a>
                                        </div>
                                        {if $setting.register_type!='close'}
                                        <a class="nav-link text-primary float-right"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
                                        {/if}
                                    </div>

                                    <script>
                                        $(".login-button").click(function () {
                                            {if $setting.enable_frontend_captcha=='Y'}
                                            $('#captcha').captcha({
                                                callback:function (){
                                                    var token = $("input[name='token']").val();
                                                    var name = $("#username").val();
                                                    var password = authCode($("#password").val(),'ENCODE',token);
                                                    var remember = $(".remember").is(':checked') ? 1 : 0;
                                                    var returnUrl = $('#returnUrl').val();
                                                    if ((name == null) || (name === "")) {
                                                        AWS.api.error("用户名不能为空！");
                                                        return false;
                                                    }
                                                    else if ((password == null) || (password === "")) {
                                                        AWS.api.error("密码不能为空！");
                                                        return false;
                                                    }
                                                    else {
                                                        $.ajax({
                                                            url: "{:url('account/login')}",
                                                            type: "POST",
                                                            data: { username: name, password: password,token:token,remember:remember,return_url:returnUrl},
                                                            dataType:"json",
                                                            success: function (result) {
                                                                if (result.code > 0) {
                                                                    if (isAjaxOpen){
                                                                        parent.window.location.reload();
                                                                    }
                                                                    layer.msg(result.msg,function (){
                                                                        window.location.href = result.url;
                                                                    })
                                                                } else {
                                                                    AWS.api.error(result.msg, result.url)
                                                                }
                                                            },
                                                            error: function (error) {
                                                                if ($.trim(error.responseText) !== '') {
                                                                    AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                                                }
                                                            }
                                                        })
                                                    }
                                                }
                                            });
                                            {else/}
                                                var token = $("input[name='token']").val();
                                                var name = $("#username").val();
                                                var password = authCode($("#password").val(),'ENCODE',token);
                                                var remember = $(".remember").is(':checked') ? 1 : 0;
                                                var returnUrl = $('#returnUrl').val();
                                                if ((name == null) || (name === "")) {
                                                    AWS.api.error("用户名不能为空！");
                                                    return false;
                                                }
                                                else if ((password == null) || (password === "")) {
                                                    AWS.api.error("密码不能为空！");
                                                    return false;
                                                }
                                                else {
                                                    $.ajax({
                                                        url: "{:url('account/login')}",
                                                        type: "POST",
                                                        data: { username: name, password: password,token:token,remember:remember,return_url:returnUrl},
                                                        dataType:"json",
                                                        success: function (result) {
                                                            if (result.code) {
                                                                if (isAjaxOpen){
                                                                    parent.window.location.reload();
                                                                }
                                                                layer.msg(result.msg,function (){
                                                                    window.location.href = result.url;
                                                                })
                                                            } else {
                                                                AWS.api.error(result.msg, result.url)
                                                            }
                                                        },
                                                        error: function (error) {
                                                            if ($.trim(error.responseText) !== '') {
                                                                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                                            }
                                                        }
                                                    })
                                                }
                                            {/if}
                                        })
                                    </script>
                                    {/if}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                {if $base = get_plugins_config('third') && get_plugins_config('third','base')['enable']}
                <div class="pb-4 position-relative mx-3">
                    <div class="border-bottom pb-1 position-absolute w-100" style="top: 6px"></div>
                    <span class="d-block bg-white mb-3 position-relative text-center" style="width: 120px;margin: 0 auto;z-index: 9">{:L('社交账号登录')}</span>
                    <span class="d-block text-center">{:hook('third_login')}</span>
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
</div>
{/block}
{block name="footer"}{/block}
