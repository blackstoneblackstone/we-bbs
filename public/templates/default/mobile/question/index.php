{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="bg-white">
        <div class="d-flex p-3 border-bottom">
            <div class="flex-fill text-right">
                <a href="{:url('question/index')}" data-pjax="pageMain" class="font-weight-bold font-11 text-primary">{:L('问题')}</a>
            </div>
            <div class="flex-fill text-center">
                <a href="{:url('article/index')}" data-pjax="pageMain" class="font-weight-bold font-11">{:L('文章')}</a>
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
        {:widget('common/category',['type'=>'question','category'=>$category,'show_type'=>'list'])}
        {/if}

        <div class="swiper-container mt-1 bg-white">
            <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide" data-type="new">
                    <a class="nav-link {if $sort=='new'}active{/if}" data-pjax="pageMain" href="{:url('question/index',['sort'=>'new','category_id'=>$category])}">{:L('最新')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="recommend">
                    <a class="nav-link  {if $sort=='recommend'}active{/if}" data-pjax="pageMain" href="{:url('question/index',['sort'=>'recommend','category_id'=>$category])}">{:L('推荐')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="hot">
                    <a class="nav-link {if $sort=='hot'}active{/if}" data-pjax="pageMain" href="{:url('question/index',['sort'=>'hot','category_id'=>$category])}">{:L('热门')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="unresponsive">
                    <a class="nav-link {if $sort=='unresponsive'}active{/if}" data-pjax="pageMain" href="{:url('question/index',['sort'=>'unresponsive','category_id'=>$category])}">{:L('待回答')}</a>
                </li>
            </ul>
        </div>

        <div class="aw-common-list" id="ajaxResult"></div>
    </div>
</div>

<script>
    var mescroll = new MeScroll('ajaxPage', {
        down: {
            callback: function (){
                mescroll.resetUpScroll();
            }
        },
        up: {
            callback: function (page)
            {
                var pageNum = page.num;
                $.ajax({
                    url: "{:url('ajax/lists')}?page="+pageNum,
                    type:"POST",
                    data:{sort:'{$sort}',item_type:'question',category_id:'{$category}'},
                    success: function(result) {
                        var curPageData = result.data.list || result.data.data;
                        var totalPage = result.data.total;
                        mescroll.endByPage(curPageData.length, totalPage)
                        if(pageNum == 1){
                            $('#ajaxResult').empty();
                        }

                        $('#ajaxResult').append(result.data.html);
                    },
                    error: function(e) {
                        //联网失败的回调,隐藏下拉刷新和上拉加载的状态
                        mescroll.endErr();
                    }
                });
            },
            page: {
                num: 0, //当前页 默认0,回调之前会加1; 即callback(page)会从1开始
                size: perPage //每页数据条数,默认10
            },
            htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
            noMoreSize: 5,
            toTop: {
                //回到顶部按钮
                src: "/static/common/image/back_top.png", //图片路径,默认null,支持网络图
                offset: 1000 //列表滚动1000px才显示回到顶部按钮
            },
            empty: {
                //列表第一页无任何数据时,显示的空提示布局; 需配置warpId才显示
                warpId:	"ajaxPage", //父布局的id (1.3.5版本支持传入dom元素)
                icon: "/static/common/image/no-data.png", //图标,默认null,支持网络图
                tip: "暂无相关数据~" //提示
            },
            lazyLoad: {
                use: true, // 是否开启懒加载,默认false
                attr: 'url' // 标签中网络图的属性名 : <img imgurl='网络图  src='占位图''/>
            }
        }
    });
</script>
{/block}
