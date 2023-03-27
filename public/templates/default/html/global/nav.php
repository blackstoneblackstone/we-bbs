<div class="navbox">
    <div class="container nav-auto">
        <div class="navbar navbar-expand-lg navbar-light">
            <a href="{$baseUrl}" class="navbar-brand">{if $setting.site_logo}<img class="logoimg-2" style="max-width: 192px;" src="{$setting.site_logo|default=$static_url.'images/logo-write.png'}">{else/}<span class="text-primary font-weight-bold">{$setting.site_name}</span>{/if}</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse newnavbar" id="navbarNavDropdown" style="justify-content: space-between">
                <div class="swiper-nav-container position-relative" style="max-width: 370px;z-index: 1">
                    <ul class="navbar-nav navbar-new-nav swiper-wrapper">
                        {foreach $navMenu as $k=>$v}
                        {if $k<5}
                        <li class="nav-item swiper-slide {$v.active ? 'cur' : ''} clearfix" style="width: auto!important">
                            {if $v.child_list}
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{$v.title}</a>
                            <div class="dropdown-menu text-center" aria-labelledby="navbarDropdownMenuLink">
                                {volist name="$v['child_list']" id="v1"}
                                <a class="dropdown-item" href="{$v1.url}" data-pjax="pageMain" title="{$v1.title}">{$v1.title}</a>
                                {/volist}
                            </div>
                            {else/}
                            <a class="nav-link" href="{$v.url}" data-pjax="pageMain" title="{$v.title}">{$v.title}</a>
                            {/if}
                        </li>
                        {/if}
                        {/foreach}
                        {if count($navMenu)>5}
                        <li class="nav-item swiper-slide {$v.active ? 'cur' : ''} clearfix" style="width: auto!important">
                            <a class="nav-link dropdown-toggle" href="#"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{:L('更多')}</a>
                            <div class="dropdown-menu text-center" aria-labelledby="navbarDropdownMenuLink">
                                {foreach $navMenu as $k=>$v1}
                                {if $k>=5}
                                <a class="dropdown-item" href="{$v1.url}" data-pjax="pageMain" title="{:L($v1.title)}">{$v1.title}</a>
                                {/if}
                                {/foreach}
                            </div>
                        </li>
                        {/if}
                    </ul>
                </div>
                <div class="nav-form position-relative" style="text-align:right;z-index: 2">
                    <form class="position-relative aw-form-search d-inline-block" action="{:url('search/index')}" method="get" id="awGlobalSearch">
                        <div class="searchbox">
                            <input id="globalSearchInput" class="aw-search-input" autocomplete="off" placeholder="{:L('请输入您想搜索的内容')}" value="{:input('get.q')}"  name="q" type="text">
                            <label class="si si-magnifier" onclick="$('#awGlobalSearch').submit();" ></label>
                            <div class="aw-dropdown" style="display: none">
                                <div class="mod-body">
                                    <p class="title text-center p-3 font-weight-bold">{:L('请输入关键字进行搜索')}</p>
                                    <div class="aw-dropdown-list aw-common-list aw-overflow-auto text-left px-3"></div>
                                    <p class="search p-3 text-left"><span>{:L('查看更多')} "</span><a onclick="$('#awGlobalSearch').submit();" class="text-danger font-weight-bold"></a>"
                                        {:L('的搜索结果')}</p>
                                </div>
                                <div class="mod-footer px-2 py-1">
                                    <a href="{:url('question/publish')}" data-pjax="pageMain" class="btn btn-primary btn-small float-right">{:L('发起问题')}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    {if $user_id}
                    <div class="dropdown d-inline-block mr-4 position-relative">
                        <a href="javascript:;" class="btn btn-sm gradientBtn px-3 text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {:L('发起')}
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-right border-0 font-size-sm">
                            <div class="text-center d-block py-2 aw-nav aw-dropdown-nav text-center aw-answer-sort" style="min-width: 100px">
                                <a href="{:url('question/publish')}" class="py-1 dropdown-item" target="_blank">{:L('问题')}</a>
                                <a href="{:url('article/publish')}" class="py-1 dropdown-item" target="_blank">{:L('文章')} </a>
                                {volist name=":config('aws.publish')" id="v"}
                                <a href="{:url($v['url'])} " class="py-1 dropdown-item" target="_blank">{$v.title}</a>
                                {/volist}
                                {:hook('publishButtons')}
                            </div>
                        </div>
                    </div>
                    <div class="dropdown d-inline-block mr-4 position-relative">
                        <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="inboxBox">
                            <i class="iconfont">&#xe628</i>
                            {if $user_id && $user_info['inbox_unread']}
                            <span class="badge badge-danger badge-pill position-absolute" id="inboxUnreadTag" style="right:-20px">{$user_info['inbox_unread']? $user_info['inbox_unread'] : '99+'}</span>
                            {/if}
                        </a>
                        <div class="dropdown-menu p-0 dropdown-menu-right border-0 font-size-sm" style="width: 350px !important;">
                            <div class="bg-light text-center">
                                <h5 class="dropdown-header text-uppercase py-2">{:L('私信消息')}</h5>
                            </div>
                            <div class="nav-items mb-0 aw-overflow-auto" id="topInboxBox" style="max-height: 400px"></div>
                            <div class="p-2 border-top">
                                <a class="btn btn-sm btn-light btn-block text-center" href="{:url('inbox/index')}">
                                    <i class="fa fa-fw fa-arrow-down mr-1"></i> {:L('加载更多')}...
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown d-inline-block mr-4 position-relative">
                        <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="notifyBox">
                            <i class="iconfont">&#xe689</i>
                            {if $user_id && $user_info['notify_unread']}
                            <span class="badge badge-danger badge-pill position-absolute" id="notifyUnreadTag" style="right:-20px">{$user_info['notify_unread']? $user_info['notify_unread'] : '99+'}</span>
                            {/if}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm" style="width: 350px !important;">
                            <div class="bg-light clearfix py-2 px-3">
                                <h5 class="text-uppercase float-left font-weight-bold font-9 mb-0">{:L('通知消息')}</h5>
                                <a class="text-muted float-right font-8" href="javascript:;" onclick="AWS.User.headerNotifyReadAll()">{:L('全部已读')}</a>
                            </div>
                            <div class="nav-items mb-0 aw-overflow-auto" id="topNotifyBox" style="max-height: 400px"></div>
                            <div class="p-2 border-top">
                                <a class="btn btn-sm btn-light btn-block text-center" href="{:url('notify/index')}">
                                    <i class="fa fa-fw fa-arrow-down mr-1"></i> {:L('加载更多')}...
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown d-inline-block position-relative">
                        <a href="javascript:;" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded" src="{$user_info.avatar}" style="width: 22px;height: 22px">
                            <span class="d-none d-sm-inline-block ml-1">{$user_info.nick_name}</span>
                            <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block"></i>
                        </a>
                        <div class="dropdown-menu p-0 border-0 font-size-sm" aria-labelledby="page-header-user-dropdown">
                            <div class="p-3 text-center bg-primary">
                                <a href="{:url('creator/index')}">
                                    <img class="img-avatar img-avatar-thumb" style="border-radius: 50%" width="48" height="48" src="{$user_info.avatar ? : '/static/libs/aw-core/media/avatars/avatar0.jpg'}" alt="">
                                </a>
                            </div>
                            <div class="p-2 text-center">
                                <a class="dropdown-item align-items-center" href="{$user_info.url}">
                                    <span>{:L('个人主页')}</span>
                                </a>
                                <a class="dropdown-item align-items-center" href="{:url('creator/index')}">
                                    <span>{:L('用户中心')}</span>
                                </a>
                                <a class="dropdown-item align-items-center" href="{:url('setting/profile')}">
                                    <span>{:L('个人资料')}</span>
                                </a>
                                <div role="separator" class="dropdown-divider"></div>
                                {if $user_info['group_id']==1 || $user_info['group_id']==2}
                                <a class="dropdown-item align-items-center" target="_blank" href="/{:config('app.admin')}">
                                    <span>{:L('管理后台')}</span>
                                </a>
                                {/if}
                                <a class="dropdown-item align-items-center aw-ajax-get"  data-url="{:url('account/logout')}" href="#">
                                    <span>{:L('退出登录')}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    {else/}
                    <a {if $theme_config['common']['login_type']=='dialog'}href="javascript:;" onclick="AWS.User.login()" {else/}href="{:url('account/login')}"{/if} class="btn btn-sm gradientBtn px-3 logon-but d-inline-block">{:L('登录')}</a>
                    {/if}
                    {if get_setting('enable_multilingual','N')=='Y'}
                    <div class="dropdown d-inline-block position-relative">
                        <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="d-none d-sm-inline-block ml-1"><i class="fa fa-language font-12"></i></span>
                        </a>
                        <div class="dropdown-menu p-0 border-0 font-size-sm" aria-labelledby="page-header-user-dropdown">
                            <div class="p-2 text-center">
                                <a class="dropdown-item align-items-center aw-ajax-get" data-url="{:url('ajax/change_lang',['lang'=>'zh-cn'])}" href="JavaScript:;">
                                    <span>{:L('中文')}</span>
                                </a>
                                <a class="dropdown-item align-items-center aw-ajax-get" data-url="{:url('ajax/change_lang',['lang'=>'en-us'])}" href="JavaScript:;">
                                    <span>{:L('英文')}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>