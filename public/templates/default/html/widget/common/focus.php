{if !empty($list)}
{volist name="list" id="v"}
{if ($v['item_type']=="question" || $v['relation_type']=='question') && $v['item_type']!='answer' }
<dl>
    <dt>
        <span class="text-muted">{$v.remark|raw}</span>
        {if isset($v['topics']) && !empty($v['topics'])}
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
        <div class="pcon aw-two-line">
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
        <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
    </dd>
</dl>
{/if}

{if $v['item_type']=="article" || $v['relation_type']=='article' }
<dl class="article">
    <dt>
        <span class="text-muted">{$v.remark|raw}</span>
        {if isset($v['topics']) && !empty($v['topics'])}
        <div class="tag d-inline-block">
            {volist name="$v['topics']" id="topic"}
            <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title|raw}</em></a>
            {/volist}
        </div>
        {/if}
    </dt>
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
        <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {$v['view_count']?:''}{:L('浏览')}</label>
        <label class="mr-2"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
        <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
        <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
    </dd>
</dl>
{/if}

{if $v['item_type']=="answer" && isset($v['answer_info'])}
<dl>
    <dt class="mb-2">
        <span class="text-muted">{$v.remark|raw}</span>
        {if isset($v['topics']) && !empty($v['topics'])}
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
        <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v['answer_info']['thanks_count'])}</label>
        <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}{:L('评论')}</label>
    </dd>
</dl>
{/if}
{/volist}
{$page|raw}
{else/}
<p class="text-center py-3 text-muted">
    <img src="{$cdnUrl}/static/common/image/empty.svg">
    <span class="d-block">{:L('暂无内容')}</span>
</p>
{/if}