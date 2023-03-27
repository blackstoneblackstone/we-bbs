{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('账号设置')}</div>
    <a class="aui-header-right text-left font-weight-bold text-primary" onclick="AWS_MOBILE.api.ajaxForm('#profileForm')">{:L('保存')}</a>
</header>
{/block}

{block name="main"}

<div class="main-container mt-1">
    <div class="bg-white">
        <form class="p-3 form-horizontal" action="{:url('account/save_profile')}" id="profileForm">
            <input type="hidden" name="uid" value="{$user_id}">
            <div class="form-group row text-center">
                <div class="col-md-2">
                    <div id="fileList_cover" class="uploader-list"></div>
                    <div id="filePicker_cover" style="margin: 0 auto">
                        <a href="{$user_info.avatar|default='/static/common/image/default-cover.svg'}" target="_blank">
                            <img class="image_preview_info" src="{$user_info.avatar|default='/static/common/image/default-cover.svg'}"
                                 id="cover_preview" width="100" height="100" alt="{$user_info.user_name}" style="border-radius: 50%">
                        </a>
                    </div>
                    <input type="hidden" name="avatar" value="{$user_info.avatar|default='/static/common/image/default-cover.svg'}" class="avatar-input">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label"> {:L('昵称')} </label>
                <div class="col-10">
                    <input type="text" class="form-control" name="nick_name" placeholder="{:L('输入用户昵称')}" value="{$user_info.nick_name}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label"> {:L('签名')} </label>
                <div class="col-10">
                    <textarea class="form-control" name="signature" placeholder="{:L('输入个人签名')}">{$user_info.signature}</textarea>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label"> {:L('性别')} </label>
                <div class="col-10 col-form-label">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="inlineRadio1" value="1" {if $user_info.sex == 1} checked {/if}>
                        <label class="form-check-label" for="inlineRadio1">{:L('男')}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="inlineRadio2" value="2" {if $user_info.sex == 2} checked {/if}>
                        <label class="form-check-label" for="inlineRadio2">{:L('女')}</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="sex" id="inlineRadio3" value="0" {if $user_info.sex == 0} checked {/if}>
                        <label class="form-check-label" for="inlineRadio3">{:L('保密')}</label>
                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label"> {:L('生日')} </label>
                <div class="col-10">
                    <input type="text" class="form-control" id="birthday" name="birthday" value="{$user_info['birthday'] ? date('Y-m-d',$user_info['birthday']) : date('Y-m-d',time())}">
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label"> {:L('邮箱')} </label>
                <div class="col-7">
                    <input type="email" class="form-control" name="email" placeholder="{:L('暂未绑定邮箱信息')}" value="{$user_info.email}" readonly disabled>
                </div>
                <div class="col-3">
                    <button class="btn btn-primary btn-sm aw-ajax-open nbutton mt-1" data-url="{:url('account/modify_email')}">{:L('修改')}</button>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-2 col-form-label"> {:L('手机')} </label>
                <div class="col-7">
                    <input type="text" class="form-control" placeholder="{:L('暂未绑定手机号码')}" name="mobile" value="{$user_info['mobile'] ? substr_replace($user_info.mobile,'****',3,4) : L('暂未绑定手机号码')}" readonly disabled>
                </div>
                <div class="col-3">
                    <button class="btn btn-primary btn-sm aw-ajax-open nbutton mt-1" data-url="{:url('account/modify_mobile')}">{:L('修改')}</button>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-3 col-form-label"> {:L('主页标识')} </label>
                <div class="col-9">
                    <input type="text" class="form-control" name="url_token" value="{$user_info['url_token'] ? $user_info['url_token'] : $user_info['user_name']}">
                </div>
            </div>
        </form>
    </div>
    <script>
        $(function (){
            // 选择日期
            $('#birthday').date({
                title: '选择日期',
                theme: 1,
                type: 1,
                curdate: false,
                endyear: (new Date()).getFullYear()
            }, function (date) {
                $('#birthday').val(date[0])
            })

            //上传头像封面
            AWS_MOBILE.upload.webUpload('filePicker_cover','cover_preview','avatar','avatar');
        })
    </script>
</div>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}{/block}