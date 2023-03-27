{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}
<div class="bg-white">
    <form method="post" action="{:url('ajax/report')}" onsubmit="return false;">
        <input type="hidden" name="item_id" value="{$item_id}">
        <input type="hidden" name="item_type" value="{$item_type}">
        <p class="text-muted mb-3 bg-light p-2 font-8">
            {:L('提交举报')}
            {:L('未经平台允许')}，{:L('禁止使用帐号的任何功能')}，{:L('发布含有产品售卖信息')}、{:L('牟利性外链及违规推广等信息或引导用户至第三方平台进行交易')}。
            {:L('请在举报时简述理由')}，{:L('感谢你与我们共同维护社区的良好氛围')}。
            <a href="{:url('page/index',['url_name'=>'rule'])}" target="_blank">{:L('点击了解更多社区规范')}</a>。
        </p>

        <div class="form-group">
            <textarea class="form-control" name="reason" rows="3" placeholder="{:L('请填写举报理由')}"></textarea>
        </div>
        <div class="form-group">
            <a class="btn btn-primary save-report-form d-block" href="javascript:;">{:L('提交举报')}</a>
        </div>
    </form>
</div>
<script>
    $(document).on('click', '.save-report-form', function (e) {
        const that = this;
        const form = $($(that).parents('form')[0]);
        return $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
                if(result.code)
                {
                    AWS_MOBILE.api.closeOpen();
                    mescroll.resetUpScroll();
                }
                AWS_MOBILE.api.error(result.msg);
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    layer.closeAll();
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });
</script>
{/block}