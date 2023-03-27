{extend name="$theme_block" /}
{block name="main"}
<style>
    .topicSelected.active em{
        background: #563d7c;
        color: #ffffff;
    }
</style>
{if !isset($search_list)}
<div class="bg-white p-3" style="min-height: 280px">
    <div class="form-group overflow-hidden d-flex">
        <input type="text" data-item-id="{$item_id}" class="flex-fill aw-form-control topicSearchInput" placeholder="{:L('输入要搜索的话题')}">
    </div>

    <form method="post" class="topic-save-form">
        <div class="mt-3 overflow-hidden w-100">
            <!--已选话题-->
            <div class="mb-3">
                <ul class="page-detail-topic py-1" id="awTopicList">
                    {if $topics}
                    {foreach $topics as $k=>$v}
                    <li class="d-inline-block position-relative aw-tag mr-2 mb-2" data-id="{$v.id}" style="padding-right: 10px">
                        <input type="hidden" name="tags[]" value="{$v.id}">
                        <a href="javascript:;" class="aw-topic d-inline-block" data-id="{$v.id}">
                            <em class="tag">{$v.title}</em>
                        </a>
                        <a class="fa fa-close position-absolute text-danger font-8 removeTopic" href="javascript:;"  style="right: 0;padding: 2px 6px;"></a>
                    </li>
                    {/foreach}
                    {/if}
                </ul>
            </div>
            <div class="topicSearchList"></div>
        </div>
        <button class="saveTopic btn btn-primary mt-1 px-4 btn-sm" type="button">{:L('保存')}</button>
    </form>
</div>
{else/}
<dl class="overflow-hidden">
    <dt class="text-muted mb-2 font-9">{:L('搜索结果')}</dt>
    <dd class="w-100 d-block">
        <ul class="page-detail-topic">
            {volist name="search_list" id="v"}
            <li class="d-inline-block position-relative aw-tag topicSelected mb-2 {$v.is_checked ? 'active' : ''}" data-title="{$v.title}" data-id="{$v.id}">
                <a href="javascript:;" class="aw-topic d-inline-block">
                    <em class="tag">{$v.title}</em>
                </a>
            </li>
            {/volist}
        </ul>
    </dd>
</dl>
{/if}
{/block}