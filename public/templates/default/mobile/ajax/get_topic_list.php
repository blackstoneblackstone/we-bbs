{volist name="list" id="v"}
<div class="px-3 py-2 rounded bg-white mb-1 topic-item">
    <dl class="position-relative">
        <dt class="mb-0">
            <a href="{:url('topic/detail',['id'=>$v['id']])}">
                <img src="{$v['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'"  class="rounded">
            </a>
        </dt>
        <dd class="info position-relative">
            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
            <p class="mb-0 font-8 aw-one-line mt-1">{$v.description|raw}</p>
            <p class="font-8 text-muted position-absolute" style="bottom: 0">
                <span class="mr-3">{:L('讨论')}：{$v.discuss}</span>
                <span class="mr-3"><span class="aw-global-focus-count">{:L('关注')}：{$v.focus}</span></span>
                {if $user_id}
                <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'ygz' : 'gz'}" onclick="AWS_MOBILE.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span>'.L('已关注').'</span>' : '<span> '.L('关注').'</span>'}</a>
                {/if}
            </p>
        </dd>
    </dl>
</div>
{/volist}