{extend name="$theme_block" /}
{block name="main"}
<div class="bg-white mt-3" style="min-height: 280px">
    <div class="form-group overflow-hidden d-flex">
        <input type="text" data-item-id="{$item_id}" data-item-type="{$item_type}" class="flex-fill aw-form-control topicSearchInput" placeholder="{:L('输入要搜索的话题')}">
        {if $user_info['permission']['create_topic_enable']=='Y' || $user_info['group_id']==1 || $user_info['group_id']==2}
        <div class="flex-fill ml-2" style="min-width: 80px"><a class="saveCreateTopic btn btn-primary btn-sm" href="javascript:;">{:L('添加话题')}</a></div>
        {/if}
    </div>

    <form method="post" action="{:url('topic/select')}" class="topic-save-form">
        <input type="hidden" name="item_id" value="{$item_id}">
        <input type="hidden" name="item_type" value="{$item_type}">
        <div class="mt-3 overflow-hidden w-100">
            {if $recent_topic_list}
            <!--最近使用话题-->
            <dl class="overflow-hidden m-1">
                <dt class="text-muted mb-2 font-9">{:L('最近使用话题')}</dt>
                <dd class="w-100 d-block">
                    {volist name="recent_topic_list" id="v"}
                    <label class="d-inline-block mx-2 mb-2">
                        <input type="checkbox" {if $v.is_checked}checked{/if} name="tags[]" value="{$v.id}"> {$v.title}
                    </label>
                    {/volist}
                </dd>
            </dl>
            {/if}
            <div class="topicSearchList" style="display: none">
                <dl class="overflow-hidden">
                    <dt class="text-muted mb-2 font-9">{:L('搜索结果')}</dt>
                    <dd class="w-100 d-block" id="searchResult">

                    </dd>
                </dl>
            </div>
        </div>
        <button class="saveTopic btn btn-primary mt-1 px-4 btn-sm" type="button">{:L('保存')}</button>
    </form>
</div>

<script>
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
        var html = '';
        $.get(url, function (result) {
            var list = result.data;
            $.each(list, function(i, item){
                var selected = item.is_checked ? 'checked' :'';
                html+='<label class="d-inline-block mx-2 mb-2"> <input type="checkbox" '+selected+' name="tags[]" value="'+item.id+'"> '+item.title+'</label>';
            });
            $('#searchResult').html(html);
            $('.topicSearchList').show();
            if(!keywords)
            {
                $('#searchResult').html();
                $('.topicSearchList').hide();
            }
        }, 'json');
    });

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
                            html+='<li class="swiper-slide"><a href="'+value.url+'"><em class="tag">'+value.title+'</em></a></li>';
                        });
                        html += '<input type="hidden" name="topics" value="'+topic+'" >'
                        $('#awTopicList').html(html);
                    }
                    AWS_MOBILE.api.closeOpen();
                } else {
                    AWS_MOBILE.api.error(result.msg);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });

    $(document).on('click', '.saveCreateTopic', function (e) {
        if(!selector.val())
        {
            return false;
        }

        AWS_MOBILE.api.post(baseUrl+'/ajax/create',{'title':selector.val(),},function (res){
            if(res.code)
            {
                var str = '<label class="d-inline-block mx-1"><input type="checkbox" checked name="tags[]" value="'+res.data.id+'"> '+res.data.title+'        </label>';
                $('.topicSearchList dd').append(str);
                selector.val('');
                AWS_MOBILE.api.success('新建话题成功');
            }else{
                AWS_MOBILE.api.error(res.msg);
            }

        });
    })
</script>
{/block}