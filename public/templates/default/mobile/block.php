{if !$_ajax_open && !$_ajax}
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    {if request()->isSsl()}
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />
    {/if}

    <title>{block name="meta_title"} {$_page_title} {/block}</title>
    <meta name="keywords" content="{block name='meta_keywords'}{$_page_keywords}{/block}">
    <meta name="description" content="{block name='meta_description'} {$_page_description} {/block}">
    <link rel="stylesheet" href="{$cdnUrl}/static/common/fonts/fonts.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" type="text/css" href="{$static_url}mobile/css/aui.min.css?v={$version|default='1.0.0'}"/>
    <link rel="stylesheet" href="{$cdnUrl}/static/libs/swiper/swiper.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" type="text/css" href="{$cdnUrl}/static/common/js/module/module.min.css?v={$version}" media="screen" />
    <link rel="stylesheet" href="{$cdnUrl}/static/libs/highlight/styles/tomorrow.css?v={$version}">
    <link rel="stylesheet" type="text/css" href="{$static_url}mobile/lib/mescroll/mescroll.min.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" href="{$static_url}mobile/css/fonts/iconfont.css?v={$version|default='1.0.0'}">
    <link rel="stylesheet" type="text/css" href="{$cdnUrl}/static/libs/captcha/css/captcha.css?v={$version}" />
    <link rel="stylesheet" type="text/css" href="{$static_url}mobile/css/app.css?v={$version|default='1.0.0'}"/>
    {block name="meta_style"}{/block}
    <script type="text/javascript" src="{$static_url}mobile/lib/mescroll/mescroll.min.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="{$static_url}mobile/lib/jquery/jquery-2.1.3.min.js?v={$version|default='1.0.0'}"></script>
    <script src="{$cdnUrl}/static/libs/layer/layer.js?v={$version}" type="text/javascript"></script>
    <script src="{$cdnUrl}/static/libs/webuploader/webuploader.js?v={$version}"></script>
    <script src="{$cdnUrl}/static/common/js/module/module.min.js?v={$version}"></script>
    <script type="text/javascript" charset="utf-8" src="http://res2.wx.qq.com/open/js/jweixin-1.6.0.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/swiper/swiper.min.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="{$static_url}mobile/js/aui.js?v={$version|default='1.0.0'}"></script>
    <script src="{$cdnUrl}/static/common/js/tools.js?v={$version}" type="text/javascript"></script>
    <script>
        window.userId = parseInt("{$user_id|default='0'}");
        window.userName = "{$user_id ? $user_info.user_name : ''}";
        window.baseUrl = '{$baseUrl}';
        window.cdnUrl = '{$cdnUrl}';
        window.perPage = "{:get_setting('contents_per_page',15)}";
        window.thisController ="{$thisController|default=''}";
        window.thisAction ="{$thisAction|default=''}";
        window.staticUrl = cdnUrl + '/static/';
        window.upload_image_ext = "{$setting.upload_image_ext}" ;
        window.upload_file_ext = "{$setting.upload_file_ext}" ;
        window.upload_image_size = "{$setting.upload_image_size}" ;
        window.upload_file_size = "{$setting.upload_file_size}" ;
        window.isAjax = "{$_ajax}" ;
        window.isAjaxOpen = "{$_ajax_open}" ;
        //代码高亮
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        });
    </script>
    <script type="text/javascript" src="{$cdnUrl}/static/libs/captcha/captcha.js?v={$version}"></script>
    <script type="text/javascript" src="{$static_url}mobile/js/aws_mobile.js?v={$version|default='1.0.0'}"></script>
    <script type="text/javascript" src="{$static_url}mobile/js/app.js?v={$version|default='1.0.0'}"></script>
    {block name="meta_script"} {/block}
