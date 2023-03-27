{extend name="$theme_block" /}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="swiper-container bg-white">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $sort=='integral'}active{/if}" href="{:url('people/lists',['sort'=>'integral'])}" data-pjax="pageMain">{:L('活跃榜')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $sort=='reputation'}active{/if}" href="{:url('people/lists',['sort'=>'reputation'])}" data-pjax="pageMain">{:L('威望榜')}</a>
            </li>
        </ul>
    </div>

    <div class="aw-common-list" id="ajaxResult"></div>
</div>
<script>
    var sort = "{$sort}";
    var mescroll = new MeScroll("ajaxPage", {
        down: {
            callback: downCallback
        },
        up: {
            callback: upCallback, //上拉加载的回调
            page: {
                num: 0, //当前页 默认0,回调之前会加1; 即callback(page)会从1开始
                size: perPage //每页数据条数,默认10
            },
            htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
            noMoreSize: 5, //如果列表已无数据,可设置列表的总数量要大于5才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看这就是为什么无更多数据有时候不显示的原因.
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

    function downCallback() {
        mescroll.resetUpScroll()
    }

    function upCallback(page) {
        var pageNum = page.num;
        $.ajax({
            url: baseUrl+'/ajax/get_people_list?page=' + pageNum,
            type:"POST",
            data:{sort:sort},
            success: function(result) {
                var curPageData = result.data.list;
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
    }
</script>
{/block}