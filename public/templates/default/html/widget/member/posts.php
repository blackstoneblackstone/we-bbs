{if $list}
{foreach $list as $key=>$v}
{if $v['item_type']=="question" }
<dl>
    <dt>
        <span class="text-muted text-muted2">{$v.remark|raw}</span>
        {if isset($v['topics']) && $v['topics']}
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
            <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
        </div>
        <div class="aw-two-line pcon">
            {if !$v['answer_info']}
            <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.detail|raw}</a>:
            {else/}
            {if $v['answer_info']['is_anonymous']}
            <a href="javascript:;" class="aw-username" >匿名用户</a>:
            {else/}
            <a href="{$v['answer_info']['user_info']['url']}" class="aw-username" >{$v['answer_info']['user_info']['name']}</a>:
            {/if}
            <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" target="_blank">{$v.answer_info.content|raw}</a>
            {/if}
        </div>
    </dd>
    <dd>
        <label>
            <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注问题')} <span class="badge">{$v.focus_count}</span></a>
        </label>
        <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
        <label class="mr-3"><i class="iconfont">&#xe601;</i> {:L('%s 评论',$v['comment_count'])}</label>
    </dd>
</dl>
{/if}

{if $v['item_type']=="article" }
<dl>
    <dt><span class="text-muted text-muted2">{$v.remark|raw}</span></dt>
    <dd>
        <div class="n-title">
            <span class="tip-s2 badge badge-secondary">{:L('文章')}</span>
            {:hook('article_badge')}
            {if $v.set_top}
            <span class="tip-d badge badge-secondary">{:L('顶')}</span>
            {/if}
            <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']}</a>
        </div>
        <div class="pcon">
            {$v.message|raw}
        </div>
    </dd>
    <dd>
        <label class="dz">
            <a type="button" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['agree_count']}</span></a>
        </label>
        <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {:L('%s 浏览',$v['view_count']?:'')}</label>
        <label class="mr-2"><i class="iconfont">&#xe601;</i> {:L('%s 评论',$v['comment_count'])}</label>
        <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
        <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
    </dd>
</dl>
{/if}

{if $v['item_type']=="answer" && isset($v['answer_info'])}
<dl>
    <dt class="mb-2">
        <span class="text-muted text-muted2">{$v.remark|raw}</span>
        {if isset($v['topics']) && $v['topics']}
        <div class="tag d-inline-block">
            {volist name="$v['topics']" id="topic"}
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
            {/volist}
        </div>
        {/if}
    </dt>
    <dd class="n-title">
        <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}">{$v.title}</a>
    </dd>
    <dd class="aw-two-line pcon">
        {if !$v['answer_info']}
        <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.detail|raw}</a>:
        {else/}
        {if $v['answer_info']['is_anonymous']}
        <a href="javascript:;" class="aw-username" >匿名用户</a>:
        {else/}
        <a href="{$v['answer_info']['user_info']['url']}" class="aw-username" >{$v['answer_info']['user_info']['name']}</a>:
        {/if}
        <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" target="_blank">{$v.answer_info.content|raw}</a>
        {/if}
    </dd>
    <dd>
        <label class="dz">
            <a type="button" class="btn btn-primary btn-sm aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
            <a type="button" class="btn btn-primary btn-sm aw-ajax-against {$v['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
        </label>
        <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢',$v['answer_info']['thanks_count'])}</label>
        <label class="mr-3"><i class="iconfont">&#xe601;</i>{:L('%s 评论',$v['answer_info']['comment_count'])}</label>
        <label class="mr-3"><i class="fa fa-comment-alt"></i>{:L('%s 回复',$v['answer_count'])}</label>
    </dd>
</dl>
{/if}

{if $type=='friend' && $v.user_info}
<div class="py-3 bg-white mb-1 border-bottom">
    <div class="overflow-hidden position-relative">
        <div class="float-left">
            <a href="{$v.user_info.url}" class="aw-username rounded d-block">
                <img src="{$v.user_info.avatar}" alt="{$v.user_info.name}" width="80" height="80">
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

{if $type=='fans' && $v.user_info}
<div class="py-3 bg-white mb-1 border-bottom">
    <div class="overflow-hidden position-relative">
        <div class="float-left">
            <a href="{$v.user_info.url}" class="aw-username rounded d-block">
                <img src="{$v.user_info.avatar}" alt="{$v.user_info.name}" width="80" height="80">
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

{if $type=='column'}
<div class="bg-white py-2 border-bottom">
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
<div class="py-2 rounded topic-item">
    <dl class="position-relative">
        <dt>
            <a href="{:url('topic/detail',['id'=>$v['id']])}">
                <img src="{$v['pic']|default='/static/common/image/topic.svg'}" class="rounded">
            </a>
        </dt>
        <dd class="info position-relative">
            <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
            <p class="mb-0 font-8 aw-two-line">{$v.description|raw}</p>
            <p class="position-absolute" style="bottom: 0">
                <span class="mr-3 font-8 text-muted">{:L('正在讨论')}：{$v.discuss}</span>
                <span class="font-8 text-muted"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
            </p>
        </dd>
    </dl>
</div>
{/if}

{/foreach}
{$page|raw}
{else/}
<p class="text-center py-3 text-muted">
    <img src="{$cdnUrl}/static/common/image/empty.svg">
    <span class="d-block">{:L('暂无内容')}</span>
</p>
{/if}
