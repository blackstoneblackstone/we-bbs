{if !empty($list)}
{volist name="list" id="v"}
<div class="answerCommentItem py-3">
    <div class="clearfix pt-0 pb-2 position-relative">
        <a href="{:get_user_url($v['uid'])}" class="float-left aw-username" data-id="{$v.uid}">
            <img src="{$v['user_info']['avatar']}" alt="{$v['user_info']['name']}" style="width: 18px;height: 18px" class="mr-1 rounded"><span>{$v['user_info']['name']}</span>
        </a>
        <a href="javascript:;" class="ml-2">{:date('Y-m-d H:i',$v['create_time'])}</a>
        {if $user_id}
        <div class="position-absolute" style="right: 0;top: 0">
            <!--<label class="mr-3 reply" data-info='{:json_encode(["uid"=>$v.uid,"user_name"=>$v.user_info.name])}'>
                <a href="javascript:;" class="font-8" data-type="reply"> <i class="icon-reply"></i>回复</a>
            </label>-->
            {if ($v['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)}
            <label class="text-muted">
                <a href="javascript:;"  data-confirm="{:L('是否删除该评论')}?" class="aw-ajax-get" title="{:L('删除评论')}" data-url="{:url('comment/delete_comment?id='.$v['id'])}"> <i class="fa fa-trash-alt font-8"></i></a>
            </label>
            {/if}
        </div>
        {/if}
    </div>
    <div class="aw-comment-text">
        <div class="post-comment-text-inner">
            <p class="text-muted font-8">
                {$v.message}
            </p>
        </div>
    </div>
</div>
{/volist}
{/if}
