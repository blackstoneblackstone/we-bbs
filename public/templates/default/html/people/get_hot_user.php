{volist name="data" id="v"}
<div class="mb-2 col-12">
    <dl class="clearfix position-relative p-2 border rounded">
        <dt class="float-left mr-2">
            <a href="{:get_user_url($val['uid'])}" class="aw-username" data-id="{$v.uid}">
                <img src="{$v['avatar']|default='/static/common/image/default-avatar.svg'}" class="rounded"  height="45" width="45">
            </a>
        </dt>
        <dd class="mb-0">
            <a href="{:get_user_url($val['uid'])}" class="aw-username" data-id="{$v.uid}">{$v.nick_name}</a>
            <p class="mb-0">
                {:L('提问')}:{$v.question_count} &nbsp;&nbsp;{:L('获赞')}:{$v.agree_count}
            </p>
        </dd>
        <dd class="position-absolute" style="right: 5px;bottom: 5px">
            <a href="javascript:;" class="cursor-pointer btn btn-sm btn-primary px-3 {$v['is_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'user','{$v.uid}')" >{:L($v['is_focus'] ? '已关注' : '关注')}</a>
        </dd>
    </dl>
</div>
{/volist}