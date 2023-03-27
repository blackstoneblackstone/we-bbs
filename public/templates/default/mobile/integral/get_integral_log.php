{if $list}
{volist name="list" id="v"}
<div class="aui-list">
    <div class="aui-list-left aw-one-line" style="line-height: unset;max-width: calc(100% - 4rem)">
        <span class="d-block mb-1 aw-one-line">{$v.remark}</span>
        <span class="d-block font-8 text-muted">{:date('Y-m-d H:i:s',$v['create_time'])}</span>
    </div>
    <div class="aui-list-right font-12 text-danger">
        {$v.integral}
    </div>
</div>
{/volist}
{/if}