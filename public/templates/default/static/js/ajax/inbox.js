var userName = $('input[name=recipient_uid]').val();
AWS.api.ajaxLoadMore('#inbox-dialog-container',baseUrl+"/ajax/dialog",{recipient_uid:userName});

$(document).on('click', '.aw-ajax-submit', function (e) {
    var that = this;
    var options = $.extend({}, $(that).data() || {});
    var form = $($(that).parents('form')[0]);
    delete options.success;
    delete options.error;
    $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if (result.code > 0) {
                $('#inbox-dialog-container').empty();
                $('textarea[name=message]').val('');
                AWS.api.ajaxLoadMore('#inbox-dialog-container',baseUrl+"/ajax/dialog",{recipient_uid:userName});
            } else {
                layer.msg(result.msg);
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