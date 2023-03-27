{extend name="$theme_block" /}
{block name="main"}
<div class="px-3">
    <h2 class="my-3 font-weight-bold">{$info.title|raw}</h2>
    <span class="text-muted mb-3 d-block font-9">{:L('发布于')} {:date('Y-m-d H:i',$info.create_time)}</span>
    <div class="aw-content">
        {$info.message|raw}
    </div>
</div>
{/block}