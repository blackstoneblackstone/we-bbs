{if !$_ajax}
<!doctype html>
<html style="height: auto">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {if request()->isSsl()}
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    {/if}
    <meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Pragma" content="no-cache">
    <meta name="renderer" content="webkit" />
    <title>{block name="meta_title"} {$_page_title|default=""} {/block}</title>
    <meta name="keywords" content="{block name='meta_keywords'}{$_page_keywords|default=""}{/block}">
    <meta name="description" content="{block name='meta_description'} {$_page_description|default=""} {/block}">
    <link rel="stylesheet" href="{$cdnUrl}/static/libs/swiper/swiper.min.css">
    <link rel="stylesheet" href="{$cdnUrl}/static/libs/layui/css/layui.css?v={$version}">
    <link rel="stylesheet" href="{$cdnUrl}/static/libs/highlight/styles/tomorrow.css?v={$version}">
    <link rel="stylesheet" href="{$cdnUrl}/static/common/fonts/fonts.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="{$cdnUrl}/static/common/css/bootstrap.min.css?v={$version}">
    <link rel="stylesheet" type="text/css" href="{$cdnUrl}/static/common/js/module/module.min.css?v={$version}" media="screen" />
    <link rel="stylesheet" type="text/css" href="{$cdnUrl}/static/libs/captcha/css/captcha.css?v={$version}" />
    <link rel="stylesheet" type="text/css" href="{$static_url}css/{:env('app_debug') ? 'app.css':'app.min.css'}">
    {block name="meta_style"}{/block}
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{$cdnUrl}/static/common/js/html5.min.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/common/js/respond.min.js?v={$version}"></script>
    <![endif]-->
    <script type="text/javascript" src="{$cdnUrl}/static/admin/plugins/jquery/jquery.min.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/admin/plugins/jquery-ui/jquery-ui.min.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/admin/plugins/bootstrap/js/bootstrap.bundle.min.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/swiper/swiper.min.js"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/layui/layui.all.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/webuploader/webuploader.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/common/js/module/module.min.js?v={$version}"></script>
    <script>
        window.userId = parseInt("{$user_id|default='0'}");
        window.userName = "{$user_info['nick_name']|default=''}";
        window.baseUrl = '{$baseUrl}';
        window.cdnUrl = '{$cdnUrl}';
        window.thisController ="{$thisController|default=''}";
        window.thisAction ="{$thisAction|default=''}";
        window.staticUrl = cdnUrl + '/static/';
        window.upload_image_ext = "{$setting.upload_image_ext}" ;
        window.upload_file_ext = "{$setting.upload_file_ext}" ;
        window.upload_image_size = "{$setting.upload_image_size}" ;
        window.upload_file_size = "{$setting.upload_file_size}" ;
        window.isAjax = parseInt("{$_ajax?1:0}");
        window.isAjaxOpen = parseInt("{$_ajax_open?1:0}");
        window.pjaxEnable =  "{$setting.pjax_enable=='Y' ? 1 : 0}" ;
        //代码高亮
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });
    </script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/captcha/captcha.js?v={$version}"></script>
    <script src="{$cdnUrl}/static/common/js/tools.js?v={$version}" type="text/javascript"></script>
    <script src="{$cdnUrl}/static/common/js/aws.js?v={$version}" type="text/javascript"></script>
    <script src="{$cdnUrl}/static/common/js/app.js?v={$version}"></script>
    {:hook('globalAssert')}
    {block name="meta_script"} {/block}
