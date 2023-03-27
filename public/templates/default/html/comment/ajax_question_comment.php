<div class="answerCommentItem py-2">
    <div class="clearfix pt-0 pb-2 position-relative">
        <a href="{:get_user_url($comment['uid'])}" class="float-left aw-username" data-id="{$comment.uid}">
            <img src="{$comment['user_info']['avatar']}" alt="{$comment['user_info']['name']}" style="width: 18px;height: 18px" class="mr-1 rounded"><span>{$comment['user_info']['name']}</span>
        </a>
        <a href="javascript:;" class="ml-2">{:date('Y-m-d H:i',$comment['create_time'])}</a>
        {if $user_id}
        <div class="position-absolute" style="right: 0;top: 0">
            <!--<label class="mr-3 reply" data-info='{:json_encode(["uid"=>$comment.uid,"user_name"=>$comment.user_info.name])}'>
                <a href="javascript:;" class="font-8" data-type="reply"> <i class="icon-reply"></i>回复</a>
            </label>-->
            {if ($comment['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)}
            <label>
                <a href="javascript:;"  data-confirm="{:L('是否删除该评论')}?" class="aw-ajax-get" title="{:L('删除评论')}" data-url="{:url('comment/delete_comment?id='.$comment['id'])}"> <i class="icon-delete"></i></a>
            </label>
            {/if}
        </div>
        {/if}
    </div>
    <div class="aw-comment-text">
        <div class="post-comment-text-inner">
            <p class="text-muted font-8">
                {$comment.message}
            </p>
        </div>
    </div>
</div>
