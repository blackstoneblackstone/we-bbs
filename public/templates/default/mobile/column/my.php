{extend name="$theme_block"
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left">
        <a href="{:url('creator/index')}" data-pjax="pageMain"><i class="fa fa-angle-left "></i></a>
    </div>
    <div class="aui-header-title aw-one-line">{:L('我的专栏')}</div>
</header>
{/block}
{block name="main"}
<div class="aui-content mescroll" id="ajaxPage">
    <div class="d-flex text-center bg-white p-3 text-muted">
        <dl class="flex-fill mb-0">
            <dt>{$user_info['answer_count']}</dt>
            <dd>{:L('回答')}</dd>
        </dl>
        <dl class="flex-fill mb-0">
            <dt>{$user_info['article_count']}</dt>
            <dd>{:L('文章')}</dd>
        </dl>
        <dl class="flex-fill mb-0">
            <dt>{$user_info['question_count']}</dt>
            <dd>{:L('问题')}</dd>
        </dl>
    </div>
    <div class="d-flex bg-white border-top p-3">
        <a href="{:url('column/apply')}" class="aui-btn aui-btn-round" style="color:#fff">{:L('申请专栏')}</a>
    </div>
    <div class="bg-white p-3">
        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
            <li class="nav-item"><a class="nav-link {if $verify==1}active{/if}" data-pjax="pageMain" href="{:url('column/my',['verify'=>1])}">{:L('已审核')}</a></li>
            <li class="nav-item"><a class="nav-link {if $verify==0}active{/if}" data-pjax="pageMain" href="{:url('column/my',['verify'=>0])}">{:L('待审核')}</a></li>
            <li class="nav-item"><a class="nav-link {if $verify==2}active{/if}" data-pjax="pageMain" href="{:url('column/my',['verify'=>2])}">{:L('已拒绝')}</a></li>
        </ul>
    </div>

    <div class="aw-common-list" id="ajaxResult"></div>
</div>

<script>
    var verify = parseInt('{$verify}')
    var MeScroll = AWS_MOBILE.api.MeScroll('ajaxPage','#ajaxResult',"{:url('ajax.column/columns')}",{verify: verify}, 10);
</script>
{/block}
