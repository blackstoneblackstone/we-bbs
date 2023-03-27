jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000))}else{date=options.expires}expires='; expires='+date.toUTCString()}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('')}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break}}}return cookieValue}};
(function(c){function g(){var b="<head><title>"+d.popTitle+"</title>";c(document).find("link").filter(function(){return"stylesheet"==c(this).attr("rel").toLowerCase()}).filter(function(){var a=c(this).attr("media");return void 0==a?!1:""==a.toLowerCase()||"print"==a.toLowerCase()}).each(function(){b+='<link type="text/css" rel="stylesheet" href="'+c(this).attr("href")+'" >'});return b+="</head>"}function h(b){return'<body><div class="'+c(b).attr("class")+'">'+c(b).html()+"</div></body>"}function k(b){c("input,select,textarea",b).each(function(){var a=c(this).attr("type");"radio"==a||"checkbox"==a?c(this).is(":not(:checked)")?this.removeAttribute("checked"):this.setAttribute("checked",!0):"text"==a?this.setAttribute("value",c(this).val()):"select-multiple"==a||"select-one"==a?c(this).find("option").each(function(){c(this).is(":not(:selected)")?this.removeAttribute("selected"):this.setAttribute("selected",!0)}):"textarea"==a&&(a=c(this).attr("value"),c.browser.mozilla?this.firstChild?this.firstChild.textContent=a:this.textContent=a:this.innerHTML=a)});return b}function l(){var b=d.id,a;try{a=document.createElement("iframe"),document.body.appendChild(a),c(a).attr({style:"border:0;position:absolute;width:0px;height:0px;left:0px;top:0px;",id:b,src:""}),a.doc=null,a.doc=a.contentDocument?a.contentDocument:a.contentWindow?a.contentWindow.document:a.document}catch(e){throw e+". iframes may not be supported in this browser.";}if(null==a.doc)throw"Cannot find document.";return a}function m(){var b;b="location=no,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=595px,height=842px,top=0,left=0,toolbar=no,scrollbars=no,personalbar=no"+(",resizable=yes,screenX="+d.popX+",screenY="+d.popY+"");b=window.open("","_blank",b);b.doc=b.document;return b}var f=0,n={mode:"iframe",popHt:500,popWd:400,popX:200,popY:200,popTitle:"",popClose:!1},d={};c.fn.printArea=function(b){c.extend(d,n,b);f++;c("[id^=printArea_]").remove();b=k(c(this));d.id="printArea_"+f;var a,e;switch(d.mode){case"iframe":e=new l;a=e.doc;e=e.contentWindow||e;break;case"popup":e=new m,a=e.doc}a.open();a.write(("iframe"!=d.mode&&d.strict?'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01'+(0==d.strict?" Trasitional":"")+'//EN" "http://www.w3.org/TR/html4/'+(0==d.strict?"loose":"strict")+'.dtd">':"")+"<html>"+g()+h(b)+"</html>");a.close();e.focus();e.print();"popup"==d.mode&&d.popClose&&e.close()}})(jQuery);

//全局pjax方法
if($.support.pjax)
{
    $.pjax.defaults.timeout = 1200;
    $(document).on('click', 'a[data-pjax]', function(event) {
        aui.showload({msg: "加载中"});
        let container = $(this).attr('data-pjax')
        let containerSelector = '#' + container;
        $.pjax.defaults.fragment = containerSelector;
        aui.hideload();
        $.pjax.click(event, {container: containerSelector,scrollTo:container});
    })
    $(document).on('pjax:timeout', function(event) {
        event.preventDefault();
    })
}

