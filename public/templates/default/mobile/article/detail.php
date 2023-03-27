{extend name="$theme_block" /}
{block name="meta_script"}
{if $theme_config['common']['enable_mathjax']=='Y'}
<style>
    .MathJax{outline:0;text-align: unset !important;}
</style>
<script async src="{$cdnUrl}/static/common/js/tex-mml-chtml.js"></script>
{/if}
{/block}

{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="{$baseUrl}" class="text-muted" data-pjax="pageMain"><i class="fa fa-home font-11"></i></a></div>
    <div class="aui-header-title">{:L('文章详情')}</div>
    <a class="aui-header-right" onclick="AWS_MOBILE.api.dialog('{:L(\'文章操作\')},$('.articleAction').html())"><i class="fa fa-ellipsis-v" style="font-size: 0.9rem;"></i></a>
</header>

<script type="text/html" class="articleAction">
    <div class="row text-center mt-3">
        {if $user_id}
        {if $user_id!=$article_info['uid']}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted"onclick="AWS_MOBILE.User.report(this,'article','{$article_info.id}');">
                <i class="icon-warning font-14"></i>
                <span class="d-block font-9 mt-1">{if $article_info['is_report']}{:L('已举报')}{else/}{:L('举报')}{/if}</span>
            </a>
        </div>
        {/if}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted"onclick="AWS_MOBILE.User.favorite(this,'article','{$article_info.id}');">
                <i class="icon-star-outlined font-14"></i>
                <span class="d-block font-9 mt-1">{if $article_info['is_favorite']}{:L('已收藏')}{else/}{:L('收藏')}{/if}</span>
            </a>
        </div>

        {if $user_id && ($user_id==$article_info['uid'] || isSuperAdmin() || isNormalAdmin())}
        {if get_user_permission('recommend_post')=='Y'}
        <div class="col-4 mb-4">
            <a href="javascript:;"  class="aw-ajax-get text-muted" data-url="{:url('article/action',['type'=>'recommend','is_recommend'=>$article_info['is_recommend'],'article_id'=>$article_info['id']])}">
                <i class="fa fa-chevron-circle-up  font-14"></i><br>
                <span class="d-block font-9 mt-1">{$article_info['is_recommend'] ? L('取消推荐') : L('推荐文章')}</span>
            </a>
        </div>
        {/if}
        {if get_user_permission('set_top_post')=='Y'}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="aw-ajax-get text-muted" data-url="{:url('article/action',['type'=>'set_top','set_top'=>$article_info['set_top'],'article_id'=>$article_info['id']])}">
                <i class="far fa-hand-point-up  font-14"></i><br>
                <span class="d-block font-9 mt-1">{$article_info['set_top'] ? L('取消置顶') : L('置顶文章')}</span>
            </a>
        </div>
        {/if}

        {if $user_id && (get_user_permission('modify_article')=='Y' || $user_info['uid']==$article_info['uid'])}
        <div class="col-4 mb-4">
            <a href="{:url('article/publish',['id'=>$article_info['id']])}" class="text-muted">
                <i class="fa fa-edit font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('编辑文章')}</span>
            </a>
        </div>
        {/if}
        {if $user_id && (get_user_permission('remove_article')=='Y' || $user_info['uid']==$article_info['uid'])}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="aw-ajax-get text-muted" data-confirm="{:L('确定要删除吗')}？" data-url="{:url('article/remove_article',['id'=>$article_info['id']])}">
                <i class="fa fa-trash-alt font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('删除文章')}</span>
            </a>
        </div>

        {if !$article_info['column_id'] && ($user_info['uid']==$article_info['uid'] || isNormalAdmin() || isSuperAdmin())}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="aw-ajax-open text-muted" data-title="{:L('收录至专栏')}" data-url="{:url('ajax.column/collect',['id'=>$article_info['id']])}">
                <i class="fa fa-book-medical font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('收录至专栏')}</span>
            </a>
        </div>
        {/if}
        {/if}
        {/if}
        {/if}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.shareBox('{$article_info.title}','{:url('article/detail',['id'=>$article_info.id],true,true)}')">
                <i class="fa fa-send font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('分享')}</span>
            </a>
        </div>
        <!--自定义文章页左侧拓展钩子-->
        {:hook('article_detail_page_left',['article_info'=>$article_info])}
    </div>
</script>
{/block}

{block name="main"}
<div class="aui-content mt-1 mescroll" id="ajaxPage">
    <div class="bg-white p-3 mb-1">
        <div class="clearfix">
            <div style="{if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}width: calc(100% - 20px){else/}width: 100%{/if}" class="float-left mb-2">
                {if !empty($article_info['topics']) || ($user_id && ($user_info['group_id']==1 || $user_info['group_id']==2))}
                <div class="page-detail-topic swiper-container py-1">
                    {if !empty($article_info['topics'])}
                    <ul class="swiper-wrapper" id="awTopicList">
                        {volist name="article_info['topics']" id="v"}
                        <li class="swiper-slide"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                        {/volist}
                    </ul>
                    {/if}
                </div>
                {/if}
            </div>
            {if $user_id && get_user_permission('edit_content_topic')=='Y' && $user_id == $article_info.uid}
            <a href="javascript:;" class="aw-ajax-open d-block text-primary float-right py-1" data-url="{:url('topic/select',['item_type'=>'article','item_id'=>$article_info['id']])}"><i class="icon-edit1"></i></a>
            {/if}
        </div>
        
        <div class="border-bottom pb-2">
            <h2 class="mb-3 questionTitle title font-weight-bold font-11">
                <h2 class="font-12 font-weight-bold mb-2">{if $article_info.set_top}
                    <i class="iconfont icon-zhiding text-warning font-14"></i>{/if}{:htmlspecialchars_decode($article_info.title)}
                </h2>
            </h2>
            <div class="text-muted font-8">
                <span class="mr-2">{:L('评论')} {$article_info.comment_count}</span>.
                <span class="ml-2 mr-2">{:L('浏览')} {$article_info.view_count}</span>.
                <span class="ml-2">{:L('最新')} {:date_friendly($article_info.update_time)}</span>
            </div>
        </div>

        <div class="author-info pb-3 mt-3">
            <dl class="overflow-hidden">
                <dt class="float-left">
                    <a href="{$article_info['user_info']['url']}" data-pjax="pageMain" class="aw-username" data-id="{$article_info.uid}">
                        <img src="{$article_info['user_info']['avatar']|default='/static/common/image/default-avatar.svg'}" onerror="this.src='/static/common/image/default-avatar.svg'"  class="rounded" width="38" height="38">
                    </a>
                </dt>
                <dd class="float-left overflow-hidden" style="padding-left: 10px;{$user_id && $user_id!=$article_info['uid'] ? 'width:calc(100% - 115px)' : 'width:calc(100% - 55px)'}">
                    <a href="{$article_info['user_info']['url']}" class="d-block aw-one-line aw-username mb-1" data-id="{$article_info.uid}">
                        <strong>{$article_info['user_info']['name']}</strong> <span class="badge badge-success">{$article_info['user_info']['group_name']|default=''}</span>
                    </a>
                    <p class="mb-0 font-8 text-muted aw-one-line">{$article_info['user_info']['signature']|default=L('这家伙很懒，还没有设置简介')}</p>
                </dd>
                {if $user_id && $user_id!=$article_info['uid']}
                <dd class="float-right" style="width: 60px">
                    <a class="badge badge-primary {$article_info['user_focus'] ? 'active ygz' : ''} d-block p-2" onclick="AWS_MOBILE.User.focus(this,'user','{$article_info.uid}')" href="javascript:;">
                        {$article_info['user_focus'] ? L('已关注') : L('关注TA')}
                    </a>
<!--                    <a onclick="AWS_MOBILE.User.inbox('{$article_info['user_info']['nick_name']}')" class="badge badge-warning text-white mt-1 d-block" href="javascript:;">{:L('发私信')}</a>
-->                </dd>
                {/if}
            </dl>
        </div>
        {:hook('pageDetailTop',['info'=>$article_info])}
        <div class="aw-content position-relative">
            {$article_info.message|raw}
        </div>
        {:hook('pageDetailBottom',['info'=>$article_info])}
    </div>
    <div class="swiper-container bg-white">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
            <li class="nav-item swiper-slide" data-type="comments" >
                <a class="nav-link active" href="javascript:;">{:L('文章评论')}</a>
            </li>
            <li class="nav-item swiper-slide" data-type="relation">
                <a class="nav-link" href="javascript:;">{:L('相关文章')}</a>
            </li>
            {if $attach_list}
            <li class="nav-item swiper-slide" data-type="attach" >
                <a class="nav-link" href="javascript:;">{:L('文章附件')}</a>
            </li>
            {/if}
        </ul>
    </div>
    <div class="answer-container">
        <div class="mb-0 border-bottom clearfix pb-3 position-relative answerSort p-3 bg-white">
            <p class="float-left mr-5 font-weight-bold">
                <span class="aw-answer-count">{$article_info.comment_count}</span> {:L('评论')}
            </p>
            <div class="float-right">
                <a href="javascript:;" class="order-item" onclick="changeOrder(this)">
                    <i class="fa fa-fw fa-angle-down d-sm-inline-block"></i><span>{:L('默认排序')}</span>
                </a>
            </div>
        </div>
    </div>
    <div id="ajaxResult"></div>
    {if $attach_list}
    {if get_plugins_config('paid_attach','enable')=='Y'}
    {:hook('attachDetail',['info'=>$article_info,'page'=>'article','attach_list'=>$attach_list??[]])}
    {else/}
    <div class="aw-attach-list mt-1 p-3 bg-white" id="attach" style="display: none">
        {volist name="attach_list" id="v"}
        <dl class="clearfix mb-0 py-1">
            <dt class="float-left">
                <p class="mb-1">{$v.name}</p>
                <p class="text-muted font-8 mb-0">{:formatBytes($v.size)}</p>
            </dt>
            <dd class="float-right mb-0">
                <a href="javascript:;" class="btn btn-primary btn-sm attach-download" data-name="{$v.auth_key}">{:L('下载')}</a>
            </dd>
        </dl>
        {/volist}
    </div>
    {/if}
    {/if}
</div>
<footer class="aui-footer row-before px-3">
    <div class="footerCommentBox w-100">
        <div class="commentForm d-flex">
            <input {if !$user_id}onclick="AWS_MOBILE.User.login()" readonly="readonly"{/if} type="text" class="commentInput flex-fill {if $user_id}aw-ajax-open{/if}" data-title="文章评论" {if $user_id}data-url="{:url('comment/comment_editor',['id'=>$article_info.id,'at_uid'=>0])}"{/if}
            placeholder="{$user_id ? L('输入您的评论内容') : L('请登录后进行评论')}...">
            <div class="flex-fill ml-3" style="line-height: 55px">
                <a href="javascript:;" class="aw-ajax-agree text-muted {$article_info['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'article','{$article_info.id}');">
                    <i class="icon-thumb_up"></i> <span class="font-9 mt-1">{$article_info['agree_count']}</span>
                </a>
            </div>

            <div class="flex-fill ml-3" style="line-height: 55px">
                <a href="javascript:;" class="aw-ajax-against text-muted {$article_info['vote_value']==-1 ? 'active' : ''}" onclick="AWS_MOBILE.User.against(this,'article','{$article_info.id}');">
                    <i class="icon-thumb_down"></i>
                </a>
            </div>
        </div>
    </div>
</footer>
<script>
    var url = "{:url('comment/get_article_comments')}";
    var sort = 'new';
    var type = 'comments';
    var param = {sort:sort,id:'{$article_info.id}'};

    $('.aw-pjax-tabs .nav-item').click(function (){
        type = $(this).data('type');
        $('.aw-pjax-tabs .nav-item a').removeClass('active');
        $(this).find('a').addClass('active');
        $('#ajaxResult').empty();

        switch (type)
        {
            case 'relation':
                $('.answer-container').hide();
                url = "{:url('ajax/get_relation_posts')}";
                param = {type:'article',id:'{$article_info.id}'};
                $('#ajaxResult').show();
                $('#attach').hide();
                mescroll.resetUpScroll();
                break;
            case 'attach':
                $('#ajaxResult').hide();
                $('#attach').show();
                mescroll.destroy();
                mescroll.removeEmpty();
                break;
            default :
                $('.answer-container').show();
                url = "{:url('comment/get_article_comments')}";
                param = {sort:sort,id:'{$article_info.id}'};
                $('#ajaxResult').show();
                $('#attach').hide();
                mescroll.resetUpScroll();
                break;
        }
    })

    function changeOrder(obj){
        aui.actionMenu({
            title: '评论排序',
            mask: true,
            touchClose: true,
            items: [
                {name: '默认排序'},
                {name: '热门排序'},
            ],
            cancle: "取消",
            theme: 1,
            location: "middle"
        },function(ret){
            if(ret.index==1)
            {
                param = {sort:'new',id:'{$article_info.id}'};
                $(obj).find('span').html('默认排序');
            }else{
                param = {sort:'hot',id:'{$article_info.id}'};
                $(obj).find('span').html('热门排序');
            }
            $('#ajaxResult').empty();
            mescroll.resetUpScroll();
        })
    }

    var mescroll = new MeScroll('ajaxPage', {
        down: {
            callback: function (){
                mescroll.resetUpScroll();
            }
        },
        up: {
            callback: function (page) {
                var pageNum = page.num;
                $.ajax({
                    url: url+'?page='+pageNum,
                    type:"POST",
                    data:param,
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
{block name="footer"}{/block}