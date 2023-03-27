{if !empty($list)}
<div class="aw-common-list">
    {volist name="$list" id="v"}
    {if $v['search_type']=="question" }
    <div class="aui-card" style="background: none">
        <div class="aui-card-main pb-1 px-0 pt-0">
            <div class="aui-card aui-card-image">
                <div class="aui-card-main">
                    <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                        <div class="float-left">
                            {if $v.is_anonymous}
                            <a href="javascript:;" class="aw-username">
                                <img src="/static/common/image/default-avatar.svg" width="40" height="40" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
                            </a>
                            {else/}
                            <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                                <img src="{$v['user_info']['avatar']}" width="40" height="40" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">
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
                            {if $v.set_top}
                            <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                            {/if}
                            <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
                        </div>
                    </div>
                    <div class="desc">
                        {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                        {if count($v['img_list'])>1}
                        <div class="d-flex aw-list-img">
                            {volist name="$v['img_list']" id="img" key="k"}
                            {if($k<4)}
                            <img src="{$img|default='/static/common/image/default-cover.svg'}" class="flex-fill mx-1 rounded aw-cut-img" style="margin-bottom: 5px;max-width: 33.3%;border-radius: 5px" >
                            {/if}
                            {/volist}
                        </div>
                        {else/}
                        <img src="{$v['img_list'][0]|default='/static/common/image/default-cover.svg'}" class="rounded aw-cut-img w-100" style="margin-bottom: 5px;max-height: 200px;" >
                        {/if}
                        {/if}
                        <div class="aw-content aw-two-line text-muted font-9">
                            {$v.detail|raw}
                        </div>
                    </div>
                </div>
                <div class="aui-card-down row-before border-top">
                    <div class="aui-btn dz">
                        <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'question','{$v.id}');">
                            <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                        </a>
                    </div>
                    <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_count']}</span></div>
                    <div class="aui-btn"><i class="iconfont icon-ganxie"></i>{$v.thanks_count}</div>
                </div>
            </div>
        </div>
    </div>
    {/if}

    {if $v['search_type']=="article" }
    <div class="aui-card" style="background: none">
        <div class="aui-card-main pb-1 px-0 pt-0">
            <div class="aui-card aui-card-image">
                <div class="aui-card-main">
                    <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                        <div class="float-left">
                            <a href="{$v['user_info']['url']}"  class="aw-user-name" data-id="{$v['user_info']['uid']}" >
                                <img src="{$v['user_info']['avatar']}" width="40" height="40" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">
                            </a>
                        </div>
                        <div class="float-left ml-2">
                            <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}" >
                                {$v['user_info']['name']}
                            </a>
                            <span class="d-block text-muted font-9">{:L('发布了文章')}</span>
                            <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['create_time'])}</em>
                        </div>
                    </div>
                    <div class="img" style="min-height: 24px;height: auto">
                        <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                            <span class="tip-s2 badge badge-secondary">{:L('文')}</span>
                            {:hook('article_badge')}
                            {if $v.set_top}
                            <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                            {/if}
                            <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']|raw}</a>
                        </div>
                    </div>
                    <div class="desc">
                        {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                        <div class="aw-list-img">
                            <img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%">
                        </div>
                        {/if}
                        <div class="aw-content aw-two-line text-muted font-9">
                            {$v.message|raw}
                        </div>
                    </div>
                </div>
                <div class="aui-card-down row-before border-top">

                    <div class="aui-btn dz">
                        <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'article','{$v['id']}');">
                            <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                        </a>
                    </div>
                    <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['comment_count']}</span></div>
                    <div class="aui-btn"><i class="si si-eye"></i> {$v['view_count']?:''}</div>
                </div>
            </div>
        </div>
    </div>
    {/if}

    {if $v['search_type']=="users" }
    <div class="aui-card mb-1 aui-card-image">
        <div class="aui-card-main">
            <div class="text-center p-3">
                <a href="{$v.url}" class="aw-username font-12 d-block" data-id="{$v.uid}">
                    <img src="{$v.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.name}" width="100" height="100" style="border-radius: 50%">
                    <p class="d-block position-relative">
                        <span>{$v.name|raw}</span>
                        {if $v.verified}
                        <img src="{$v.verified_icon}" class="position-relative" width="20" height="20" style="top: 3px">
                        {/if}
                    </p>
                </a>
                <p class="text-color-info font-9 mt-2 aw-one-line">{$v['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
            </div>
            {if $user_id && $v['uid']!=$user_id}
            <div class="text-center pb-3" style="background: none;">
                <a class="{if $v.has_focus}active ygz{/if} px-4 btn btn-sm btn-primary mr-3" href="javascript:;" onclick="AWS_MOBILE.User.focus(this,'user','{$v.uid}')">{if $v.has_focus}{:L('已关注')}{else}{:L('关注')}{/if}</a>
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
    {/if}

    {if $v['search_type']=="topic" }
    <div class="px-3 py-2 rounded bg-white mb-1 topic-item">
        <dl class="position-relative">
            <dt class="mb-0">
                <a href="{:url('topic/detail',['id'=>$v['id']])}">
                    <img src="{$v['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'"  class="rounded">
                </a>
            </dt>
            <dd class="info position-relative">
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="font-weight-bold">#{$v.title|raw}</a>
                <p class="mb-0 font-8 aw-one-line">{$v.description|raw}</p>
                <p class="font-8 text-muted position-absolute" style="bottom: 0">
                    <span class="mr-3">{:L('讨论')}：{$v.discuss}</span>
                    <span class="mr-3"><span class="aw-global-focus-count">{:L('关注')}：{$v.focus}</span></span>
                    {if $user_id}
                    <a href="javascript:;" class="cursor-pointer {$v['has_focus'] ? 'ygz' : 'gz'}" onclick="AWS_MOBILE.User.focus(this,'topic','{$v.id}')" >{$v['has_focus'] ? '<span>{:L('已关注')}</span>' : '<span> {:L('关注')}</span>'}</a>
                    {/if}
                </p>
            </dd>
        </dl>
    </div>
    {/if}

    {if $v['search_type']=="answer" }
    <dl>
        <dt>
            {if (!$v['answer_info'])}
            {if $v.is_anonymous}
            <a href="javascript:;" class="aw-username">
                <img src="/static/common/image/default-avatar.svg" onerror="this.src='/static/common/image/default-avatar.svg'" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
            </a>
            {/if}
            <i>{:L('发起了提问')}</i>
            <em class="time">{:date_friendly($v['create_time'])}</em>
            {else/}
            {if $v['answer_info']['is_anonymous']}
            <a href="javascript:;" class="aw-username" >
                <img src="/static/common/image/default-avatar.svg" onerror="this.src='/static/common/image/default-avatar.svg'" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
            </a>
            {else/}
            <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" data-pjax="WrapBody">
                <img src="{$v['answer_info']['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
            </a>
            {/if}
            <i>{:L('回复了问题')}（{$v['answer_count']}{:L('回复')}）</i>
            <em class="time">{:date_friendly($v['answer_info']['create_time'])}</em>
            {/if}
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
                {if (!$v['answer_info'])}
                <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title|raw}</a>
                {else/}
                <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}">{$v.title|raw}</a>
                {/if}
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
            {if (!$v['answer_info'])}
            <label>
                <a type="button" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注问题')} <span class="badge focus-count">{$v.focus_count}</span></a>
            </label>
            <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v.thanks_count)}</label>
            <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['comment_count']}{:L('评论')}</label>
            {else/}
            <label class="dz">
                <a type="button" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
                <a type="button" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
            </label>
            <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v['answer_info']['thanks_count'])}</label>
            <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}{:L('评论')}</label>
            {/if}
        </dd>
    </dl>
    {/if}

    {:hook('search_template_'.$v['search_type'],$v)}

    {/volist}
</div>
{/if}