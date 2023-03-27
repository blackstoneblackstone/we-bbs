{if !empty($list)}
{volist name="list" id="v"}
<div class="answerCommentItem p-3">
    <div class="d-flex">
        <div class="flex-fill" style="max-width: 36px">
            <a href="{:get_user_url($v['uid'])}" class="float-left aw-username" data-id="{$v.uid}">
                <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'"  alt="{$v['user_info']['name']}" width="36" height="36" class="rounded">
            </a>
        </div>
        <div class="flex-fill ml-2 aw-comment-text position-relative">
            <a href="{:get_user_url($v['uid'])}" class="d-block aw-username font-weight-bold mb-2" data-id="{$v.uid}">
                <span>{$v['user_info']['name']}</span>
            </a>

            <div class="post-comment-text-inner text-muted font-9">
                {$v.message}
            </div>

            <div class="clearfix mt-2">
                <a href="javascript:;" class="float-left text-muted font-9">{:date('Y-m-d H:i',$v['create_time'])}</a>
                {if $user_id}
                <div class="float-right" >
                    {if ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)}
                    <a href="javascript:;"  data-confirm="{:L('是否删除该评论')}?" class="aw-ajax-get text-muted font-8" title="{:L('删除评论')}" data-url="{:url('comment/delete_answer_comment?id='.$v['id'])}"> <i class="fa fa-trash-alt"></i></a>
                    {/if}
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>
{/volist}
{/if}
