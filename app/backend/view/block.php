{if !$_ajax}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>{:isset($page_title) && $page_title  ? $page_title : (isset($breadCrumb['left']) ? $breadCrumb['left'][0] : get_setting('site_name'))} - 后台管理</title>
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="/favicon.ico" rel="shortcut icon" type="image/x-icon"/>
    <link rel="stylesheet" href="/static/libs/layui/css/layui.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/libs/jstree/dist/themes/default/style.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/libs/webuploader/webuploader.css?v={$version|default='1.0.0'}">
    <!--<link rel="stylesheet" href="/static/common/css/ui.css">-->
    <link rel="stylesheet" type="text/css" href="{$cdnUrl}/static/common/js/module/module.min.css?v={$version}" media="screen" />
    <link rel="stylesheet" href="/static/libs/select2/css/select2.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/libs/bootstrap-table/bootstrap-table.min.css?v={$version|default='1.0.0'}"/>
    <link rel="stylesheet" href="/static/libs/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.css?v={$version|default='1.0.0'}"/>
    <link rel="stylesheet" href="/static/libs/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css?v={$version|default='1.0.0'}"/>
    <link rel="stylesheet" href="/static/libs/bootstrap-table/extensions/editable/css/bootstrap-editable.css?v={$version|default='1.0.0'}"/>
    <link rel="stylesheet" href="/static/libs/codemirror/codemirror.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/libs/codemirror/theme/material.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/plugins/fontawesome-free/css/all.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/plugins/jqvmap/jqvmap.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/common/fonts/fonts.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/dist/css/adminlte.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/plugins/overlay-scrollbars/css/OverlayScrollbars.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/libs/swiper/swiper.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="/static/admin/css/admin.css?v={$version|default='1.0.0'}">

    <script src="/static/admin/plugins/jquery/jquery.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/jquery-ui/jquery-ui.min.js?v={$version|default='1.0.0'}"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <script src="/static/admin/plugins/bootstrap/js/bootstrap.bundle.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/moment/moment.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/sparklines/sparkline.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/overlay-scrollbars/js/jquery.overlayScrollbars.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/dist/js/adminlte.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/jquery-validation/jquery.validate.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/plugins/jquery-validation/localization/messages_zh.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/layui/layui.all.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/webuploader/webuploader.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/pjax/jquery.pjax.js"></script>
    <script src="/static/libs/select2/js/select2.full.min.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="/static/libs/ztree/js/jquery.ztree.core.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="/static/libs/ztree/js/jquery.ztree.excheck.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/jstree/dist/jstree.min.js?v={$version|default='1.0.0'}"></script>

    <script src="/static/libs/bootstrap-table/bootstrap-table.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/locale/bootstrap-table-zh-CN.min.js?v={$version|default='1.0.0'}"></script>

    <script src="/static/libs/bootstrap-table/extensions/mobile/bootstrap-table-mobile.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/toolbar/bootstrap-table-toolbar.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/treegrid/bootstrap-table-treegrid.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/jquery-treegrid/js/jquery.treegrid.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/export/tableExport.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/export/bootstrap-table-export.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/multiple-sort/bootstrap-table-multiple-sort.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/editable/js/bootstrap-editable.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/bootstrap-table/extensions/editable/bootstrap-table-editable.min.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/codemirror/codemirror.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/codemirror/mode/css/css.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/codemirror/mode/xml/xml.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/codemirror/mode/javascript/javascript.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/libs/codemirror/mode/htmlmixed/htmlmixed.js?v={$version|default='1.0.0'}"></script>

    <script src="/static/libs/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/common/js/module/module.min.js?v={$version}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/swiper/swiper.min.js?v={$version|default='1.0.0'}"></script>
    <script>
        window.G_BASE_URL = '{$base_url}';
        window.userId = parseInt("{$user_id|default='0'}");
        window.baseUrl = '{$base_url}';
        window.cdnUrl = '{$cdnUrl}';
        window.thisController = "{$thisController|default=''}";
        window.thisAction = "{$thisAction|default=''}";
        window.staticUrl = cdnUrl + '/static/';
        window.upload_image_ext = "{$setting.upload_image_ext}";
        window.upload_file_ext = "{$setting.upload_file_ext}";
        window.upload_image_size = "{$setting.upload_image_size}";
        window.upload_file_size = "{$setting.upload_file_size}";
        window.isAjax = "{$_ajax}";
        window.isAjaxOpen = "{$_ajax_open}";
        layui.use('layer',
            function () {
                var layer = layui.layer;
            });
    </script>
    <script src="/static/common/js/tools.js?v={$version|default='1.0.0'}"></script>
    <script src="/static/admin/js/aws-admin.js?v={$version|default='1.0.0'}"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed aw-overflow-auto">
