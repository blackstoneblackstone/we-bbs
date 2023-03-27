{extend name="$theme_block" /}
{block name="header"}
<div class="userBg pb-2" style="background: #835bc3;">
    <header class="aui-header" style="background: none">
        <div class="aui-header-title" style="left: 1rem;">
            <div class="headerSearch">
                <a class="searchForm " href="{:url('search/index')}" data-pjax="pageMain">
                    <input type="text" class="searchInput bg-white" placeholder="{:L('输入您想搜索的内容')}...">
                    <label><i class="iconfont iconsearch1"></i></label>
                </a>
            </div>
        </div>
        <div class="aui-header-right">
            <a href="{:url('setting/index')}" data-pjax="pageMain"><i class="far fa-sun text-light font-12"></i></a>
        </div>
    </header>

    <div class="px-3 pt-3 mt-1 mx-2 bg-white text-muted rounded position-relative">
        <div class="d-flex" style="margin-top: 2rem">
            <div class="flex-fill" style="max-width: 60px">
                <img src="{$user_info.avatar}" style="border-radius: 50%" onerror="this.src='/static/common/image/default-avatar.svg'" width="60" height="60">
            </div>
            <div class="flex-fill ml-2">
                <h3 class="font-11 mb-2 pt-1 font-weight-bold">{$user_info.name} <span class="badge badge-success font-9">{$user_info.group_name}</span></h3>
                <p class="aw-one-line font--9">{$user_info.signature|default=L('这家伙还没有留下自我介绍～')}</p>
            </div>
        </div>
        <a href="{:url('people/index',['name'=>$user_info.url_token])}" class="font-9 position-absolute" data-pjax="pageMain" style="right: 0.5rem;top:1rem">{:L('个人主页')} <i class="iconfont iconright1 font-9"></i></a>
        <div class="position-absolute text-muted" style="top:1rem;left: 1rem">
            <a class="mr-4 position-relative text-muted" href="{:url('notify/index')}" data-pjax="pageMain">
                <i class="fa fa-bell" style="font-size: 1rem"></i>
                {if $user_info['notify_unread']>0}
                <span class="aui-badge aui-badge-small position-absolute " style="top: -6px;left: 6px;">{:intval($user_info['notify_unread'])}</span>
                {/if}
            </a>

            <a href="{:url('inbox/index')}" class="position-relative text-muted " data-pjax="pageMain">
                <i class="fa fa-comments" style="font-size: 1rem"></i>
                {if $user_info['inbox_unread']>0}
                <span class="aui-badge aui-badge-small position-absolute " style="top: -6px;left: 6px;">{:intval($user_info['inbox_unread'])}</span>
                {/if}
            </a>
        </div>

        <div class="bg-white d-flex py-3 text-center text-muted rounded">
            <div class="flex-fill">
                <span class="d-block font-12">{$user_info.reputation}</span>
                <span class="d-block font-9 mt-1 text-muted">{$setting.power_unit}</span>
            </div>
            <div class="flex-fill">
                <a href="{:url('integral/index')}" data-pjax="pageMain">
                    <span class="d-block text-muted font-12">{:num2string($user_info['integral'])}</span>
                    <span class="d-block font-9 mt-1 text-muted">{$setting.score_unit}</span>
                </a>
            </div>
            <div class="flex-fill">
                <a href="{:url('focus/index',['type'=>'fans'])}" data-pjax="pageMain">
                    <span class="d-block text-muted font-12">{$user_info.fans_count}</span>
                    <span class="d-block font-9 mt-1 text-muted">{:L('粉丝')}</span>
                </a>
            </div>
            <div class="flex-fill">
                <span class="d-block text-muted font-12">{$user_info.agree_count}</span>
                <span class="d-block font-9 mt-1 text-muted">{:L('获赞')}</span>
            </div>
        </div>
    </div>
</div>
{/block}
{block name="main"}
<div class="bg-white mx-2 rounded mt-2">
    <div class="aui-grids">
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('manage/dynamic')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-airudiantubiaohuizhi-zhuanqu_zixundongtai" style="color: #05c091;"></i></div>
                <div class="aui-grid-text">{:L('我的动态')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('manage/question')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-tubiaozhuanqu-09" style="color: #fe2f24;"></i></div>
                <div class="aui-grid-text">{:L('我的提问')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('manage/article')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-24" style="color: #db639b;"></i></div>
                <div class="aui-grid-text">{:L('我的文章')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('manage/answer')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-huida" style="color: #fe6503;"></i></div>
                <div class="aui-grid-text">{:L('我的回答')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('focus/index')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-yiguanzhu" style="color: #197DE0;"></i></div>
                <div class="aui-grid-text">{:L('我的关注')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('favorite/index')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-shoucang" style="color: #fec002;"></i></div>
                <div class="aui-grid-text">{:L('我的收藏')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('draft/index')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-caogaoxiang" style="color: #EE0000;"></i></div>
                <div class="aui-grid-text">{:L('我的草稿')}</div>
            </a>
        </div>
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('column/my')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-shequ" style="color: rgba(148,83,174,0.88);"></i></div>
                <div class="aui-grid-text">{:L('我的专栏')}</div>
            </a>
        </div>
        {if ($setting['register_type'] != 'close')}
        <div class="aui-grid aui-col-xs-3">
            <a href="{:url('invitation/index')}" data-pjax="pageMain">
                <div class="aui-grid-icon"><i class="iconfont icon-yaoqingyouli" style="color: rgba(20,185,200,0.88);"></i></div>
                <div class="aui-grid-text">{:L('我的邀请')}</div>
            </a>
        </div>
        {/if}

        <!--用户菜单拓展钩子-->
        {:hook('userCenterNav')}
    </div>

    {:hook('userCenterIndex')}
</div>
{/block}
{block name="sideMenu"}{/block}