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
        <input type="text" data-item-id="{$item_id}" data-item-type="{$item_type}" class="flex-fill aw-form-control topicSearchInput" placeholder="{:L('输入要搜索的话题标题或话题ID')}">
        {if $user_info['permission']['create_topic_enable']=='Y' || $user_info['group_id']==1 || $user_info['group_id']==2}
        <div class="flex-fill ml-2" style="min-width: 80px"><a class="saveCreateTopic btn btn-primary btn-sm" href="javascript:;">{:L('添加话题')}</a></div>
        {/if}
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

            {if $recent_topic_list}
            <!--最近使用话题-->
            <dl class="overflow-hidden">
                <dt class="text-muted mb-2 font-9">{:L('最近使用话题')}</dt>
                <dd class="w-100 d-block">
                    <ul class="page-detail-topic">
                        {volist name="recent_topic_list" id="v"}
                        <li class="d-inline-block position-relative aw-tag mb-2 topicSelected {$v.is_checked ? 'active' : ''}" data-title="{$v.title}" data-id="{$v.id}">
                            <a href="javascript:;" class="aw-topic d-inline-block">
                                <em class="tag">{$v.title}</em>
                            </a>
                        </li>
                        {/volist}
                    </ul>
                </dd>
            </dl>
            {/if}
            <ul  class="topicSearchList page-detail-topic"></ul>
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