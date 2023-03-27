{extend name="$theme_block" /}
{block name="main"}
<div class="aw-first-login bg-white p-3">
    <div class="first-login-head pt-2">
        <h2 class="font-10 text-muted mb-2">Hi , {$user_info['user_name']} , {:L('欢迎来到')} {:get_setting('site_name')}</h2>
    </div>
    <div class="first-login-body">
        <form action="{:url('account/welcome_first_login')}" method="post">
            <input type="hidden" name="uid" value="{$user_id}" />
            <div class="block mb-0">
                <ul class="nav nav-tabs nav-tabs-block mb-1 aw-pjax-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="" data-pjax="tabMain">{:L('基本信息')}</a>
                    </li>
                </ul>
                <div class="block-content tab-content pb-3" id="tabMain">
                    <div class="profile-details text-center mt-2 mb-3">
                        <div class="profile-image">
                            <div id="fileList_cover" class="uploader-list"></div>
                            <div id="filePicker_cover" style="margin: 0 auto">
                                <a href="{$user_info['avatar']|default='/static/common/image/default-cover.svg'}" target="_blank">
                                    <img class="image_preview_info" src="{$user_info.avatar|default='/static/common/image/default-cover.svg'}" id="cover_preview" width="100" height="100">
                                </a>
                            </div>
                            <input type="hidden" name="avatar" value="{$user_info.avatar|default='/static/common/image/default-cover.svg'}" class="avatar-input">
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label class="aw-form-label"> {:L('昵称')} </label>
                        <div class="aw-form-controls">
                            <label>
                                <input type="text" class="aw-form-control" placeholder="" id="nickname" value="{$user_info['nick_name'] ? $user_info['nick_name'] : ''}" name="nick_name" />
                            </label>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="aw-form-label"> {:L('性别')} </label>
                        <div class="aw-form-controls">
                            <div class="custom-control custom-radio custom-control-inline">
                                <input value="1" type="radio" id="sex1" name="sex" {if $user_info.sex==1} checked {/if} class="custom-control-input">
                                <label class="custom-control-label" for="sex1">{:L('男')}</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input value="2" type="radio" id="sex2" name="sex" {if $user_info.sex==2} checked {/if} class="custom-control-input">
                                <label class="custom-control-label" for="sex2">{:L('女')}</label>
                            </div>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input value="0" type="radio" id="sex0" name="sex" {if $user_info.sex==0} checked {/if} class="custom-control-input">
                                <label class="custom-control-label" for="sex0">{:L('保密')}</label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="aw-form-label"> {:L('生日')} </label>
                        <div class="aw-form-controls">
                            <label>
                                <input type="text" class="aw-form-control" id="birthday" name="birthday" value="{$user_info['birthday'] ? date('Y年m月d日',$user_info['birthday']) : 0}">
                            </label>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <label class="aw-form-label"> {:L('介绍')} </label>
                        <div class="aw-form-controls">
                            <label>
                                <input type="text" class="aw-form-control" placeholder="如：80后IT男.." id="welcome_signature" value="{$user_info['signature'] ? $user_info['signature'] : ''}" name="signature" />
                            </label>
                        </div>
                    </div>
                    <div class="first-login-footer clearfix">
                        <a class="float-left go-back text-muted" onclick="parent.layer.closeAll()" href="javascript:;">{:L('跳过')}</a>
                        <button class="btn btn-primary float-right btn-sm aw-ajax-form" type="button">{:L('完成')}</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    layui.laydate.render({
        elem: '#birthday',
        type:'date',
        format: 'yyyy-MM-dd',
        trigger: 'click'
    });
    AWS.upload.webUpload('filePicker_cover','cover_preview','avatar','avatar');
</script>
{/block}