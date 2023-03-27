<div class="answerCommentItem py-2">
    <div class="clearfix pt-0 pb-2 position-relative">
        <a href="{:get_user_url($uid)}" class="float-left aw-username" data-id="{$uid}">
            <img src="{$user_info['avatar']}" alt="{$user_info['name']}"  style="width: 18px;height: 18px" class="mr-1 rounded"><span>{$user_info['name']}</span>
        </a>
        <a href="javascript:;" class="ml-2">{:date('Y-m-d H:i',$create_time)}</a>
        <div class="position-absolute" style="right: 0;top: 0">
            <!--<label class="mr-3 reply" data-info='{:json_encode(["uid"=>$uid,"user_name"=>$user_info.name])}'>
                <a href="javascript:;" class="font-8" data-type="reply"> <i class="icon-reply"></i>回复</a>
            </label>-->
            <label>
                <a href="javascript:;"  data-confirm="{:L('是否删除该评论')}?" class="aw-ajax-get" title="{:L('删除评论')}" data-url="{:url('question/delete_answer_comment?id='.$id)}"> <i class="icon-delete"></i></a>
            </label>
        </div>
    </div>
    <div class="aw-comment-text">
        <div class="post-comment-text-inner">
            <p class="text-muted font-8">
                {$message|raw}
            </p>
        </div>
    </div>
</div>