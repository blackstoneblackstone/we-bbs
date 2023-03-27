{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title">{:L('注册')}</div>
    <div class="aui-header-right"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1">
    <div class="bg-white p-3 mb-1">
        <form action="" method="POST">
            <div class="block mb-0">
                <div class="block-content tab-content bg-white" id="tabMain">
                    {:token_field()}
                    <div class="form-group">
                        <input id="username" name="username" placeholder="{:L('用户名')}" type="text" class="aw-form-control" value=""/>
                    </div>
                    <div class="form-group">
                        <input type="password" id="password" name="password" placeholder="{:L('登录密码')}" class="aw-form-control" value=""/>
                    </div>
                    <input type="hidden" id="inviteCode" name="invitation_code" placeholder="{:L('邀请码')}" class="aw-form-control" value="{:input('invitation_code')}"/>
                    {if $register_type=='mobile'}
                    <input type="hidden" class="register_type" value="mobile"/>
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                    </div>
                    <div class="form-group d-flex" style="border-bottom: 1px solid #ebebeb">
                        <input type="text" style="border-bottom: 0" id="code" class="aw-form-control flex-fill" name="code" placeholder="{:L('请输入短信验证码')}">
                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                    </div>
                    <div class="form-group" style=" margin-top:30px;">
                        <button type="button" class="btn btn-block btn-primary login-button">{:L('注册')}</button>
                    </div>
                    <script>
                        $(".login-button").click(function () {
                            var token = $("input[name='__token__']").val();
                            var name = $("#username").val();
                            var password = authCode($("#password").val(),'ENCODE',token);
                            var mobile = parseInt($("#mobile").val());
                            var code = parseInt($("#code").val());
                            var inviteCode = $('#inviteCode').length > 0 ? $("#inviteCode").val() : '';
                            if ((name == null) || (name === "")) {
                                layer.msg("用户名不能为空！");
                                return false;
                            }
                            else if ((password == null) || (password === "")) {
                                layer.msg("密码不能为空！");
                                return false;
                            }else if(mobile==null || AWS.common.isPhoneNumber(mobile))
                            {
                                layer.msg("请输入有效的手机号码！");
                                return false;
                            }else if(!code)
                            {
                                layer.msg("请输入短信验证码");
                                return false;
                            }
                            else {
                                $.ajax({
                                    url: "{:url('account/register')}",
                                    type: "POST",
                                    data: {
                                        username: name,
                                        password: password,
                                        token:token,
                                        mobile:mobile,
                                        code:code,
                                        invitation_code:inviteCode
                                    },
                                    dataType:"json",
                                    success: function (result) {
                                        let msg = result.msg ? result.msg : '注册成功';
                                        if (result.code > 0) {
                                            AWS_MOBILE.api.success(msg, result.url)
                                        } else {
                                            AWS_MOBILE.api.error(msg, result.url)
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
                    {/if}

                    {if $register_type=='email'}
                    <input type="hidden" class="register_type" value="email"/>
                    <input type="hidden" name="captcha" id="captcha">
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="email" name="email" placeholder="{:L('请输入邮箱')}">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-block btn-primary login-button">{:L('注册')}</button>
                    </div>
                    <script>
                        $(document).ready(function ()
                        {
                            $(".login-button").click(function () {
                                var name = $("#username").val();
                                {if $setting.enable_frontend_captcha=='Y'}
                                $('#captcha').captcha({
                                    callback: function () {
                                        var token = $("input[name='__token__']").val();
                                        var password = authCode($("#password").val(),'ENCODE',token);
                                        var inviteCode = $('#inviteCode').length > 0 ? $("#inviteCode").val() : '';
                                        var email = $("#email").val();
                                        $.ajax({
                                            url: "{:url('account/register')}",
                                            type: "POST",
                                            data: {
                                                username: name,
                                                password: password,
                                                token:token,
                                                email:email,
                                                invitation_code:inviteCode
                                            },
                                            dataType:"json",
                                            success: function (result) {
                                                let msg = result.msg ? result.msg : '操作成功';
                                                if (result.code > 0) {
                                                    AWS_MOBILE.api.success(msg, result.url)
                                                } else {
                                                    AWS_MOBILE.api.error(msg, result.url)
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
                                {else/}
                                    var token = $("input[name='__token__']").val();
                                    var password = authCode($("#password").val(),'ENCODE',token);
                                    var inviteCode = $('#inviteCode').length > 0 ? $("#inviteCode").val() : '';
                                    var email = $("#email").val();
                                    $.ajax({
                                        url: "{:url('account/register')}",
                                        type: "POST",
                                        data: {
                                            username: name,
                                            password: password,
                                            token:token,
                                            email:email,
                                            invitation_code:inviteCode,
                                            register_type:"email"
                                        },
                                        dataType:"json",
                                        success: function (result) {
                                            let msg = result.msg ? result.msg : '操作成功';
                                            if (result.code > 0) {
                                                AWS_MOBILE.api.success(msg, result.url)
                                            } else {
                                                AWS_MOBILE.api.error(msg, result.url)
                                            }
                                        },
                                        error: function (error) {
                                            if ($.trim(error.responseText) !== '') {
                                                AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                                            }
                                        }
                                    })
                                    {/if}
                                    })
                                })
                    </script>
                    {/if}

                    {if $register_type=='all'}
                    <input type="hidden" class="register_type" value="mobile"/>
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                    </div>
                    <div class="form-group d-flex" style="border-bottom: 1px solid #ebebeb">
                        <input type="text" style="border-bottom: 0" id="code" class="aw-form-control flex-fill" name="code" placeholder="{:L('请输入短信验证码')}">
                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                    </div>
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="email" name="email" placeholder="{:L('请输入邮箱')}">
                    </div>

                    <div class="form-group" style=" margin-top:30px;">
                        <button type="button" class="btn btn-block btn-primary login-button">{:L('注册')}</button>
                    </div>
                    <script>
                        $(".login-button").click(function () {
                            var token = $("input[name='__token__']").val();
                            var name = $("#username").val();
                            var password = authCode($("#password").val(),'ENCODE',token);
                            var mobile = parseInt($("#mobile").val());
                            var code = parseInt($("#code").val());
                            var email = $("#email").val();
                            var inviteCode = $('#inviteCode').length > 0 ? $("#inviteCode").val() : '';
                            if ((name == null) || (name === "")) {
                                AWS_MOBILE.api.error("用户名不能为空！");
                                return false;
                            }
                            else if ((password == null) || (password === "")) {
                                AWS_MOBILE.api.error("密码不能为空！");
                                return false;
                            }else if(mobile==null || AWS.common.isPhoneNumber(mobile))
                            {
                                AWS_MOBILE.api.error("请输入有效的手机号码！");
                                return false;
                            }else if(!code)
                            {
                                AWS_MOBILE.api.error("请输入短信验证码");
                                return false;
                            }
                            else {
                                $.ajax({
                                    url: "{:url('account/register')}",
                                    type: "POST",
                                    data: {
                                        username: name,
                                        password: password,
                                        token:token,
                                        mobile:mobile,
                                        code:code,
                                        email:email,
                                        invitation_code:inviteCode,
                                    },
                                    dataType:"json",
                                    success: function (result) {
                                        let msg = result.msg ? result.msg : '注册成功';
                                        if (result.code > 0) {
                                            AWS_MOBILE.api.success(msg, result.url)
                                        } else {
                                            AWS_MOBILE.api.error(msg, result.url)
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
                    {/if}

                    <div class="text-muted text-left font-8  pb-3">
                        {:L('注册即代表同意')}<a href="javascript:;" class="register-agreement" onclick="AWS_MOBILE.api.dialog('{:L(\'注册协议\')}',$('#agreement').html())">《{:L('注册协议')}》</a>
                    </div>
                    <script type="text/html" id="agreement">
                        <div class="p-3">
                            {$agreement|raw}
                        </div>
                    </script>
                </div>
            </div>
        </form>
    </div>
</div>
{/block}

{block name="sideMenu"}{/block}

{block name="footer"}{/block}