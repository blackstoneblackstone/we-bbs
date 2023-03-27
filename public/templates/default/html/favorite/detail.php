{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>$user_id])}
            <div class="col-10" id="wrapMain">
                <div class="bg-white p-3">
                    <div class="d-flex">
                        <div class="flex-fill">
                            <h3 class="mb-2 font-11">{$title}</h3>
                            <!--<a onclick="AWS.User.focus(this,'favorite','{$id}')" class="text-success {if $focus}active ygz{/if}">{if $focus}已关注{else}+关注{/if}</a>-->
                        </div>
                        <div class="flex-fill">
                            <div class="d-flex text-center">
                                <dl class="flex-fill mb-0">
                                    <dt>{:L('内容数')}</dt>
                                    <dd class="mb-0">{$post_count|num2string}</dd>
                                </dl>
                                <dl class="flex-fill mb-0">
                                    <dt>{:L('评论数')}</dt>
                                    <dd class="mb-0">{$comment_count|num2string}</dd>
                                </dl>
                                <dl class="flex-fill mb-0">
                                    <dt>{:L('关注数')}</dt>
                                    <dd class="mb-0">{$focus_count|num2string}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-1 bg-white">
                    {if $list}
                    {:widget('member/parse',['list'=>$list,'page'=>$page])}
                    {else/}
                    <p class="aw-text-center mt-4text-meta">
                        <img src="{$cdnUrl}/static/common/image/empty.svg">
                        <span class="mt-3 d-block ">{:L('暂无收藏记录')}</span>
                    </p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}