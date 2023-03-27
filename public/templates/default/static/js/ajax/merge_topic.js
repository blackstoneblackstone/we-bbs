var selector = $('.topicSearchInput');

$(selector).bind('compositionstart', function (e) {
    e.target.composing = true
})

$(selector).bind('compositionend', function (e) {
    e.target.composing = false
    trigger(e.target, 'input')
});

function trigger(el, type) {
    var e = document.createEvent('HTMLEvents')
    e.initEvent(type, true, false)
    el.dispatchEvent(e)
}

$(selector).bind('input', function (e){
    if (e.target.composing) {
        return
    }
    var keywords = selector.val();
    var itemId = selector.data('item-id');
    var itemType =selector.data('item-type');
    var url = baseUrl+'/ajax/get_topic/?keywords=' + keywords + '&limit=5&item_id='+itemId+'&item_type='+itemType;
    url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
    $.get(url, function (result) {
        $('.topicSearchList').html(result);
    }, 'html');
});

/*选择话题*/
$(document).on('click', '.topicSelected', function (e) {
    var id = $(this).data('id');
    var title = $(this).data('title');
    var html = '';
    if($(this).hasClass("active"))
    {
        $(this).removeClass("active")
        $("#awTopicList li[data-id='" + id + "']").detach();
    }else{
        $(this).addClass("active")
        $("#awTopicList li[data-id='" + id + "']").detach();
        html = '<li class="d-inline-block position-relative aw-tag mr-2 my-1" data-id="'+id+'" style="padding-right: 10px">' +
            '                        <input type="hidden" name="tags[]" value="'+id+'">' +
            '                        <a href="javascript:;" class="aw-topic d-inline-block" data-id="'+id+'">' +
            '                            <em class="tag">'+title+'</em>\n' +
            '                        </a>' +
            '                        <a class="fa fa-close position-absolute text-danger font-8 removeTopic" href="javascript:;"  style="right: 0;padding: 2px 6px;"></a>' +
            '                    </li>';
        $('#awTopicList').append(html);
    }
})

/*删除话题*/
$(document).on('click', '.removeTopic', function (e) {
    var id = $(this).parent('li').data('id');
    $(".topicSelected[data-id='" + id + "']").removeClass('active');
    $(this).parent('li').detach();
})

/*添加话题*/
$(document).on('click', '.saveTopic', function (e) {
    const that = this;
    let form = $($(that).parents('form')[0]);
    return $.ajax({
        url: form.attr('action'),
        dataType: 'json',
        type: 'post',
        data: form.serialize(),
        success: function (result) {
            if (result.code) {
                if(result.data.list)
                {
                    let html = '';
                    var topic = [];
                    $.each(result.data.list, function(index, value) {
                        topic.push(value.id);
                        html+='<li class="d-inline-block aw-tag"><a href="'+value.url+'"><em class="tag">'+value.title+'</em></a></li>';
                    });
                    html += '<input type="hidden" name="topics" value="'+topic+'" >'
                    parent.$('#awTopicList').html(html);
                }
                parent.layer.closeAll();
                //window.location.reload();
            } else {
                AWS.api.error(result.msg);
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
