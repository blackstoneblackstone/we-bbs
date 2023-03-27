{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-md-10"  id="wrapMain">
                {include file="setting/nav"}
                <div class="bg-white mt-1 p-3" id="tabMain">
                    {if isset($config) && $config}
                    <div class="d-flex text-center">
                        {if in_array('wechat',$config['enable'])}
                        <dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="fab fa-wechat text-success" style="font-size: 3rem"></i></dt>
                            <dd class="text-muted mt-1">{:L('微信')}</dd>
                            <dd class="mt-3">
                                {if in_array('wechat',$third)}
                                <a class="btn btn-danger btn-sm text-white" target="_blank" href="{:url('ThirdAuth/unbind',['platform'=>'wechat'])}">{:L('解绑账号')}</a>
                                {else/}
                                <a data-url="{:url('ThirdAuth/qrcode')}" href="javascript:;" data-width="100" class="btn btn-primary btn-sm text-white aw-ajax-open">{:L('绑定账号')}</a>
                                {/if}
                            </dd>
                        </dl>
                        {/if}

                        {if in_array('qq',$config['enable'])}
                        <dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="fab fa-qq text-primary" style="font-size:3rem"></i></dt>
                            <dd class="text-muted mt-1">QQ</dd>
                            <dd class="mt-3">
                                <a class="btn {:in_array('qq',$third) ? 'btn-danger' : 'btn-primary'} btn-sm text-white" target="_blank" href="{:in_array('qq',$third) ? url('ThirdAuth/unbind',['platform'=>'qq']) : url('ThirdAuth/bind',['platform'=>'qq'])}">{if(in_array('qq',$third))}{:L('解绑账号')}{else/}{:L('绑定账号')}{/if}</a>
                            </dd>
                        </dl>
                        {/if}

                        {if in_array('weibo',$config['enable'])}
                        <dl class="flex-fill mb-0 col-sm-12">
                            <dt><i class="fab fa-weibo text-warning" style="font-size: 3rem"></i></dt>
                            <dd class="text-muted mt-1">{:L('微博')}</dd>
                            <dd class="mt-3">
                                <a class="btn {:in_array('weibo',$third) ? 'btn-danger' : 'btn-primary'} btn-sm text-white" target="_blank" href="{:in_array('weibo',$third) ? url('ThirdAuth/unbind',['platform'=>'weibo']) : url('ThirdAuth/bind',['platform'=>'weibo'])}">{if(in_array('weibo',$third))}{:L('解绑账号')}{else/}{:L('绑定账号')}{/if}</a>
                            </dd>
                        </dl>
                        {/if}
                    </div>
                    {else/}
                    <p class="text-center py-3 text-muted">
                        <img src="{$cdnUrl}/static/common/image/empty.svg">
                        <span class="d-block  ">{:L('本站暂未开启第三方登录')}</span>
                    </p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}