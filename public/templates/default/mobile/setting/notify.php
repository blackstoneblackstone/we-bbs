{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('消息通知')}</div>
    <div class="aui-header-right text-primary font-11 text-left font-weight-bold" onclick="AWS_MOBILE.api.ajaxForm('#notifyForm')">{:L('保存')}</div>
</header>
{/block}

{block name="main"}
<div class="main-container">
    <form method="post" action="{:url('setting/notify')}" id="notifyForm">
        <div class="aw-mod bg-white p-3">
            <div class="aw-mod-head mb-0">
                <p class="mod-head-title font-12 ">{:L('私信设置')}</p>
            </div>
            <div class="aw-mod-body">
                <dl>
                    <dt class="text-muted my-2 font-9">{:L('谁可以给我发私信')}:</dt>
                    <dd class="row px-0 font-9">
                        <label class="col-4"><input type="radio" value="all" name="inbox_setting" {if isset($user_setting['inbox_setting']) && $user_setting['inbox_setting']=='all'} checked="checked" {/if}> {:L('所有人')}</label>
                        <label class="col-4"><input type="radio" value="focus" name="inbox_setting" {if isset($user_setting['inbox_setting']) && $user_setting['inbox_setting']=='focus'} checked="checked" {/if}> {:L('我关注的人')}</label>
                    </dd>
                </dl>
            </div>
        </div>
        {foreach $notify_setting as $k=>$v}
        <div class="aw-mod bg-white p-3 mt-1">
            <div class="aw-mod-head mb-0">
                <p class="mod-head-title font-12">{$types[$k]}</p>
            </div>
            <div class="aw-mod-body">
                <dl>
                    <dt class="text-muted my-2 font-9">{:L('哪些情况可以给我发')}$types[$k]:</dt>
                    <dd class="row px-0 font-9">
                        {foreach $v['config'] as $k1=>$v1}
                        {if $v1.user_setting}
                        <label class="col-6 mt-2">
                            <input name="notify_setting[{$k}][]" type="checkbox" value="{$v1.name}" {if isset($user_setting['notify_setting'][$k]) && in_array($v1.name,$user_setting['notify_setting'][$k])} checked="checked" {/if}> {$v1.title}
                        </label>
                        {/if}
                        {/foreach}
                    </dd>
                </dl>
            </div>
        </div>
        {/foreach}
    </form>
</div>
{/block}
{block name="footer"}{/block}