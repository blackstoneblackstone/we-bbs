{if $list}
{volist name="list" id="v"}
{if $type=='question'}
<dl>
    <dt>
        {if $v.is_anonymous}
        <a href="javascript:;" class="aw-username">
            <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
        </a>
        {else/}
        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}" target="_blank">
            <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
        </a>
        {/if}
        <i>{:L('发起了提问')}</i>
        <em class="time">{:date_friendly($v['create_time'])}</em>
        {if $v['topics']}
        <div class="tag d-inline-block">
            {volist name="$v['topics']" id="topic"}
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
            {/volist}
        </div>
        {/if}
    </dt>
    <dd>
        <div class="n-title">
            <span class="tip-s1 badge badge-secondary">{:L('问答')}</span>
            <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title|raw}</a>
        </div>
        <div class="pcon">
            <div class="aw-two-line">{$v.detail|raw}</div>
        </div>
    </dd>
    <dd>
        <label>
            <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注问题')} <span class="badge focus-count">{$v.focus_count}</span></a>
        </label>
        <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{$v.thanks_count}{:L('感谢')}</label>
        <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['comment_count']}{:L('评论')}</label>
    </dd>
</dl>
{/if}

{if $type=='friend'}
<div class="p-3 bg-white mb-1 border-bottom">
    <div class="overflow-hidden position-relative">
        <div class="float-left">
            <a href="{$v.user_info.url}" class="aw-username rounded d-block">
                <img src="{$v.user_info.avatar}" class="rounded" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.user_info.name}" width="80" height="80">
            </a>
            {if $v['user_info']['is_online']}
            <span class="online-dot"></span>
            {else/}
            <span class="offline-dot"></span>
            {/if}
        </div>

        <div class="float-right" style="width: calc(100% - 95px)">
            <h3 class="mb-1">
                <a href="{$v.user_info.url}" class="aw-username font-12">{$v.user_info.name}</a>
            </h3>
            <p class="text-muted font-9 aw-one-line">{$v['user_info']['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
            <div class="text-muted mt-1">
                <label>{:get_setting("score_unit")}:{$v.user_info.integral}</label>
            </div>
        </div>

        {if $user_id && $v['user_info']['uid']!=$user_id}
        <div class="position-absolute" style="right: 0;bottom: 0">
            <button class="btn btn-primary btn-sm px-3 {if $v.has_focus}active ygz{/if} mr-3" onclick="AWS.User.focus(this,'user','{$v.user_info.uid}')">{if $v.has_focus}{:L('已关注')}{else}{:L('关注')}{/if}</button>
            <button class="btn btn-outline-primary px-3 btn-sm" onclick="AWS.User.inbox('{$v.user_info.nick_name}')">{:L('私信')}</button>
        </div>
        {/if}
    </div>
</div>
{/if}

{if $type=='fans'}
<div class="p-3 bg-white mb-1 border-bottom">
    <div class="overflow-hidden position-relative">
        <div class="float-left">
            <a href="{$v.user_info.url}" class="aw-username rounded d-block">
                <img src="{$v.user_info.avatar}" class="rounded" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.user_info.name}" width="80" height="80">
            </a>
            {if $v['user_info']['is_online']}
            <span class="online-dot"></span>
            {else/}
            <span class="offline-dot"></span>
            {/if}
        </div>

        <div class="float-right" style="width: calc(100% - 95px)">
            <h3 class="mb-1">
                <a href="{$v.user_info.url}" class="aw-username font-12">{$v.user_info.name}</a>
            </h3>
            <p class="text-muted font-9 aw-one-line">{$v['user_info']['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
            <div class="text-muted mt-1">
                <label>{:get_setting("score_unit")}:{$v.user_info.integral}</label>
            </div>
        </div>

        {if $user_id && $v['user_info']['uid']!=$user_id}
        <div class="position-absolute" style="right: 0;bottom: 0">
            <button class="btn btn-primary btn-sm px-3 {if $v.has_focus}active ygz{/if} mr-3" onclick="AWS.User.focus(this,'user','{$v.user_info.uid}')">{if $v.has_focus}{:L('互相关注')}{else}{:L('关注')}{/if}</button>
            <button class="btn btn-outline-primary px-3 btn-sm" onclick="AWS.User.inbox('{$v.user_info.nick_name}')">{:L('私信')}</button>
        </div>
        {/if}
    </div>
</div>
{/if}

{if $type=='column'}
<div class="p-3 bg-white border-bottom">
    <div class="mt-1">
        <h3 class="aw-one-line font-12"><a href="{:url('column/detail',['id'=>$v['id']])}">{$v.name}</a></h3>
        <p class="text-muted my-1 aw-two-line">{$v.description}</p>
        <a href="javascript:;" class="text-color-info mr-2 font-9">{$v.join_count|num2string} {:L('用户')}</a>
        <a href="javascript:;" class="text-color-info mr-2 font-9">{$v.post_count|num2string} {:L('内容')} </a>
        <a href="javascript:;" class="text-color-info font-9">{$v.focus_count|num2string} {:L('关注')} </a>
    </div>
</div>
{/if}

{if $type=='topic'}
<div class="px-3 py-2 rounded topic-item">
    <dl class="position-relative">
        <dt>
            <a href="{:url('topic/detail',['id'=>$v['id']])}">
                <img src="{$v['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'" class="rounded">
            </a>
        </dt>
        <dd class="info position-relative">
            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
            <p class="mb-0 font-8 aw-one-line">{$v.description|raw}</p>
            <p class="position-absolute" style="bottom: 0">
                <span class="mr-3 font-8 text-muted">{:L('正在讨论')}：{$v.discuss}</span>
                <span class="font-8 text-muted"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
            </p>
        </dd>
    </dl>
</div>
{/if}
{/volist}
{/if}
