{extend name="$theme_block" /}
{block name="header"}
<div class="w-top-img" style="height:300px;background: url('{$static_url}images/help.png') center center;background-size:cover;">
    <div style="background: rgba(0,0,0,.25);height:300px;">
    {include file="global/nav"}
    </div>
</div>
{/block}
{block name="main"}
<div class="container" style="margin-top: -100px">
    <div class="bg-white p-3" style="border-radius: 10px">
        <div class="text-center py-3">
            <h2 class="col-12 font-weight-bolder">{:L('快速找到解决方案，轻松解决问题')}</h2>
            <p class="mb-3 w-100 text-muted mt-3">3 {:L('分钟快速创建你的知识社区')}</p>
        </div>
        <div class="col-12 pb-3">
            <form action="{:url('search/index')}" method="get" id="homeSearch">
                <div class="w-100 px-3 py-1" style="background: #eee;border-radius: 50px">
                    <i class="iconfont">&#xe610;</i>
                    <input type="text" autocomplete="off" style="width: 96%;height: 32px;line-height: 32px;" value="{:input('get.q')}"  name="q" placeholder="{:L('请输入你遇到的问题进行搜索')}">
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container mt-2 aw-wrap" id="tabMain">
    {if $list}
    <div class="row">
        {foreach $list as $k=>$v}
        <div class="col-md-3">
            <div class="bg-white p-3" style="border-radius: 10px">
                <div class="d-flex pb-2">
                    {if $v.image}
                    <div class="flex-fill mr-2" style="background-image:url({$v.image});background-size:cover;width: 1.5rem;max-width:1.5rem;height: 1.5rem;border-radius: 5px;"></div>
                    {/if}
                    <h4 class="aw-one-line font-weight-bolder font-13"><a href="{:url('help/detail',['token'=>$v.url_token])}" data-pjax="wrapMain">{$v.title}</a></h4>
                </div>
                {if isset($v.chapters) && $v.chapters}
                <ul class="pb-2">
                    {foreach $v['chapters'] as $k1=>$v1}
                    <li class="py-1 aw-one-line"><a href="{:url($v1['item_type'].'/detail',['id'=>$v1['item_id']])}" target="_blank">{$v1.info.title}</a></li>
                    {/foreach}
                </ul>
                {/if}
                <div class="text-center border-top pt-3">
                    <a href="{:url('help/detail',['token'=>$v.url_token])}" data-pjax="wrapMain" class="text-primary">{:L('查看全部')}</a>
                </div>
            </div>
        </div>
        {/foreach}
    </div>
    {$page|raw}
    {else/}
    <p class="text-center py-3 text-muted">
        <img src="{$cdnUrl}/static/common/image/empty.svg">
        <span class="d-block">{:L('暂无内容')}</span>
    </p>

    {/if}
</div>
{/block}