</head>
<body id="pageMain">
    <div class="aui-container">
        {$_style|raw}
        <!--全局自定义头部html-->
        {$setting.header_html|htmlspecialchars_decode|raw}
        {block name="style"} <!--自定义样式-->{/block}
        <div id="mobileMain">
            {block name="header"}
            <header class="aui-header mb-1">
                <div class="aui-header-title" style="left: 1rem">
                    <div class="headerSearch">
                        <a class="searchForm" href="{:url('search/index')}" data-pjax="pageMain">
                            <input type="text" class="searchInput" placeholder="{:L('输入您想搜索的内容')}...">
                            <label><i class="iconfont iconsearch1"></i></label>
                        </a>
                    </div>
                </div>
                <a class="aui-header-right" href="{:url('creator/index')}" data-pjax="pageMain"><i class="iconfont icon-yonghu font-15"></i></a>
            </header>
            {/block}

            <!--全局页面内容顶部钩子-->
            {:hook('pageTop')}

            {block name="main"} {/block}
        </div>
        {block name="sideMenu"} {/block}
        {block name="footer"}
        {if $user_id}
        <script type="text/html" class="publishBtnBox">
            <div class="row text-center mt-3">
                <div class="col-3 mb-4">
                    <a href="{:url('question/publish')}" class="text-muted">
                        <i class="icon-help-with-circle font-14"></i>
                        <span class="d-block font-9 mt-1">{:L('问题')}</span>
                    </a>
                </div>

                <div class="col-3 mb-4">
                    <a href="{:url('article/publish')}" class="text-muted">
                        <i class="far fa-file-alt font-14"></i>
                        <span class="d-block font-9 mt-1">{:L('文章')}</span>
                    </a>
                </div>
                {volist name=":config('aws.publish')" id="v"}
                <!--<div class="col-3 mb-4">
                    <a href="{:url($v['url'])}" class="text-muted">
                        <i class="{$v.icon|default='far fa-file-alt'} font-14"></i>
                        <span class="d-block font-9 mt-1">{$v.title}</span>
                    </a>
                </div>-->
                {/volist}
            </div>
        </script>
        {/if}
        <footer class="aui-footer row-before">
            <div class="aui-footer-list {if $thisController=='index' && $thisAction=='index'}active{/if}">
                <a href="{$baseUrl}"><i class="iconfont icon-faxian"></i><p class="mt-1">{:L('发现')}</p></a>
            </div>
            <div class="aui-footer-list {if ($thisController=='question' && $thisAction=='index') || ($thisController=='article' && $thisAction=='index') || ($thisController=='column' && $thisAction=='index')}active{/if}">
                <a href="{:url('question/index')}" data-pjax="pageMain"><i class="iconfont icon-shequ"></i><p class="mt-1">{:L('社区')}</p></a>
            </div>
            <div class="aui-footer-list position-relative publishAction justify-content-center">
                <div class="publishBtn" onclick="{if $user_id}AWS_MOBILE.api.dialog('{:L(\'发布\')}',$('.publishBtnBox').html()){else/}AWS_MOBILE.User.login(){/if}"><i class="fa fa-plus "></i></div>
            </div>
            <div class="aui-footer-list {if $thisController=='extend' && $thisAction=='index'}active{/if}">
                <a href="{:url('extend/index')}" data-pjax="pageMain"><i class="iconfont icon-52"></i><p class="mt-1">{:L('更多')}</p></a>
            </div>
            <div class="aui-footer-list {if $thisController=='creator'}active{/if}">
                <a href="{if $user_id}{:url('creator/index')}{else/}{:url('account/login')}{/if}" data-pjax="pageMain"><i class="iconfont icon-xiazai"></i><p class="mt-1">{:L('我的')}</p></a>
            </div>
        </footer>
        {/block}
        {$_script|raw}
        <div id="mask" style="display: none"></div>
        <div id="aw-ajax-box" style="display: none" class="aw-ajax-box">
            <div id="aw-mask" style="display: none"></div>
            <div class="aw-ajax-content p-2">
                <div class="ajaxBoxTitle">
                    <div class="title"></div>
                    <a href="javascript:;" class="closeAjaxOpen"><i class="si si-close text-muted"></i></a>
                </div>
                <div class="ajaxBoxContent aw-overflow-auto" style="max-height: 80vh"></div>
            </div>
        </div>
    </div>
    <form id="attach-download-form" action="{:url('upload/download')}" method="post">
        <input type="hidden" id="attach-name" name="name" value="">
        <input type="hidden" id="attachType" name="type" value="0">
    </form>
    <script>
        //滚动TAB
        $(function () {
            var activeK = parseInt($('.i-active').index());
            activeK = activeK?activeK:0;
            var navSwiper = new Swiper('.swiper-container', {
                speed: 600,
                grabCursor: true,
                slidesPerView: "auto",
                initialSlide: activeK,
                slidesPerGroup: 1
            })
        })
        {if $jsSdkConfig}
        var WEIXIN_IMG_COVER;
        if ($('.aw-content img').length)
        {
            WEIXIN_IMG_COVER = $('.aw-content img').first().attr('src');
        } else {
            WEIXIN_IMG_COVER = '{$setting.site_logo}';
        }

        if (WEIXIN_IMG_COVER.indexOf("http") < 0)
        {
            WEIXIN_IMG_COVER = "{$baseUrl}" + WEIXIN_IMG_COVER;
        }
        var wxConfig = {$jsSdkConfig|raw};
        aui.wxConfig(wxConfig);
        aui.wxShare({
            imgUrl: WEIXIN_IMG_COVER,
            link:  wxConfig.url,
            title: document.title,
            desc: $('meta[name="description"]').attr('content'),
        })
        {/if}
    </script>
    {if $user_id && !$user_info['is_valid_email'] && $user_info['email'] && $setting['register_valid_type']=='email'}
    <script>
        aui.announce({
            title: '{:L(\'邮箱验证\')}',
            msg: '<div class="p-3"><a href="javascript:;" class="aw-ajax-get text-danger" data-url="{:url('account/send_valid_mail')}">{:L(\'你的邮箱\')} {$user_info['email']} {:L(\'还未验证\')}, {:L(\'点击这里重新发送验证邮件\')}</a></div>'
        });
    </script>
    {/if}
</body>
</html>
{else/}
{$_style|raw}
{block name="main"} {/block}
{$_script|raw}
{/if}
