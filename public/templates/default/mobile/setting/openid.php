{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('账号绑定')}</div>
</header>
{/block}

{block name="main"}
<div class="p-3 mt-1 bg-white" id="tabMain">
    {if get_plugins_config('third','enable')}
    <div class="d-flex text-center">
        {if in_array('wechat',$config['enable'])}
        <dl class="flex-fill mb-0 col-sm-12">
            <dt><i class="fab fa-wechat text-success" style="font-size: 3rem"></i></dt>
            <dd class="text-muted mt-1">{:L('微信')}</dd>
            <dd class="mt-3">
                {if in_array('wechat',$third)}
                <a class="btn btn-danger btn-sm text-white" target="_blank" href="{:url('ThirdAuth/unbind',['platform'=>'wechat'])}">{:L('解绑账号')}</a>
                {else/}
                <a href="{:url('ThirdAuth/bind',['platform'=>'wechat'])}" class="btn btn-primary btn-sm text-white">{:L('绑定账号')}</a>
                {/if}
            </dd>
        </dl>
        {/if}

        {if in_array('qq',$config['enable'])}
        <dl class="flex-fill mb-0 col-sm-12">
            <dt><i class="fab fa-qq text-primary" style="font-size:3rem"></i></dt>
            <dd class="text-muted mt-1">QQ</dd>
            <dd class="mt-3">
                <a class="btn {:in_array('qq',$third) ? 'btn-danger' : 'btn-primary'} btn-sm text-white" target="_blank" href="{:in_array('qq',$third) ? url('ThirdAuth/unbind',['platform'=>'qq']) : url('ThirdAuth/bind',['platform'=>'qq'])}">
                    {:L(in_array('qq', $third) ? '解绑账号' : '绑定账号')}
                </a>
            </dd>
        </dl>
        {/if}

        {if in_array('weibo',$config['enable'])}
        <dl class="flex-fill mb-0 col-sm-12">
            <dt><i class="fab fa-weibo text-warning" style="font-size: 3rem"></i></dt>
            <dd class="text-muted mt-1">{:L('账号设置')}微博</dd>
            <dd class="mt-3">
                <a class="btn {:in_array('weibo',$third) ? 'btn-danger' : 'btn-primary'} btn-sm text-white" target="_blank" href="{:in_array('weibo',$third) ? url('ThirdAuth/unbind',['platform'=>'weibo']) : url('ThirdAuth/bind',['platform'=>'weibo'])}">
                    {:L(in_array('weibo', $third) ? '解绑账号' : '绑定账号')}
                </a>
            </dd>
        </dl>
        {/if}
    </div>
    {else/}
    <p class="text-center text-muted">
        <img src="{$cdnUrl}/static/common/image/empty.svg">
        <span class="d-block">{:L('本站暂未开启第三方登录')}</span>
    </p>
    {/if}
</div>
{/block}
{block name="sideMenu"}{/block}
{block name="footer"}{/block}