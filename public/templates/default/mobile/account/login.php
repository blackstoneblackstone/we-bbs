{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title">{:L('登录')}</div>
    <div class="aui-header-right"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1">
    <div class="bg-white p-3 mb-1">
        <form method="POST">
            <div class="block mb-0">
                <ul class="nav nav-tabs text-center mb-1 aw-pjax-tabs border-0">
                    <li class="nav-item">
                        <a class="nav-link {if $login_type!='mobile'}active{/if}" href="{:url('account/login?type=email')}" data-pjax="pageMain">{:L('密码登录')}</a>
                    </li>
                    {if get_plugins_config('sms','base') && get_plugins_config('sms','base')!='N'}
                    <li class="nav-item">
                        <a class="nav-link {if $login_type=='mobile' || get_setting('register_valid_type')=='mobile'}active{/if}" href="{:url('account/login?type=mobile')}" data-pjax="pageMain">{:L('手机登录')}</a>
                    </li>
                    {/if}
                </ul>
                <div class="block-content tab-content bg-white" id="tabMain">
                    <input type="hidden" name="token" value="{:token()}" />
                    {if $login_type=='mobile'}
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                    </div>
                    <div class="form-group d-flex" style="border-bottom: 1px solid #ebebeb">
                        <input type="text" style="border-bottom: 0" class="aw-form-control flex-fill" id="code" name="code" placeholder="{:L('请输入')}6{:L('位短信验证码')}">
                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary login-button">{:L('登录')}</button>
                    </div>
                    <div class="form-group clearfix font-size-sm" style=" margin-top:30px;">
                        <div class="float-left">
                            <input type="checkbox" class="remember" name="remember"> {:L('记住我')}
                        </div>
                        {if $setting.register_type!='close'}
                        <a class="nav-link text-primary float-right p-0"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
                        {/if}
                    </div>

                    <script>
                        $(".login-button").click(function () {
                            var token = $("input[name='token']").val();
                            var name = $("#mobile").val();
                            var code = $("#code").val();
                            var remember = $(".remember").is(':checked') ? 1 : 0;
                            if ((name == null) || (name === "")) {
                                AWS_MOBILE.api.error("手机号不能为空！");
                                return false;
                            }
                            else if ((code == null) || (code === "")) {
                                AWS_MOBILE.api.error("手机验证码不能为空！");
                                return false;
                            }
                            else {
                                $.ajax({
                                    url: "{:url('account/login')}",
                                    type: "POST",
                                    data: { mobile: name, code: code,token:token,remember:remember},
                                    dataType:"json",
                                    success: function (result) {
                                        if (result.code > 0) {
                                            if (isAjaxOpen){
                                                parent.window.location.reload();
                                            }
                                            AWS_MOBILE.api.success(result.msg, result.url)
                                        } else {
                                            AWS_MOBILE.api.error(result.msg, result.url)
                                        }
                                    },
                                    error: function (error) {
                                        if ($.trim(error.responseText) !== '') {
                                            AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                        }
                                    }
                                })
                            }
                        })
                    </script>
                    {else/}
                    <input type="hidden" name="captcha" id="captcha">
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="username" name="username" placeholder="{:L('请输入手机号/邮箱/用户名')}">
                    </div>
                    <div class="form-group">
                        <input type="password" class="aw-form-control" id="password" name="password" placeholder="{:L('请输入密码')}">
                    </div>

                    <div class="form-group" style=" margin-top:30px;">
                        <button type="button" class="btn btn-block btn-primary login-button">{:L('登录')}</button>
                    </div>

                    <div class="form-group clearfix font-size-sm" style=" margin-top:30px;">
                        <div class="float-left">
                            <input type="checkbox" class="remember" name="remember"> {:L('记住我')}
                            <a href="{:url('account/find_password')}" class="ml-3 text-muted font-9">{:L('忘记密码')}？</a>
                        </div>
                        {if $setting.register_type!='close'}
                        <a class="nav-link text-primary float-right p-0"  data-pjax="boxMain" href="{:url('account/register')}">{:L('没有账号')}？{:L('去注册')}</a>
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
                                    if ((name == null) || (name === "")) {
                                        AWS_MOBILE.api.error("用户名不能为空！");
                                        return false;
                                    }
                                    else if ((password == null) || (password === "")) {
                                        AWS_MOBILE.api.error("密码不能为空！");
                                        return false;
                                    }
                                    else {
                                        $.ajax({
                                            url: "{:url('account/login')}",
                                            type: "POST",
                                            data: { username: name, password: password,token:token,remember:remember},
                                            dataType:"json",
                                            success: function (result) {
                                                if (result.code > 0) {
                                                    if (isAjaxOpen){
                                                        parent.window.location.reload();
                                                    }
                                                    AWS_MOBILE.api.success(result.msg, result.url)
                                                } else {
                                                    AWS_MOBILE.api.error(result.msg, result.url)
                                                }
                                            },
                                            error: function (error) {
                                                if ($.trim(error.responseText) !== '') {
                                                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
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
                                if ((name == null) || (name === "")) {
                                    AWS_MOBILE.api.error("用户名不能为空！");
                                    return false;
                                }
                                else if ((password == null) || (password === "")) {
                                    AWS_MOBILE.api.error("密码不能为空！");
                                    return false;
                                }
                                else {
                                    $.ajax({
                                        url: "{:url('account/login')}",
                                        type: "POST",
                                        data: { username: name, password: password,token:token,remember:remember},
                                        dataType:"json",
                                        success: function (result) {
                                            if (result.code > 0) {
                                                if (isAjaxOpen){
                                                    parent.window.location.reload();
                                                }
                                                AWS_MOBILE.api.success(result.msg, result.url)
                                            } else {
                                                AWS_MOBILE.api.error(result.msg, result.url)
                                            }
                                        },
                                        error: function (error) {
                                            if ($.trim(error.responseText) !== '') {
                                                AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
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
        {if $base = get_plugins_config('third') && get_plugins_config('third','base')['enable']}
        <div class="mt-5 position-relative pb-3">
            <div class="border-bottom pb-1 position-absolute w-100" style="top: 6px"></div>
            <span class="d-block bg-white mb-4 position-relative text-center" style="width: 120px;margin: 0 auto;z-index: 9">{:L('社交账号登录')}</span>
            <span class="d-block text-center">{:hook('third_login')}</span>
        </div>
        {/if}
    </div>
</div>
{/block}

{block name="sideMenu"}{/block}

{block name="footer"}{/block}