{volist name="list" id="v"}
<div class="aui-card mb-1 aui-card-image">
    <div class="aui-card-main">
        <div class="text-center p-3">
            <a href="{$v.url}" class="aw-username font-12 d-block" data-id="{$v.uid}">
                <img src="{$v.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.name}" width="100" height="100" style="border-radius: 50%">
                <p class="d-block position-relative">
                    <span>{$v.name}</span>
                    {if $v.verified}
                    <img src="{$v.verified_icon}" class="position-relative" width="20" height="20" style="top: 3px">
                    {/if}
                </p>
                <strong class="aui-badge aui-badge-success">{$v['group_name']}</strong>
            </a>
            <p class="text-color-info font-9 mt-2 aw-one-line">{$v['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
        </div>
        {if $user_id && $v['uid']!=$user_id}
        <div class="text-center pb-3" style="background: none;">
            <a class="{if $v.has_focus}active ygz{/if} px-4 btn btn-sm btn-primary mr-3" href="javascript:;" onclick="AWS_MOBILE.User.focus(this,'user','{$v.uid}')">
                {if $v.has_focus}{:L('已关注')}{else}{:L('关注')}{/if}
            </a>
            <a class="px-4 btn btn-sm btn-outline-primary" href="javascript:;" onclick="AWS_MOBILE.User.inbox('{$v.nick_name}')">{:L('私信')}</a>
        </div>
        {/if}
    </div>
    <div class="aui-card-down row-before">
        <div class="aui-btn">{$setting.score_unit} {:num2string($v.integral)}</div>
        <div class="aui-btn">{$setting.power_unit} {$v.reputation}</div>
        <div class="aui-btn">{:L('获赞')} {$v.agree_count}</div>
    </div>
</div>
{/volist}