//邀请回答
var questionId = $('#question-id').val();
var url = baseUrl+"/ajax/invite?question_id="+questionId;
url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
$(document).on('input propertychange', '#invite-users', function () {
    $('#ajaxList').empty();
    var name = $(this).val();
    let isAuto = true;
    layui.flow.load({
        isAuto: isAuto,
        elem: '#ajaxList',
        done: function (page, next) {
            $.ajax({
                type:  'POST',
                url: url,
                data: {name:name,page:page},
                dataType: '',
                success: function (res) {
                    var total = 0;
                    if (res.data !== undefined) {
                        total = res.data.last_page;
                        next(res.data.html, page < total);
                    } else {
                        total = $($(res)[0]).data('total');
                        next(res, page < total);
                    }
                }
            });
        }
    });
});

//邀请回答按钮
$(document).on('click', '.question-invite', function () {
    var that = $(this);
    var uid = that.data('uid');
    var isInvite = that.data('invite');
    var questionId = that.data('id');
    var url = baseUrl+"/ajax/save_question_invite?question_id="+questionId;
    AWS.api.post(url,{uid:uid,has_invite:isInvite},function (res) {
        if(res.data.invite)
        {
            that.addClass('active');
            that.removeClass('question-invite');
            that.data('invite',1);
            that.text('已邀请');
        }else{
            that.removeClass('active');
            that.data('invite',0);
            that.text('邀请回答');
        }
        layer.msg(res.msg);
    });
})