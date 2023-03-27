{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}
{if !$_ajax_open}
<style>
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
        min-width: 400px;
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
</style>
{/if}
<style>
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
    .bline{display:flex; align-items:center; margin-bottom:20px;}
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
                                <ul class="nav nav-tabs nav-tabs-block p-3 mb-1 aw-pjax-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="{:url('account/register')}" data-pjax="tabMain">{:L('用户注册')}</a>
                                    </li>
                                    <li class="nav-item ml-auto">
                                         <a class="nav-link text-primary"  data-pjax="boxMain" href="{:url('account/login')}">{:L('已有账号')}？{:L('去登录')}</a>
                                    </li>
                                </ul>
                                <div class="block-content tab-content p-3 bg-white" id="tabMain">
                                    {:token_field()}
                                    <input name="return_url" id="returnUrl" type="hidden" value="{:base64_encode($return_url)}">
                                    <div class="form-group bline">
                                        <input id="username" name="username" placeholder="{:L('用户名')}" type="text" class="aw-form-control" value=""/>
                                    </div>
                                    <div class="form-group bline">
                                        <input type="password" id="password" name="password" placeholder="{:L('登录密码')}" class="aw-form-control" value=""/>
                                    </div>

                                    <input type="hidden" id="inviteCode" name="invitation_code" placeholder="{:L('邀请码')}" class="aw-form-control" value="{:input('invitation_code')}"/>

                                    {if $register_type=='mobile'}
                                    <input type="hidden" class="register_type" value="mobile"/>
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                                    </div>
                                    <div class="form-group bline d-flex" style="border-bottom: 1px solid #ebebeb">
                                        <input type="text" style="border-bottom: 0" id="code" class="aw-form-control flex-fill" name="code" placeholder="{:L('请输入短信验证码')}">
                                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                                    </div>
                                    <div class="form-group newbut" style=" margin-top:30px;">
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
                                            var returnUrl = $('#returnUrl').val();
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
                                                        invitation_code:inviteCode,
                                                        return_url : returnUrl
                                                    },
                                                    dataType:"json",
                                                    success: function (result) {
                                                        let msg = result.msg ? result.msg : '注册成功';
                                                        if (result.code > 0) {
                                                            AWS.api.success(msg, result.url)
                                                        } else {
                                                            AWS.api.error(msg, result.url)
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
                                    {/if}

                                    {if $register_type=='email'}
                                    <input type="hidden" class="register_type" value="email"/>
                                    <input type="hidden" name="captcha" id="captcha">
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="email" name="email" placeholder="{:L('请输入邮箱')}">
                                    </div>
                                    <div class="form-group newbut">
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
                                                        var returnUrl = $('#returnUrl').val();
                                                        $.ajax({
                                                            url: "{:url('account/register')}",
                                                            type: "POST",
                                                            data: {
                                                                username: name,
                                                                password: password,
                                                                token:token,
                                                                email:email,
                                                                invitation_code:inviteCode,
                                                                return_url : returnUrl
                                                            },
                                                            dataType:"json",
                                                            success: function (result) {
                                                                let msg = result.msg ? result.msg : '操作成功';
                                                                if (result.code > 0) {
                                                                    AWS.api.success(msg, result.url)
                                                                } else {
                                                                    AWS.api.error(msg, result.url)
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
                                                {else/}
                                                    var token = $("input[name='__token__']").val();
                                                    var password = authCode($("#password").val(),'ENCODE',token);
                                                    var inviteCode = $('#inviteCode').length > 0 ? $("#inviteCode").val() : '';
                                                    var email = $("#email").val();
                                                    var returnUrl = $('#returnUrl').val();
                                                    $.ajax({
                                                        url: "{:url('account/register')}",
                                                        type: "POST",
                                                        data: {
                                                            username: name,
                                                            password: password,
                                                            token:token,
                                                            email:email,
                                                            invitation_code:inviteCode,
                                                            register_type:"email",
                                                            return_url : returnUrl
                                                        },
                                                        dataType:"json",
                                                        success: function (result) {
                                                            let msg = result.msg ? result.msg : '操作成功';
                                                            if (result.code > 0) {
                                                                AWS.api.success(msg, result.url)
                                                            } else {
                                                                AWS.api.error(msg, result.url)
                                                            }
                                                        },
                                                        error: function (error) {
                                                            if ($.trim(error.responseText) !== '') {
                                                                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
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
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="mobile" name="mobile" placeholder="{:L('请输入手机号')}">
                                    </div>
                                    <div class="form-group bline d-flex" style="border-bottom: 1px solid #ebebeb">
                                        <input type="text" style="border-bottom: 0" id="code" class="aw-form-control flex-fill" name="code" placeholder="{:L('请输入短信验证码')}">
                                        <button type="button" data-sms='#mobile' class="flex-fill text-primary" style="min-width: 110px;line-height: 38px;outline: none;border: none;background: none">{:L('获取短信验证码')}</button>
                                    </div>
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="email" name="email" placeholder="{:L('请输入邮箱')}">
                                    </div>

                                    <div class="form-group newbut" style=" margin-top:30px;">
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
                                            var returnUrl = $('#returnUrl').val();
                                            if ((name == null) || (name === "")) {
                                                AWS.api.error("用户名不能为空！");
                                                return false;
                                            }
                                            else if ((password == null) || (password === "")) {
                                                AWS.api.error("密码不能为空！");
                                                return false;
                                            }else if(mobile==null || AWS.common.isPhoneNumber(mobile))
                                            {
                                                AWS.api.error("请输入有效的手机号码！");
                                                return false;
                                            }else if(!code)
                                            {
                                                AWS.api.error("请输入短信验证码");
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
                                                        return_url:returnUrl
                                                    },
                                                    dataType:"json",
                                                    success: function (result) {
                                                        let msg = result.msg ? result.msg : '注册成功';
                                                        if (result.code > 0) {
                                                            AWS.api.success(msg, result.url)
                                                        } else {
                                                            AWS.api.error(msg, result.url)
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
                                    {/if}

                                    <div class="text-muted text-left font-8  pb-3">
                                        {:L('注册即代表同意')}<a href="javascript:;" class="register-agreement">《{:L('注册协议')}》</a>
                                    </div>
                                    <script type="text/html" id="agreement">
                                        <div style="padding: 30px">
                                            {$agreement|raw}
                                        </div>
                                    </script>
                                    <script>
                                        $('.register-agreement').click(function (){
                                            layer.open({
                                                type: 1,
                                                title: '注册协议',
                                                closeBtn: 1,
                                                area: ['90%', '90%'],
                                                shadeClose: true,
                                                content: $('#agreement').html()
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
