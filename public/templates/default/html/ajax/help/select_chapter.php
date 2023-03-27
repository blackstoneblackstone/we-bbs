{extend name="$theme_block" /}
{block name="main"}
<div style="min-height: 400px" class="mt-1">
    {if $list}
    <div class="row">
        {foreach $list as $k=>$v}
        <div class="col-4">
            <div class="bg-white p-3 text-center" style="border-radius: 10px">
                <i class="fa fa-check-circle" style="color: #28a745;{$v.selected?'':'display: none'}"></i>
                <a href="javascript:;" class="aw-one-line font-11 selected" data-item-id="{$item_id}" data-type="{$item_type}" data-id="{$v.id}">{$v.title}</a>
            </div>
        </div>
        {/foreach}
    </div>
    {else/}
    <p class="text-center py-3 text-muted">
        <img src="{$cdnUrl}/static/common/image/empty.svg">
        <span class="d-block">{:L('暂无内容')}</span>
    </p>
    {/if}
</div>

<script>
    $(document).on('click', '.selected', function(event) {
        var that = $(this);
        var itemId = $(this).data('item-id');
        var itemType = $(this).data('type');
        var targetId = $(this).data('id');

        $.ajax({
            type: 'POST',
            url: "{:url('ajax.Help/select_chapter')}",
            data: {
                item_id:itemId,
                item_type:itemType,
                id:targetId
            },
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (res) {
                that.parent().find('i').toggle();
                layer.msg(res.msg);
            },
            error: function (xhr) {
                let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                AWS.events.onAjaxError(ret, error);
            }
        });
    })
</script>
{/block}