<div class="bg-white">
    <form method="post" onsubmit="return false" id="commentForm">
        <input type="hidden" name="article_id" value="{$id}">
        <input type="hidden" name="at_uid" value="{$at_uid}">
        <input type="hidden" name="pid" value="{$pid}">
        <textarea type="text" name="message" class="form-control commentMessage" rows="6" placeholder="{$at_user_info ? '@'.$at_user_info['nick_name'] : L('写下您的评论吧')}"></textarea>
        <div class="overflow-hidden mt-3">
            <div class="float-left aw-username" data-id="{$user_info.uid}">
                <img src="{$user_info['avatar']|default='/static/plugin/aw-home/images/avatars/avatar-2.jpg'}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$user_info['nick_name']}" class="rounded" style="width: 16px;height: 16px">
                <span>{$user_info['nick_name']}</span>
            </div>
            <div class="float-right">
                <button type="button" class="saveComment btn btn-primary btn-sm px-4">{:L('发布')}</button>
            </div>
        </div>
    </form>

    <script>
        $(document).on('click', '.saveComment', function (e) {
            var form = $('#commentForm');
            $.ajax({
                url: "{:url('comment/save_article_comment')}",
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                success: function (result) {
                    if(result.code)
                    {
                        mescroll.resetUpScroll();
                        AWS_MOBILE.api.closeOpen();
                        $('.commentMessage').val('');
                    }
                    AWS_MOBILE.api.success(result.msg);
                }
            });
        });
    </script>
</div>