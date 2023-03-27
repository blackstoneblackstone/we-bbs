{extend name="$theme_block" /}
{block name="main"}
<div class="p-3" id="tabMain">
    {if !empty($list)}
    {volist name="list" id="v"}
    {if $v['item_type']=="question" || $v['relation_type']=='question' }
    <dl>
        <dt>
            <span class="text-muted">{$v.remark|raw}</span>
        </dt>
        <dd class="mt-2">
            <div class="n-title">
                <span class="tip-s1 badge badge-secondary">问答</span>
                <a href="{:url('question/detail',['id'=>$v['id']])}" target="_blank">{$v.title}</a>
            </div>
        </dd>
    </dl>
    {/if}

    {if $v['item_type']=="article" || $v['relation_type']=='article' }
    <dl class="article">
        <dt>
            <span class="text-muted">{$v.remark|raw}</span>
        </dt>
        <dd class="mt-2">
            <div class="n-title">
                <span class="tip-s2 badge badge-secondary">文章</span>
                {:hook('article_badge')}
                <a href="{:url('article/detail',['id'=>$v['id']])}"  target="_blank">{$v['title']}</a>
            </div>
        </dd>
    </dl>
    {/if}

    {if $v['item_type']=="answer" || $v['relation_type']=='answer' }
    <dl>
        <dt class="mb-2">
            <span class="text-muted">{$v.remark|raw}</span>
        </dt>
        <dd class="n-title mt-2">
            <a href="{:url('question/detail',['id'=>$v['id'],'answer'=>$v['answer_info']['id']])}"  target="_blank">{$v.title}</a>
        </dd>
    </dl>
    {/if}

    {if $v['item_type']=='topic' && $v['relation_type']==''}
    <dl>
        <dt class="mb-2">
            <span class="text-muted">{$v.remark|raw}</span>
        </dt>
    </dl>
    {/if}
    {/volist}
    {$page|raw}
    {else/}
    <p class="text-center py-3 text-muted">
        <img src="{$cdnUrl}/static/common/image/empty.svg">
        <span class="d-block">暂无内容</span>
    </p>
    {/if}
</div>
{/block}