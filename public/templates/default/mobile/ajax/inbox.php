{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}

{if $user_name}
<style>
    .mescroll-upwarp{display: none !important;}
</style>
<div id="inboxPage" class="mescroll ">
    <div class="mb-1 aw-overflow-auto aui-chat" id="inboxDialogContainer" style="height: 300px;background: #F4F4F4;"></div>
</div>
{/if}
<div class="bg-white">
    <form method="post" action="{:url('inbox/send')}" class="pb-3 pt-2">
        {if $user_name}
        <input type="hidden" name="recipient_uid" value="{$user_name}">
        {else/}
        <div class="form-group position-relative">
            <input type="text" class="form-control" id="searchUser" name="recipient_uid" value="" placeholder="{:L('搜索您想要发送私信的用户')}...">
            <div class="aw-dropdown mt-2 border px-3" style="display: none">
                <div class="aw-dropdown-list aw-common-list aw-overflow-auto text-left"></div>
            </div>
        </div>
        {/if}
        <div class="form-group overflow-hidden">
            <textarea type="text" name="message" rows="4" class="form-control float-left" placeholder="{:L('私信内容')}"></textarea>
        </div>
        <div class="overflow-hidden">
            <button class="btn btn-primary px-3 btn-sm d-block aw-ajax-submit float-right" type="button">{:L('发送私信')}</button>
        </div>
    </form>
</div>

<script>
    {if $user_name}
    var url = "{:url('inbox/page')}";
    var inboxScroll = new MeScroll('inboxPage', {
        down: {
            callback: function (){
                inboxScroll.resetUpScroll();
            }
        },
        up: {
            callback: function (page) {
                var pageNum = page.num;
                $.ajax({
                    url: url+'?page='+pageNum,
                    type:"POST",
                    data:{receiver:"{$user_name}"},
                    success: function(result) {
                        var curPageData = result.data.list;
                        var totalPage = result.data.total;
                        inboxScroll.endByPage(curPageData.length, totalPage)
                        if(pageNum == 1){
                            $('#inboxDialogContainer').empty();
                        }
                        $('#inboxDialogContainer').append(result.data.html);
                    },
                    error: function(e) {
                        //联网失败的回调,隐藏下拉刷新和上拉加载的状态
                        inboxScroll.endErr();
                    }
                });
            },
            page: {
                num: 0, //当前页 默认0,回调之前会加1; 即callback(page)会从1开始
                size: 10 //每页数据条数,默认10
            },
            htmlNodata: '<p class="nodata">-- 暂无更多私信 --</p>',
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
    {/if}

    $(document).on('click', '.aw-ajax-submit', function (e) {
        var that = this;
        var form = $($(that).parents('form')[0]);
        $.ajax({
            url: form.attr('action'),
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
                if (result.code > 0) {
                    $('#inboxDialogContainer').empty();
                    $('textarea[name=message]').val('');
                    {if $user_name}
                    inboxScroll.resetUpScroll();
                    {/if}
                } else {
                    AWS_MOBILE.api.success(result.msg);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    layer.closeAll();
                    AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                }
            }
        });
    });
</script>
{/block}
