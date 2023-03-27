{extend name="$theme_block" /}
{block name="main"}
<div class="favorite-tag-list overflow-auto">
    <div class="favorite-body">
        {volist name="list" id="v"}
        <div class="favorite-item overflow-hidden p-3 bg-white mb-2">
            <div class="favorite-item-inner float-left">
                <h4 class="favorite-item-name">{$v.title}</h4>
                <div class="mt-2 text-muted"><span class="favorite-post-count">{$v.post_count}</span> {:L('条内容')}</div>
            </div>
            {if $v['is_favorite']}
            <a class="favorite-ajax-get btn btn-primary btn-sm px-3 active float-right" data-url="{:url('favorite/dialog',['item_id'=>$item_id,'item_type'=>$item_type,'tag_id'=>$v['id']])}"  href="javascript:;">{:L('取消收藏')}</a>
            {else/}
            <a class="favorite-ajax-get btn btn-primary btn-sm px-3 float-right" data-url="{:url('favorite/dialog',['item_id'=>$item_id,'item_type'=>$item_type,'tag_id'=>$v['id']])}" href="javascript:;">{:L('收藏')}</a>
            {/if}
        </div>
        {/volist}
    </div>
</div>
<div class="p-3 bg-white">
    <div class="no-info text-center ">
        <p class="aw-text-meta mt-4">
            去 <a href="javascript:;" class="text-primary create-favorite">{:L('创建收藏夹')}</a>
        </p>
    </div>
    <div class="favorite-tag-add" style="display: none">
        <form action="{:url('favorite/save_favorite')}" method="post">
            <div class="form-group">
                <input type="text" name="title" class="aw-form-control" placeholder="{:L('标签名字')}">
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-outline-primary  px-3 btn-sm cancel-create">{:L('取消')}</button>
                <button type="button" class="btn btn-primary btn-sm px-3 save-favorite-tag">{:L('确认创建')}</button>
            </div>
        </form>
    </div>
</div>

<script>
    /**
     * 创建收藏夹
     */
    $(document).on('click', '.create-favorite', function (e) {
        $('.no-info').hide();
        $('.favorite-tag-add').show();
    });

    /**
     * 提交收藏夹标签
     */
    $(document).on('click', '.save-favorite-tag', function (e)
    {
        var form = $($(this).parents('form')[0]);
        $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
                var msg = result.msg ? result.msg : '操作成功';
                if (result.code) {
                    $('.no-info').show();
                    $('.favorite-tag-add').hide();
                    AWS_MOBILE.User.favorite(this,'{$item_type}','{$item_id}')
                } else {
                    AWS_MOBILE.api.error(msg);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });
    /**
     * 添加收藏
     */
    $(document).on('click', '.favorite-ajax-get', function (e) {
        var that = this;
        var options = $.extend({}, $(that).data() || {});
        if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
            options.url = $(that).attr("data-url");
        }
        AWS_MOBILE.api.ajax(options.url, function (res) {
            if (res.code) {
                if ($(that).hasClass('active')) {
                    $(that).text('收藏');
                    $(that).removeClass('active');
                    AWS_MOBILE.api.success('取消成功');
                } else {
                    $(that).text('取消收藏');
                    $(that).addClass('active');
                    AWS_MOBILE.api.success('收藏成功');
                }
                $(that).parents('.favorite-item').find('.favorite-post-count').text(res.data.post_count);
            } else {
                AWS_MOBILE.api.error('操作失败');
            }
        });
    });
</script>
{/block}
