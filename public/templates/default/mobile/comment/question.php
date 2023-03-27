{extend name="$theme_block" /}
{block name="main"}
<div class="mescroll" id="ajaxComment" style="height: 70vh;margin-bottom: 40px">
    <div id="ajaxResultComment"></div>
</div>
<footer class="aw-footer border-top">
    <div class="questionAction px-3 py-1" style="height: 100%">
        <div class="headerSearch">
            <form class="searchForm clearfix" id="AnswerCommentForm">
                <input type="hidden" name="answer_id" value="{$id}">
                <input type="text" {if !$user_id}onclick="AWS_MOBILE.User.login()" readonly="readonly" {/if} class="searchInput" name="message" style="width: calc(100% - 40px)"
                placeholder="{$user_id ? L('输入您的评论内容') : L('请先登录')}...">
                <a href="javascript:;" {if !$user_id}onclick="AWS_MOBILE.User.login()" {/if} class="text-primary {if $user_id}saveAnswerComment{/if} float-right" style="height: 32px;line-height: 32px">
                {:L('提交')}
                </a>
            </form>
        </div>
    </div>
</footer>

<script>

    $(document).on('click', '.saveQuestionComment', function (e) {
        var form = $('#QuestionCommentForm');
        $.ajax({
            url: "{:url('comment/save_question_comment')}",
            dataType: 'json',
            type: 'post',
            data: form.serialize(),
            success: function (result) {
                if(result.code)
                {
                    mescroll.resetUpScroll();
                    AWS_MOBILE.api.closeOpen();
                    $('.searchInput').val('');
                }
                AWS_MOBILE.api.success(result.msg);
            }
        });
    });

    var mescroll = new MeScroll("ajaxComment", {
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
            url: baseUrl+'/comment/get_question_comments?id={$id}&page=' + pageNum,
            type:"POST",
            success: function(result) {
                var curPageData = result.data.list || result.data.data;
                var totalPage = result.data.total;
                mescroll.endByPage(curPageData.length, totalPage)
                if(pageNum == 1){
                    $('#ajaxResultComment').empty();
                }
                $('#ajaxResultComment').append(result.data.html);
            },
            error: function(e) {
                mescroll.endErr();
            }
        });
    }
</script>
{/block}