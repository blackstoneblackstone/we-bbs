{if !empty($relation_posts)}
{volist name="relation_posts" id="v"}
{if $type=='question'}
<dl class="mb-0 p-3 bg-white border-bottom">
    <dt class="d-block aw-one-line font-weight-normal font-9">
        <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
    </dt>
    <dd class="mt-2 text-color-info mb-0">
        <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
        <label class="mr-2 mb-0">{$v.focus_count} {:L('关注')}</label>
        <label class="mr-2 mb-0">{$v['answer_count']} {:L('回答')}</label>
        <label class="mb-0">{$v['comment_count']} {:L('评论')}</label>
    </dd>
</dl>
{/if}
{if $type=='article'}
<dl class="mb-0 p-3 bg-white border-bottom">
    <dt class="d-block aw-one-line font-weight-normal">
        <a href="{:url('article/detail',['id'=>$v['id']])}">{$v.title}</a>
    </dt>
    <dd class="mt-2 font-9 text-color-info mb-0">
        <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
        <label class="mr-2 mb-0">{$v['comment_count']} {:L('评论')}</label>
    </dd>
</dl>
{/if}
{/volist}
{/if}
