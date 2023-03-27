{if $user_id && !empty($topic_list)}
<div class="r-box mb-2 hot-topic">
    <div class="r-title">
        <h4>{:L('关注话题')}</h4>
        <a href="{:url('focus/index',['type'=>'topic'])}" target="_blank"><label class="iconfont">&#xe660;</label></a>
    </div>
    <div class="hot-list hot-yh-list pb-2">
        <div class="sidebarFocusTopic aw-tag">
            {foreach $topic_list as $key=>$v}
            <a href="{:url('topic/detail',['id'=>$v['id']])}" data-id="{$v['id']}"  class="topic-btn d-inline-block mb-2"><em class="tag">{$v.title}</em></a>
            {/foreach}
        </div>
        {foreach $topic_list as $key=>$v}
        <div class="bg-light rounded p-2 sidebarTopicHover" id="topic{$v.id}" style="{if $key!=0}display: none {/if}">
            <div class="clearfix">
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="text-primary float-left">{$v.title}</a>
                <a href="{:url('topic/detail',['id'=>$v['id']])}" class="icon-arrow-right float-right"></a>
            </div>
            <p class="mt-1 mb-0">
                <b>{$v.question_count}</b> <span>{:L('提问')}</span>
                <b>{$v.article_count}</b> <span>{:L('文章')}</span>
            </p>
        </div>
        {/foreach}
    </div>
</div>
<script>
    $('.sidebarFocusTopic a').mousemove(function(){
        var topic = $(this).attr('data-id');
        $('.sidebarTopicHover').hide();
        $('#topic'+topic).show();
    });
</script>
{/if}
