{extend name="$theme_block" /}
{block name="main"}
<div class="card" style="min-height: 200px">
    <div class="card-header border-0 bg-white">
        <div class="input-group input-group-sm">
            <input type="text" class="form-control form-control-alt" placeholder="{:L('请输入您想搜索的话题标题或话题ID')}..." name="target_id" id="redirectTarget">
        </div>
    </div>
    <div class="card-body" id="ajaxList"></div>
</div>

<script>
    var selector = $('#redirectTarget');

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
        var itemId = "{$item_id}";
        var url = baseUrl+'/ajax/get_topic/?keywords=' + keywords + '&limit=5&item_id='+itemId;
        url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
        $.get(url, function (result) {
            $('#ajaxList').html(result);
        }, 'html');
    });

    $(document).on('click', '.topicSelected', function(event) {
        var itemId = "{$item_id}";
        var targetId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: "{:url('ajax.Topic/merge_topic',['_ajax'=>1])}",
            data: {
                item_id:itemId,
                target_id:targetId
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if(res.code===1)
                {
                    parent.window.location.href=res.url;
                    parent.window.layer.closeAll();
                }else{
                    return layer.msg(res.msg);
                }
            },
            error: function (xhr) {
                let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                AWS.events.onAjaxError(ret, error);
            }
        });
    })
</script>
{/block}