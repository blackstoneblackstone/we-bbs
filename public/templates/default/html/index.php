{extend name="$theme_block" /}
{block name="header"}
{if get_theme_setting('home.search_enable')=='Y'}
<div class="w-top-img" style="background: url('{$theme_config['home']['search_bg']|default=$static_url.'images/top-img.png'}') center center; background-size:auto 100%;">
    {include file="global/nav"}
    <div class="container index-search">
        <div class="row">
            <h2 class="col-12">{$theme_config['home']['search_title_text']|default=$setting['site_name'].' '.L('知识问答社区')}</h2>
            <p class="mb-3 w-100" style="color: #eee">{$theme_config['home']['search_min_text']|default='3 '.L('分钟快速创建你的知识社区')}</p>
            <div class="col-12">
                <form action="{:url('search/index')}" method="get" id="homeSearch">
                <span>
                    <i class="iconfont">&#xe610;</i>
                    <input type="text" autocomplete="off"  value="{:input('get.q')}"  name="q" placeholder="{:L('请输入你遇到的问题进行搜索')}">
                    <button type="button" class="btn gradientBtn px-4 ml-1" style="height: 42px;" onclick="$('#homeSearch').submit();" >{:L('搜索')}</button>
                </span>
                </form>
            </div>
        </div>
    </div>
</div>
{else/}
{__block__}
{/if}
{/block}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 bg-white mb-2">
            <div class="nav nav-tabs px-4 aw-pjax-a" role="tablist">
                {if $user_id}
                <a class="nav-item nav-link {if $sort=='focus'}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>'focus'])}">{:L('关注')}</a>
                {/if}
                <a class="nav-item nav-link {if $sort=='recommend' && !$type}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>'recommend'])}">{:L('推荐')}</a>
                <a class="nav-item nav-link {if $sort=='new' && !$type}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>'new'])}">{:L('最新')}</a>
                <a class="nav-item nav-link {if $sort=='hot' && !$type}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>'hot'])}">{:L('热门')}</a>
                <a class="nav-item nav-link {if $sort=='unresponsive' && !$type}active{/if}" data-pjax="tabMain" href="{:url('index/index',['sort'=>'unresponsive'])}" >{:L('待回答')}</a>
                {volist name=":config('aws.tabs')" id="v"}
                <a class="nav-link nav-item {if $type==$key}active{/if}" href="{:url('index/index',['sort'=>'new','type'=>$key])}" data-pjax="tabMain">{$v.title}</a>
                {/volist}
            </div>
            <div class="tab-content" id="tabMain">
                <div class="tab-pane fade show active">
                    <div class="aw-common-list">
                        {:widget('common/lists',['sort'=>$sort,'item_type'=>$type])}
                    </div>
                </div>
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            {if get_theme_setting('home.sidebar_show_items') && in_array('write_nav',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/writeNav')}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('announce',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/announce')}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('focus_topic',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/focusTopic',['uid'=>$user_id])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('hot_topic',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('column',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('hot_users',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotUsers',['uid'=>$user_id])}
            {/if}

            {if get_theme_setting('home.sidebar_show_items') && in_array('diy_content',get_theme_setting('home.sidebar_show_items'))}
            {$theme_config['home']['sidebar_diy_content']|raw|htmlspecialchars_decode}
            {/if}

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>

<!--友情链接小部件-->
{:widget('common/links')}

{/block}
