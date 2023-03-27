{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left">
        <a href="{:url('creator/index')}" data-pjax="pageMain"><i class="fa fa-angle-left "></i></a>
    </div>
    <div class="aui-header-title aw-one-line">{:L('我的邀请')}</div>
</header>
{/block}

{block name="main"}
<div class="main-container mescroll mt-1" id="ajaxPage">
    <div class="mb-2 mx-2 text-center text-light py-3 rounded" style="background-color: #563d7c;">
        <h2 class="font-15 text-light mb-2">{$quota}</h2>
        <p>{:L('邀请名额还剩')}</p>
    </div>

    {if($quota >= 1)}
    <div class="bg-white p-3">
        {foreach $active_types as $key => $val}
        <button class="btn btn-success btn-sm to-invitation" data-type="{$key}">{:L($val)}</button>
        {/foreach}
    </div>
    {/if}

    <ul class="aui-lists" id="ajaxResult">
    </ul>
</div>
<script type="text/javascript">
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('invitation/records')}",{}, 10);
    $(document).on('click', '.to-invitation', function () {
        $.get('/invitation/page/', {type: $(this).data('type')}, function (res) {
            AWS_MOBILE.api.dialog('邀请注册', res)
        })
    })

    $(document).on('click', '.generate-invitation', function () {
        let _form = $(this).parent().parent()
        $.post(_form.attr('action'), _form.serializeArray(), function (res) {
            if (res.code == 1) {
                AWS_MOBILE.api.tipsRefresh(res.msg)
            } else {
                AWS_MOBILE.api.error(res.msg)
            }
        }, 'json')
    })
</script>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}{/block}