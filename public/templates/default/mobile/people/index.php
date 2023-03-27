{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{$user.nick_name}{:L('个人主页')}</div>
    <div class="aui-header-right"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
</header>
{/block}

{block name="main"}
<div class="main-container mescroll mt-1" id="ajaxPage">
    <div class="px-3 pt-3 text-white position-relative" style="padding-bottom: 4rem;background: #563d7cf7;"></div>
    <div class="bg-white position-relative" style="border-top-left-radius: 10px;border-top-right-radius: 10px;">
        <div class="d-flex px-3" style="height: 70px">
            <div class="position-relative flex-fill" style="top: -1.8rem;max-width: 80px">
                <img src="{$user.avatar}" style="border-radius: 50%" onerror="this.src='/static/common/image/default-avatar.svg'" width="80" height="80">
            </div>

            <div class="flex-fill d-flex text-center pt-2">
                <div class="flex-fill">
                    <span class="d-block text-muted font-12">{$user.reputation}</span>
                    <span class="d-block ">{$setting.power_unit}</span>
                </div>
                <div class="flex-fill">
                    <span class="d-block text-muted font-12">{$user.fans_count}</span>
                    <span class="d-block ">{:L('粉丝')}</span>
                </div>
                <div class="flex-fill">
                    <span class="d-block text-muted font-12">{$user.agree_count}</span>
                    <span class="d-block">{:L('获赞')}</span>
                </div>
            </div>
        </div>
        <div class="px-3 pb-3">
            <h3 class="text-muted font-12 mb-1">{$user.name}</h3>
            <span class="badge badge-success font-9">{$user.group_name}</span>
            <p class="aw-one-line mt-1">{$user.signature|default=L('这家伙还没有留下自我介绍～')}</p>

            {if $user_id}
            <div class="aw-home-user-btn mt-2 position-absolute" style="right: 10px;top: 60px">
                {if $user_id==$user.uid}
                <a class="info-btn" href="{:url('member/setting/profile')}">{:L('编辑资料')}</a>
                {else/}
                <a class="btn btn-primary btn-sm px-4 {if $user['has_focus']}active{/if} {$user['has_focus'] ? 'ygz' : 'gz'}" href="javascript:;"
                   onclick="AWS_MOBILE.User.focus(this,'user','{$user.uid}')">{:L($user['has_focus'] ? '已关注' : '关注')}</a>
                <a  class="btn btn-outline-primary btn-sm px-4 mr-2" href="javascript:;" onclick="AWS_MOBILE.User.inbox('{$user.nick_name}')">{:L('私信')}</a>
                {/if}
            </div>
            {/if}
        </div>
    </div>
    <div class="swiper-container bg-white mt-1">
        <ul class="aw-pjax-tabs nav nav-tabs px-2 nav-tabs-block bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide {if $type=='dynamic'}i-active{/if}">
                <a class="nav-link {if $type=='dynamic'}active{/if}" data-pjax="pageMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'dynamic'])}">{:L('动态')}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='question'}i-active{/if}">
                <a class="nav-link {if $type=='question'}active{/if}" data-pjax="pageMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'question'])}">{:L('提问')} {$question_count}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='answer'}i-active{/if}">
                <a class="nav-link {if $type=='answer'}active{/if}" data-pjax="pageMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'answer'])}">{:L('回答')} {$answer_count}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='article'}i-active{/if}">
                <a class="nav-link {if $type=='article'}active{/if}" data-pjax="pageMain" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'article'])}">{:L('文章')} {$article_count}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='friend'}i-active{/if}">
                <a class="nav-link {if $type=='friend'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'friend'])}" data-pjax="pageMain">{:L('关注的人')}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='fans'}i-active{/if}">
                <a class="nav-link {if $type=='fans'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'fans'])}" data-pjax="pageMain">{:L('关注TA的')}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='column'}i-active{/if}">
                <a class="nav-link {if $type=='column'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'column'])}" data-pjax="pageMain">{:L('关注的专栏')}</a>
            </li>
            <li class="nav-item swiper-slide {if $type=='topic'}i-active{/if}">
                <a class="nav-link {if $type=='topic'}active{/if}" href="{:url('people/index',['name'=>$user['url_token'],'type'=>'topic'])}" data-pjax="pageMain">{:L('关注的话题')}</a>
            </li>
            {:hook('people_nav',['user'=>$user])}
        </ul>
    </div>
    <div id="ajaxResult" class="aw-common-list"></div>
</div>

<script>
    var mescroll = new MeScroll("ajaxPage", {
        down: {
            callback: function(){
                mescroll.resetUpScroll()
            }
        },
        up: {
            callback: function (page) {
                var pageNum = page.num;
                $.ajax({
                    url: baseUrl+'/manage/get_user_post?page=' + pageNum,
                    type:"POST",
                    data:{
                        type:'{$type}',
                        uid:"{$user.uid}"
                    },
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
            }, //上拉加载的回调
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
</script>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}{/block}