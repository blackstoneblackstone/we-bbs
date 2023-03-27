{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-md-10"  id="wrapMain">
                {include file="setting/nav"}
                <div class="bg-white mt-1 p-4" id="tabMain">
                    <form method="post" action="{:url('setting/verified')}">
                        {if isset($info['status'])}
                        <div class="form-group">
                            <label>{:L('认证状态')}</label>
                            <div>
                                {if $info['status']==1}
                                <span class="badge badge-danger">{:L('正在审核中')}</span>
                                {/if}

                                {if $info['status']==2 && $user_info['verified']}
                                <span class="badge badge-success">{:L('审核通过')}</span>
                                {/if}

                                {if $info['status']==3}
                                <span class="badge badge-info">{:L('拒绝审核')}{$info.reason ? ':'.$info.reason : ''}</span>
                                {/if}
                            </div>
                        </div>
                        {/if}
                        <div class="form-group row">
                            <label class="col-md-2 col-form-label"> {:L('认证类型')} </label>
                            <div class="col-md-4 col-form-label">
                                {foreach $verify_type as $key => $v}
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="type" value="{$key}" {if !empty($info) && ($info['status']!=0 && $info['status']!=3) && !$user_info.verified}readonly disabled{/if} {if $user_info.verified==$key || $key=='personal' || (!empty($info) && $info.type==$key)} checked {/if}>
                                    <label class="form-check-label">{$v}</label>
                                </div>
                                {/foreach}
                            </div>
                        </div>
                        <div id="field"></div>
                        <div class="form-group">
                            <input type="hidden" name="id" value="{$info['id']|default=0}">
                            <button class="btn btn-primary btn-sm aw-ajax-form px-4" type="button" {if !empty($info) && ($info['status']!=0 && $info['status']!=3)}readonly disabled{/if}>{:L('确 定')}</button>
                        </div>
                    </form>
                    <script>
                        let defaultType = "{:isset($info['type']) ? $info['type'] : 'personal'}";
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
            </div>
        </div>
    </div>
</div>
{/block}