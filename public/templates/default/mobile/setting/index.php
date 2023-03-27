{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"> <a href="{:url('creator/index')}" data-pjax="pageMain"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('设置')}</div>
</header>
{/block}

{block name="main"}
<div class="main-container mt-1">
    <div class="aui-card">
        <div class="aui-card-main" style="padding: 0;">
            <ul class="aui-lists">
                <li class="aui-list">
                    <a href="{:url('setting/profile')}" data-pjax="pageMain">
                        <div class="aui-list-left">
                            <i class="far fa-sun"></i>{:L('账号设置')}
                        </div>
                        <div class="aui-list-right">
                            <i class="iconfont aui-btn-right iconright1"></i>
                        </div>
                    </a>
                </li>
                <li class="aui-list">
                    <a href="{:url('setting/notify')}" data-pjax="pageMain">
                        <div class="aui-list-left"><i class="fa fa-bell text-muted"></i>{:L('消息设置')}</div>
                        <div class="aui-list-right"><i class="iconfont aui-btn-right iconright1"></i></div>
                    </a>
                </li>
                <li class="aui-list">
                    <a href="{:url('setting/security')}" data-pjax="pageMain">
                        <div class="aui-list-left"><i class="icon-eye-off"></i>{:L('安全设置')}</div>
                        <div class="aui-list-right"><i class="iconfont aui-btn-right iconright1"></i></div>
                    </a>
                </li>

                <li class="aui-list">
                    <a href="{:url('setting/openid')}" data-pjax="pageMain">
                        <div class="aui-list-left"><i class="fa fa-link"></i>{:L('账号绑定')}</div>
                        <div class="aui-list-right"><i class="iconfont aui-btn-right iconright1"></i></div>
                    </a>
                </li>

                <li class="aui-list">
                    <a href="{:url('setting/verified')}" data-pjax="pageMain">
                        <div class="aui-list-left"><i class="aui-btn-right fa fa-user-check  "></i>{:L('用户认证')}</div>
                        <div class="aui-list-right"><i class="iconfont aui-btn-right iconright1"></i></div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <a data-url="{:url('account/logout')}" data-confirm="{:L('是否确认退出登录')}?" class="aw-ajax-get text-danger w-100 mt-4 bg-white py-2 d-block text-center font-weight-bold font-11">{:L('退出登录')}</a>
</div>
{/block}
{block name="footer"}{/block}