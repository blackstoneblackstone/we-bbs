{extend name="$theme_block" /}

{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$static_url}images/help.png') center center;background-size:cover;">
    <div style="background: rgba(0,0,0,.25);height:300px;">
        {include file="global/nav"}
    </div>
</div>
{/block}

{block name="main"}
<div class="container" style="margin-top: -100px">
    <div class="bg-white p-3" style="border-radius: 10px">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb px-0 pt-0" style="background: transparent">
                <li class="breadcrumb-item"><a href="{$baseUrl}">{:L('社区')}</a></li>
                <li class="breadcrumb-item"><a href="{:url('help/index')}">{:L('帮助中心')}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{$info.title}</li>
            </ol>
        </nav>
        <div class="d-flex pb-2">
            {if $info.image}
            <div class="flex-fill mr-2" style="background-image:url({$info.image});background-size:cover;width:38px;max-width:38px;height: 38px;border-radius: 5px;"></div>
            {/if}
            <h2 class="font-weight-bolder">{$info.title}</h2>
        </div>

        <p class="mb-3 w-100 text-muted mt-3">{$info.description|raw}</p>
    </div>
</div>

<div class="container aw-wrap mt-2">
    <div class="bg-white p-3">
        {if !empty($list)}
        <ul class="pb-2">
            {foreach $list as $k=>$v}
            <li class="py-1 aw-one-line"><a href="{:url($v['item_type'].'/detail',['id'=>$v['item_id']])}" target="_blank">{$v.info.title}</a></li>
            {/foreach}
        </ul>
        {$page|raw}
        {else/}
        <p class="text-center py-3 text-muted">
            <img src="{$cdnUrl}/static/common/image/empty.svg">
            <span class="d-block">{:L('暂无内容')}</span>
        </p>
        {/if}
    </div>
</div>
{/block}