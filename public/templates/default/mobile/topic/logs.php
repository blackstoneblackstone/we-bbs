{extend name="$theme_block" /}
{block name="main"}
<div class="px-3 py-2" id="tabMain">
    {if !empty($list)}
    {volist name="list" id="v"}
    {if $v['item_type']=="question" || $v['relation_type']=='question' }
    <dl class="py-2 border-bottom">
        <dt>
            <span class="text-muted font-9">{$v.remark|raw}</span>
        </dt>
        <dd class="mt-1">
            <div class="n-title">
                <span class="tip-s1 badge badge-secondary">{:L('问答')}</span>
                <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a>
            </div>
        </dd>
    </dl>
    {/if}

    {if $v['item_type']=="article" || $v['relation_type']=='article' }
    <dl class="py-2 border-bottom">
        <dt>
            <span class="text-muted font-9">{$v.remark|raw}</span>
        </dt>
        <dd class="mt-1">
            <div class="n-title">
                <span class="tip-s2 badge badge-secondary">{:L('文章')}</span>
                {:hook('article_badge')}
                <a href="{:url('article/detail',['id'=>$v['id']])}"  target="_blank">{$v['title']}</a>
            </div>
        </dd>
    </dl>
    {/if}

    {if $v['item_type']=="answer" || $v['relation_type']=='answer' }
    <dl class="py-2 border-bottom">
        <dt>
            <span class="text-muted font-9">{$v.remark|raw}</span>
        </dt>
        <dd class="n-title mt-1">
            <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}"  target="_blank">{$v.title}</a>
        </dd>
    </dl>
    {/if}

    {if $v['item_type']=='topic' && $v['relation_type']==''}
    <dl class="py-2 border-bottom">
        <dt>
            <span class="text-muted font-9">{$v.remark|raw}</span>
        </dt>
    </dl>
    {/if}
    {/volist}
    {$page|raw}
    {else/}
    <p class="text-center py-3 text-muted">
        <img src="{$cdnUrl}/static/common/image/empty.svg">
        <span class="d-block">{:L('暂无内容')}</span>
    </p>
    {/if}
</div>
{/block}