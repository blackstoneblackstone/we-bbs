//全局pjax方法
if($.support.pjax && pjaxEnable==1)
{
    $.pjax.defaults.timeout = 1200;
    $(document).on('click', 'a[data-pjax],a[target!=_blank]', function(event) {
       // var index = layer.load();
        let container = $(this).attr('data-pjax')
        let containerSelector = '#' + container;
        $.pjax.defaults.fragment = containerSelector;
        $.pjax.click(event, {container: containerSelector,scrollTo:container});
        //layer.close(index);
    })
    $(document).on('pjax:timeout', function(event) {
        event.preventDefault();
    })
}

/*二维码生成*/
$(document).ready(function ()
{
    let qc = $('.aw-qrcode-container')
    if (qc.length) {
        qc.each(function(){
            let that = $(this);
            let url = that.data('share');
            that.find('.aw-qrcode').qrcode({
                render: "canvas",
                width: 90,
                height: 90,
                text: url,
            });
            let myCanvas=that.find('.aw-qrcode').find('canvas')[0];
            let img=convertCanvasToImage(myCanvas);
            that.find('.aw-qrcode').append(img);
            that.find('.aw-qrcode canvas').hide();
        });
    }

    function convertCanvasToImage(canvas) {
        let image = new Image();
        image.src = canvas.toDataURL("image/png");
        return image;
    }

    /*$('.aw-qrcode-container a').mousemove(function (){
        $('.aw-qrcode').show();
    }).mouseout(function (){
        $('.aw-qrcode').hide();
    });*/
})

$.fn.extend({
    insertAtCaret: function (textFieldValue)
    {
        var textObj = $(this).get(0);
        if (document.all && textObj.createTextRange && textObj.caretPos)
        {
            var caretPos = textObj.caretPos;
            caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) === '' ?
                textFieldValue + '' : textFieldValue;
        }
        else if (textObj.setSelectionRange)
        {
            var rangeStart = textObj.selectionStart,
                rangeEnd = textObj.selectionEnd,
                tempStr1 = textObj.value.substring(0, rangeStart),
                tempStr2 = textObj.value.substring(rangeEnd);
            textObj.value = tempStr1 + textFieldValue + tempStr2;
            textObj.focus();
            var len = textFieldValue.length;
            textObj.setSelectionRange(rangeStart + len, rangeStart + len);
            textObj.blur();
        }
        else
        {
            textObj.value += textFieldValue;
        }
    },

    highText: function (searchWords, htmlTag, tagClass)
    {
        return this.each(function ()
        {
            $(this).html(function high(replaced, search, htmlTag, tagClass)
            {
                var pattarn = search.replace(/\b(\w+)\b/g, "($1)").replace(/\s+/g, "|");
                return replaced.replace(new RegExp(pattarn, "ig"), function (keyword)
                {
                    return $("<" + htmlTag + " class=" + tagClass + ">" + keyword + "</" + htmlTag + ">").outerHTML();
                });
                //$(this).text()换成$(this).html() 防止js标签转为字符
            }($(this).html(), searchWords, htmlTag, tagClass));
        });
    },

    outerHTML: function (s)
    {
        return (s) ? this.before(s).remove() : jQuery("<p>").append(this.eq(0).clone()).html();
    }
});

$(document).ready(function ()
{
    //定时任务执行脚本
    $.get(baseUrl+'/cron/run');
    //返回顶部
    if ($('.aw-back-top').length)
    {
        $(window).scroll(function ()
        {
            if ($(window).scrollTop() > ($(window).height() / 2))
            {
                $('.aw-back-top').fadeIn();
            }
            else
            {
                $('.aw-back-top').fadeOut();
            }
        });
    }

    $('.carousel').carousel();
    $('[data-toggle="popover"]').popover();
});

$(document).on('click','.aw-pjax-a a',function (){
    $(this).addClass('active');
    $(this).siblings().removeClass('active');
});

$(document).on('click','.aw-pjax-buttons a',function (){
    $(this).addClass('btn-primary text-white').removeClass('btn-outline-primary text-primary');
    $(this).siblings().removeClass('btn-primary text-white').addClass('btn-outline-primary text-primary');
});

//pjax标签导航点击
$(document).on('click','.aw-pjax-tabs li',function (){
    $(this).parent('.aw-pjax-tabs').find('li a').removeClass('active');
    $(this).find('a').addClass('active');
});

//发送短信
$(document).on('click', '[data-sms]', function (t) {
    let that = this;
    let mobile = $($(that).data('sms')).val();
    if(!mobile)
    {
        AWS_MOBILE.api.error('请输入手机号');
        return ;
    }

    if($.cookie("sms")){
        let count = $.cookie("sms");
        $(that).prop('disabled', true);
        $(that).text(count+'秒');
        let resend = setInterval(function(){
            count--;
            if (count > 0){
                $(that).text(count+'秒');
                $.cookie("captcha", count, {path: '/', expires: (1/86400)*count});
                $(that).addClass('sending');
            }else {
                $(that).prop('disabled', false);
                clearInterval(resend);
                $(that).text("获取验证码")
                $(that).removeClass('sending');
            }
        }, 1000);
    }

    if($(that).hasClass('sending')){
        return false;
    }

    $.ajax({
        url: baseUrl + '/ajax/sms/',
        type: "post",
        dataType: "json",
        data: {
            mobile: mobile,
        },
        success: function (result) {
            if(!result.code)
            {
                layer.msg(result.msg);
                return false;
            }
            var count = 60;
            var inl = setInterval(function () {
                $(that).prop('disabled', true);
                count -= 1;
                var text = count + ' 秒后重试';
                $.cookie("sms", count, {path: '/', expires: (1/86400)*count});
                $(that).addClass('sending');
                $(that).text(text);
                if (count <= 0) {
                    clearInterval(inl);
                    $(that).prop('disabled', false);
                    $(that).text('获取短信验证码');
                    $(that).removeClass('sending');
                }
            }, 1000);
            return layer.msg(result.msg);
        }
    });
});

