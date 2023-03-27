<dl class="overflow-hidden">
    <dt class="text-muted mb-2 font-9">{:L('搜索结果')}</dt>
    <dd class="w-100 d-block">
        <ul class="page-detail-topic">
            {volist name="questions" id="v"}
            <li class="d-block position-relative py-2 selectedQuestion" data-item-id="{$item_id}" data-id="{$v.id}">
                <a href="javascript:;" class="d-block">{$v.title}</a>
            </li>
            {/volist}
        </ul>
    </dd>
</dl>
