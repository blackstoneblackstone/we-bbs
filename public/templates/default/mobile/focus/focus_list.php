{if $list}
{volist name="list" id="v"}
{if $type=='question'}
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        {if $v.is_anonymous}
                        <a href="javascript:;" class="aw-username">
                            <img src="/static/common/image/default-avatar.svg" width="45" height="45" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                        </a>
                        {else/}
                        <a href="{$v['user_info']['url']}" data-pjax="WrapBody" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                            <img src="{$v['user_info']['avatar']}" width="45" height="45" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">
                        </a>
                        {/if}
                    </div>
                    <div class="float-left ml-2">
                        {if $v.is_anonymous}
                        <a href="javascript:;" class="aw-username">{:L('匿名用户')}</a>
                        {else/}
                        <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                            {$v['user_info']['name']}
                        </a>
                        {/if}
                        <span class="d-block text-muted font-9">{:L('发起了提问')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['create_time'])}</em>
                    </div>
                </div>
                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        <span class="tip-s1 badge badge-secondary">{:L('问')}</span>
                        <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                    </div>
                </div>
                <div class="desc">
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v.detail|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'question','{$v.id}');">
                        <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_count']}</span></div>
                <div class="aui-btn" onclick="AWS_MOBILE.User.shareBox('{$v.title}','{:url('question/detail',['id'=>$v.id],true,true)}')"><i class="iconfont iconshare"></i>{:L('分享问题')}</div>
            </div>
        </div>
    </div>
</div>
{/if}

{if $type=='friend'}
<div class="aui-card mb-1 aui-card-image">
    <div class="aui-card-main">
        <div class="text-center p-3">
            <a href="{$v.user_info.url}" class="aw-username font-12 d-block" data-id="{$v.user_info.uid}">
                <img src="{$v.user_info.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.user_info.name}" width="100" height="100" style="border-radius: 50%">
                <p class="d-block position-relative">
                    <span>{$v.user_info.name}</span>
                </p>
            </a>
            <p class="text-color-info font-9 mt-2 aw-one-line">{$v.user_info.signature|default=L('这家伙还没有留下自我介绍～')}</p>
        </div>
        {if $user_id && $v.user_info.uid!=$user_id}
        <div class="text-center pb-3" style="background: none;">
            <a class="{if $v.has_focus}active ygz{/if} px-4 btn btn-sm btn-primary mr-3" href="javascript:;" onclick="AWS_MOBILE.User.focus(this,'user','{$v.user_info.uid}')">{if $v.has_focus}{:L('取消关注')}{else}{:L('关注')}{/if}</a>
            <a class="px-4 btn btn-sm btn-outline-primary" href="javascript:;" onclick="AWS_MOBILE.User.inbox('{$v.user_info.nick_name}')">{:L('私信')}</a>
        </div>
        {/if}
    </div>
    <div class="aui-card-down row-before">
        <div class="aui-btn">{$setting.score_unit} {:num2string($v.user_info.integral)}</div>
        <div class="aui-btn">{$setting.power_unit} {$v.user_info.reputation}</div>
        <div class="aui-btn">{:L('获赞')} {$v.user_info.agree_count}</div>
    </div>
</div>
{/if}

{if $type=='fans'}
<div class="aui-card mb-1 aui-card-image">
    <div class="aui-card-main">
        <div class="text-center p-3">
            <a href="{$v.user_info.url}" class="aw-username font-12 d-block" data-id="{$v.user_info.uid}">
                <img src="{$v.user_info.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.user_info.name}" width="100" height="100" style="border-radius: 50%">
                <p class="d-block position-relative">
                    <span>{$v.user_info.name}</span>
                </p>
            </a>
            <p class="text-color-info font-9 mt-2 aw-one-line">{$v.user_info.signature|default=L('这家伙还没有留下自我介绍～')}</p>
        </div>
        {if $user_id && $v.user_info.uid!=$user_id}
        <div class="text-center pb-3" style="background: none;">
            <a class="{if $v.has_focus}active ygz{/if} px-4 btn btn-sm btn-primary mr-3" href="javascript:;" onclick="AWS_MOBILE.User.focus(this,'user','{$v.user_info.uid}')">{if $v.has_focus}{:L('互相关注')}{else}{:L('关注')}{/if}</a>
            <a class="px-4 btn btn-sm btn-outline-primary" href="javascript:;" onclick="AWS_MOBILE.User.inbox('{$v.user_info.nick_name}')">{:L('私信')}</a>
        </div>
        {/if}
    </div>
    <div class="aui-card-down row-before">
        <div class="aui-btn">{$setting.score_unit} {:num2string($v.user_info.integral)}</div>
        <div class="aui-btn">{$setting.power_unit} {$v.user_info.reputation}</div>
        <div class="aui-btn">{:L('获赞')} {$v.user_info.agree_count}</div>
    </div>
</div>
{/if}

{if $type=='column'}
<div class="aui-card mb-1 aui-card-image">
    <div class="aui-card-main">
        <div class="text-center p-3">
            <a href="{:url('column/detail',['id'=>$v['id']])}" class="d-block text-center">
                <img src="{$v.cover}" alt="{$v.name}" width="100" height="100" onerror="this.src='/static/common/image/default-cover.svg'" style="border-radius: 50%">
                <span class="d-block">{$v.name}</span>
            </a>
            <p class="text-color-info font-9 mt-2 aw-two-line">{$v.description|raw}</p>
        </div>
    </div>
    <div class="aui-card-down row-before" style="padding: 0;">
        <div class="aui-list" style="background: none;">
            <div class="aui-list-left text-muted">
                <span class="mr-4">{:L('文章')} {$v.post_count|num2string}</span>
                <span>{:L('关注')} {$v.focus_count|num2string}</span>
            </div>
            <div class="aui-list-right">
                <a href="{:url('column/detail',['id'=>$v['id']])}" data-pjax="pageMain"><span style="color: #aaa;">{:L('查看详情')}</span><i class="iconfont aui-btn-right iconright1"></i></a>
            </div>
        </div>
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
            <p class="mb-0 font-8 aw-one-line mt-1">{$v.description|raw}</p>
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
