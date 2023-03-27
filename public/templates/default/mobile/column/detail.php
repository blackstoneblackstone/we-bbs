{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">{:L('专栏详情')}</div>
    <div class="aui-header-right" onclick="AWS_MOBILE.api.dialog('{:L(\'专栏操作\')}',$('.columnAction').html())">
        <i class="fa fa-ellipsis-v" style="font-size: 0.9rem;"></i>
    </div>
</header>

<script type="text/html" class="columnAction">
    <div class="row text-center mt-3">
        {if $user_id}
        {if $column_info['uid']==$user_id}
        <div class="col-4 mb-4">
            <a href="{:url('column/apply',['id'=>$column_info.id])}" class="text-muted">
                <i class="fa fa-edit font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('编辑专栏')}</span>
            </a>
        </div>
        {/if}
        {if $column_info['uid']==$user_id}
        <div class="col-4 mb-4">
            <a href="{:url('article/publish',['column_id'=>$column_info.id])}" class="text-muted">
                <i class="fa fa-trash-alt font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('发表文章')}</span>
            </a>
        </div>
        {/if}
        {/if}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.shareBox('{$column_info.name}','{:url('column/detail',['id'=>$column_info.id],true,true)}')">
                <i class="fa fa-send font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('分享')}</span>
            </a>
        </div>
    </div>
</script>
{/block}

{block name="main"}
<div class="main-container mt-1 mescroll" id="ajaxPage">
    <div class="aui-card mb-1 aui-card-image">
        <div class="aui-card-main">
            <div class="text-center p-3">
                <img src="{$column_info.cover}" onerror="this.src='/static/common/image/default-cover.svg'" width="100" height="100" style="border-radius: 50%">
                <h3 class="font-12">{$column_info.name}</h3>
                <p class="text-color-info font-9 mt-2">{$column_info.description|raw}</p>
                <!--<div class="d-flex">
                    <div class="flex-fill">
                        <a href="{$column_info['user_info']['url']}" class="aw-username avatar" data-id="{$column_info['user_info']['uid']}">
                            <img src="{$column_info['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="rounded aw-user-img">
                        </a>
                        <a href="{$column_info['user_info']['url']}" class="aw-username name" data-id="{$column_info['user_info']['uid']}">{$column_info['user_info']['name']}</a>
                    </div>
                </div>-->
            </div>
        </div>
        <div class="aui-card-down row-before">
            <div class="aui-btn">{:L('文章')} {$column_info['post_count']}</div>
            <div class="aui-btn">{:L('关注')} {$column_info['focus_count']}</div>
            <div class="aui-btn">
                <a onclick="AWS_MOBILE.User.focus(this,'column','{$column_info.id}')" href="javascript:;" class="px-3 {$focus ? 'gz': 'ygz'}">
                    {if !$focus}{:L('已关注')}{else}+{:L('关注')}{/if}
                </a>
            </div>
        </div>
    </div>
    <div class="swiper-container">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide" data-type="focus">
                <a class="nav-link {if $sort=='column'}active{/if}" data-pjax="pageMain" href="{:url('column/detail',['id'=>$column_info['id']])}">{:L('专栏文章')}</a>
            </li>
            <li class="nav-item swiper-slide" data-type="new">
                <a class="nav-link {if $sort=='other'}active{/if}" data-pjax="pageMain" href="{:url('column/detail',['id'=>$column_info['id'],'sort'=>'other'])}">{:L('其他文章')}</a>
            </li>
        </ul>
    </div>
    <div id="ajaxResult"></div>
</div>

<script>
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
            url: baseUrl+'/ajax/get_column_article?page=' + pageNum,
            type:"POST",
            data:{sort:'{$sort}',id:'{$column_info.id}'},
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
    }
</script>
{/block}
{block name="footer"}{/block}