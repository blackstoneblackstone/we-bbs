{extend name="$theme_block" /}
{block name="header"}
{if !$from}
{__block__}
{/if}
{/block}
{block name="main"}
<div class="container mt-2">
    <div class="bg-white p-3">
        <h2 class="my-3 text-center">{$info.title|raw}</h2>
        {if $info.description}
        <div class="bg-light my-2 p-3">
            {$info.description|raw}
        </div>
        {/if}
        <div class="aw-content">
            {$info.contents|raw}
        </div>
    </div>
</div>
{/block}
{block name="footer"}
{if !$from}
{__block__}
{/if}
{/block}

