{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"><a href="javascript:;" onclick="window.history.back()"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title aw-one-line">{:L('通知私信')}</div>
    <a class="aui-header-right"><i class="far fa-sun text-muted" style="font-size: 1rem"></i></a>
</header>
{/block}

{block name="main"}
<div class="aui-content mt-1">
    <div class="aui-card mb-1">
        <div class="aui-card-main" style="padding: 0;">
            <div class="aui-lists">
                <div class="aui-list">
                    <a href="{:url('setting/profile')}" data-pjax="pageMain">
                        <div class="aui-list-left">
                            <h3>{:L('您有新的未读私信')}</h3>
                            <div class="newInbox mt-2">
                                <dl >
                                    <dt>
                                        <img src="{$user_info.avatar}" style="border-radius: 50%;height: 40px" onerror="this.src='/static/common/image/default-avatar.svg'" width="40" height="40">
                                    </dt>
                                </dl>
                            </div>
                        </div>
                        <div class="aui-list-right">
                            <i class="iconfont aui-btn-right iconright1"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="swiper-container">
        <div class="swiper-wrapper">
            {volist name="$notify_group" id="v"}
            <dl class="swiper-slide text-center bg-white mr-1 p-3 rounded">
                <a data-pjax="wrapMain" href="{:url('notify/index',['type'=>$key])}">
                    <dt class="mb-2 font-15 bg-warning text-white" style="border-radius: 50%;width: 56px;height: 56px;line-height: 56px"><i class="fa fa-bell"></i></dt>
                    <dd>{$v}</dd>
                </a>
            </dl>
            {/volist}
        </div>
    </div>
</div>

{/block}

{block name="sideMenu"}{/block}
{block name="footer"}{/block}