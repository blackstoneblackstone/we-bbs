{extend name="$theme_block" /}
{block name="main"}
<style>
    .layui-layer-btn .layui-layer-btn0 {
        color: #fff!important;
    }
</style>
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-md-10" id="wrapMain">
                <div class="bg-white">
                    <div class="aw-nav-container">
                        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
                            <li class="nav-item">
                                <a class="nav-link active" data-pjax="wrapMain" href="{:url('invitation/index')}">{:L('邀请记录')}</a>
                            </li>
                        </ul>
                    </div>
                    {if($quota >= 1)}
                    <div class="p-3">
                        {foreach $active_types as $key => $val}
                        <button class="btn btn-success btn-sm to-invitation" data-type="{$key}">{:L($val)}</button>
                        {/foreach}
                        <span class="text-danger p-2">({:L('邀请名额还剩 %s',$quota)})</span>
                    </div>
                    {/if}
                    <div id="tabMain" class="p-3">
                        {if !empty($list)}
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>{:L('邀请时间')}</th>
                                <th>{:L('邀请方式')}</th>
                                <th>{:L('链接')}/{:L('邮箱')}</th>
                                <th>{:L('状态')}</th>
                                <th>{:L('过期时间')}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $list as $v}
                            <tr>
                                <td class="text-muted">{:date('Y-m-d H:i', $v['create_time'])}</td>
                                <td>{$v.active_type_label}</td>
                                <td>
                                    {if $v.invitation_email}
                                    <p><b>{:L('邮箱')}</b>：{$v.invitation_email}</p>
                                    {/if}
                                    <p>
                                        <b>{:L('链接')}</b>：{$v.invitation_link}
                                        <span class="badge badge-secondary" onclick="AWS.common.copyText('{$v.invitation_link}')" style="cursor: pointer">{:L('复制')}</span>
                                    </p>
                                </td>
                                <td>{$v.active_status_label}</td>
                                <td>{:date('Y-m-d H:i', $v['active_expire'])}</td>
                            </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        {$page|raw}
                        {else/}
                        <p class="text-center mt-4 text-meta">
                            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无邀请记录')}">
                            <span class="mt-3 d-block ">{:L('暂无邀请记录')}</span>
                        </p>
                        {/if}
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).on('click', '.to-invitation', function () {
        $.get('/invitation/page/', {type: $(this).data('type')}, function (res) {
            layer.open({
                title: '邀请注册',
                area:['50%', 'auto'],
                content: res,
                btn: ['生成邀请'],
                yes: function () {
                    let _form = $('#invitation-form')
                    $.post(_form.attr('action'), _form.serializeArray(), function (res) {
                        if (res.code == 1) {
                            layer.msg(res.msg, {time: 2000},  function () {
                                window.location.reload()
                            })
                        } else {
                            layer.msg(res.msg, {type: 1})
                        }
                    }, 'json')
                }
            });
        })
    })
</script>
{/block}