{extend name="$theme_block" /}
{block name="main"}
<div class="p-3 bg-white">
    <div class="my-3 text-center">
        <h3>{:L('向专栏推荐文章')}</h3>
        <p class="text-muted">{:L('选择文章推荐给该专栏')}，{:L('待专栏创建者处理推荐')}。</p>
    </div>
    <!--<div class="form-group overflow-hidden d-flex">
        <input type="text" class="flex-fill form-control" placeholder="输入要搜索的文章标题或文章ID">
    </div>-->

    <div class="result-list mt-3">
        {if !empty($list)}
        {foreach $list as $key=>$v}
        <ul>
            <li class="py-2">
                <a href="javascript:;" class="recommend-article-btn" data-id="{$v.id}">{$v['title']|raw}</a>
            </li>
        </ul>
        {/foreach}
        {$page|raw}

        {else/}
        <p class="text-center py-3 text-muted">
            <img src="{$cdnUrl}/static/common/image/empty.svg">
            <span class="d-block">{:L('暂无内容')}</span>
        </p>

        {/if}
    </div>
</div>

<script>
    $(document).on('click', '.recommend-article-btn', function ()
    {
        var articleId = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: "{:url('ajax.Column/recommend_article')}",
            data: {
                column_id:"{$column_id}",
                article_id:articleId
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                if(res.code)
                {
                    return layer.msg(res.msg,{},function (){
                        parent.layer.closeAll();
                    });
                }else{
                    return layer.msg(res.msg,{},function (){
                        window.location.reload();
                    });
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
