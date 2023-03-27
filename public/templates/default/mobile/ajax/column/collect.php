{extend name="$theme_block" /}
{block name="main"}
{if $column_list}
<div class="px-3 bg-white pb-3">
    {foreach $column_list as $key=>$val}
    <div class="py-3 bg-white mb-1 border-bottom">
        <div class="overflow-hidden position-relative">
            <div class="float-left">
                <a href="{:url('column/detail',['id'=>$val['id']])}" class="d-block">
                    <img src="{$val.cover}" alt="{$val.name}" class="rounded" width="40" height="40">
                </a>
            </div>

            <div class="float-right" style="width: calc(100% - 50px)">
                <a href="{:url('column/detail',['id'=>$val['id']])}" class="font-10">{$val.name}</a>
                <p class="text-muted font-9 aw-one-line">{$val['description']|default='暂无专栏介绍～'}</p>
            </div>

            <div class="position-absolute" style="right: 0;bottom: 0">
                <button class="btn btn-outline-primary btn-sm px-3" onclick="AWS.User.saveColumnArticle('{$article_id}','{$val.id}',this)" type="button">收录至专栏</button>
            </div>
        </div>
    </div>
    {/foreach}
</div>
{else/}
<p class="text-center py-3 text-muted">
    <img src="{$cdnUrl}/static/common/image/empty.svg">
    <span class="d-block">{if isNormalAdmin() || isSuperAdmin()}该用户{else/}您{/if}还未创建专栏,去<a href="{:url('column/apply')}" target="_blank">申请</a> </span>
</p>
{/if}
{/block}
