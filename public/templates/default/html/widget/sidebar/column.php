{if !empty($list)}
<div class="r-box mb-2 hot-topic">
    <div class="r-title">
        <h4>{:L('热门专栏')}</h4>
        <a href="{:url('column/index',['sort'=>'hot'])}" target="_blank"><label class="iconfont">&#xe660;</label></a>
    </div>
    <div class="hot-list">
        {volist name="list" id="v"}
        <dl class="row d-flex">
            <dt class="flex-fill" style="max-width: 41px">
                <a href="{:url('column/detail',['id'=>$v['id']])}">
                    <div style='background-image:url("{$v['cover']|default='/static/common/image/topic.svg'}") ;background-size: cover;width:41px;height:41px'></div>
                </a>
            </dt>
            <dd class="flex-fill" style="width: calc(100% - 41px)">
                <h3><a href="{:url('column/detail',['id'=>$v['id']])}">{$v.name}</a></h3>
                <label>{:L('文章')}:<i>{$v.post_count}</i></label>
                <label>{:L('浏览')}:<i>{$v.view_count}</i></label>
            </dd>
        </dl>
        {/volist}
    </div>
</div>
{/if}
