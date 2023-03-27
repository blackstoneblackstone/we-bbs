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
    <div class="aui-header-title">{:L('问题详情')}</div>
    <div class="aui-header-right" onclick="AWS_MOBILE.api.dialog('{:L(\'问题操作\')}',$('.questionAction').html())">
        <i class="fa fa-ellipsis-v" style="font-size: 0.9rem;"></i>
    </div>
</header>

<script type="text/html" class="questionAction">
    <div class="row text-center mt-3">
        {if $user_id}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.favorite(this,'question','{$question_info.id}')">
                <i class="fa fa-star font-14"></i>
                <span class="d-block font-9 mt-1">{if $checkFavorite} {:L('已收藏')}{else}{:L('收藏')}{/if}</span>
            </a>
        </div>
        {if $user_id!=$question_info['uid']}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted" {if !$checkReport}onclick="AWS_MOBILE.User.report(this,'question','{$question_info.id}');"{/if}>
                <i class="icon-warning font-14"></i>
                <span class="d-block font-9 mt-1">{if $checkReport}{:L('已举报')}{else/}{:L('举报')}{/if}</span>
            </a>
        </div>
        {/if}
        {if $question_info['uid']==$user_id || get_user_permission('modify_question')=='Y'}
        <div class="col-4 mb-4">
            <a href="{:url('question/publish?id='.$question_info['id'])}" class="text-muted">
                <i class="fa fa-edit font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('编辑问题')}</span>
            </a>
        </div>
        {/if}
        {if $question_info['uid']==$user_id || get_user_permission('remove_question')=='Y'}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="aw-ajax-get text-muted" data-confirm="{:L('确定要删除吗')}？" data-url="{:url('ajax.Question/remove_question',['id'=>$question_info['id']])}">
                <i class="fa fa-trash-alt font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('删除问题')}</span>
            </a>
        </div>
        {/if}
        {if get_user_permission('recommend_post')=='Y'}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="aw-ajax-get text-muted" data-url="{:url('ajax.Question/manager',['id'=>$question_info['id'],'type'=>$question_info['is_recommend'] ? 'un_recommend' : 'recommend'])}">
                <i class="fa fa-chevron-circle-up  font-14"></i><br>
                <span class="d-block font-9 mt-1">{$question_info['is_recommend'] ? L('取消推荐') : L('推荐问题')}</span>
            </a>
        </div>
        
        {/if}
        {if get_user_permission('set_top_post')=='Y'}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="aw-ajax-get text-muted" data-url="{:url('ajax.Question/manager',['id'=>$question_info['id'],'type'=> $question_info['set_top'] ? 'unset_top' : 'set_top'])}">
                <i class="far fa-hand-point-up  font-14"></i><br>
                <span class="d-block font-9 mt-1">{$question_info['set_top'] ? L('取消置顶') : L('置顶问题')}</span>
            </a>
        </div>
        {/if}
        {/if}
        <div class="col-4 mb-4">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.shareBox('{$question_info.title}','{:url('question/detail',['id'=>$question_info.id],true,true)}')">
                <i class="fa fa-send font-14"></i><br>
                <span class="d-block font-9 mt-1">{:L('分享')}</span>
            </a>
        </div>
    </div>
</script>

{/block}

