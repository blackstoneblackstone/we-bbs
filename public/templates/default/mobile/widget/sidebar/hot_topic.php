{if !empty($topic_list)}
<div class="aui-card mb-1">
    <div class="aui-card-main" style="padding: 0;">
        <div class="aui-lists">
            <div class="aui-list">
                <a href="{:url('topic/index',['type'=>'discuss'])}" data-pjax="pageMain">
                    <div class="aui-list-left font-weight-bold">{:L('热门话题')}</div>
                    <div class="aui-list-right">
                        <span class="font-8 text-muted">{:L('去话题广场')}</span>
                        <i class="iconfont aui-btn-right font-8  iconright1"></i>
                    </div>
                </a>
            </div>
            <div class="topicList swiper-container py-3 mx-3">
                <ul class="swiper-wrapper">
                {volist name="topic_list" id="v"}
                <li class="swiper-slide text-center mr-3" style="max-width: 80px;">
                    <a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic d-block mb-1" data-id="{$v.id}">
                        <img src="{$v['pic']|default='/static/common/image/topic.svg'}" width="60" height="60" class="rounded">
                    </a>
                    <a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic d-block aw-two-line" data-id="{$v.id}">{$v.title}</a>
                </li>
                {/volist}
                </ul>
            </div>
        </div>
    </div>
</div>
{/if}
