{if $list}
{volist name="list" id="v"}
{if $v['search_type']=="question" }
<dl>
    <dt class="mb-1">
        {if $v.is_anonymous}
        <a href="javascript:;" class="aw-username">
            <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
        </a>
        {else/}
        <a href="{$v['user_info']['url']}" data-pjax="WrapBody" class="aw-user-name" data-id="{$v['user_info']['uid']}">
            <img src="{$v['user_info']['avatar']}" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
        </a>
        {/if}
        <i>{:L('发起了提问')}</i>
        <em class="time">{:date_friendly($v['create_time'])}</em>
    </dt>
    <dd>
        <div class="n-title">
            <span class="tip-s1 badge badge-secondary">{:L('问答')}</span>
            {if $v.set_top}
            <span class="tip-d badge badge-secondary">{:L('顶')}</span>
            {/if}
            <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
        </div>
        <div class="pcon mt-1">
            <div class="aw-two-line">
                {$v.detail|raw}
            </div>
        </div>
    </dd>
</dl>
{/if}

{if $v['search_type']=="article" }
<dl>
    <dd>
        <div class="n-title">
            <span class="tip-s2 badge badge-secondary">{:L('文章')}</span>
            {:hook('article_badge')}
            {if $v.set_top}
            <span class="tip-d badge badge-secondary">{:L('顶')}</span>
            {/if}
            <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']|raw}</a>
        </div>
        <div class="pcon mt-1">
            <div class="aw-three-line">
                {$v.message|raw}
            </div>
        </div>
    </dd>
</dl>
{/if}

{if $v['search_type']=="users" }
<dl class="d-flex position-relative">
    <dt class="flex-fill mr-3 mb-0" style="width: 54px;height: 54px">
        <a href="{$v.url}" class="rounded d-block"><img src="{$v.avatar}" alt="{$v.name}" style="width: 54px;height: 54px"></a>
        <span class="{$v['is_online'] ? 'online-dot' : 'offline-dot'}"></span>
    </dt>
    <dd class="flex-fill w-100 mb-0">
        <h3 class="font-11 mb-1"><a href="{$v.url}">{$v.name|raw}</a></h3>
        <p class="aw-one-line">{$v['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
    </dd>
    {if $user_id && $user_id!=$v.uid}
    <dd class="position-absolute" style="right: 0;top:10px">
        <a href="javascript:;" class="mr-3 text-primary {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'user','{$v.uid}')">
            {$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> {:L('已关注')}</span>' : '<span><i class="icon-plus-circle text-primary"></i> {:L('关注')}</span>'}
        </a>
        <a href="javascript:;" class="aw-send-inbox text-smooth">{:L('私信')}</a>
    </dd>
    {/if}
</dl>
{/if}

{if $v['search_type']=="topic" }
<dl class="topic position-relative clearfix d-flex">
    <dt class="flex-fill" style="max-width: 60px;margin-right: 15px">
        <a href="{:url('topic/detail',['id'=>$v['id']])}">
            <img src="{$v['pic']|default='/static/common/image/topic.svg'}" class="rounded" style="width: 60px;height: 60px">
        </a>
    </dt>
    <dd class="flex-fill" style="width: calc(100% - 75px);">
        <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold"># {$v.title|raw}</a>
        <div class="mb-0 font-8 aw-one-line">{$v.description|raw}</div>
        <p class="font-8 text-muted">
            <span class="mr-3">{:L('正在讨论')}：{$v.discuss}</span>
            <span class="mr-3"><span class="aw-global-focus-count">{:L('关注人数')}：{$v.focus}</span></span>
            {if $user_id}
            <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span><i class="icon-minus-circle text-danger"></i> {:L('已关注')}</span>' : '<span><i class="icon-plus-circle text-primary"></i> {:L('关注')}</span>'}</a>
            {/if}
        </p>
    </dd>
</dl>
{/if}

{if $v['search_type']=="answer" }
<dl>
    <dt>

        {if $v['answer_info']['is_anonymous']}
        <a href="javascript:;" class="aw-username" >
            <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
        </a>
        {else/}
        <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" data-pjax="WrapBody">
            <img src="{$v['answer_info']['user_info']['avatar']}" class="circle" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
        </a>
        {/if}
        <i>{:L('回复了问题')}（{$v['answer_count']}{:L('回复')}）</i>
        <em class="time">{:date_friendly($v['answer_info']['create_time'])}</em>
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
            {if $v.set_top}
            <span class="tip-d badge badge-secondary">{:L('顶')}</span>
            {/if}
            <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}">{$v.title|raw}</a>
        </div>
        <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
            {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
            <div class="col-md-12 t-imglist row">
                {volist name="$v['img_list']" id="img" key="k"}
                {if($k<4)}
                <div class="col-md-4 aw-list-img">
                    <img src="{$img|default='/static/common/image/default-cover.svg'}" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" >
                </div>
                {/if}
                {/volist}
            </div>
            <div class="ov-3 col-md-12">
                <div class="aw-two-line">
                    {$v.detail|raw}
                </div>
            </div>
            {else/}
            <div class="aw-two-line">
                {$v.detail|raw}
            </div>
            {/if}
        </div>
    </dd>
    <dd>
        <label class="dz">
            <a type="button" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
            <a type="button" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
        </label>
        <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v['answer_info']['thanks_count'])}</label>
        <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}{:L('评论')}</label>
    </dd>
</dl>
{/if}

{:hook('search_template_'.$v['search_type'],$v)}

{/volist}
{/if}