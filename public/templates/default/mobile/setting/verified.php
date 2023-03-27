{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('用户认证')}</div>
</header>
{/block}

{block name="main"}
<div class="p-3 mt-1 bg-white" id="verifyMain">
    <form method="post" action="{:url('setting/verified')}">
        {if isset($info['status'])}
        <div class="form-group">
            <label>{:L('认证状态')}</label>
            <div>
                {if $info['status']==1}
                <span class="badge badge-danger">{:L('正在审核中')}</span>
                {/if}

                {if $info['status']==2 || $user_info['verified']}
                <span class="badge badge-success">{:L('审核通过')}</span>
                {/if}

                {if $info['status']==3}
                <span class="badge badge-info">{:L('拒绝审核')}{$info.reason ? ':'.$info.reason : ''}</span>
                {/if}
            </div>
        </div>
        {/if}
        <div class="form-group row">
            <label class="col-4"> {:L('认证类型')} </label>
            <div class="col-8">
                {foreach $verify_type as $key => $v}
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="type" value="{$key}" {if $user_info.verified==$key || $key=='personal'} checked {/if}>
                    <label class="form-check-label">{$v}</label>
                </div>
                {/foreach}
            </div>
        </div>
        <div id="field"></div>
        <div class="form-group">
            <input type="hidden" name="id" value="{$info['id']|default=0}">
            <button class="btn btn-primary btn-sm aw-ajax-form px-4" type="button" {if $user_info.verified} disabled {/if}>{:L('确 定')}</button>
        </div>
    </form>
    <script>
        var defaultType = "{:isset($info['type']) ? $info['type'] : 'personal'}";
        $(document).ready(function() {
            $('input[type=radio][name=type]').change(function() {
                if (this.value) {
                    renderHtml(this.value)
                }
            });
        });
        renderHtml(defaultType);
        function renderHtml(type)
        {
            $.ajax({
                type: 'GET',
                url: baseUrl+'/ajax/verify_type?type='+type+'&_ajax=1',
                dataType: '',
                success: function (res) {
                    $('#field').html(res)
                }
            });
        }
    </script>
</div>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}{/block}