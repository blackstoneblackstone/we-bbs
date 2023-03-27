{if !empty($list)}
{volist name="list" id="v"}
<div class="aui-card mb-1 aui-card-image">
    <div class="aui-card-main">
        <div class="text-center p-3">
            <a href="{:url('column/detail',['id'=>$v['id']])}" class="d-block text-center">
                <img src="{$v.cover}" alt="{$v.name}" width="100" height="100" onerror="this.src='/static/common/image/default-cover.svg'" style="border-radius: 50%">
                <span class="d-block font-11 font-weight-bold">{$v.name}</span>
            </a>
            <p class="text-muted font-9 mt-2 aw-two-line">{$v.description|raw}</p>
        </div>
    </div>
    <div class="aui-card-down row-before" style="padding: 0;">
        <div class="aui-list" style="background: none;">
            <div class="aui-list-left text-muted">
                <span class="mr-4">{:L('文章')} {$v.post_count|num2string}</span>
                <span>{:L('关注')} {$v.focus_count|num2string}</span>
            </div>
            <div class="aui-list-right">
                <a href="{:url('column/detail',['id'=>$v['id']])}" data-pjax="pageMain"><span style="color: #aaa;">{:L('查看详情')}</span><i class="iconfont aui-btn-right iconright1"></i></a>
            </div>
        </div>
    </div>
</div>
{/volist}
{/if}