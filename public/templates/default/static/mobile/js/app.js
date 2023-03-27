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
    $(function(){
        setTimeout(function(){
            $('.aw-list-img').FlyZommImg({
                rollSpeed: 200,//切换速度
                miscellaneous: false,//是否显示底部辅助按钮
                closeBtn: true,//是否打开右上角关闭按钮
                hideClass: 'hide-preview',//不需要显示预览的 class
                imgQuality: 'original',//图片质量类型  thumb 缩略图  original 默认原图
                urlProperty:false,//原始图片
                //左滑动回调 两个参数 第一个动向 'left,firstClick,close' 第二个 当前操作DOM
                slitherCallback: function (direction, DOM) {
                    setTimeout(function(){
                        $('.fly-zoom-box-img').css('width','100%').css('height','auto').css('top',0).css('bottom',0).css('margin','auto');
                    },1)
                }
            });
        },500);
    })

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
                    AWS_MOBILE.api.error(result.msg);
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
                        $(that).text('获取验证码');
                        $(that).removeClass('sending');
                    }
                }, 1000);
                return AWS_MOBILE.api.error(result.msg);
            }
        });
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