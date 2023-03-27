<style>
    .nav-main li{line-height:34px; text-align: left;}
    .nav-main li a{display: block;  padding-left:40px;}
    .badge-primary{font-size: 12px; font-weight: normal}
    .new-block{ padding:15px 15px; background: #ffffff;}
    .name{ font-size: 16px;}
    .dropdown-item.active a, .dropdown-item:active  a{ color: #ffffff;}
    .dropdown-item{ padding:10px 0;}
    .dropdown-item:hover{ color: #ffffff;}
    .nav-tabs-block{background: #ffffff; padding: 0 15px;}
    .list-group .list-group-item.active {
        border-left: 2px solid #563d7c;
        background: #fff;
    }
    .list-group .list-group-item.active > a {
        color: #563d7c;
        font-weight: 600;
    }
    .nav-tabs a.nav-link{ font-size:15px !important;}
    .aw-common-list dl dd h2{ font-size:16px; font-weight: bold;}
    .aw-common-list dl{ border-bottom: 1px solid #f1f1f1; padding: 10px 0;}
    .notefun a{ font-size: 14px;}
    .text-color-info label{ font-size: 14px;}
</style>
<div class="col-md-2 px-0">
    <div class="userNav">
        <div class="userNavAvatar">
            <ul class="list-group border-0">
                <li class="list-group-item {if $thisController=='creator'}active{/if} border-0">
                    <a href="{:url('creator/index')}" data-pjax="wrapMain">
                        <i class="fa fa-home"></i> {:L('用户首页')}
                    </a>
                </li>
                <li class="list-group-item border-0 {if $thisController=='focus'}active{/if}">
                    <a href="{:url('focus/index')}" data-pjax="wrapMain">
                        <i class="fa fa-heart"></i> {:L('我的关注')}
                    </a>
                </li>
                <li class="list-group-item border-0 {if $thisController=='notify'}active{/if}">
                    <a href="{:url('notify/index')}" data-pjax="wrapMain">
                        <i class="fa fa-bell"></i> {:L('我的通知')}
                        {if $user_info['notify_unread']}
                        <span class="badge badge-primary badge-pill">{$user_info['notify_unread']<=99 ? $user_info['notify_unread'] : '99+'}</span>
                        {/if}
                    </a>
                </li>
                <li class="list-group-item border-0 {if $thisController=='inbox'}active{/if}">
                    <a href="{:url('inbox/index')}" data-pjax="wrapMain">
                        <i class="fa fa-comments"></i> {:L('我的私信')}
                        {if $user_info['inbox_unread']}
                        <span class="badge badge-primary badge-pill">{$user_info['inbox_unread']<=99 ? $user_info['inbox_unread'] : '99+'}</span>
                        {/if}
                    </a>
                </li>
                <li class="list-group-item border-0 {if $thisController=='integral'}active{/if}">
                    <a href="{:url('integral/index')}" data-pjax="wrapMain">
                        <i class="fa fa-database"></i> {:L('我的积分')}
                    </a>
                </li>

                {if ($setting['register_type'] != 'close')}
                <li class="list-group-item border-0 {if $thisController=='invitation'}active{/if}">
                    <a href="{:url('invitation/index')}" data-pjax="wrapMain">
                        <i class="fa fa-paper-plane"></i> {:L('我的邀请')}
                        <span class="badge badge-primary badge-pill">{$invite_quota}</span>
                    </a>
                </li>
                {/if}

                <li class="list-group-item border-0 {if $thisController=='favorite'}active{/if}">
                    <a href="{:url('favorite/index')}" data-pjax="wrapMain">
                        <i class="fa fa-flag"></i> {:L('我的收藏')}
                    </a>
                </li>

                <li class="list-group-item border-0 {if $thisController=='draft'}active{/if}">
                    <a href="{:url('draft/index')}" data-pjax="wrapMain">
                        <i class="fa fa-edit"></i> {:L('我的草稿')}
                    </a>
                </li>

                <li class="list-group-item border-0 {if $thisController=='approval'}active{/if}">
                    <a href="{:url('approval/index')}" data-pjax="wrapMain">
                        <i class="fa fa-user-clock "></i> {:L('审核记录')}
                    </a>
                </li>

                <li class="list-group-item border-0 {if $thisController=='records'}active{/if}">
                    <a href="{:url('records/index')}" data-pjax="wrapMain">
                        <i class="fa fa-user-clock "></i> {:L('浏览记录')}
                    </a>
                </li>

                <!--用户菜单拓展钩子-->
                {:hook('user_nav')}
                <li class="list-group-item border-0 {if $thisController=='setting'}active{/if}">
                    <a href="{:url('setting/profile')}" data-pjax="wrapMain">
                        <i class="far fa-sun"></i> {:L('账号设置')}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>