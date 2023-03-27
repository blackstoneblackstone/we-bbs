{if !empty($list)}
{volist name="list" id="v"}
{if ($v['item_type']=="question" || $v['relation_type']=='question') && $v['item_type']!='answer' }
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title bg-white px-3 clearfix position-relative text-muted py-1">
                    {$v.remark|raw}
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
                <div class="aui-btn"><i class="iconfont icon-ganxie"></i>{$v.thanks_count}</div>
            </div>
        </div>
    </div>
</div>
{/if}

{if $v['item_type']=="article" || $v['relation_type']=='article' }
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title bg-white px-3 clearfix position-relative text-muted py-1">
                    {$v.remark|raw}
                </div>
                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        <span class="tip-s2 badge badge-secondary">{:L('文')}</span>
                        {:hook('article_badge')}
                        <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['title']}</a>
                    </div>
                </div>
                <div class="desc">
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v.message|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">

                <div class="aui-btn dz">
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'article','{$v['id']}');">
                        <i class="iconfont iconpraise"></i><span>{$v.agree_count}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['comment_count']}</span></div>
                <div class="aui-btn"><i class="far fa-eye"></i> {$v['view_count']?:''}</div>
            </div>
        </div>
    </div>
</div>

{/if}

{if $v['item_type']=="answer" && isset($v['answer_info'])}
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title bg-white px-3 clearfix position-relative text-muted py-1">
                    {$v.remark|raw}
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
                    <a href="javascript:;" class="text-muted aw-ajax-agree {$v['vote_value']==1 ? 'active' : ''}" title="{:L('点赞回答')}" onclick="AWS_MOBILE.User.agree(this,'answer','{$v['answer_info']['id']}');">
                        <i class="iconfont iconpraise"></i><span>{$v['answer_info']['agree_count']}</span>
                    </a>
                </div>
                <div class="aui-btn"><i class="iconfont iconmessage"></i><span>{$v['answer_info']['comment_count']}</span></div>
                <div class="aui-btn" onclick="AWS_MOBILE.User.shareBox('{$v.title}','{:url('question/detail',['id'=>$v.id],true,true)}')"><i class="iconfont iconshare"></i>{:L('分享')}</div>
            </div>
        </div>
    </div>
</div>
{/if}
{/volist}
{/if}