<div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="/static/common/image/logo.png" alt="{$setting.site_name}" height="60">
</div>

{if !$_ajax_open}
<div class="wrapper" id="wrapMain">
    {block name="top"}
    <nav class="main-header navbar navbar-expand navbar-white">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <div class="swiper-container navbar-nav">
            <ul class="js_left_menu top-nav swiper-wrapper" id="topNav">
                {$_nav|raw}
            </ul>
        </div>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="{$baseUrl}" target="_blank">
                    <i class="fas fa-home"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                    <i class="fas fa-expand-arrows-alt"></i>
                </a>
            </li>
            <!--<li class="nav-item">
                <a class="nav-link" data-widget="control-sidebar" data-controlsidebar-slide="true" href="#"
                   role="button">
                    <i class="fas fa-th-large"></i>
                </a>
            </li>-->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="fa fa-sync"></i>
                </a>
                <div class="dropdown-menu font-size-sm text-center" aria-labelledby="dropdownClear">
                    <a class="dropdown-item aw-ajax-get" data-url="{:url('index/clear',['type'=>'cache'])}"
                       href="javascript:void(0)">清除缓存</a>
                    <a class="dropdown-item aw-ajax-get" data-url="{:url('index/clear',['type'=>'log'])}"
                       href="javascript:void(0)">清除日志</a>
                    <a class="dropdown-item aw-ajax-get" data-url="{:url('index/clear',['type'=>'tmp'])}"
                       href="javascript:void(0)">清除临时文件</a>
                    <a class="dropdown-item aw-ajax-get" data-url="{:url('index/clear',['type'=>'all'])}"
                       href="javascript:void(0)">清除全部</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#" aria-expanded="false">
                    <i class="far fa-bell"></i>
                    {if $notify_count}
                    <span class="badge badge-danger navbar-badge" style="top: 2px;right: -6px">{$notify_count}</span>
                    {/if}
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0;">
                    <span class="dropdown-item dropdown-header">{$notify_count} 个问题待处理</span>
                    {volist name="notify_list" id="v"}
                    <div class="dropdown-divider"></div>
                    <a href="{$v.url}" class="dropdown-item">
                        {$v.text}
                    </a>
                    {/volist}
                </div>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <img src="{$user_info.avatar}" class="d-inline-block" width="20" height="20">
                    <span class="d-inline-block">{$user_info.name}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="left: inherit; right: 0;">
                    <div class="p-3 text-center bg-primary">
                        <a href="javascript:;">
                            <img class="img-avatar img-avatar-thumb" width="48" height="48" src="{$user_info.avatar ? : '/static/libs/aw-core/media/avatars/avatar0.jpg'}" alt="">
                        </a>
                    </div>
                    <div class="p-2 text-center">
                        <a class="dropdown-item align-items-center aw-ajax-get"  data-url="{:url('admin/Index/logout')}" href="#">
                            <span>{:L('退出登录')}</span>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
    </nav>
    {/block}

    {block name="left"}
    <aside class="main-sidebar sidebar-dark-primary">
        <a href="{$baseUrl}" class="brand-link bg-white">
             <img src="{$setting.site_logo}" alt="{$setting.site_name}" class="brand-image">
        </a>
        <div class="sidebar" id="sidebar">
            <nav class="mt-3 mb-2">
                <ul class="sidebar-menu nav nav-pills no_radius nav-sidebar flex-column nav-child-indent js_left_menu_show"
                    data-widget="treeview" role="menu" data-accordion="true">
                    {$_menu|raw}
                </ul>
            </nav>
        </div>
    </aside>
    {/block}

    <div class="content-wrapper aw-overflow-auto">
        <div id="mainTab">
            <!--后台全局页面提示钩子-->
            {:hook('adminTips')}
            {if $breadCrumb && $breadCrumb.left.0}
            <div class="bg-white mb-1">
                <div class="p-3">
                    <div class="container-fluid">
                        <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
                            <h1 class="flex-sm-fill h3 my-2">
                                {$breadCrumb.left.0} <small class="d-sm-inline-block mt-2 mt-sm-0 font-size-base font-w400 text-muted">{$breadCrumb.left.1}</small>
                            </h1>
                            <nav class="flex-sm-00-auto ml-sm-3 d-xs-none" aria-label="breadcrumb">
                                <ol class="breadcrumb breadcrumb-alt mb-0">
                                    <li class="breadcrumb-item"><a href="{:url('Index/index')}">后台首页</a></li>
                                    <li class="breadcrumb-item" aria-current="page">
                                        <a class="link-fx" href="{:url($breadCrumb.right.url)}">{$breadCrumb.right.title}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            {/if}
            <div class="container-fluid">
                {if !$version_info.data || $version_info.data.authorize=='免费版'}
                <div class="alert alert-danger mt-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>您还未授权，授权后可获取更多功能及支持 <a href="javascript:;" class="aw-ajax-open" data-url="https://wenda.wecenter.com/timeline/version" data-title="版本区别" style="text-decoration: none;">【查看版本区别】</a> </p>
                </div>
                {/if}

                {if isset($email_tips) && $email_tips}
                <div class="alert alert-warning mt-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>您还未配置邮箱，未配置邮箱可能会导致用户无法完成注册;去 <a href="{:url('admin.Config/config',['group'=>3])}"  style="text-decoration: none;">【配置邮箱】</a> </p>
                </div>
                {/if}

                {if $version_info.code==200}
                <div class="alert alert-success alert-dismissable mt-3">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <p>您有新版本待更新 <a href="{:url('plugin.Upgrade/index')}" style="text-decoration: none;">去升级</a> </p>
                </div>
                {/if}
            </div>
            {block name="main"} {/block}
        </div>
    </div>

    {block name="footer"}
    <footer class="main-footer">
        <strong>Copyright © {:date('Y')} <a href="https://wenda.wecenter.com">WeCenter</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b><a class="font-w600" href="https://wenda.wecenter.com" target="_blank">WeCenter V{:config('version.version')}</a></b>
        </div>
    </footer>
    {/block}
    <aside class="control-sidebar control-sidebar-dark"></aside>
    <script>
        $(function () {
            let k = parseInt($('.i-active').index())
            let navSwiper = new Swiper('.swiper-container', {
                speed: 600,
                grabCursor: true,
                slidesPerView: "auto",
                initialSlide: k,
                slidesPerGroup: 3
            })
        })

        $(function () {
            var firstNav = $("#topNav");
            //点击顶部第一级菜单栏
            firstNav.on("click", "li a", function () {
                var that = $(this);
                var pid =  that.attr("data-id");
                firstNav.find('li').removeClass("active");
                that.parent().addClass('active');
                $("#sidebar .sidebar-menu>li").addClass("d-none");
                if (that.attr("data-url") == "javascript:;") {
                    var sonList = $("#sidebar .sidebar-menu > li[data-pid='" + pid + "']");
                    sonList.removeClass("d-none");
                    var sidenav;
                    sidenav = $("#sidebar .sidebar-menu li[data-pid='" + pid + "']:first>a");
                    if (sidenav && sidenav.attr("href") != "javascript:;") {
                        /*var url = sidenav.attr("href");
                        AWS_ADMIN.common.jump(url);*/
                        sidenav.trigger('click');
                    }
                }
            });

            // 左侧菜单高亮
            $('#sidebar .sidebar-menu>li a.nav-link').on('click', function () {
                if ($(this).attr('href') !== '#') {
                    $("#sidebar .sidebar-menu>li a.nav-link").removeClass('active');
                    $(this).addClass('active');
                    /*$(this).parents('.nav-item').last().siblings().children('a').removeClass('active')
                    $(this).parents('.nav-item').last().children('a').addClass('active')*/
                }
            });

        })
    </script>
</div>
{else/}
<!--后台全局页面提示钩子-->
{:hook('adminTips')}
<div id="wrapMain" class="aw-overflow-auto">
{block name="main"} {/block}
</div>
{/if}
</body>
</html>
{else/}
{block name="main"} {/block}
{/if}