</head>
<body id="pageMain" class="aw-overflow-auto {if get_theme_setting('common.filter-grey','N')=='Y'}filter-grey{/if}" style="height: auto">
{$_style|raw}
{if !$_ajax && !$_ajax_open}
<!--全局自定义头部html-->
{$setting.header_html|htmlspecialchars_decode|raw}
{/if}
{block name="style"} <!--自定义样式-->{/block}
{if !$_ajax_open && !$_ajax}
<div class="container-fluid w-container-fluid" id="headerMain">
    {block name="header"}
    <div class="navbox-2 {:get_theme_setting('common.fixed_navbar')=='Y' ? 'suspension' : 'suspensions'}">
        <div class="container nav-auto">
            <div class="navbar navbar-expand-lg navbar-light">
                <a href="{$baseUrl}" class="navbar-brand">{if $setting.site_logo}<img class="logoimg-2" style="max-width: 192px;" src="{$setting.site_logo|default=$static_url.'images/logo-color.jpg'}">{else/}<span class="text-primary font-weight-bold">{$setting.site_name}</span>{/if}</a>
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
                                        <p class="search p-3 text-left"><span>{:L('查看更多')} "</span><a href="javascript:;" onclick="$('#awGlobalSearch').submit();" class="text-danger font-weight-bold"></a>" {:L('的搜索结果')}</p>
                                    </div>
                                    <div class="mod-footer px-2 py-1">
                                        <a href="{:url('question/publish')}" data-pjax="pageMain" class="btn btn-primary btn-small float-right">{:L('发起问题')}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        {if $user_id}
                        <div class="dropdown d-inline-block mr-4 position-relative">
                            <a href="javascript:;" class="btn btn-sm gradientBtn px-3 text-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{:L('发起')}</a>
                            <div class="dropdown-menu p-0 dropdown-menu-right border-0 font-size-sm">
                                <div class="text-center d-block py-2 aw-nav aw-dropdown-nav text-center aw-answer-sort" style="min-width: 100px">
                                    {if($user_info['permission']['publish_question_enable']=='Y')}
                                    <a href="{:url('question/publish')}" class="py-1 dropdown-item" target="_blank">{:L('问题')}</a>
                                    {/if}
                                    {if($user_info['permission']['publish_article_enable']=='Y')}
                                    <a href="{:url('article/publish')}" class="py-1 dropdown-item" target="_blank">{:L('文章')} </a>
                                    {/if}
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
                                <span class="badge badge-danger badge-pill position-absolute" id="inboxUnreadTag" style="right:-20px">{$user_info['inbox_unread']<=99 ? $user_info['inbox_unread']: '99+'}</span>
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
                                <span class="badge badge-danger badge-pill position-absolute" id="notifyUnreadTag" style="right:-20px">{$user_info['notify_unread']<=99 ? $user_info['notify_unread'] : '99+'}</span>
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
    {/block}
</div>
<!--全局页面内容顶部钩子-->
{:hook('pageTop')}
{/if}

<div style="height: auto;{$theme_config['common']['fixed_navbar']=='Y' && !$_ajax_open && !$_ajax && !($thisController=='column' && $thisAction=='index') && (!(!request()->plugin && $thisController=='index' && $thisAction=='index') || get_theme_setting('home.search_enable')=='N') ? 'margin-top:80px' : ''}" id="wrapMain">
    {block name="main"} {/block}
</div>

{if !$_ajax_open && !$_ajax}
<!--全局页面内容底部钩子-->
{:hook('pageBottom')}
{/if}

{block name="footer"}
{if !$_ajax}
{if !$_ajax_open}
<div class="foot">
    <div class="container justify-content-center">
        <div class="py-3 footauto clearfix">
            <div class="float-left ">All Rights Reserved Powered BY <a class="font-w600" href="https://wenda.wecenter.com" target="_blank">WeCenter V{:config('version.version')}</a> © {:date('Y',time())} <a target="_blank" class="ml-3" rel="noopener noreferrer" href="https://beian.miit.gov.cn/">{$setting.icp}</a></div>
            {if $footerMenu}
            <div class="float-right">
                <ul>
                {foreach $footerMenu as $k=>$v}
                    <li class="d-inline-block mx-1"><a href="{$v.url}" title="{$v.title}" target="_blank">{$v.title}</a></li>
                {/foreach}
                </ul>
            </div>
            {/if}
        </div>
    </div>
</div>
<a class="aw-back-top hidden-xs" href="javascript:;" onclick="$.scrollTo(1, 600, {queue:true});"><i class="icon-arrow-up-circle"></i></a>
<script>
    $(function () {
        let topNavSwiper = new Swiper('.swiper-nav-container', {
            speed: 600,
            grabCursor: true,
            slidesPerView: "auto",
            initialSlide: 0,
            slidesPerGroup: 1
        })
    })
