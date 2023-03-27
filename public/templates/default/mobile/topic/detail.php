{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">{:L('话题详情')}</div>
    <div class="aui-header-right" onclick="AWS_MOBILE.api.dialog('{:L(\'话题操作\')}',$('.topicAction').html())">
        <i class="fa fa-ellipsis-v" style="font-size: 0.9rem;"></i>
    </div>
</header>

<script type="text/html" class="topicAction">
    <div class="row text-center mt-3">
        {if $user_id && $user_info['permission']['topic_manager']=='Y'}
        <li class="col-4 mb-3">
            <a href="{:url('topic/manager',['id'=>$topic_info['id']])}" class="text-center d-block text-muted">
                <i class="icon-edit d-block font-14"></i>
                <span class="d-block font-9 mt-2">{:L('编辑话题')}</span>
            </a>
        </li>
        {/if}
        {if $user_id && get_user_permission('lock_topic')=='Y'}
        <li class="col-4 mb-3">
            <a href="javascript:;" data-confirm="{:L($topic_info['lock'] ? '是否取消锁定该话题?' : '是否锁定该话题?锁定话题后其他用户将无法对该话题进行编辑')}" data-url="{:url('ajax/lock',['id'=>$topic_info['id']])}" class="aw-ajax-get text-center d-block text-muted">
                <i class="icon-lock d-block font-14"></i>
                <span class="d-block font-9 mt-2">{if $topic_info['lock']}{:L('取消锁定')}{else}{:L('锁定话题')}{/if}</span>
            </a>
        </li>
        {/if}
        {if $user_id && get_user_permission('remove_topic')=='Y'}
        <li class="col-4 mb-3">
            <a href="javascript:;" data-confirm="{:L('是否删除该话题?')}" data-url="{:url('topic/remove_topic',['id'=>$topic_info['id']])}" class="aw-ajax-get text-center d-block text-muted">
                <i class="icon-trash d-block font-14"></i>
                <span class="d-block font-9 mt-2">{:L('删除话题')}</span>
            </a>
        </li>
        {/if}

        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.shareBox('{$topic_info.title}','{:url('topic/detail',['id'=>$topic_info.id],true,true)}')">
                <i class="fa fa-send font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('分享')}</span>
            </a>
        </div>
    </div>
</script>
{/block}

{block name="main"}
<div class="main-container mt-1 mescroll" id="ajaxPage">
    <div class="px-3 text-white " style="background-image:url('{$static_url}mobile/img/topic_bg.png')">
        <dl class="topic-detail-top pt-3 pb-2 d-flex mb-0">
            <dt class="flex-fill" style="max-width: 50px">
                <img src="{$topic_info['pic']|default='/static/common/image/topic.svg'}" onerror="this.src='/static/common/image/topic.svg'"  class="rounded" alt="{$topic_info.title}" width="50" height="50">
            </dt>
            <dd class="flex-fill ml-2">
                <h4 class="mb-0 font-12 clearfix font-weight-bold text-white">
                    {$topic_info.title}
                    {if $user_id}
                    <a href="javascript:;" style="height: 26px;line-height: 1.2" class="btn btn-primary btn-sm float-right px-3 cursor-pointer {$topic_info['has_focus'] ? 'ygz' :'gz'}" onclick="AWS_MOBILE.User.focus(this,'topic','{$topic_info.id}')">
                        {$topic_info['has_focus'] ? '<span>'.L('已关注').'</span>' : '<span>+ '.L('关注').'</span>'}
                    </a>
                    {/if}
                </h4>
                <p class="mt-1 font-8">{:L('讨论')} {$topic_info['discuss']}   {:L('关注')} {$topic_info['focus']} </p>
            </dd>
        </dl>
        {if $topic_info['description']}
        <div class="pb-2">
            <div class="aw-two-line ">
                {:str_cut(strip_tags(htmlspecialchars_decode($topic_info['description'])),0,100)}
                {if mb_strlen(strip_tags($topic_info['description']))>=100}
                <a href="{:url('topic/detail',['type'=>'about','id'=>$topic_info['id']])}"  data-pjax="aw-index-main" class="pl-3 text-primary">{:L('查看详情')}</a>
                {/if}
            </div>
        </div>
        {/if}

        <div class="d-flex py-3">
            <a class="flex-fill text-light" href="{:url('question/publish',['topic_id'=>$topic_info['id']])}">
                <i class="fa icon-help-with-circle d-inline-block"></i> {:L('发讨论')}
            </a>
            <a class="flex-fill text-light" href="{:url('article/publish',['topic_id'=>$topic_info['id']])}" >
                <i class="far fa-file-alt d-inline-block" ></i> {:L('写文章')}
            </a>
            <a class="flex-fill aw-ajax-open text-light" href="javascript:;" data-title="话题日志" data-url="{:url('topic/logs',['id'=>$topic_info['id']])}">
                <i class="icon-book d-inline-block"></i> {:L('话题日志')}
            </a>
        </div>
    </div>
    <div class="swiper-container">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide" >
                <a class="nav-link {if !$type}active {/if}" data-pjax="pageMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'sort'=>$sort])}">{:L('综合')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='question'}active{/if}" data-pjax="pageMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'question','sort'=>$sort])}">{:L('问题')}</a>
            </li>
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='article'}active{/if}" data-pjax="pageMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'article','sort'=>$sort])}">{:L('文章')}</a>
            </li>
            {volist name=":config('aws.tabs')" id="v"}
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='article'}active{/if}" data-pjax="pageMain" href="{:url($v['url'],['topic_id'=>$topic_info['id'],'type'=>$key,'sort'=>$sort])}">{$v.title}</a>
            </li>
            {/volist}
            <li class="nav-item swiper-slide">
                <a class="nav-link {if $type=='about'}active{/if}" data-pjax="pageMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'about'])}">{:L('详情')}</a>
            </li>
        </ul>
    </div>
    {if $type!='about'}
    <div id="ajaxResult"></div>
    {else/}
    <div class="p-3 bg-white aw-content">
        {:htmlspecialchars_decode($topic_info['description'])}
    </div>
    {/if}
</div>
{if $type!='about'}
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
            url: baseUrl+'/ajax/lists?page=' + pageNum,
            type:"POST",
            data:{
                sort:'{$sort}',
                item_type:'{$type}',
                topic_ids:'{$topic_info.id}'
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
    }
</script>
{/if}
{/block}
{block name="footer"}{/block}