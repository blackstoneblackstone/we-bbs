$(document).ready(function ()
{
    /*异步获取评论*/

    let articleHeight = $('.aw-article').height();
    if(articleHeight>$(window).height() && $(window).scrollTop()+($(window).height() / 2) < articleHeight)
    {
        $('.aw-article-wrap .actions').addClass('fixed-bottom').addClass('bg-white').addClass('text-center').addClass('p-3');
    }else{
        $('.aw-article-wrap .actions').removeClass('fixed-bottom').removeClass('bg-white').removeClass('text-center').removeClass('p-3');
    }

});

/**
 * 回复评论
 */
$(document).on('click', '.article-comment-reply', function ()
{
    let uid = $(this).data('info');
    let username = $(this).data('username');
    let commentItem = $('#article-comment-'+$(this).data('comment-id'));
    let commentEditor = $('.aw-article-comment-editor');
    commentEditor.addClass('bg-light').removeClass('bg-white');
    commentItem.find('.replay-editor').append(commentEditor).show();
    commentEditor.find('[name=at_info]').val(uid);
    commentEditor.find('[name=pid]').val($(this).data('comment-id'));
    commentEditor.find(".commentMessage").val('@'+username+' ').focus();
});

/**
 * 提交评论
 */
$(document).on('click', '.aw-article-comment-submit', function () {
    let that = this;
    let form = $($(that).parents('form')[0]);
    let data = form.serializeArray();
    $(that).attr('disabled',true);
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: data,
        success: function (result) {
            if(result.code)
            {
                if(result.code===1)
                {
                    $("#article-comment-list").prepend(result.data.html);
                    $('.aw-comment-count').text(result.data.comment_count);
                    $('.aw-article-comment-editor').show();
                    $('.replay-editor').hide();
                    window.location.reload();
                }
            }else{
                $(that).attr('disabled',false);
            }
            layer.msg(result.msg);
        }
    });
});