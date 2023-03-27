{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="bg-white">
        <div class="d-flex p-3 border-bottom">
            <div class="flex-fill text-right">
                <a href="{:url('question/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:L('问题')}</a>
            </div>
            <div class="flex-fill text-center">
                <a href="{:url('article/index')}" data-pjax="pageMain" class="font-weight-bold font-11 text-primary">{:L('文章')}</a>
            </div>
            <div class="flex-fill text-left">
                <a href="{:url('column/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:L('专栏')}</a>
            </div>
        </div>
        <!--热门用户、话题-->
        <!--<div class="hotUserTopic d-flex p-2">
            <div class="flex-fill mr-1">
                <a href="{:url('people/lists')}">
                    <img src="{$static_url}mobile/img/hot-user.png" style="width: 100%;display: inline-block">
                </a>
            </div>
            <div class="flex-fill ml-1">
                <a href="{:url('topic/index')}">
                    <img src="{$static_url}mobile/img/hot-topic.png" style="width: 100%;display: inline-block">
                </a>
            </div>
        </div>-->
    </div>

    <div id="wrapMain">
        {if $setting.enable_category=='Y'}
        {:widget('common/category',['type'=>'article','category'=>$category,'show_type'=>'list'])}
        {/if}

        <div class="swiper-container mt-1 bg-white">
            <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide" data-type="new">
                    <a class="nav-link {if $sort=='new'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'new','category_id'=>$category])}">{:L('最新')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="recommend">
                    <a class="nav-link {if $sort=='recommend'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'recommend','category_id'=>$category])}">{:L('推荐')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="hot">
                    <a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="pageMain" href="{:url('article/index',['sort'=>'hot','category_id'=>$category])}">{:L('热门')}</a>
                </li>
            </ul>
        </div>

        <div class="aw-common-list" id="ajaxResult"></div>
    </div>
</div>

<script>
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax/lists')}",{sort:'{$sort}',item_type:'article',category_id:"{$category}"},perPage);
</script>
{/block}
