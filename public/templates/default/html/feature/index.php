{extend name="$theme_block" /}
{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$static_url}images/feature.png') center center;background-size:cover;">
    <div style="background: rgba(0,0,0,.25);height:300px;">
        {include file="global/nav"}
        <div class="container" style="margin-top: 70px">
            <div class="row text-center">
                <h2 class="col-12 text-white">{:L('全部专题')}</h2>
                <p class="mb-3 w-100" style="color: #eee">{:L('以话题聚合形式汇集最新热点')}</p>
            </div>
        </div>
    </div>
{/block}
{block name="main"}
<div class="container mt-2 aw-wrap">
    <div id="tabMain">
        {if $list}
        {foreach $list as $k=>$v}
        <div class="bg-white p-3 d-flex">
            <div class="flex-fill" style="width: 30%;min-width: 30%;max-width: 30%;">
                <a href="{:url('feature/detail',['token'=>$v['url_token']])}" data-pjax="wrapMain">
                <div style="height:150px;background: url('{$v.image}') center center;background-size:cover;border-radius: 10px"></div>
                </a>
            </div>
            <div class="flex-fill ml-3">
                <h4><a href="{:url('feature/detail',['token'=>$v['url_token']])}" data-pjax="wrapMain">{$v.title}</a></h4>
                <p class="text-muted">{$v.description|raw}</p>
                {if !empty($v['topics'])}
                <div class="mt-3">
                    {foreach $v['topics'] as $topic}
                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" class="aw-topic" data-id="{$topic.id}" target="_blank"><em class="tag">{$topic.title}</em></a>
                    {/foreach}
                </div>
                {/if}
            </div>
        </div>
        {/foreach}
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