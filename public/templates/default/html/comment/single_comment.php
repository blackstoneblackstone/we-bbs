<div class="bg-white p-3 mb-1 aw-article-comment-item" id="article-comment-{$comment_info.id}">
    <div class="clearfix pt-0 pb-2">
        <div class="user-details-card-avatar float-left" style="position: relative">
            <a href="{:url('index/index',['uid'=>$comment_info['uid']])}">
                <img src="{$comment_info['user_info']['avatar']}" alt="{$comment_info['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
        </div>
        <div class="user-details-card-name float-left ml-2">
            <a href="{$comment_info['user_info']['url']}">{$comment_info['user_info']['name']}</a><br> <span class="ml-0"> {:date_friendly($comment_info['create_time'])} </span>
        </div>
    </div>
    <div class="articleCommentContent" style="padding-left: 45px;">
        <div class="aw-content">{$comment_info.message|raw}</div>
        <div class="actions">
            <div class="font-9 mt-2">
                <a href="javascript:;" class="text-muted aw-ajax-agree mr-3 {if $comment_info['vote_value']==1}active{/if}" onclick="AWS.User.agree(this,'article_comment','{$comment_info.id}')"><i class="icon-thumb_up"></i> {:L('点赞')} <span>{$comment_info.agree_count}</span></a>
                {if $user_id}
                <a href="javascript:;" class="mr-3 text-muted article-comment-reply" data-username="{$comment_info['user_info']['user_name']}"> <i class="icon-reply"></i> {:L('回复')} </a>
                {if $user_id==$comment_info['uid'] || $user_info['group_id']==1 || $user_info['group_id']==2}
                <a href="javascript:;" class="text-muted aw-ajax-get" data-confirm="{:L('确定要删除吗')}？" data-url="{:url('comment/remove_article_comment',['id'=>$comment_info.id])}"> <i class="icon-delete mr-1"></i>{:L('删除')} </a>
                {/if}
                {/if}
            </div>
        </div>
        <div class="replay-editor mt-2" style="display: none"></div>
    </div>
</div>