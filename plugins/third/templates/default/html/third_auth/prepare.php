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
        min-width: 0;
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
    .logo a{color:#ffffff !important;}
    .bline{display:flex; align-items:center; margin-bottom:1rem;}
    .bline input{ width:100%}
    .nav-tabs{ height:64px;}
    .nav-tabs a.nav-link{ height:60px; line-height:60px;}
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active{ height:60px; line-height:60px;}
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
                                        <a class="nav-link active" href="javascript:;">绑定已有账号</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="{$bind_url}">自动创建账号</a>
                                    </li>
                                </ul>
                                <div class="block-content tab-content p-3 bg-white" id="tabMain">
                                    <input type="hidden" name="token" value="{:token()}" />
                                    <input type="hidden" name="return_url" value="{:base64_encode($bind_url)}" />
                                    <div class="form-group bline">
                                        <input type="text" class="aw-form-control" id="username" name="username" placeholder="请输入手机号/邮箱/用户名">
                                    </div>
                                    <div class="form-group bline">
                                        <input type="password" class="aw-form-control" id="password" name="password" placeholder="请输入密码">
                                    </div>

                                    <div class="form-group newbut" style=" margin-top:30px;">
                                        <button type="button" class="btn btn-block btn-primary login-button">登录并绑定</button>
                                    </div>
                                    <script>
                                        $(".login-button").click(function () {
                                            var token = $("input[name='token']").val();
                                            var name = $("#username").val();
                                            var password = authCode($("#password").val(),'ENCODE',token);
                                            var remember = $(".remember").is(':checked') ? 1 : 0;
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
                                                    data: { username: name, password: password,token:token,remember:remember},
                                                    dataType:"json",
                                                    success: function (result) {
                                                        if (result.code > 0) {
                                                            if (isAjaxOpen){
                                                                parent.window.location.reload();
                                                            }
                                                            AWS.api.success(result.msg, result.url)
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