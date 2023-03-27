/**
 * 创建收藏夹
 */
$(document).on('click', '.create-favorite', function (e) {
    $('.no-info').hide();
    $('.createFavoriteBox').show();
    $('.favorite-tag-add').show();
    $('.favorite-body').hide();
    $('.listCreate').hide();
});

$(document).on('click', '.cancel-create', function (e) {
    $('.createFavoriteBox').hide();
    $('.favorite-body').show();
    $('.listCreate').show();
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
                setTimeout(function () {
                    window.location.reload();
                },1000);
            } else {
                AWS.api.error(msg);
            }
        },
        error: function (error) {
            if ($.trim(error.responseText) !== '') {
                layer.closeAll();
                AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
            }
        }
    });
});

/**
 * 添加收藏
 */
$(document).on('click', '.favorite-ajax-get', function (e) {
    const that = this;
    const options = $.extend({}, $(that).data() || {});
    if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
        options.url = $(that).attr("data-url");
    }
    return AWS.api.ajax(options.url, function (res) {
        if (res.code) {
            if ($(that).hasClass('active')) {
                $(that).text('收藏');
                $(that).removeClass('active');
                AWS.api.success('取消成功');
            } else {
                $(that).text('取消收藏');
                $(that).addClass('active');
                AWS.api.success('收藏成功');
            }
            $(that).parents('.favorite-body').find('.favorite-post-count').text(res.data.post_count);
            setTimeout(function () {
                parent.layer.closeAll() || layer.closeAll();
                parent.window.location.reload();
            }, 1000);
        } else {
            AWS.api.error('操作失败');
        }
    });
});