{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="swiper-container bg-white">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide">
                <a class="nav-link {if !$type || $type=='new'}active{/if}" href="{:url('topic/index',['type'=>'new','pid'=>$pid])}" data-pjax="pageMain">{:L('最新')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='focus'}active{/if}" href="{:url('topic/index',['type'=>'focus','pid'=>$pid])}" data-pjax="pageMain">{:L('关注最多')}</a>
            </li>

            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='discuss'}active{/if}" href="{:url('topic/index',['type'=>'discuss','pid'=>$pid])}" data-pjax="pageMain">{:L('讨论最多')}</a>
            </li>
        </ul>
    </div>
    <div class="bg-white py-2">
        <div class="swiper-container mx-3" style="margin: 0">
            <ul class="nav nav-pills n-nav swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide">
                    <a class="nav-link mb-0 {if !$pid}active c-active{/if}" data-k="0" data-pjax="pageMain" href="{:url('topic/index',['type'=>$type,'pid'=>0])}">{:L('全部话题')}</a>
                </li>
                {foreach $parent_list as $k => $v}
                <li class="nav-item swiper-slide">
                    <a class="nav-link {if $pid==$v.id} active c-active{/if}" data-k="{$k}" data-pjax="pageMain" href="{:url('topic/index',['type'=>$type,'pid'=>$v.id])}">{$v.title}</a>
                </li>
                {/foreach}
            </ul>
        </div>
    </div>

    <div class="aw-common-list aw-topic-list" id="ajaxResult"></div>
</div>
<script>
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax/get_topic_list')}",{type:'{$type}',pid:'{$pid}'},perPage);
</script>
{/block}