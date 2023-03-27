{if !empty($data)}
{foreach $data as $key=>$v}
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
                    <div class="aw-list-img mb-1" style="max-height: 180px;height: auto">
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
{/foreach}
{/if}