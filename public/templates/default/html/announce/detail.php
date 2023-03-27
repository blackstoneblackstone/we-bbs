{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="bg-white p-3">
            <h2 class="my-3 text-center">{$info.title|raw}</h2>
            <div class="bg-light my-2 p-3 mb-3">
                {:L('发布于 %s',date_friendly($info.create_time))}
            </div>

            <div class="aw-content">
                {$info.message|raw}
            </div>
        </div>
    </div>
</div>
{/block}