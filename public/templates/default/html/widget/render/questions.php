{if !empty($list)}
{foreach $list as $key=>$v}
{:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'question'])}
<div class="questionItem">
    <dl>
        <dt>
            {if (!$v['answer_info'])}
            {if $v.is_anonymous}
            <a href="javascript:;" class="aw-username">
                <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" data-pjax="WrapBody" class="aw-user-name" data-id="{$v['user_info']['uid']}" target="_blank">
                <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
            </a>
            {/if}
            <i>{:L('发起了提问')}</i>
            <em class="time">{:date_friendly($v['update_time'])}</em>
            {else/}
            {if $v['answer_info']['is_anonymous']}
            <a href="javascript:;" class="aw-username" >
                <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="{:L('匿名用户')}">{:L('匿名用户')}
            </a>
            {else/}
            <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" target="_blank">
                <img src="{$v['answer_info']['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="circle" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
            </a>
            {/if}
            <i>{:L('回复了问题')}</i>
            <em class="time">{:date_friendly($v['answer_info']['create_time'])}</em>
            {/if}
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
                {if $v.set_top}
                <span class="tip-d badge badge-secondary">{:L('顶')}</span>
                {/if}
                {if (!$v['answer_info'])}
                <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title|raw}</a>
                {else/}
                <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}" target="_blank">{$v.title|raw}</a>
                {/if}
            </div>
            <div class="pcon {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                {if $v['img_list'] && get_theme_setting('common.list_show_image')=='Y'}
                <div class="col-md-12 t-imglist row">
                    {volist name="$v['img_list']" id="img" key="k"}
                    {if($k<4)}
                    <div class="col-md-4 aw-list-img">
                        <img src="{$img|default='/static/common/image/default-cover.svg'}?w=100&h=100" class="rounded w-100 aw-cut-img" style="margin-bottom: 5px;" >
                    </div>
                    {/if}
                    {/volist}
                </div>
                <div class="ov-3 col-md-12">
                    <div class="aw-two-line">{$v.detail|raw}</div>
                </div>
                {else/}
                <div class="aw-two-line">{$v.detail|raw}</div>
                {/if}
            </div>
        </dd>
        <dd>
            {if (!$v['answer_info'])}
            <label>
                <a type="button" href="javascript:;" class="{$v['has_focus'] ? 'ygz' : 'gz'} btn btn-primary btn-sm" onclick="AWS.User.focus(this,'question','{$v.id}')">{$v['has_focus'] ? L('已关注') : L('关注问题')} <span class="badge focus-count">{$v.focus_count}</span></a>
            </label>
            <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
            <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}{:L('评论')}</label>
            {else/}
            <label class="dz">
                <a type="button" href="javascript:;" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>{:L('赞同')}<span class="badge">{$v['answer_info']['agree_count']}</span></a>
                <a type="button" href="javascript:;" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="{:L('反对回答')}" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>{:L('反对')}<span class="badge"></span></a>
            </label>
            <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{:L('%s 人感谢', $v['answer_info']['thanks_count'])}</label>
            <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}{:L('评论')}</label>
            <label class="mr-3"><i class="fa fa-comment-alt"></i>{$v['answer_count']}{:L('回复')}</label>
            {/if}
        </dd>
    </dl>
</div>
{/foreach}
{$page|raw}
{else/}
<p class="text-center py-3 text-muted">
    <img src="{$cdnUrl}/static/common/image/empty.svg">
    <span class="d-block">{:L('暂无内容')}</span>
</p>
{/if}
