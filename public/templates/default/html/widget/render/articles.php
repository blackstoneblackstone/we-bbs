{if !empty($list)}
{foreach $list as $key=>$v}
<!--自定义内容列表页拓展钩子,可自定义内容页插入内如，如每多少条内容显示一条广告-->
{:hook('postsListsExtend',['info'=>$v,'key'=>$key,'type'=>'article'])}
<div class="articleItem">
    <dl>
        <dd>
            <div class="n-title">
                {:hook('article_badge')}
                {if $v.set_top}
                <span class="tip-d badge badge-secondary">{:L('顶')}</span>
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
                    {if $v['topics']}
                    <div class="tags mt-1">
                        {volist name="$v['topics']" id="topic"}
                        <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                        {/volist}
                    </div>
                    {/if}
                </div>
                {else/}
                <div class="aw-three-line">
                    {$v.message|raw}
                </div>
                {if $v['topics']}
                <div class="tags mt-1">
                    {volist name="$v['topics']" id="topic"}
                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                    {/volist}
                </div>
                {/if}
                {/if}
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
</div>
{/foreach}
{$page|raw}
{else/}
<p class="text-center py-3 text-muted">
    <img src="{$cdnUrl}/static/common/image/empty.svg">
    <span class="d-block">{:L('暂无内容')}</span>
</p>
{/if}