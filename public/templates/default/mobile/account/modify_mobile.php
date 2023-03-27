{extend name="$theme_block" /}
{block name="main"}
<div class="d-block">
    {if !$step}
    <h3 class="text-center mb-2">{:L('身份验证')}</h3>
    <p class="text-color-info text-center font-9">
        {:L('为了保护你的账号安全，请验证身份')}，<br>{:L('验证成功后进行下一步操作')}
    </p>
    <form class="p-3" action="{:url('account/modify_mobile')}" id="verify">
        <input type="hidden" name="step" value="0">
        <input type="hidden" name="uid" value="{$user_id}">
        {if $user_info.mobile}
        <div class="form-group">
            <input type="hidden" id="mobile" value="{$user_info.mobile}">
            <input type="text" class="aw-form-control" disabled placeholder="{:L('使用手机')} {:substr_replace($user_info.mobile,'****',3,4)} {:L('验证')}" value="{:L('使用手机')} {:substr_replace($user_info.mobile,'****',3,4)} {:L('验证')}">
        </div>
        <div class="form-group clearfix d-flex">
            <label class="flex-fill mr-2">
                <input type="text" class="aw-form-control verify-text" name="code" placeholder="{:L('输入您的短信验证码')}">
            </label>
            <button class="btn btn-primary px-4 flex-fill send-sms" type="button" data-sms='#mobile'>{:L('获取验证码')}</button>
        </div>
        {else/}
        <div class="form-group">
            <input type="password" class="aw-form-control border-0 border-bottom verify-text" name="password" placeholder="{:L('输入当前账号密码')}">
        </div>
        {/if}
        <button class="btn btn-primary w-100 verify-btn" type="button">{:L('验证')}</button>
    </form>
    <script>
        $(function(){
            $('.verify-text').bind('input propertychange', function (e) {
                let that = this;
                if($(that).val()!=='')
                {
                    $('.verify-btn').removeAttr('disabled');
                }else{
                    $('.verify-btn').attr('disabled','disabled');
                }
            })
        })

        $('.verify-btn').click(function (){
            var code = $('.verify-text').val();
            if ((code == null) || (code === "")) {
                AWS_MOBILE.api.error("手机验证码不能为空！");
                return false;
            }
            var that = this;
            var form = $($(that).parents('form')[0]);
            var url = form.attr('action');
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                success: function (result)
                {
                    if(result.code===0)
                    {
                        AWS_MOBILE.api.success(result.msg)
                    }else{
                        $.ajax({
                            type: 'GET',
                            url: result.url,
                            dataType: 'html',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (ret) {
                                AWS_MOBILE.api.dialog('', ret)
                            },
                            error: function (xhr) {
                                let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                                AWS_MOBILE.events.onAjaxError(ret, error);
                            }
                        });
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        });
    </script>
    {/if}

    {if $step==1}
    <h3 class="text-center mb-2">{:L('更改手机号')}</h3>
    <p class="text-color-info text-center font-9">
        {:L('请验证您的手机号')}，<br>{:L('验证成功后进行下一步操作')}
    </p>
    <form class="p-3" action="{:url('account/modify_mobile')}">
        <input type="hidden" name="step" value="1">
        <input type="hidden" name="uid" value="{$user_id}">
        <div class="form-group">
            <input type="text" class="aw-form-control border-0 border-bottom" id="mobile" name="mobile" placeholder="{:L('输入新的手机号')}" value="">
        </div>
        <div class="form-group d-flex clearfix">
            <label class="flex-fill mr-2">
                <input type="text" class="aw-form-control verify-text" name="code" placeholder="{:L('输入您的短信验证码')}" value="">
            </label>
            <button class="btn btn-primary px-4 send-sms flex-fill" data-sms='#mobile' type="button">{:L('获取验证码')}</button>
        </div>
        <div class="overflow-hidden">
            <button class="saveModify btn btn-primary w-100 mt-1" type="button">{:L('提交修改')}</button>
        </div>
    </form>

    <script>
        $('.saveModify').click(function (){
            var code = $('.verify-text').val();
            var phone = $('#mobile').val();
            if ((phone == null) || (phone === "")) {
                AWS_MOBILE.api.error("手机号不能为空！");
                return false;
            }
            if ((code == null) || (code === "")) {
                AWS_MOBILE.api.error("手机验证码不能为空！");
                return false;
            }
            var that = this;
            var form = $($(that).parents('form')[0]);
            var url = form.attr('action');
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                success: function (result)
                {
                    if(result.code===0)
                    {
                        AWS_MOBILE.api.success(result.msg)
                    }else{
                        $.ajax({
                            type: 'GET',
                            url: result.url,
                            dataType: 'html',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (ret) {
                                AWS_MOBILE.api.dialog('', ret)
                            },
                            error: function (xhr) {
                                let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                                AWS_MOBILE.events.onAjaxError(ret, error);
                            }
                        });
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        });
    </script>
    {/if}
</div>
{/block}