//自定义拓展
(function ($){
    $.extend(
        {
            // 滚动到指定位置
            scrollTo : function (type, duration, options)
            {
                if (typeof type == 'object') {$(type).offset().top}
                $('html, body').animate({scrollTop: type}, {
                    duration: duration,
                    queue: options.queue
                });
            }
        })
})(jQuery);

function cutImg(obj){
    let image=new Image();
    image.src=obj.src;
    let $this=$(obj);
    let iWidth=$this.width();
    let iHeight=$this.height();
    if(image.width*iHeight!==1*iWidth*image.height){
        //原始图片的尺寸与CSS里固定的图片尺寸比例不一致，则进行处理
        if(image.width/image.height>=iWidth/iHeight){
            $this.height(iHeight+'px');
            $this.width((image.width*iHeight)/image.height+'px');
        }else{
            $this.width(iWidth+'px');
            $this.height((image.height*iWidth)/image.width+'px');
        }
        //用cite装起来，做出裁剪效果
        if(!$this.parent().is('cite'))
        {
            $this.wrap('<cite />');
        }
    }
}

$(document).ready(function ()
{
    //图片裁切
    let aci = $('.aw-cut-img')
    if (aci.length) {
        aci.each(function(){
            let image=new Image();
            image.src=this.src;
            if(image.complete){
                cutImg(this);
            } else{
                this.onload=function(){
                    cutImg(this);
                };
            }
        });
    }

    //灯箱效果
    let acimg = $(".aw-content img")
    if (acimg.length) {
        acimg.each(function(i) {
            if (!this.parentNode.href) {
                $(this).wrap("<a href='" + this.src + "' data-fancybox='fancybox' data-fancybox-group='aw-thumb' data-caption='" + this.alt + "'></a>")
            }
        })
    }

    /*用户头像提示box*/
    AWS.User.showCard('.aw-username, .aw-user-img, .aw-user-name','user');

    //话题小卡片
    AWS.User.showCard('.aw-topic , .aw-topic-img','topic');

    //小卡片mouseover
    $(document).on('mouseover', '#aw-card-tips', function ()
    {
        clearTimeout(AWS.config.card_box_hide_timer);
        $(this).show();
    });

    //私信
    $(document).on('click', '#inboxBox', function ()
    {
        var that = $(this);
        if(!that.find('dropdown-menu').is(":hidden"))
        {
            $.ajax({
                url:baseUrl+'/inbox/index?header=1',
                type: "GET",
                dataType: "html",
                success: function (ret) {
                    $('#topInboxBox').html(ret);
                },
            })
        }
    });

    //通知
    $(document).on('click', '#notifyBox', function ()
    {
        var that = $(this);
        if(!that.find('dropdown-menu').is(":hidden"))
        {
            $.ajax({
                url:baseUrl+'/notify/index?header=1',
                type: "GET",
                dataType: "html",
                success: function (ret) {
                    $('#topNotifyBox').html(ret);
                },
            })
        }
    });

    //小卡片mouseout
    $(document).on('mouseout', '#aw-card-tips', function ()
    {
        $(this).hide();
    });

    //用户小卡片关注更新缓存
    $(document).on('click', '.aw-card-tips-user .follow', function ()
    {
        let uid = $(this).parents('.aw-card-tips').find('.name').attr('data-id');
        $.each(AWS.config.cacheUserData, function (i, a)
        {
            if (a.match('data-id="' + uid + '"'))
            {
                if (AWS.config.cacheUserData.length === 1)
                {
                    AWS.config.cacheUserData = [];
                }
                else
                {
                    AWS.config.cacheUserData[i] = '';
                }
            }
        });
    });

    //搜索下拉
    AWS.Dropdown.bind_dropdown_list('#globalSearchInput', 'search');

    //返回顶部
    if ($('.aw-back-top').length)
    {
        $(window).scroll(function ()
        {
            if ($(window).scrollTop() > ($(window).height() / 2))
            {
                $('.aw-back-top').fadeIn();
            }
            else
            {
                $('.aw-back-top').fadeOut();
            }
        });
    }

    $("[data-fancybox]").fancybox({
        openEffect  : 'none',
        closeEffect : 'none',
        prevEffect : 'none',
        nextEffect : 'none',
        closeBtn  : false,
        helpers : {
            title : {
                type : 'inside'
            },
            buttons	: {}
        },
        afterLoad : function() {
            this.title = (this.index + 1) + ' / ' + this.group.length + (this.title ? ' - ' + this.title : '');
        }
    });

    //复制链接
    let clipboard = new Clipboard('.aw-clipboard');
    clipboard.on('success', function(e) {
        layer.msg('复制成功')
    });
    clipboard.on('error', function(e) {
        layer.msg('复制失败')
    });
});

// 附件下载
$(document).on('click', '.attach-download', function () {
    $('#attach-name').val($(this).data('name'))
    $('#attach-download-form').submit();
})

//附件删除

// 附件下载
$(document).on('click', '.aw-attach-delete', function () {
    var that =$(this);
    var attachId= $(this).data('id');
    var attachKey= $(this).data('key');
    AWS.api.post(baseUrl+'/upload/remove_attach',{id:attachId,access_key:attachKey},function (res){
        if(res.code>0)
        {
            that.parents('tr').detach();
        }else{
            layer.msg('附件删除失败');
        }
    })
})
