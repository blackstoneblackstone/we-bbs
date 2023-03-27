{if !empty($announce_list)}
<div class="r-box mb-2">
    <div class="r-title">
        <h4>{:L('站点公告')}</h4>
        <!--<a href="{:url('announce/index')}" target="_blank"><label class="iconfont">&#xe660;</label></a>-->
    </div>
    <div class="hot-list hot-yh-list pb-2">
        {volist name="announce_list" id="v"}
        <dl class="clearfix border-bottom">
            <dt class="mb-2 w-100" style="height: auto">
                <a href="{:url('announce/detail',['id'=>$v.id])} " class="text-danger aw-two-line">{$v.title}</a>
            </dt>
            <dd class="pt-1 px-0 text-muted aw-three-line pstyle">
                {$v.message|raw}
            </dd>
        </dl>
        {/volist}
    </div>
</div>
{/if}
