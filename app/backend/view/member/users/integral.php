{extend name="block" /}
{block name="main"}
<div class="p-3">
    <div class="mb-2">
        <form action="{:url('integral')}">
            <input type="hidden" name="uid" value="{$uid}">
            <div class="d-flex">
                <input type="text" class="form-control d-flex mr-1" name="integral" value="">
                <button type="button" class="flex-fill btn btn-primary aw-ajax-form" style="width: 120px;">操作积分</button>
            </div>
            <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
                填写您需要操作的积分；如 100 或 -100
            </div>
        </form>
    </div>
    {if $list}
    <table class="table table-divider">
        <thead>
        <tr>
            <th>积分描述</th>
            <th>积分变化</th>
            <th>当前余额</th>
            <th>变动时间</th>
        </tr>
        </thead>
        <tbody>
        {volist name="list" id="v"}
        <tr>
            <td class="text-muted">{$v.remark}</td>
            <td>{$v.integral}</td>
            <td>{$v.balance}</td>
            <td>{:date('Y-m-d H:i:s',$v['create_time'])}</td>
        </tr>
        {/volist}
        </tbody>
    </table>
    {$page|raw}
    {/if}
</div>
{/block}