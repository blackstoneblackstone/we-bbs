{if !empty($list)}
<div class="aw-common-list">
    {volist name="$list" id="v"}
    {switch name="$v['search_type']"}
    {case value="question" }
    <dl>
        <dt>
            {if (!$v['answer_info'])}
            {if $v.is_anonymous}
            <a href="javascript:;" class="aw-username">
                <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="匿名用户">匿名用户
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" data-pjax="WrapBody" class="aw-user-name" data-id="{$v['user_info']['uid']}">
                <img src="{$v['user_info']['avatar']}" class="circle" alt="{$v['user_info']['name']}">{$v['user_info']['name']}
            </a>
            {/if}
            <i>发起了提问</i>
            <em class="time">{:date_friendly($v['create_time'])}</em>
            {else/}
            {if $v['answer_info']['is_anonymous']}
            <a href="javascript:;" class="aw-username" >
                <img src="/static/common/image/default-avatar.svg" class="aw-user-img circle" alt="匿名用户">匿名用户
            </a>
            {else/}
            <a href="{$v['answer_info']['user_info']['url']}" data-id="{$v['answer_info']['uid']}" class="aw-user-name" data-pjax="WrapBody">
                <img src="{$v['answer_info']['user_info']['avatar']}" class="circle" alt="{$v['answer_info']['user_info']['name']}">{$v['answer_info']['user_info']['name']}
            </a>
            {/if}
            <i>回复了问题（{$v['answer_count']}回复）</i>
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
                <span class="tip-s1 badge badge-secondary">问答</span>
                {if $v.set_top}
                <span class="tip-d badge badge-secondary">顶</span>
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
            <label class="ml-3 mr-3"><i class="iconfont">&#xe870;</i> {$v.agree_count}</label>
            <label class="mr-3"><i class="iconfont">&#xe601;</i> {$v['comment_count']}评论</label>
            {else/}
            <label class="dz">
                <a type="button" class="btn btn-primary btn-sm aw-ajax-agree  {$v['answer_info']['vote_value']==1 ? 'active' : ''}" title="点赞回答" onclick="AWS.User.agree(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe870;</i>赞同<span class="badge">{$v['answer_info']['agree_count']}</span></a>
                <a type="button" class="btn btn-primary btn-sm aw-ajax-against  {$v['answer_info']['vote_value']==-1 ? 'active' : ''}" title="反对回答" onclick="AWS.User.against(this,'answer','{$v['answer_info']['id']}');"><i class="iconfont">&#xe68f;</i>反对<span class="badge"></span></a>
            </label>
            <label class="ml-3 mr-3"><i class="iconfont">&#xe625;</i>{$v['answer_info']['thanks_count']}人感谢</label>
            <label class="mr-3"><i class="iconfont">&#xe601;</i>{$v['answer_info']['comment_count']}评论</label>
            {/if}
        </dd>
    </dl>
    {/case}

    {case value="article" }
    <dl>
        <dd>
            <div class="n-title">
                <span class="tip-s2 badge badge-secondary">文章</span>
                {:hook('article_badge')}
                {if $v.set_top}
                <span class="tip-d badge badge-secondary">顶</span>
                {/if}
                <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']|raw}</a>
            </div>
            <div class="pcon {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}row{/if}">
                {if ($v['img_list'] || $v.cover) && get_theme_setting('common.list_show_image')=='Y'}
                <div class="col-md-3 aw-list-img"><img src="{$v.cover|default=$v['img_list'][0]}" class="rounded aw-cut-img" alt="{$v['title']}" width="100%"></div>
                <div class="ov-3 col-md-9">
                    <div class="aw-three-line">
                        {$v.message|raw}
                    </div>
                </div>
                {else/}
                <div class="aw-three-line">
                    {$v.message|raw}
                </div>
                {/if}
            </div>
        </dd>
        <dd>
            <label class="dz">
                <a type="button" class="btn btn-primary btn-sm" onclick="AWS.User.agree(this,'article','{$v['id']}');"><i class="iconfont">&#xe870;</i>赞同<span class="badge">{$v['agree_count']}</span></a>
            </label>
            <label class="ml-4 mr-2"><i class="iconfont">&#xe640;</i> {$v['view_count']?:''}浏览</label>
            <label class="mr-2"><i class="iconfont">&#xe601;</i> {$v['comment_count']}评论</label>
            <label class="mr-2"><i class="iconfont">&#xe699;</i><a href="{$v['user_info']['url']}" style="color: #8790a4" class="aw-username name" data-id="{$v['user_info']['uid']}">{$v['user_info']['name']}</a></label>
            <label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label>
        </dd>
    </dl>
    {/case}

    {default /}
    {/switch}
    {/volist}
</div>
{/if}