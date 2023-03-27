<div class="aw-nav-container clearfix bg-white">
    <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
        <li class="nav-item"><a class="nav-link {if $thisAction=='profile'}active {/if}" data-pjax="tabMain" href="{:url('setting/profile')}">{:L('账号设置')}</a></li>
        <li class="nav-item"><a class="nav-link {if $thisAction=='notify'}active {/if}" data-pjax="tabMain" href="{:url('setting/notify')}">{:L('消息通知')}</a></li>
        <li class="nav-item"><a class="nav-link {if $thisAction=='security'}active {/if}" data-pjax="tabMain" href="{:url('setting/security')}">{:L('安全设置')}</a></li>
        <li class="nav-item"><a class="nav-link {if $thisAction=='openid'}active {/if}" data-pjax="tabMain" href="{:url('setting/openid')}">{:L('账号绑定')}</a></li>
        <li class="nav-item"><a class="nav-link {if $thisAction=='verified'}active {/if}" data-pjax="tabMain" href="{:url('setting/verified')}">{:L('用户认证')}</a></li>
        <!--用户设置导航-->
        {:hook('user_setting_nav')}
    </ul>
</div>