{block name="main"}
<div class="aui-content mt-1 mescroll" id="ajaxPage">
    <div class="aw-question-container">
        <div class="bg-white p-3 mb-1">
            <div class="pb-2">
                <div class="clearfix">
                    {if !empty($question_info['topics']) || ($user_id && ($user_info['group_id']==1 || $user_info['group_id']==2))}
                    <div style="{if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2)}width: calc(100% - 20px){else/}width: 100%{/if}" class="float-left mb-2">
                        <div class="page-detail-topic swiper-container py-1">
                            {if !empty($question_info['topics'])}
                            <ul class="swiper-wrapper" id="awTopicList">
                                {volist name="question_info['topics']" id="v"}
                                <li class="swiper-slide"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                                {/volist}
                            </ul>
                            {/if}
                        </div>
                    </div>
                    {/if}
                    {if $user_id && get_user_permission('edit_content_topic')=='Y' && $user_id == $question_info.uid}
                    <a href="javascript:;" class="aw-ajax-open d-block text-primary float-right py-1" data-url="{:url('topic/select',['item_type'=>'question','item_id'=>$question_info['id']])}"><i class="icon-edit1"></i></a>
                    {/if}
                </div>

                <div class="border-bottom pb-2">
                    <h2 class="mb-3 questionTitle title font-weight-bold font-11">
                        {if $question_info.set_top}
                        <i class="iconfont icon-zhiding text-warning font-11"></i>
                        {/if}
                        {:htmlspecialchars_decode($question_info.title)}
                    </h2>
                    <div class="text-muted font-8">
                        <span class="mr-2">{:L('回答')} {$question_info.answer_count}</span>.
                        <span class="ml-2 mr-2">{:L('关注')} {$question_info.focus_count}</span>.
                        <span class="ml-2">{:L('更新')} {:date_friendly($question_info.update_time)}</span>
                    </div>
                </div>
            </div>

            {if !$question_info['is_anonymous']}
            <div class="author-info pb-3 mt-3">
                <dl class="overflow-hidden">
                    <dt class="float-left">
                        <a href="{$question_info['user_info']['url']}" data-pjax="pageMain" class="aw-username" data-id="{$question_info.uid}">
                            <img src="{$question_info['user_info']['avatar']|default='/static/common/image/default-avatar.svg'}" onerror="this.src='/static/common/image/default-avatar.svg'"  class="rounded" width="38" height="38">
                        </a>
                    </dt>
                    <dd class="float-left overflow-hidden" style="padding-left: 10px;{$user_id && $user_id!=$question_info['uid'] ? 'width:calc(100% - 115px)' : 'width:calc(100% - 55px)'}">
                        <a href="{$question_info['user_info']['url']}" class="d-block aw-one-line aw-username mb-1" data-id="{$question_info.uid}" target="_blank">
                            <strong>{$question_info['user_info']['name']}</strong> <span class="badge badge-success">{$question_info['user_info']['group_name']|default=''}</span>
                        </a>
                        <p class="mb-0 font-8 text-muted aw-one-line">{$question_info['user_info']['signature']|default=L("这家伙还没有留下自我介绍～")}</p>
                    </dd>
                    {if $user_id && $user_id!=$question_info['uid']}
                    <dd class="float-right" style="width: 60px">
                        <a class="badge badge-primary {$question_info['user_focus'] ? 'active ygz' : 'gz'} d-block p-2" onclick="AWS_MOBILE.User.focus(this,'user','{$question_info.uid}')" href="javascript:;">{:L($question_info['user_focus'] ? '已关注' : '关注TA')}</a>
<!--                        <a onclick="AWS_MOBILE.User.inbox('{$question_info['user_info']['nick_name']}')" class="badge badge-warning text-white mt-1 d-block" href="javascript:;">{:L('发私信')}</a>
-->                    </dd>
                    {/if}
                </dl>
            </div>
            {/if}

            {:hook('pageDetailTop',['info'=>$question_info])}

            <div class="aw-content position-relative" id="question-content">
                <div id="show-all" >{$question_info.detail|raw}</div>
                {if $question_info.detail}
                <div class="aw-question-show aw-alpha-hidden" style="display: none">
                    <span style="cursor: pointer;" class="py-2"><i class="icon-chevrons-down"></i> {:L('阅读全文')}</span>
                </div>
                <div class="aw-question-hide aw-alpha-hidden" style="display: none;background:none;position: inherit;height: auto">
                    <span style="position: unset;float: left;cursor: pointer" class="py-2"><i class="icon-chevrons-up"></i> {:L('收起全文')}</span>
                </div>
                {/if}
            </div>

            {:hook('pageDetailBottom',['info'=>$question_info])}
        </div>
        <div class="swiper-container bg-white">
            <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-2 bg-white swiper-wrapper" style="flex-wrap: nowrap;">
                <li class="nav-item swiper-slide" data-type="answers">
                    <a class="nav-link active" href="javascript:;">{:L('回答列表')}</a>
                </li>
                <li class="nav-item swiper-slide" data-type="relation">
                    <a class="nav-link" href="javascript:;">{:L('相关问题')}</a>
                </li>
                {if $attach_list}
                <li class="nav-item swiper-slide" data-type="attach" >
                    <a class="nav-link" href="javascript:;">{:L('问题附件')}</a>
                </li>
                {/if}
            </ul>
        </div>
        <div class="answer-container">
            <div class="mb-0 border-bottom clearfix pb-3 position-relative answerSort p-3 bg-white">
                <p class="float-left mr-5 font-weight-bold">
                    <span class="aw-answer-count">{$question_info.answer_count}</span> {:L('回答')}
                </p>
                <div class="float-right">
                    <a href="javascript:;" class="order-item" onclick="changeOrder(this)">
                        <i class="fa fa-fw fa-angle-down d-sm-inline-block"></i><span>{$sort_texts[$sort]}</span>
                    </a>
                </div>
            </div>
        </div>
        <div id="ajaxResult" class="aw-answer-body aw-answer-list"></div>
        {if $attach_list}
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
    </div>
