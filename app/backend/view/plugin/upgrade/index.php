{extend name="block" /}
{block name="main"}
<div class="p-3">
    <div class="card card-rounded">
        <div class="card-body">
            <table class="table table-hover border-0">
                <tbody>
                <tr class="border-0">
                    <td style="text-align: left" class="border-0">
                        <b>当前版本：</b>WeCenter V{:config('version.version')}
                        {if $info.code==200}
                        <span class="badge badge-danger"><i class="glyphicon glyphicon-info-sign"></i> {$info.msg}</span>
                        {if isset($info.data.bind)}
                        <a href="javascript:;" data-url="{:url('check')}" data-title="版本检测" class="badge badge-success aw-ajax-open">开始更新</a>
                        {else/}
                        <a href="javascript:;" class="badge badge-danger aw-ajax-open" data-url="{:url('bind')}" data-title="绑定云平台 / <a href='https://wenda.wecenter.com/register/' style='color:red' target='_blank'>注册云平台</a>">绑定账号</a>
                        {/if}
                        {/if}
                        {if $info.code==201}
                        <span class="badge badge-success">
                        <i class="glyphicon glyphicon-info-sign"></i> {$info.msg}
                    </span>
                        {/if}
                    </td>
                </tr>
                <tr  class="border-0">
                    <td style="text-align: left"  class="border-0">
                        <b>通信状态：</b>
                        {if $info.code}
                        <a href="javascript:;" class="badge badge-success">通信正常</a>
                        {else/}
                        <a href="javascript:;" class="badge badge-danger">通信异常</a>
                        {/if}
                    </td>
                </tr>
                <tr  class="border-0">
                    <td style="text-align: left"  class="border-0">
                        <b>云端账号：</b>
                        {if isset($info.data.bind) && $info.data.bind}
                        <span class="badge badge-success">{$info.data.bind}</span>
                        <a href="javascript:;" data-url="{:url('unbind')}" class="badge badge-danger aw-ajax-get">解绑</a>
                        {else/}
                        <span class="badge badge-warning">未绑定账号</span>
                        <a href="javascript:;" class="badge badge-danger aw-ajax-open" data-url="{:url('bind')}" data-title="绑定云平台 / <a href='https://wenda.wecenter.com/register/' style='color:red' target='_blank'>注册云平台</a>">绑定账号</a>
                        {/if}
                    </td>
                </tr>
                <tr  class="border-0">
                    <td style="text-align: left" class="border-0">
                        <b>授权认证：</b> <span class="badge badge-primary">{$info.data ? $info.data.authorize : '学习版'}</span>
                        <a href="javascript:;" class="aw-ajax-open" data-title="版本区别" data-url="https://wenda.wecenter.com/timeline/version"><span class="badge badge-danger"><i class="glyphicon glyphicon-info-sign"></i> 查看版本区别</span></a>
                        <a href="javascript:;" class="aw-ajax-open badge badge-secondary" data-url="https://wenda.wecenter.com/timeline/contact" data-title="授权咨询"> 授权咨询</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
{/block}