{extend name="$theme_block" /}
{block name="main"}
<div class="container mt-2">
    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 mb-2">
            <div class="nav nav-tabs pl-4 pr-4 aw-pjax-a bg-white" role="tablist">
                <a class="nav-item nav-link {if $sort=='integral'}active{/if}" data-pjax="tabMain" href="{:url('people/lists',['sort'=>'integral'])}">{:L('活跃榜')} </a>
                <a class="nav-item nav-link {if $sort=='reputation'}active{/if}" data-pjax="tabMain" href="{:url('people/lists',['sort'=>'reputation'])}"> {:L($setting.power_unit.'榜')} </a>
            </div>
            <div id="tabMain" class="mt-2">
                <div class="plist row row-cols-2">
                    {volist name="list" id="v"}
                    <div class="people-list mb-3 col-md-6">
                        <dl class="position-relative d-flex mb-0 p-4 bg-white ">
                            <dt class="flex-fill mt-2 mr-3" style="max-width: 80px">
                                <a href="{$v.url}" class="aw-username rounded d-block" data-id="{$v.uid}">
                                    <img src="{$v.avatar}" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v.name}" width="80" height="80" style="border-radius: 50%">
                                </a>
                                {if $v['is_online']}
                                <span class="online-dot"></span>
                                {else/}
                                <span class="offline-dot"></span>
                                {/if}
                            </dt>
                            <dd class="flex-fill mb-0">
                                <h3 class="mb-1">
                                    <a href="{$v.url}" class="aw-username font-12" data-id="{$v.uid}">{$v.name}</a>
                                </h3>
                                <p class="text-color-info font-9 mt-2 aw-one-line">{$v['signature']|default=L('这家伙还没有留下自我介绍～')}</p>
                                <div class="text-color-info mt-2 font-9">
                                    <label class="text-muted">{:L($setting.score_unit)}: {$v.integral}</label> |
                                    <label class="text-muted">{:L($setting.power_unit)}: {$v.reputation}</label> |
                                    <label class="text-muted">{:L('获赞')}: {$v.agree_count}</label>
                                </div>

                                <div style="right: 1rem;bottom: .7rem">
                                    {if $user_id && $v['uid']!=$user_id}
                                    <a class="{if $v.has_focus}active ygz{/if} px-4 btn btn-sm btn-primary" href="javascript:;" onclick="AWS.User.focus(this,'user','{$v.uid}')">{$v.has_focus ? L('已关注'): L('关注')}</a>
                                    <a class="px-4 btn btn-sm btn-outline-primary" href="javascript:;" onclick="AWS.User.inbox('{$v.nick_name}')">{:L('私信')}</a>
                                    {else/}
                                    <button type="button" class="px-4 btn btn-sm btn-primary" disabled>{$v.has_focus ? L('已关注'): L('关注')}</button>
                                    <button type="button" class="px-4 btn btn-sm btn-outline-primary" disabled>{:L('私信')}</button>
                                    {/if}
                                </div>
                            </dd>
                        </dl>
                    </div>
                    {/volist}
                </div>
                {if $page}
                {$page|raw}
                {/if}
            </div>
        </div>
        <div class="aw-right radius col-md-3 px-xs-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            {if in_array('write_nav',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/writeNav')}
            {/if}

            {if in_array('announce',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/announce')}
            {/if}

            {if in_array('focus_topic',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/focusTopic',['uid'=>$user_id])}
            {/if}

            {if in_array('hot_topic',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}
            {/if}

            {if in_array('column',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
            {/if}

            {if in_array('hot_users',get_theme_setting('home.sidebar_show_items'))}
            {:widget('sidebar/hotUsers',['uid'=>$user_id])}
            {/if}

            {if in_array('diy_content',get_theme_setting('home.sidebar_show_items'))}
            {$theme_config['home']['sidebar_diy_content']|raw|htmlspecialchars_decode}
            {/if}

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
{/block}