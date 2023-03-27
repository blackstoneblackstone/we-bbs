{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">第三方登录</div>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1">
    <div class="bg-white p-3 mb-1">
        <form action="" method="POST">
            <div class="block mb-0">
                <ul class="nav nav-tabs text-center mb-1 aw-pjax-tabs border-0">
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
                    <div class="form-group">
                        <input type="text" class="aw-form-control" id="username" name="username" placeholder="请输入手机号/邮箱/用户名">
                    </div>
                    <div class="form-group">
                        <input type="password" class="aw-form-control" id="password" name="password" placeholder="请输入密码">
                    </div>

                    <div class="form-group" style=" margin-top:30px;">
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
{/block}

{block name="sideMenu"}{/block}

{block name="footer"}{/block}