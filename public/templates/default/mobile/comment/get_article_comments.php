{if !empty($data)}
{volist name="data" id="v"}
<div class="p-3 bg-white">
    <div class="d-flex">
        <div class="flex-fill" style="max-width: 32px">
            <a href="{:get_user_url($v['uid'])}">
                <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" onerror="this.src='/static/common/image/default-avatar.svg'" style="width: 32px;height: 32px">
            </a>
        </div>
        <div class="flex-fill ml-2 aw-comment-text position-relative">
            <a href="{$v['user_info']['url']}" class="d-block aw-username font-weight-bold mb-2" >{$v['user_info']['name']}</a>
            <div class="post-comment-text-inner text-muted">
                {$v.message|raw}
            </div>
            <div class="clearfix mt-2 pb-3">
                <a href="javascript:;" class="float-left text-muted font-9">{:date('Y-m-d H:i',$v['create_time'])}</a>
                <div class="float-right font-8 text-muted">
                    <a href="javascript:;" class="text-muted aw-ajax-agree mr-3 {if $v['vote_value']==1}active{/if}" onclick="AWS_MOBILE.User.agree(this,'article_comment','{$v.id}')">
                        <i class="iconfont iconpraise"></i> <span>{$v.agree_count}</span>
                    </a>
                    {if $user_id}
                    <a href="javascript:;" class="mr-3 text-muted aw-ajax-open" data-title="回复 {$v['user_info']['name']} 的评论" data-url="{:url('comment/comment_editor',['id'=>$v.article_id,'at_uid'=>$v.uid,'pid'=>$v.id])}"> <i class="iconfont iconmessage"></i></a>
                    {if $user_id==$v['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                    <a href="javascript:;" class="text-muted aw-ajax-get" data-confirm="{:L('确定要删除吗')}？" data-url="{:url('comment/remove_article_comment',['id'=>$v.id])}"> <i class="fa fa-trash-alt mr-1"></i></a>
                    {/if}
                    {/if}
                </div>
            </div>

            {if isset($v['childs']) && $v['childs']}
            {volist name="$v['childs']" id="v1"}
            <div class="d-flex py-3 border-top">
                <div class="flex-fill" style="max-width: 32px">
                    <a href="{:get_user_url($v1['uid'])}">
                        <img src="{$v1['user_info']['avatar']}" alt="{$v1['user_info']['name']}" onerror="this.src='/static/common/image/default-avatar.svg'" style="width: 32px;height: 32px">
                    </a>
                </div>
                <div class="flex-fill ml-2 aw-comment-text position-relative">
                    <a href="{$v1['user_info']['url']}" class="d-block aw-username font-weight-bold mb-2" >{$v1['user_info']['name']}</a>
                    <div class="post-comment-text-inner text-muted">
                        {$v1.message|raw}
                    </div>
                    <div class="clearfix mt-2">
                        <a href="javascript:;" class="float-left text-muted font-9">{:date('Y-m-d H:i',$v1['create_time'])}</a>
                        <div class="float-right font-8 text-muted">
                            <a href="javascript:;" class="text-muted aw-ajax-agree mr-3 {if $v1['vote_value']==1}active{/if}" onclick="AWS_MOBILE.User.agree(this,'article_comment','{$v1.id}')">
                                <i class="iconfont iconpraise"></i> <span>{$v1.agree_count}</span>
                            </a>
                            {if $user_id}
                            {if $user_id==$v1['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                            <a href="javascript:;" class="text-muted aw-ajax-get" data-confirm="{:L('确定要删除吗')}？" data-url="{:url('comment/remove_article_comment',['id'=>$v1.id])}"> <i class="fa fa-trash-alt mr-1"></i></a>
                            {/if}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
            {/volist}
            {/if}
        </div>
    </div>
</div>
{/volist}
{/if}