</div>
<script>
    let questionId = parseInt("{$question_info.id}");
    var answerId = parseInt("{$answer_id ? $answer_id : 0}");
    var showAll = $('#show-all');
    var url = "{:url('question/answers')}";
    var sort = 'new';
    var type = 'answer';
    var param = {sort:sort,question_id:questionId};
    $(document).ready(function ()
    {
        if(showAll.height() >= 120)
        {
            showAll.show().css('height','120px');
            $('.aw-question-show').show();
        }

        $(document).on('click', '.aw-question-show', function (e) {
            $('.aw-question-show').hide();
            showAll.show().css('height','auto');
            $('.aw-question-hide').show();
        });

        $(document).on('click', '.aw-question-hide', function (e) {
            $('.aw-question-hide').hide();
            showAll.show().css('height','120px');
            $('.aw-question-show').show();
        });
    });

    function changeOrder(obj){
        aui.actionMenu({
            title: '回答排序',
            mask: true,
            touchClose: true,
            items: [
                {name: '最新排序'},
                {name: '热门排序'},
                {name: '只看楼主'},
                {name: '关注的人'},
            ],
            cancle: "取消",
            theme: 1,
            location: "middle"
        },function(ret){
            if(ret.index==2)
            {
                param = {sort:'hot',question_id:questionId}
                $(obj).find('span').html('热门排序');
            }else if(ret.index==3){
                param = {sort:'publish',question_id:questionId}
                $(obj).find('span').html('只看楼主');
            }else if(ret.index==4){
                param = {sort:'focus',question_id:questionId}
                $(obj).find('span').html('关注的人');
            }else{
                param = {sort:'new',question_id:questionId}
                $(obj).find('span').html('默认排序');
            }
            $('#ajaxResult').empty();
            mescroll.resetUpScroll();
        })
    }

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
                param = {type:'question',id:questionId};
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
                url = "{:url('question/answers')}";
                param = {sort:sort,question_id:questionId};
                $('#ajaxResult').show();
                $('#attach').hide();
                mescroll.resetUpScroll();
                break;
        }
    })

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

                        if(type=='answer')
                        {
                            $('.aw-answer-item .aw-answer-content').each(function(){
                                if($(this).height() >= 200)
                                {
                                    $(this).css('height','200px');
                                    $(this).parents('.aw-answer-item').find('.aw-answer-show').show();
                                }
                            });
                            $(document).on('click', '.aw-answer-show', function (e) {
                                $(this).hide();
                                $(this).parents('.aw-answer-item').find('.aw-answer-content').show().css('height','auto');
                                $(this).parents('.aw-answer-item').find('.aw-answer-hide').show();
                            });
                            $(document).on('click', '.aw-answer-hide', function (e) {
                                $(this).hide();
                                $(this).parents('.aw-answer-item').find('.aw-answer-content').show().css('height','200px');
                                $(this).parents('.aw-answer-item').find('.aw-answer-show').show();
                            });
                        }
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

{block name="footer"}
<footer class="aw-footer border-top">
    <div class="py-1 d-flex text-center justify-content-center" style="height: 100%">
        <a href="javascript:;" class="flex-fill pt-1 {$question_info['vote_value']==1 ? 'active' : ''}" onclick="AWS_MOBILE.User.agree(this,'question','{$question_info.id}');" title="{:L('这是个好问题')}">
            <dl class="text-center">
                <dt><i class="fa fa-thumbs-up"></i></dt>
                <dd class="text-muted font-8">{:L('好问题')}</dd>
            </dl>
        </a>
        <a class="d-inline-block flex-fill pt-1" onclick="AWS_MOBILE.User.invite(this,'{$question_info.id}')">
            <dl class="text-center">
                <dt><i class="fa fa-user-plus"></i></dt>
                <dd class="text-muted font-8">{:L('邀请回答')}</dd>
            </dl>
        </a>

        <a onclick="AWS_MOBILE.User.focus(this,'question','{$question_info.id}')" class="pt-1 d-inline-block flex-fill {if $question_info['has_focus']}active{/if}">
            <dl class="text-center">
                <dt><i class="fa fa-heart"></i></dt>
                <dd class="text-muted font-8">{$question_info['has_focus'] ? L('已关注') : L('关注问题')}</dd>
            </dl>
        </a>

        <a href="javascript:;" data-title="回答问题" data-url="{:url('ajax/editor',['question_id'=>$question_info['id'],'answer_id'=>0])}" class="aw-ajax-open d-inline-block pt-1 flex-fill">
            <dl class="text-center">
                <dt><i class="fa fa-edit"></i></dt>
                <dd class="text-muted font-8">{:L('写回答')}</dd>
            </dl>
        </a>
    </div>
</footer>
{/block}