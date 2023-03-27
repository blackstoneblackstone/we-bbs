{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>$user_id])}
            <div class="col-md-10" id="wrapMain">
                <div class="bg-white">
                    <div class="aw-nav-container mb-2">
                        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3">
                            <li class="nav-item"><a class="nav-link active" data-pjax="tabMain" href="javascript:;">{:L('浏览记录')}</a></li>
                        </ul>
                    </div>
                    <div id="tabMain">
                        <div class="aw-common-list">
                            {if !empty($list)}
                            {foreach $list as $key=>$v}
                            <div class="record-item">
                                <dl>
                                    <dt><a href="{$v.url}" target="_blank"><span class="badge badge-primary">{$v.label}</span> {$v.title}</a></dt>
                                    <dd class="aw-content aw-two-line">{$v.content|raw}</dd>
                                    <dd class="mb-0"><label><i class="iconfont">&#xe68a;</i>{:date_friendly($v['create_time'])}</label></dd>
                                </dl>
                            </div>
                            {/foreach}
                            {$page|raw}
                            {else/}
                            <p class="text-center py-3 text-muted">
                                <img src="{$cdnUrl}/static/common/image/empty.svg">
                                <span class="d-block">{:L('暂无内容')}</span>
                            </p>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}