{if !empty($topic_list)}
<div class="r-box mb-2 hot-topic">
    <div class="r-title">
        <h4>{:L('热门话题')}</h4>
        <a href="{:url('topic/index',['type'=>'discuss'])}" target="_blank"><label class="iconfont">&#xe660;</label></a>
    </div>
    <div class="hot-list">
        {volist name="topic_list" id="v"}
        <dl class="row d-flex">
            <dt class="flex-fill" style="max-width: 41px">
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}">
                    <div style='background-image:url("{$v['pic']|default='/static/common/image/topic.svg'}") ;background-size: cover;width:41px;height:41px'></div>
                </a>
            </dt>
            <dd class="flex-fill" style="width: calc(100% - 41px)">
                <h3 class="aw-one-line"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}">{$v.title}</a></h3>
                <label>{:L('讨论')}:<i>{$v.discuss}</i></label>
                <label>{:L('关注')}:<i>{$v.focus}</i></label>
            </dd>
        </dl>
        {/volist}
    </div>
</div>
{/if}