</script>
{if $user_id && !$user_info['is_valid_email'] && $user_info['email'] && $setting['register_valid_type']=='email'}
<script>
    var str = '<div class="p-3"><a href="javascript:;" class="aw-ajax-get text-danger" data-url="{:url('account/send_valid_mail')}">{:L('你的邮箱 %s 还未验证,点击这里重新发送验证邮件',$user_info.email)}</a></div>';
    var width =$(window).width() > 600 ? '600px' : '85%';
    layer.open({
        title: '',
        type: 1,
        scrollbar: false,
        shade: 0.7,
        area: [width],
        content: str,
    })
</script>
{/if}

{if $user_id && $user_info['is_first_login']}
<script>
    AWS.User.firstLogin();
</script>
{/if}

<script>
    //导航悬浮
    var topMain = $(".navbox").height();
    $(window).scroll(function(){
        if ($(window).scrollTop()>topMain + 200){
            $('.navbox').addClass('suspension');
        }
        else
        {
            $('.navbox').removeClass('suspension');
        }

        //更换logo
        if($('.navbox').hasClass('suspension')){
            $(".logoimg").attr('src',"{$setting['site_logo']|default=$static_url.'images/logo-color.jpg'}");
        }else(
            $(".logoimg").attr('src',"{:get_theme_setting('common.bg_logo') ? : $static_url.'images/logo-write.png'}")
        )
    });

</script>
{/if}
<div id="aw-ajax-box"></div>
{/if}
{/block}

<!--自动加载js文件-->
{$_script|raw}

{if !$isMobile}
<div id="browserCheck" style="display: none;">
    <div class="p-3">
        <div class="mb-2">
            <p>{:L('你的浏览器版本过低，可能导致网站部分内容不能正常使用!')}</p>
            <p>{:L('为了能正常使用网站功能，请使用以下浏览器')}</p>
        </div>
        <ul class="d-flex text-center">
            <li class="flex-fill">
                <img src="{$cdnUrl}/static/common/image/icon_Chrome 2x.jpg">
                <span class="d-block">Chrome</span>
            </li>
            <li class="flex-fill">
                <img src="{$cdnUrl}/static/common/image/icon_Firefox 2x.jpg">
                <span class="d-block">Firefox</span>
            </li>
            <li class="flex-fill">
                <img src="{$cdnUrl}/static/common/image/icon_Safari 2x.jpg">
                <span class="d-block">Safari</span>
            </li>
            <li class="flex-fill">
                <img src="{$cdnUrl}/static/common/image/icon_IE 10 2x.jpg">
                <span class="d-block">IE 10+</span>
            </li>
        </ul>
    </div>
</div>
<script>
    var mb = AWS.common.browser();
    if ("IE10" !== mb  && "FF" !== mb && "Chrome" !== mb && "Safari" !== mb) {
        var width =$(window).width() > 600 ? '600px' : '85%';
        layer.open({
            title: '',
            type: 1,
            scrollbar: false,
            shade: 0.7,
            area: [width],
            content: $('#browserCheck').html(),
        })
    }
</script>
{/if}
{if !$_ajax && !$_ajax_open}
<!--全局自定义底部html-->
{$setting.footer_html|htmlspecialchars_decode|raw}
{/if}
{block name="script"} <!--自定义js脚本-->{/block}

{:hook('globalAssertJs')}
<form id="attach-download-form" action="{:url('upload/download')}" method="post">
    <input type="hidden" id="attach-name" name="name" value="">
    <input type="hidden" id="attachType" name="type" value="0">
</form>
</body>
</html>
{else/}
<div style="{$theme_config['common']['fixed_navbar']=='Y' && !$_ajax_open && !$_ajax && !($thisController=='column' && $thisAction=='index') && (!(!request()->plugin && $thisController=='index' && $thisAction=='index') || get_theme_setting('home.search_enable')=='N') ? 'margin-top:80px' : ''}" id="wrapMain">
    {block name="main"} {/block}
</div>
{/if}