var AWS_MOBILE = {
    api:{
        /**
         * 发送Ajax请求
         * @param options
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
        ajax: function (options, success, error)
        {
            options = typeof options === 'string' ? {url: options} : options;
            options.url = options.url + (options.url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            options = $.extend({
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: "json",
                success: function (ret) {
                    if (typeof success === 'function') {
                        success(ret);
                    } else {
                        AWS_MOBILE.events.onAjaxSuccess(ret, success);
                    }
                },
                error: function (xhr) {
                    if (typeof error === 'function') {
                        error(xhr);
                    } else {
                        var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                        AWS_MOBILE.events.onAjaxError(ret, error);
                    }
                }
            }, options);
            return $.ajax(options);
        },

        /**
         * ajax POST提交
         * @param url
         * @param data
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
        post: function (url, data, success, error) {
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            return $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (ret) {
                    if (ret.code === 1) {
                        if (typeof success === 'function') {
                            success(ret);
                        } else {
                            AWS_MOBILE.events.onAjaxSuccess(ret, success);
                        }
                    } else {
                        if (typeof error === 'function') {
                            error(ret);
                        } else {
                            AWS_MOBILE.events.onAjaxError(ret, error);
                        }
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_MOBILE.events.onAjaxError(ret, error);
                }
            });
        },

        /**
         * ajax表单提交
         * @param element 表单标识
         * @param success 成功回调
         */
        ajaxForm:function (element,success){
            let url = $(element).attr('action');
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(element).serialize(),
                success: function (result)
                {
                    if (typeof success != 'function') {
                        let msg = result.msg ? result.msg : '操作成功';
                        if (result.code > 0) {
                            AWS_MOBILE.api.success(msg, result.url)
                        } else {
                            AWS_MOBILE.api.error(msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },


        /**
         * ajax GET提交
         * @param url
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
        get: function (url, success, error) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                /*beforeSend: function(){
                    layer.load();
                },*/
                success: function (ret) {
                    if (typeof success != 'function') {
                        if (result.code > 0) {
                            AWS_MOBILE.api.success(result.msg, result.url)
                        } else {
                            AWS_MOBILE.api.error(result.msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_MOBILE.events.onAjaxError(ret, error);
                }
            });
        },

        success: function (message, url)
        {
            if(message)
            {
                aui.toast({
                    msg: message,
                    location: "bottom"
                },function(ret){
                    if (typeof url !== 'undefined' && url) {
                        window.location.href = url;
                    }
                });
            }else{
                aui.toast({
                    msg: '提示信息',
                    location: "bottom"
                },function(ret){
                    if (typeof url !== 'undefined' && url) {
                        window.location.href = url;
                    }
                });
            }
        },
        // 提示并刷新
        tipsRefresh: function(msg) {
            aui.toast({
                msg: msg,
                location: "middle"
            },function() {
                window.location.reload()
            });
        },
        error: function (message, url) {
            if(message)
            {
                aui.toast({
                    msg: message,
                    location: "bottom"
                },function(ret){
                    if (typeof url !== 'undefined' && url) {
                        window.location.href = url;
                    }
                });
            }else{
                aui.toast({
                    msg: message,
                    location: "bottom"
                },function(ret){
                    if (typeof url !== 'undefined' && url) {
                        window.location.href = url;
                    }
                });
            }
        },

        MeScroll:function (wrap,container,url,param,perPage,callBack){
            var meScroll = new MeScroll(wrap, {
                down: {
                    callback: function (){
                        meScroll.resetUpScroll();
                    }
                },
                up: {
                    callback: function (page) {
                        var pageNum = page.num;
                        $.ajax({
                            url: url+'?page='+pageNum,
                            type:"POST",
                            data:param,
                            success: function(result) {
                                if (typeof callBack === 'function') {
                                    callBack(result);
                                } else {
                                    var curPageData = result.data.list || result.data.data;
                                    var totalPage = result.data.total;
                                    meScroll.endByPage(curPageData.length, totalPage)
                                    if(pageNum == 1){
                                        $(container).empty();
                                    }
                                    $(container).append(result.data.html);
                                }
                            },
                            error: function(e) {
                                //联网失败的回调,隐藏下拉刷新和上拉加载的状态
                                meScroll.endErr();
                            }
                        });
                    }, //上拉加载的回调
                    //以下是一些常用的配置,当然不写也可以的.
                    page: {
                        num: 0, //当前页 默认0,回调之前会加1; 即callback(page)会从1开始
                        size: perPage //每页数据条数,默认10
                    },
                    htmlNodata: '<p class="nodata">-- 暂无更多数据 --</p>',
                    noMoreSize: 5, //如果列表已无数据,可设置列表的总数量要大于5才显示无更多数据;避免列表数据过少(比如只有一条数据),显示无更多数据会不好看这就是为什么无更多数据有时候不显示的原因.
                    toTop: {
                        //回到顶部按钮
                        src: "/static/common/image/back_top.png", //图片路径,默认null,支持网络图
                        offset: 1000 //列表滚动1000px才显示回到顶部按钮
                    },
                    empty: {
                        //列表第一页无任何数据时,显示的空提示布局; 需配置warpId才显示
                        warpId:	"ajaxPage", //父布局的id (1.3.5版本支持传入dom元素)
                        icon: "/static/common/image/no-data.png", //图标,默认null,支持网络图
                        tip: "暂无相关数据~" //提示
                    },
                    lazyLoad: {
                        use: true, // 是否开启懒加载,默认false
                        attr: 'url' // 标签中网络图的属性名 : <img imgurl='网络图  src='占位图''/>
                    }
                }
            });
            return meScroll;
        },

        //打开侧边菜单
        openMenu:function() {
            aui.sidemenu.open({
                moveType:'main-move',
                speed: 10,
            }).then(function (ret) {});
        },

        //关闭侧边菜单
        closeMenu:function() {
            aui.sidemenu.close({speed: 10}).then(function (ret) {});
        },

        //关闭弹窗
        closeOpen:function ()
        {
            $('#aw-ajax-box').hide();
            $('#aw-ajax-box #aw-mask').hide();
            $('#aw-ajax-box .ajaxBoxContent').html();
            $('#aw-ajax-box .ajaxBoxTitle .title').text();

            //恢复底层滚动
            var contentStyle = document.getElementById("mobileMain").style;
            var scrollTop = $('#mobileMain').attr('data-top');
            contentStyle.top = '0px';
            contentStyle.position ="static";
            $(document).scrollTop(scrollTop);
        },

        open: function (url, title, options) {
            title = options && options.title ? options.title : (title ? title : "");
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1";
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (ret) {
                    AWS_MOBILE.api.dialog(title, ret)
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_MOBILE.events.onAjaxError(ret);
                }
            });
        },

        // 模态框弹窗
        dialog: function (title, ret) {
            $('#aw-ajax-box').show();
            $('#aw-ajax-box #aw-mask').show();
            if (title) {
                $('#aw-ajax-box .ajaxBoxTitle .title').text(title)
            } else {
                $('#aw-ajax-box .ajaxBoxTitle .title').detach();
            }
            $('#aw-ajax-box .ajaxBoxContent').html(ret);

            //禁止底层滚动
            var scrollTop = document.body.scrollTop;
            $('#mobileMain').attr('data-top',scrollTop);
            var contentStyle = document.getElementById("mobileMain").style;
            contentStyle.position = 'fixed';
            contentStyle.top = "-"+scrollTop+"px";
            contentStyle.width = '100%';
            //关闭弹窗
            $(document).on('click', '.closeAjaxOpen,#aw-mask', function(event) {
                AWS_MOBILE.api.closeOpen();
            })
        },

        copyUrl:function (url){
            aui.copy(url);
            aui.toast({msg: '复制成功'});
        }
    },

    events: {
        //请求成功的回调
        onAjaxSuccess: function (ret, onAjaxSuccess) {
            let data = typeof ret.data !== 'undefined' ? ret.data : null;
            let url = typeof ret.url !== 'undefined' ? ret.url : null;
            let msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '';
            if (typeof onAjaxSuccess === 'function') {
                var result = onAjaxSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            AWS_MOBILE.api.success(msg, url);
        },
        //请求错误的回调
        onAjaxError: function (ret, onAjaxError) {
            let data = typeof ret.data !== 'undefined' ? ret.data : null;
            let url = typeof ret.url !== 'undefined' ? ret.url : null;
            let msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '';
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            AWS_MOBILE.api.error(msg, url);
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            response = typeof response === 'object' ? response : JSON.parse(response);
            return response;
        }
    },
    
    User:{
        login:function (){
            AWS_MOBILE.api.open(baseUrl+'/account/login','用户登录');
            //AWS_MOBILE.api.error('您还未登录,请先登录');
        },
        /**
         * 用户赞同
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        agree:function (element,itemType,itemId) {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            let that = $(element);
            let hasClass = that.hasClass('active') ? 1 : 0;
            let voteValue = hasClass ? 0 : 1;
            $.ajax({
                url: baseUrl+'/ajax/set_vote/',
                dataType: 'json',
                type: 'post',
                data: {
                    item_id: itemId,
                    item_type: itemType,
                    vote_value: voteValue
                },
                success: function (result) {
                    let value = result.data.vote_value;
                    if (result.code) {
                        if (!value && hasClass) {
                            that.removeClass('active');
                        }
                        if (!value && !hasClass) {
                            that.parents('.dz').find('.aw-ajax-against').removeClass('active');
                            that.parents('div').find('.aw-ajax-against').removeClass('active');
                        }
                        if (value === 1) {
                            that.addClass('active');
                            that.css('color','#a68ad4 !important');
                            that.parents('.dz').find('.aw-ajax-against').removeClass('active');
                            that.parents('div').find('.aw-ajax-against').removeClass('active');
                        }
                        that.find('span').text(result.data.agree_count);
                        AWS_MOBILE.api.success(result.msg);
                    }else{
                        AWS_MOBILE.api.error(result.msg);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.msg('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        /**
         * 用户反对
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        against:function (element,itemType,itemId){
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            let that = $(element);
            let hasClass = that.hasClass('active') ? 1 : 0;
            let voteValue = hasClass ? 0 : -1;

            $.ajax({
                url: baseUrl+'/ajax/set_vote/',
                dataType: 'json',
                type: 'post',
                data: {
                    item_id: itemId,
                    item_type: itemType,
                    vote_value: voteValue
                },
                success: function (result) {
                    let value = result.data.vote_value;
                    if (result.code>0) {
                        if (!value && hasClass) {
                            that.removeClass('active');
                        }
                        if (!value && !hasClass) {
                            that.parents('.dz').find('.aw-ajax-agree').removeClass('active');
                            that.parents('.dz').find('.aw-ajax-agree').find('span').text(result.data.agree_count)

                            that.parents('div').find('.aw-ajax-agree').removeClass('active');
                            that.parents('div').find('.aw-ajax-agree').find('span').text(result.data.agree_count)
                        }
                        if (value === -1) {
                            that.addClass('active');
                            that.css('color','#a68ad4 !important');
                            that.parents('.actions').find('.aw-ajax-agree').removeClass('active');
                            that.parents('.actions').find('.aw-ajax-agree').find('span').text(result.data.agree_count)

                            that.parents('div').find('.aw-ajax-agree').removeClass('active');
                            that.parents('div').find('.aw-ajax-agree').find('span').text(result.data.agree_count)
                        }
                        AWS_MOBILE.api.success(result.msg);
                    }else{
                        AWS_MOBILE.api.error(result.msg);
                    }
                }
            });
        },

        /**
         * 感谢回答
         * @param element
         * @param itemId
         * @returns {boolean|*|jQuery}
         */
        thanks:function (element,itemId)
        {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            return AWS_MOBILE.api.ajax(baseUrl+'/question/thanks?id='+itemId,function (res){
                if(res.code)
                {
                    $(element).attr('onclick','javascript:;').addClass('active');
                    $(element).find('span').text('已感谢');
                }
                AWS_MOBILE.api.error(res.msg);
            });
        },
        /**
         * 全局关注
         * @param element
         * @param type
         * @param id
         */
        focus:function (element,type,id){
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            let that = $(element);
            switch (type) {
                case 'topic':
                case 'user':
                case 'question':
                case 'column':
                case 'favorite':
                default:
                    AWS_MOBILE.api.post(
                        baseUrl+'/ajax/update_focus/',
                        {id: id, type: type},
                        function (res) {
                            if (res.code) {
                                if(that.hasClass('ygz'))
                                {
                                    that.removeClass('ygz').addClass('gz');
                                    that.text('关注');
                                    AWS_MOBILE.api.success('取消关注成功');
                                }else {
                                    that.addClass('ygz').removeClass('gz');
                                    that.text('已关注');
                                    AWS_MOBILE.api.success('关注成功');
                                }

                                if(that.parent().find('.focus-count'))
                                {
                                    that.parent().find('.focus-count').text(res.data.count);
                                }
                            }
                        });
                    break;
            }
        },

        /**
         * 保存草稿
         * @param element
         * @param item_type
         * @param itemId
         * @returns {boolean}
         */
        draft:function (element,item_type,itemId)
        {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }

            let form = $($(element).parents('form')[0]);
            let formData = {};
            let t = form.serializeArray();
            $.each(t, function() {
                formData[this.name] = this.value;
            });

            $.ajax({
                url:baseUrl + '/ajax/save_draft',
                dataType: 'json',
                type:'post',
                data:{
                    data:formData,
                    item_id:itemId,
                    item_type:item_type
                },
                success: function (result)
                {
                    let msg = result.msg ? result.msg : '保存成功';
                    if(result.code> 0)
                    {
                        AWS_MOBILE.api.success(msg)
                    }else{
                        AWS_MOBILE.api.error(msg)
                    }
                },
                error:  function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        report:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            let that = $(element);
            AWS_MOBILE.api.open(baseUrl+'/ajax/report?item_type='+itemType+'&item_id='+itemId,'内容举报',that.data())
        },

        favorite:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }

            return AWS_MOBILE.api.open(baseUrl+'/favorite/dialog?item_type='+itemType+'&item_id='+itemId,'用户收藏',{});
        },

        invite:function (element,itemId)
        {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            let that = $(element);
            AWS_MOBILE.api.open(baseUrl+'/ajax/invite?question_id='+itemId,'邀请用户');
        },
        /**
         * 删除通知
         * @param element
         * @param id
         */
        deleteNotify:function  (element,id)
        {
            AWS_MOBILE.api.post(baseUrl+'/notify/delete',{id:id},function (res){
                if(res.code) {
                    $(element).parents('dl').detach();
                    $(element).parents('.header-inbox-item').detach();
                    $('.header-notify-count').text(parseInt($('.header-notify-count').text())-1);
                }
            });
        },

        /**
         * 已读通知
         * @param element
         * @param id
         */
        readNotify:function(element,id)
        {
            AWS_MOBILE.api.post(baseUrl+'/notify/read',{id:id},function (res){
                if(res.code)
                {
                    $(element).hide();
                    $(element).parents('.header-inbox-item').detach();
                    if(parseInt($('#notifyUnreadTag').text()<=1))
                    {
                        $('#notifyUnreadTag').detach();
                    }else{
                        $('#notifyUnreadTag').text(parseInt($('#notifyUnreadTag').text())-1);
                    }
                }
            });
        },

        readAll:function ()
        {
            AWS_MOBILE.api.get(baseUrl+'/notify/read_all',function (res){
                window.location.reload();
            });
        },
        /**
         * 发送私信
         */
        inbox:function(user_name){
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }

            if(userName==user_name || user_name==userId)
            {
                return layer.msg('自己不可以给自己发送私信');
            }

            if(!user_name)
            {
                return AWS_MOBILE.api.open(baseUrl+'/ajax/inbox','新对话', {})
            }else{
                return AWS_MOBILE.api.open(baseUrl+'/ajax/inbox?user_name='+user_name,'与 '+user_name+ ' 对话', {})
            }
        },

        /**
         * 通用不感兴趣
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean|*}
         */
        uninterested:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                AWS_MOBILE.User.login();
                return false;
            }
            return AWS_MOBILE.api.post(baseUrl+'/ajax/uninterested',{id:itemId,type:itemType},function (res){
                if(res.code)
                {
                    $(element).parent().detach();
                }
                AWS_MOBILE.api.error(res.msg);
            });
        },

        share: function (title, url, desc, type) {
            let target_url;
            switch (type) {
                //分享QQ好友
                case 'qq':
                    target_url = 'http://connect.qq.com/widget/shareqq/index.html?url=' + url + '&sharesource=qzone&title=' + title + '&desc=' + desc;
                    window.open(target_url);
                    break;
                //分享新浪微博
                case 'weibo':
                    target_url = "https://service.weibo.com/share/share.php?url=" + + url + '&title=' + title;
                    window.open(target_url);
                    break;
                case 'qzone':
                    target_url = 'http://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' + url + '&title=' + title + '&desc=' + desc;
                    window.open(target_url);
                    break;

                case 'wechat':
                    if(aui.isWx()){
                        AWS_MOBILE.api.closeOpen();

                        /*aui.wxShare({
                            imgUrl: '', // 分享显示的缩略图地址
                            link:  url,    // 分享地址
                            title: title,   // 分享标题
                            desc: desc,     // 分享描述
                        });*/

                        return aui.wxShareModal({
                            mask: true,
                            touchClose: true
                        },function(ret){
                            console.log("请点击右上角进行分享");
                        });
                    }else{
                        return AWS_MOBILE.api.error('请在微信中打开使用')
                    }
                    break;
            }
        },

        shareBox:function (title, url)
        {
            var html =
                '<div class="text-center row py-3">' +
                '      <div class="col-3"><a href="javascript:;" onclick="AWS_MOBILE.api.copyUrl(\''+url+'\')"><i class="icon-link font-14"></i><br><span class="text-muted">链接</span></a></div> ' +
                '      <div class="col-3"><a href="javascript:;" onclick="AWS_MOBILE.User.share(\''+title+'\', \''+url+'\', \'\',\'weibo\')" ><i class="fab fa-weibo text-warning font-14"></i><br><span class="text-muted">微博</span></a></div> ' +
                '      <div class="col-3"><a href="javascript:;" onclick="AWS_MOBILE.User.share(\''+title+'\', \''+url+'\', \'\',\'qzone\')"><i class="fab fa-qq text-primary font-14"></i><br><span class="text-muted">腾讯</span></a></div>' +
                '      <div class="col-3"><a href="javascript:;" onclick="AWS_MOBILE.User.share(\''+title+'\', \''+url+'\', \'\',\'wechat\')"><i class="fab fa-weixin text-success font-14"></i><br><span class="text-muted">微信</span></a></div>' +
                '</div>';

           return AWS_MOBILE.api.dialog('分享',html);
        }
    },

    init: function (){
        //ajax获取
        $(document).on('click', '.aw-ajax-get,.ajax-get', function (e) {
            e.preventDefault();
            e.target.blur();
            let that = this;
            let options = $.extend({}, $(that).data() || {});
            if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
                options.url = $(that).attr("data-url");
            }
            let success = typeof options.success === 'function' ? options.success : null;
            let error = typeof options.error === 'function' ? options.error : null;
            delete options.success;
            delete options.error;

            if(options.login && !userId)
            {
                AWS_MOBILE.api.error('您还未登录,请登录后再操作!');
                return false;
            }

            if (options.confirm) {
                aui.confirm({
                    title: "", //可选
                    msg: options.confirm,
                    btns: ['确认', '取消']
                }, function (ret) {
                    if(ret.index==1)
                    {
                        if (typeof options.cancel !== 'undefined') {
                            AWS_MOBILE.api.ajax(options.cancel, success, error);
                        }
                    }else{
                        AWS_MOBILE.api.ajax(options.url, success, error);
                    }
                });
            } else {
                AWS_MOBILE.api.ajax(options.url, success, error);
            }
        });

        //弹窗点击
        $(document).on('click', '.ajax-open,.aw-ajax-open', function (e) {
            let that = this;
            let options = $(that).data();
            let url = $(that).data("url") ? $(that).data("url") : $(that).attr('href');
            let title = $(that).attr("title") || $(that).data("title") || $(that).data('original-title');
            if(options.login && !userId)
            {
                AWS_MOBILE.api.error('您还未登录,请登录后再操作!');
                return false;
            }
            AWS_MOBILE.api.open(url,title,options);
            return false;
        });

        //ajax表单提交不带验证
        $(document).on('click', '.aw-ajax-form', function (e) {
            let that = this;
            let options = $.extend({}, $(that).data() || {});
            let form = $($(that).parents('form')[0]);
            let success = typeof options.success === 'function' ? options.success : null;
            delete options.success;
            delete options.error;
            $.ajax({
                url: form.attr('action'),
                dataType: 'json',
                type: 'post',
                data: form.serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (result) {
                    if (typeof success !== 'function') {
                        let msg = result.msg ? result.msg : '操作成功';
                        if (result.code > 0) {
                            AWS_MOBILE.api.success(msg, result.url)
                        } else {
                            aui.toast({
                                msg: msg,
                                location: "bottom"
                            },function(ret){
                                if (typeof url !== 'undefined' && url) {
                                    window.location.href = url;
                                }
                            });
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        AWS_MOBILE.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        });
    },
    upload:{
        /**
         * 上传组件
         * @param listContainer 多文件内容器
         * @param filePicker 文件选择按钮
         * @param preview 图片预览显示容器
         * @param field 字段名称
         * @param more 多选上传
         * @param type 上传类型
         * @param path 上传路径
         */
        webUpload : function (filePicker, preview, field, path, type, more, listContainer) {
            type = type || 'img';
            path = path || 'common';
            let upload_allowExt,size;
            if(type==='img')
            {
                upload_allowExt = upload_image_ext.replace(/\|/g, ",");
            }else{
                upload_allowExt = upload_file_ext.replace(/\|/g, ",");
            }

            if (type==='img') {
                size = upload_image_size * 1024;
            } else {
                size = upload_file_size * 1024;
            }
            var $list = $("#" + listContainer + "");
            var GUID = WebUploader.Base.guid();                            // 一个GUID
            var uploader = WebUploader.create({
                auto: true,                                                // 选完文件后，是否自动上传。
                swf: '/static/libs/webuploader/uploader.swf',     // 加载swf文件，路径一定要对
                server: baseUrl+'/upload/index?upload_type=' + type+'&path='+path, // 文件接收服务端
                pick: '#' + filePicker,                              // 选择文件的按钮。可选。
                resize: false,                                             // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                chunked: true,                                             // 是否分片
                chunkSize: 5 * 1024 * 1024,                                // 分片大小
                threads: 1,                                                // 上传并发数
                formData: {
                    // 由于Http的无状态特征，在往服务器发送数据过程传递一个进入当前页面是生成的GUID作为标示
                    GUID: GUID,                                            // 自定义参数
                },
                compress: false,
                fileSingleSizeLimit: size,                                 // 限制大小200M，单文件
                //fileSizeLimit: allMaxSize*1024*1024,                     // 限制大小10M，所有被选文件，超出选择不上
                accept: {
                    title: '上传图片/文件',
                    extensions: upload_allowExt,                           // 允许上传的类型 'gif,jpg,jpeg,bmp,png'
                    mimeTypes: '*',                                        // 默认全部文件，为兼容上传文件功能，如只上传图片可写成img/*
                }
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $list,
                    $percent = $li.find('.progress .progress-bar');
                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<div class="progress progress-striped active">' +
                        '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                        '</div>' +
                        '</div>').appendTo($li).find('.progress-bar');
                }
                $percent.css('width', percentage * 100 + '%');
            });

            uploader.on('uploadSuccess', function (file, response) {
                if (response.code == 0) {
                    AWS_MOBILE.api.success(response.msg);
                }
                let url = response.url;
                if (more == true) {
                    var images = '<div class="row"><div class="col-6"><input type="text" name="' + field + '[]" value="' + url + '" class="form-control"/></div> <div class="col-3"><input class="form-control input-sm" type="text" name="' + field + '_title[]" value="' + file.name + '" ></div> <div class="col-xs-3"><button type="button" class="btn btn-block btn-warning remove_images">移除</button></div></div>';
                    var images_list = $('#more_images_' + field).html();

                    $('#more_images_' + field).html(images + images_list);

                } else {
                    $("input[name='" + field + "']").val(url);
                    $("#" + preview).attr('src', url);
                    $("#" + preview).parent("a").attr('href', url);
                }
            });
            uploader.on('uploadComplete', function (file) {
                $list.find('.progress').fadeOut();
            });
            // 错误提示
            uploader.on("error", function (type) {
                if (type == "Q_TYPE_DENIED") {
                    AWS_MOBILE.api.success('请上传' + upload_allowExt + '格式的文件！');
                } else if (type == "F_EXCEED_SIZE") {
                    AWS_MOBILE.api.success('单个文件大小不能超过' + size / 1024 + 'kb！');
                } else if (type == "F_DUPLICATE") {
                    AWS_MOBILE.api.success('请不要重复选择文件');
                } else {
                    AWS_MOBILE.api.success('上传出错！请检查后重新上传！错误代码' + type);
                }
            });
        },

        removeAttach:function (obj,attachId,access_key)
        {
            AWS_MOBILE.api.post(baseUrl+'/upload/remove_attach',{id:attachId,access_key:access_key},function (res){
                if(res.code)
                {
                    $(obj).parents('tr').detach();
                }
                AWS_MOBILE.api.success(res.msg);
            });

            return false;
        },

        attachUpload:function (elem,bindAction,uploadList,path,access_key,id)
        {
            if(!id)
            {
                $(uploadList).hide();
                $(bindAction).hide();
            }
            var uploadListIns = layui.upload.render({
                elem: elem
                ,elemList: $(uploadList).find('tbody')
                ,url: baseUrl + "/upload/index?path=" + path +'&access_key='+access_key
                ,accept: 'file'
                ,multiple: true
                ,number: 3
                ,auto: false
                ,bindAction: bindAction
                ,choose: function(obj){
                    var that = this;
                    var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                    //读取本地文件
                    obj.preview(function(index, file, result){
                        $(uploadList).show();
                        $(bindAction).show();
                        var tr = $(['<tr id="upload-'+ index +'">'
                            ,'<td>'+ file.name +'</td>'
                            ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                            ,'<td><div class="layui-progress" lay-filter="progress-demo-'+ index +'"><div class="layui-progress-bar" lay-percent=""></div></div></td>'
                            ,'<td>'
                            ,'<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                            ,'<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                            ,'</td>'
                            ,'</tr>'].join(''));

                        //单个重传
                        tr.find('.demo-reload').on('click', function(){
                            obj.upload(index, file);
                        });

                        //删除
                        tr.find('.demo-delete').on('click', function(){
                            delete files[index]; //删除对应的文件
                            tr.remove();
                            uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                        });

                        that.elemList.append(tr);
                        layui.element.render('progress'); //渲染新加的进度条组件
                    });
                }
                ,done: function(res, index, upload){ //成功的回调
                    var that = this;
                    if(res.code === 1){ //上传成功
                        var tr = that.elemList.find('tr#upload-'+ index),tds = tr.children();
                        tds.eq(3).html('上传成功'); //清空操作
                        delete this.files[index]; //删除文件队列已经上传成功的文件
                        return;
                    }
                    this.error(index, upload);
                }
                ,allDone: function(obj){ //多文件上传完毕后的状态回调
                    console.log(obj)
                }
                ,error: function(index, upload){ //错误回调
                    var that = this;
                    var tr = that.elemList.find('tr#upload-'+ index)
                        ,tds = tr.children();
                    tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
                }
                ,progress: function(n, elem, e, index){
                    layui.element.progress('progress-demo-'+ index, n + '%');
                }
            });
        },

        upload:function(list, filePicker_image, image_preview, image, more, upload_allowext, size, type, path) {
            if (upload_allowext) {
                upload_allowext = upload_allowext.replace(/\|/g, ",");
            }
            if (size) {
                size = size * 1024;
            } else {
                size = 10240 * 1024 * 1024;
            }
            type = type || 'img';
            path = path || 'common';
            var $list = $("#" + list + "");
            var GUID = WebUploader.Base.guid();                            // 一个GUID
            var uploader = WebUploader.create({
                auto: true,                                                // 选完文件后，是否自动上传。
                swf: '/static/js/plugins/webuploader-0.1.5/uploader.swf',     // 加载swf文件，路径一定要对
                server: '/upload/index' + '?upload_type=' + type+'&path='+path, // 文件接收服务端
                pick: '#' + filePicker_image,                              // 选择文件的按钮。可选。
                resize: false,                                             // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                chunked: true,                                             // 是否分片
                chunkSize: 5 * 1024 * 1024,                                // 分片大小
                threads: 1,                                                // 上传并发数
                formData: {
                    // 由于Http的无状态特征，在往服务器发送数据过程传递一个进入当前页面是生成的GUID作为标示
                    GUID: GUID,                                            // 自定义参数
                },
                compress: false,
                fileSingleSizeLimit: size,                                 // 限制大小200M，单文件
                //fileSizeLimit: allMaxSize*1024*1024,                     // 限制大小10M，所有被选文件，超出选择不上
                accept: {
                    title: '上传图片/文件',
                    extensions: upload_allowext,                           // 允许上传的类型 'gif,jpg,jpeg,bmp,png'
                    mimeTypes: '*',                                        // 默认全部文件，为兼容上传文件功能，如只上传图片可写成img/*
                }
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $list,
                    $percent = $li.find('.progress .progress-bar');
                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<div class="progress progress-striped active">' +
                        '<div class="progress-bar" role="progressbar" style="width: 0%">' +
                        '</div>' +
                        '</div>').appendTo($li).find('.progress-bar');
                }
                //$li.find('p.state').text('上传中');
                $percent.css('width', percentage * 100 + '%');
            });
            uploader.on('uploadSuccess', function (file, response) {
                if (response.code === 0) {
                    AWS_MOBILE.api.success(response.msg);
                }
                var url = response.url;
                if (more === true) {
                    var images = '<div class="row"><div class="col-6"><input type="text" name="' + image + '[]" value="' + url + '" class="form-control"/></div> <div class="col-3"><input class="form-control input-sm" type="text" name="' + image + '_title[]" value="' + file.name + '" ></div> <div class="col-xs-3"><button type="button" class="btn btn-block btn-warning remove_images">移除</button></div></div>';
                    var images_list = $('#more_images_' + image).html();

                    $('#more_images_' + image).html(images + images_list);

                } else {
                    $("input[name='" + image + "']").val(url);
                    $("#" + image_preview).attr('src', url);
                    $("#" + image_preview).parent("a").attr('href', url);
                }
            });
            uploader.on('uploadComplete', function (file) {
                $list.find('.progress').fadeOut();
            });
            // 错误提示
            uploader.on("error", function (type) {
                if (type === "Q_TYPE_DENIED") {
                    AWS_MOBILE.api.success('请上传' + upload_allowext + '格式的文件！');
                } else if (type === "F_EXCEED_SIZE") {
                    AWS_MOBILE.api.success('单个文件大小不能超过' + size / 1024 + 'kb！');
                } else if (type === "F_DUPLICATE",{time: 500}) {
                    AWS_MOBILE.api.success('请不要重复选择文件');
                } else {
                    AWS_MOBILE.api.success('上传出错！请检查后重新上传！错误代码' + typ);
                }
            });
        }
    },

    // 通用方法封装处理
    common: {
        // 判断字符串是否为空
        isEmpty: function (value) {
            return value == null || this.trim(value) === "";

        },
        // 判断一个字符串是否为非空串
        isNotEmpty: function (value) {
            return !AWS.common.isEmpty(value);
        },
        // 空格截取
        trim: function (value) {
            if (value == null) {
                return "";
            }
            return value.toString().replace(/(^\s*)|(\s*$)|\r|\n/g, "");
        },
        // 比较两个字符串（大小写敏感）
        equals: function (str, that) {
            return str === that;
        },
        // 比较两个字符串（大小写不敏感）
        equalsIgnoreCase: function (str, that) {
            return String(str).toUpperCase() === String(that).toUpperCase();
        },
        // 将字符串按指定字符分割
        split: function (str, sep, maxLen) {
            if (AWS.common.isEmpty(str)) {
                return null;
            }
            var value = String(str).split(sep);
            return maxLen ? value.slice(0, maxLen - 1) : value;
        },
        // 字符串格式化(%s )
        sprintf: function (str) {
            var args = arguments, flag = true, i = 1;
            str = str.replace(/%s/g, function () {
                var arg = args[i++];
                if (typeof arg === 'undefined') {
                    flag = false;
                    return '';
                }
                return arg;
            });
            return flag ? str : '';
        },
        // 数组去重
        uniqueFn: function (array) {
            var result = [];
            var hashObj = {};
            for (var i = 0; i < array.length; i++) {
                if (!hashObj[array[i]]) {
                    hashObj[array[i]] = true;
                    result.push(array[i]);
                }
            }
            return result;
        },
        // 获取form下所有的字段并转换为json对象
        formToJSON: function (formId) {
            var json = {};
            $.each($("#" + formId).serializeArray(), function (i, field) {
                json[field.name] = field.value;
            });
            return json;
        },
        // pjax跳转页
        jump: function (url) {
            $.pjax({url: url, container: '#aw-wrap'})
        },
        // 序列化表单，不含空元素
        serializeRemoveNull: function (serStr) {
            return serStr.split("&").filter(function (item) {
                    var itemArr = item.split('=');
                    if (itemArr[1]) {
                        return item;
                    }
                }
            ).join("&");
        },
        isMobile:function () {
            let userAgentInfo = navigator.userAgent;
            let mobileAgents = [ "Android", "iPhone", "SymbianOS", "Windows Phone", "iPad","iPod"];
            let mobile_flag = false;
            //根据userAgent判断是否是手机
            for (let v = 0; v < mobileAgents.length; v++) {
                if (userAgentInfo.indexOf(mobileAgents[v]) > 0) {
                    mobile_flag = true;
                    break;
                }
            }
            let screen_width = window.screen.width;
            let screen_height = window.screen.height;
            //根据屏幕分辨率判断是否是手机
            if(screen_width < 500 && screen_height < 800){
                mobile_flag = true;
            }
            return mobile_flag;
        },
        isPhoneNumber:function(phone)
        {
            var flag = false;
            var message = "";
            var myreg = /^(((13[0-9]{1})|(14[0-9]{1})|(17[0]{1})|(15[0-3]{1})|(15[5-9]{1})|(18[0-9]{1}))+\d{8})$/;
            if(phone == ''){
                message = "手机号码不能为空！";
            }else if(phone.length !=11){
                message = "请输入有效的手机号码！";
            }else if(!myreg.test(phone)){
                message = "请输入有效的手机号码！";
            }else{
                flag = true;
            }
            return flag
        },
        isEmail:function (email){
            var flag = false;
            var message = "";
            var myReg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
            if(email ===''){
                message = "邮箱不能为空！";
            }else if(!myReg.test(email)){
                message = "请输入有效的邮箱地址！";
            }else{
                flag = true;
            }
            return flag;
        },
        browser:function (){
            var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串
            var isOpera = userAgent.indexOf("Opera") > -1;
            if (isOpera) {
                return "Opera"
            }
            //判断是否Opera浏览器
            if (userAgent.indexOf("Firefox") > -1) {
                return "FF";
            }
            //判断是否Firefox浏览器
            if (userAgent.indexOf("Chrome") > -1){
                return "Chrome";
            }
            if (userAgent.indexOf("Safari") > -1) {
                return "Safari";
            }
            //判断是否Safari浏览器
            var u = window.navigator.userAgent.toLocaleLowerCase(),
                ie11 = /(trident)\/([\d.]+)/,
                b = u.match(ie11);
            if ((userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1 && !isOpera) || b) {
                var IE10 = false;
                var reIE = new RegExp("MSIE (\\d+\\.\\d+);");
                reIE.test(userAgent);
                var fIEVersion = parseFloat(RegExp["$1"]);
                IE10 = fIEVersion == 10.0;
                if (fIEVersion > 10 || fIEVersion == 10 || b) {
                    return "IE10";
                }else{
                    return "IE";
                }
            }
            return "no";
        },
        // 复制文本
        copyText: function (text) {
            let input = document.getElementById('copy-input')
            if (input == null) {
                input = document.createElement('input');
                input.setAttribute('id', 'copy-input')
                input.setAttribute('type', 'text')
                input.setAttribute('style', 'position: absolute;top: 0;left: 0;opacity: 0;z-index: -100;')
                document.body.appendChild(input)
                input = document.getElementById('copy-input')
            }

            input.value = text;
            input.select()
            document.execCommand("copy")
            AWS_MOBILE.api.success('复制成功')
        }
    },
}

AWS_MOBILE.init();