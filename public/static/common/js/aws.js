jQuery.cookie=function(name,value,options){if(typeof value!='undefined'){options=options||{};if(value===null){value='';options.expires=-1}var expires='';if(options.expires&&(typeof options.expires=='number'||options.expires.toUTCString)){var date;if(typeof options.expires=='number'){date=new Date();date.setTime(date.getTime()+(options.expires*24*60*60*1000))}else{date=options.expires}expires='; expires='+date.toUTCString()}var path=options.path?'; path='+(options.path):'';var domain=options.domain?'; domain='+(options.domain):'';var secure=options.secure?'; secure':'';document.cookie=[name,'=',encodeURIComponent(value),expires,path,domain,secure].join('')}else{var cookieValue=null;if(document.cookie&&document.cookie!=''){var cookies=document.cookie.split(';');for(var i=0;i<cookies.length;i++){var cookie=jQuery.trim(cookies[i]);if(cookie.substring(0,name.length+1)==(name+'=')){cookieValue=decodeURIComponent(cookie.substring(name.length+1));break}}}return cookieValue}};
(function(c){function g(){var b="<head><title>"+d.popTitle+"</title>";c(document).find("link").filter(function(){return"stylesheet"==c(this).attr("rel").toLowerCase()}).filter(function(){var a=c(this).attr("media");return void 0==a?!1:""==a.toLowerCase()||"print"==a.toLowerCase()}).each(function(){b+='<link type="text/css" rel="stylesheet" href="'+c(this).attr("href")+'" >'});return b+="</head>"}function h(b){return'<body><div class="'+c(b).attr("class")+'">'+c(b).html()+"</div></body>"}function k(b){c("input,select,textarea",b).each(function(){var a=c(this).attr("type");"radio"==a||"checkbox"==a?c(this).is(":not(:checked)")?this.removeAttribute("checked"):this.setAttribute("checked",!0):"text"==a?this.setAttribute("value",c(this).val()):"select-multiple"==a||"select-one"==a?c(this).find("option").each(function(){c(this).is(":not(:selected)")?this.removeAttribute("selected"):this.setAttribute("selected",!0)}):"textarea"==a&&(a=c(this).attr("value"),c.browser.mozilla?this.firstChild?this.firstChild.textContent=a:this.textContent=a:this.innerHTML=a)});return b}function l(){var b=d.id,a;try{a=document.createElement("iframe"),document.body.appendChild(a),c(a).attr({style:"border:0;position:absolute;width:0px;height:0px;left:0px;top:0px;",id:b,src:""}),a.doc=null,a.doc=a.contentDocument?a.contentDocument:a.contentWindow?a.contentWindow.document:a.document}catch(e){throw e+". iframes may not be supported in this browser.";}if(null==a.doc)throw"Cannot find document.";return a}function m(){var b;b="location=no,statusbar=no,directories=no,menubar=no,titlebar=no,toolbar=no,dependent=no,width=595px,height=842px,top=0,left=0,toolbar=no,scrollbars=no,personalbar=no"+(",resizable=yes,screenX="+d.popX+",screenY="+d.popY+"");b=window.open("","_blank",b);b.doc=b.document;return b}var f=0,n={mode:"iframe",popHt:500,popWd:400,popX:200,popY:200,popTitle:"",popClose:!1},d={};c.fn.printArea=function(b){c.extend(d,n,b);f++;c("[id^=printArea_]").remove();b=k(c(this));d.id="printArea_"+f;var a,e;switch(d.mode){case"iframe":e=new l;a=e.doc;e=e.contentWindow||e;break;case"popup":e=new m,a=e.doc}a.open();a.write(("iframe"!=d.mode&&d.strict?'<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01'+(0==d.strict?" Trasitional":"")+'//EN" "http://www.w3.org/TR/html4/'+(0==d.strict?"loose":"strict")+'.dtd">':"")+"<html>"+g()+h(b)+"</html>");a.close();e.focus();e.print();"popup"==d.mode&&d.popClose&&e.close()}})(jQuery);
var AWS = {
    config: {
        cacheUserData: [],
        cashTopicData: [],
        card_box_hide_timer: '',
        card_box_show_timer: '',
        dropdown_list_xhr: '',
        loading_timer: '',
        loading_bg_count: 12,
        loading_mini_bg_count: 9,
        notification_timer: '',
        time: {
            waitTime: 3000
        }
    },

    api: {
        /**
         * 加载更多
         * @param element
         * @param url
         * @param data
         * @param callback
         * @param dataType
         */
        ajaxLoadMore: function (element, url, data, callback,dataType) {
            url = url ? url : $(element).data('url');
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            let isAuto =  true;
            layui.flow.load({
                isAuto: isAuto,
                elem: element,
                done: function (page, next) {
                    url = url + (url.indexOf("?") > -1 ? "&" : "?") + "page=" + page;
                    $.ajax({
                        type: data ? 'POST' : 'GET',
                        url: url,
                        data: data,
                        dataType: dataType ? dataType : '',
                        success: function (res) {
                            if (typeof (callback) != 'function') {
                                if(res.data!= undefined)
                                {
                                    var total = res.data.last_page;
                                    next(res.data.html, page < total);
                                }else{
                                    var total = $($(res)[0]).data('total');
                                    next(res, page < total);
                                }
                            } else {
                                callback(res,page, next);
                            }
                        }
                    });
                }
            });
        },

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
                        AWS.events.onAjaxSuccess(ret, success);
                    }
                },
                error: function (xhr) {
                    if (typeof error === 'function') {
                        error(xhr);
                    } else {
                        var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                        AWS.events.onAjaxError(ret, error);
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
                            AWS.events.onAjaxSuccess(ret, success);
                        }
                    } else {
                        if (typeof error === 'function') {
                            error(ret);
                        } else {
                            AWS.events.onAjaxError(ret, error);
                        }
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS.events.onAjaxError(ret, error);
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
        get: function (url, success, error,dataType) {
            $.ajax({
                type: 'GET',
                url: url,
                dataType: dataType ? dataType : 'json',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                /*beforeSend: function(){
                    layer.load();
                },*/
                success: function (ret) {
                    if (typeof success != 'function') {
                        if (result.code > 0) {
                            AWS.api.success(result.msg, result.url)
                        } else {
                            AWS.api.error(result.msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS.events.onAjaxError(ret, error);
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
                            AWS.api.success(msg, result.url)
                        } else {
                            AWS.api.error(msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        success: function (message, url)
        {
            if(message)
            {
                layer.msg(message,function (){
                    parent.layer.closeAll() || layer.closeAll();
                    if (typeof url !== 'undefined' && url) {
                        window.location.href = url;
                    }
                })
            }else{
                parent.layer.closeAll() || layer.closeAll();
                if (typeof url !== 'undefined' && url) {
                    window.location.href = url;
                }
            }
        },

        error: function (message, url) {
            if(message)
            {
                var index =layer.alert(message,{
                    yes:function (){
                        layer.close(index);
                        if (typeof url !== 'undefined' && url) {
                            window.location.href = url;
                        }
                    }
                })
            }else{
                if (typeof url !== 'undefined' && url) {
                    window.location.href = url;
                }
            }
        },

        msg: function (message, url) {
            layer.msg(message,{},function (){
                if (typeof url !== 'undefined' && url) {
                    parent.layer.closeAll() || layer.closeAll();
                    window.location.href = url;
                }else{
                    window.location.reload();
                }
            })
        },

        /**
         * 打开一个弹出窗口
         * @param url
         * @param title
         * @param options
         * @returns {*}
         */
        open: function (url, title, options) {
            title = options && options.title ? options.title : (title ? title : "");
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1";
            let width = options.width ? options.width : $(window).width() > 600 ? '600px' : '95%';
            let height = options.height ? options.height : $(window).height() > 550 ? '550px' : '90%';
            let area = !options.height ? [width] :[width, height];
            let max = options.max ? true : false;
            options = $.extend({
                type: 2,
                title: title,
                shadeClose: true,
                scrollbar: false,
                shade: 0.7,
                maxmin: max,
                moveOut: true,
                area: area,
                content: url,
                success: function (layero, index) {
                    var that = this;
                    var iframe = $(layero).find('iframe');
                    //检测弹窗是否是提示信息
                    var text = iframe.find('body').text();
                    if (text.indexOf('"code":0') != -1 || text.indexOf('"code":1') != -1) {
                        var result = JSON.parse(text);
                        parent.layer.close(index);
                        return layer.msg(result.msg,{time: 500});
                    }
                    layer.setTop(layero);
                    $(layero).data("callback", that.callback);
                    if ($(layero).height() > $(window).height()) {
                        //当弹出窗口大于浏览器可视高度时,重定位
                        layer.style(index, {
                            top: 0,
                            height: $(window).height()
                        });
                    }
                    var iframe = $(layero).find('iframe');
                    //设定iframe的高度为当前iframe内body的高度
                    iframe.css('height', iframe[0].contentDocument.body.offsetHeight);
                    //重新调整弹出层的位置，保证弹出层在当前屏幕的中间位置
                    $(layero).css('top', (window.innerHeight - iframe[0].offsetHeight) / 2);
                    layer.iframeAuto(index);
                }
            }, options ? options : {});
            return layer.open(options);
        },

        ajaxModal:function (url,title,width){
            if(!title) title='';
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1";
            width = width ? width : ($(window).width() > 600 ? '600px' : '95%');
            $.ajax({
                type: 'GET',
                url: url,
                dataType: 'html',
                success: function (ret) {
                    layer.open({
                        title: title,
                        type: 1,
                        fix: false, //不固定
                        maxmin: false,
                        shade: 0.8,
                        area:width,
                        content: ret,
                        scrollbar: false,
                        moveOut: true,
                        success: function (layero, index) {
                            layer.iframeAuto(index);
                        },
                    });
                },
            });
        },

        htmlModal:function (element,title,width)
        {
            if(!title) title='';
            width = width ? width : ($(window).width() > 600 ? '600px' : '95%');
            layer.open({
                title: title,
                type: 1,
                fix: false, //不固定
                maxmin: false,
                area:width,
                shade: 0.8,
                content: $(element).html(),
                scrollbar: false,
                moveOut: true,
                success: function (layero, index) {
                    layer.iframeAuto(index);
                },
            });
        },

        //post方式打开
        postOpen: function (url, title, data, options) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'html',
                success: function () {
                    return AWS.api.open(url, title, options);
                }
            });
        },
        downLoad: function (url,data) {
            $.ajax({
                url: url,
                type: "POST",
                data: data,
                success: function (response, status, request) {
                    var disp = request.getResponseHeader('Content-Disposition');
                    if (disp && disp.search('attachment') !== -1) {  //判断是否为文件
                        var form = $('<form method="POST" action="' + url + '">');
                        $.each(data, function (k, v) {
                            form.append($('<input type="hidden" name="' + k +
                                '" value="' + v + '">'));
                        });
                        $('body').append(form);
                        form.submit();
                    }
                }
            });
        },
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
            AWS.api.success(msg, url);
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
            AWS.api.error(msg, url);
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            response = typeof response === 'object' ? response : JSON.parse(response);
            return response;
        }
    },

    User:{
        /**
         * 切换语言
         */
        changeLang:function (lang)
        {
            AWS.api.post(baseUrl+'/ajax/changeLang',{lang:lang},function (res){
                if(res){
                    window.location.reload();
                }else{
                    layer.msg('语言包不存在');
                }
            })
        },
        /*登录*/
        login:function(){
            let that = this;
            let options = $.extend({}, $(that).data() || {});
            let url = baseUrl+'/account/login/'
            if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
                options.url = $(that).attr("data-url");
            }
            var width = AWS.common.isMobile() ? '90%' : '500px';
            return AWS.api.open(url,'',{width:width});
        },

        /*首次登录*/
        firstLogin:function(){
            return AWS.api.open(baseUrl+'/account/welcome_first_login','',{});
        },

        // @人功能
        at_user_lists: function(selector, limit) {
            $(selector).keyup(function (e) {
                var _this = $(this),
                    flag = _getCursorPosition($(this)[0]).start;
                if ($(this).val().charAt(flag - 1) == '@')
                {
                    _init();
                    $('#aw-ajax-box .content_cursor').html($(this).val().substring(0, flag));
                } else
                {
                    var lis = $('.aw-invite-dropdown li');
                    switch (e.which)
                    {
                        case 38:
                            var _index;
                            if (!lis.hasClass('active'))
                            {
                                lis.eq(lis.length - 1).addClass('active');
                            }
                            else
                            {
                                $.each(lis, function (i, e)
                                {
                                    if ($(this).hasClass('active'))
                                    {
                                        $(this).removeClass('active');
                                        if ($(this).index() == 0)
                                        {
                                            _index = lis.length - 1;
                                        }
                                        else
                                        {
                                            _index = $(this).index() - 1;
                                        }
                                    }
                                });
                                lis.eq(_index).addClass('active');
                            }
                            break;
                        case 40:
                            var _index;
                            if (!lis.hasClass('active'))
                            {
                                lis.eq(0).addClass('active');
                            }
                            else
                            {
                                $.each(lis, function (i, e)
                                {
                                    if ($(this).hasClass('active'))
                                    {
                                        $(this).removeClass('active');
                                        if ($(this).index() == lis.length - 1)
                                        {
                                            _index = 0;
                                        }
                                        else
                                        {
                                            _index = $(this).index() + 1;
                                        }
                                    }
                                });
                                lis.eq(_index).addClass('active');
                            }
                            break;
                        case 13:
                            $.each($('.aw-invite-dropdown li'), function (i, e)
                            {
                                if ($(this).hasClass('active'))
                                {
                                    $(this).click();
                                }
                            });
                            break;
                        default:
                            if ($('.aw-invite-dropdown')[0])
                            {
                                var ti = 0;
                                for (var i = flag; i > 0; i--)
                                {
                                    if ($(this).val().charAt(i) == "@")
                                    {
                                        ti = i;
                                        break;
                                    }
                                }
                                $.get(baseUrl + '/ajax.search/search_result/?type=users&q=' + encodeURIComponent($(this).val().substring(flag, ti).replace('@', '')) + '&limit=' + limit, function (result)
                                {
                                    var list = result.data.list;
                                    if ($('.aw-invite-dropdown')[0])
                                    {
                                        if (result.code != 0)
                                        {
                                            var html = '';
                                            $('.aw-invite-dropdown').html('');
                                            $.each(list, function (i, a)
                                            {
                                                html += '<li><img src="' + a.avatar + '"/><a>' + a.name + '</a></li>'
                                            });
                                            $('.aw-invite-dropdown').append(html);
                                            _display();
                                            $('.aw-invite-dropdown li').click(function ()
                                            {
                                                _this.val(_this.val().substring(0, ti) + '@' + $(this).find('a').text() + " ").focus();
                                                $('.aw-invite-dropdown').detach();
                                            });
                                        }
                                        else
                                        {
                                            $('.aw-invite-dropdown').hide();
                                        }
                                    }
                                    if (_this.val().length == 0)
                                    {
                                        $('.aw-invite-dropdown').hide();
                                    }
                                }, 'json');
                            }
                    }
                }
            });
            $(selector).keydown(function (e) {
                var key = e.which;
                if ($('.aw-invite-dropdown').is(':visible')) {
                    if (key == 38 || key == 40 || key == 13) {
                        return false;
                    }
                }
            });

            //初始化插入定位符
            function _init() {
                if (!$('.content_cursor')[0]) {
                    $('#aw-ajax-box').append('<span class="content_cursor"></span>');
                }
                var left = $(selector).offset().left,
                    top = $(selector).offset().top + $(selector).height();
                $('#aw-ajax-box').find('.content_cursor').css({
                    'left': parseInt($(selector).offset().left + parseInt($(selector).css('padding-left')) + 2),
                    'top': parseInt($(selector).offset().top + parseInt($(selector).css('padding-left'))-25)
                });

                if (!$('.aw-invite-dropdown')[0])
                {
                    $('#aw-ajax-box').append('<ul class="aw-invite-dropdown"></ul>');
                }
            };

            //初始化列表和三角型
            function _display() {
                $('.aw-invite-dropdown').css({
                    'left': $('.content_cursor').offset().left + $('.content_cursor').innerWidth(),
                    'top': $('.content_cursor').offset().top - 85
                }).show();
            };

            //获取当前textarea光标位置
            function _getCursorPosition(textarea)
            {
                var rangeData = {
                    text: "",
                    start: 0,
                    end: 0
                };

                textarea.focus();

                if (textarea.setSelectionRange) { // W3C
                    rangeData.start = textarea.selectionStart;
                    rangeData.end = textarea.selectionEnd;
                    rangeData.text = (rangeData.start != rangeData.end) ? textarea.value.substring(rangeData.start, rangeData.end) : "";
                } else if (document.selection) { // IE
                    var i,
                        oS = document.selection.createRange(),
                        // Don't: oR = textarea.createTextRange()
                        oR = document.body.createTextRange();
                    oR.moveToElementText(textarea);

                    rangeData.text = oS.text;
                    rangeData.bookmark = oS.getBookmark();

                    // object.moveStart(sUnit [, iCount])
                    // Return Value: Integer that returns the number of units moved.
                    for (i = 0; oR.compareEndPoints('StartToStart', oS) < 0 && oS.moveStart("character", -1) !== 0; i++) {
                        // Why? You can alert(textarea.value.length)
                        if (textarea.value.charAt(i) == '\n') {
                            i++;
                        }
                    }
                    rangeData.start = i;
                    rangeData.end = rangeData.text.length + rangeData.start;
                }

                return rangeData;
            };
        },

        /**
         * 发送私信
         */
        inbox:function(user_name,isDialog){
            if (!parseInt(userId)) {
                AWS.User.login();
                return false;
            }

            isDialog = isDialog?isDialog:true;

            if(userName==user_name || user_name==userId)
            {
                return layer.msg('自己不可以给自己发送私信');
            }

            var url = user_name?baseUrl+'/ajax/inbox?user_name='+user_name:baseUrl+'/ajax/inbox';
            var title = user_name?'与 '+user_name+ ' 对话':'新私信';

            if(isDialog)
            {
                return AWS.api.open(url,title, {})
            }else{
                AWS.common.openNewWindow(url);
            }
        },

        /**
         * 已读私信
         * @param element
         * @param id
         */
        headerInboxRead:function (element,id)
        {
            AWS.api.post(baseUrl+'/inbox/read',{id:id},function (res){
                if(res.code)
                {
                    var unreadCountTag = $('#inboxUnreadTag');
                    $(element).parents('li').detach();
                    if(parseInt(unreadCountTag.text())>1)
                    {
                        unreadCountTag.text(parseInt(unreadCountTag.text())-1);
                    }else{
                        unreadCountTag.detach();
                    }
                }
            });
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
                AWS.User.login();
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
                    layer.closeAll();
                    let value = result.data.vote_value;
                    if (result.code) {
                        if (!value && hasClass) {
                            that.removeClass('active');
                        }
                        if (!value && !hasClass) {
                            that.parents('.dz').find('.aw-ajax-against').removeClass('active');
                            that.parent().parent('div').find('.aw-ajax-against').removeClass('active');
                        }
                        if (value === 1) {
                            that.addClass('active');
                            that.parents('.dz').find('.aw-ajax-against').removeClass('active');
                            that.parent().parent('div').find('.aw-ajax-against').removeClass('active');
                        }
                        that.find('span').text(result.data.agree_count);
                        layer.msg(result.msg);
                    }else{
                        layer.msg(result.msg);
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
                AWS.User.login();
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

                            that.parent().parent('div').find('.aw-ajax-agree').removeClass('active');
                            that.parent().parent('div').find('.aw-ajax-agree').find('span').text(result.data.agree_count)
                        }
                        if (value === -1) {
                            that.addClass('active');
                            that.parents('.actions').find('.aw-ajax-agree').removeClass('active');
                            that.parents('.actions').find('.aw-ajax-agree').find('span').text(result.data.agree_count)

                            that.parent().parent('div').find('.aw-ajax-agree').removeClass('active');
                            that.parent().parent('div').find('.aw-ajax-agree').find('span').text(result.data.agree_count)
                        }
                        layer.msg(result.msg);
                    }else{
                        layer.msg(result.msg);
                    }
                }
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
                AWS.User.login();
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
                    AWS.api.post(
                        baseUrl+'/ajax/update_focus/',
                        {id: id, type: type},
                        function (res) {
                            if (res.code) {
                                if(that.hasClass('ygz'))
                                {
                                    that.removeClass('ygz').addClass('gz');
                                    that.text('关注');
                                    AWS.api.success('取消关注成功');
                                }else {
                                    that.addClass('ygz').removeClass('gz');
                                    that.text('已关注');
                                    AWS.api.success('关注成功');
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
         * 用户收藏
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        favorite:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                AWS.User.login();
                return false;
            }

            return AWS.api.open(baseUrl+'/favorite/dialog?item_type='+itemType+'&item_id='+itemId,'用户收藏',{});
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
                AWS.User.login();
                return false;
            }
            return AWS.api.ajax(baseUrl+'/answer/thanks?id='+itemId,function (res){
                if(res.code)
                {
                    $(element).attr('onclick','javascript:;').addClass('active');
                    $(element).find('span').text('已感谢');
                }
                layer.msg(res.msg);
            });
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
                AWS.User.login();
                return false;
            }
            return AWS.api.post(baseUrl+'/ajax/uninterested',{id:itemId,type:itemType},function (res){
                if(res.code)
                {
                    $(element).parent().detach();
                }
                layer.msg(res.msg);
            });
        },

        /**
         * 通用举报
         * @param element
         * @param itemType
         * @param itemId
         * @returns {boolean}
         */
        report:function (element,itemType,itemId)
        {
            if (!parseInt(userId)) {
                AWS.User.login();
                return false;
            }
            let that = $(element);
            layer.confirm('确定要举报吗？', {}, function(){
                layer.closeAll();
                AWS.api.open(baseUrl+'/ajax/report?item_type='+itemType+'&item_id='+itemId,'',that.data())
            });
        },

        /**
         * 公共邀请
         * @param element
         * @param itemId
         * @returns {boolean}
         */
        invite:function (element,itemId)
        {
            if (!parseInt(userId)) {
                AWS.User.login();
                return false;
            }
            let that = $(element);
            AWS.api.open(baseUrl+'/ajax/invite?question_id='+itemId,'',that.data());
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
                AWS.User.login();
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
                        AWS.api.success(msg)
                    }else{
                        AWS.api.error(msg)
                    }
                },
                error:  function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        /**
         * 用户卡片
         * @param selector
         * @param type
         * @param time
         */
        showCard: function (selector,type, time) {
            time = time ? time : 300;
            $(document).on('mouseover', selector, function () {
                clearTimeout(AWS.config.card_box_hide_timer);
                var _this = $(this);
                var ajaxBox = $('#aw-ajax-box');
                AWS.config.card_box_show_timer = setTimeout(function () {
                    var url;
                    var uid = _this.attr('data-id');
                    var html;
                    if (type === 'user') {
                        url = baseUrl + '/ajax/get_user_info?uid=';
                        //判断用户id or 话题id 是否存在
                        if (uid) {
                            if (AWS.config.cacheUserData.length == 0) {
                                $.get(url + uid, function (result) {
                                    var data = result.data;
                                    var focusText = data.is_focus ? '已关注' : '关注';
                                    var focusClass = data.is_focus ? 'ygz' : 'gz';
                                    var signature = data.signature ? data.signature : '这家伙啥都没留下..';
                                    var foot = '';
                                    var onLineText= '';
                                    var groupName = data.group_name ? data.group_name : '未知组';
                                    var verifiedIcon = data.verified_icon ? '<img src="'+data.verified_icon+'" style="width: 18px;height: 18px">' : '';
                                    if(data.check_online=='Y')
                                    {
                                        onLineText = data.is_online ? ' <span class="badge badge-success">在线</span>' : ' <span class="badge badge-danger">离线</span>';
                                    }
                                    //动态插入盒子
                                    if(userId && userId!=uid)
                                    {
                                        foot = '<div class="aw-mod-footer clearfix">' +
                                            '<span>' +
                                            '<a class="text-muted" href="javascript:void(0)" onclick="AWS.User.inbox(\'' + data.nick_name + '\')"><i class="icon icon-inbox"></i> 私信</a>&nbsp;&nbsp;&nbsp;&nbsp;<!--<a  class="text-color-999" href="javascript:void(0)" onclick=""><i class="icon icon-at"></i> 问Ta</a>-->' +
                                            '</span>' +
                                            '<a class="btn btn-primary follow btn-sm float-right ' + focusClass + '" href="javascript:void(0)" onclick="AWS.User.focus(this,\'user\',' +data.uid +')"><span>' + focusText + '</span> <em>|</em> <b>' + data.fans_count + '</b></a>' +
                                            '</div>';
                                    }
                                    html = '<div id="aw-card-tips" class="aw-card-tips aw-card-tips-user">' +
                                        '<div class="aw-mod">' +
                                        '<div class="aw-mod-head" style="height: auto">' +
                                        '<a href="' + data.url + '" class="img">' +
                                        '<img src="' + data.avatar + '" alt="' + data.name + '" onerror="this.src=\'/static/common/image/default-avatar.svg\'" width="50" height="50"/>' +
                                        '</a>' +
                                        '<p class="title clearfix">' +
                                        '<a href="' + data.url + '" class="name" data-id="' + data.uid + '">'+ data.name + '</a> <span class="badge badge-success">'+groupName+'</span> ' + onLineText + verifiedIcon +
                                        '</p>' +
                                        '<p class="aw-user-center-follow-meta">' +
                                        '<span>威望: <em class="aw-text-color-green">' + data.reputation + '</em></span>' +
                                        '<span>赞同: <em class="aw-text-color-orange">' + data.agree_count + '</em></span>' +
                                        '</p>' +
                                        '</div>' +
                                        '<div class="aw-mod-body pb-3">' +
                                        '<p class="font-9 aw-two-line">' + signature + '</p>' +
                                        '</div>' + foot +
                                        '</div>' +
                                        '</div>';

                                    ajaxBox.html(html).show();
                                    //判断是否为游客or自己
                                    if (userId === data.uid || data.uid < 0) {
                                        $('#aw-card-tips .aw-mod-footer').hide();
                                    }
                                    _init();
                                    //缓存
                                    if (html) {
                                        AWS.config.cacheUserData.push(html);
                                    }
                                }, 'json');
                            } else {
                                var flag = 0;
                                //遍历缓存中是否含有此id的数据
                                $.each(AWS.config.cacheUserData, function (i, a) {
                                    if (a.match('data-id="' + uid + '"')) {
                                        ajaxBox.html(a);
                                        $('#aw-card-tips').removeAttr('style');
                                        _init();
                                        flag = 1;
                                    }
                                });
                                if (flag === 0) {
                                    $.get(url + uid, function (result) {
                                        var data = result.data;
                                        var focusText = data.is_focus ? '已关注' : '关注';
                                        var focusClass = data.is_focus ? 'ygz' : 'gz';
                                        var signature = data.signature ? data.signature : '这家伙啥都没留下..';
                                        var onLineText= '';
                                        var groupName = data.group_name ? data.group_name : '未知组';
                                        var verifiedIcon = data.verified_icon ? '<img src="'+data.verified_icon+'" style="width: 18px;height: 18px">' : '';
                                        if(data.check_online=='Y')
                                        {
                                            onLineText = data.is_online ? ' <span class="badge badge-success">在线</span>' : ' <span class="badge badge-danger">离线</span>';
                                        }
                                        //动态插入盒子
                                        html = '<div id="aw-card-tips" class="aw-card-tips aw-card-tips-user">' +
                                            '<div class="aw-mod">' +
                                            '<div class="aw-mod-head" style="height: auto">' +
                                            '<a href="' + data.url + '" class="img">' +
                                            '<img src="' + data.avatar + '" onerror="this.src=\'/static/common/image/default-avatar.svg\'" alt="' + data.name + '" width="50" height="50"/>' +
                                            '</a>' +
                                            '<p class="title clearfix">' +
                                            '<a href="' + data.url + '" class="name" data-id="' + data.uid + '">' + data.name + '</a> <span class="badge badge-success">'+groupName+'</span>' + onLineText + verifiedIcon +
                                            '</p>' +
                                            '<p class="aw-user-center-follow-meta">' +
                                            '<span>威望: <em class="aw-text-color-green">' + data.reputation + '</em></span>' +
                                            '<span>赞同: <em class="aw-text-color-orange">' + data.agree_count + '</em></span>' +
                                            '</p>' +
                                            '</div>' +
                                            '<div class="aw-mod-body pb-2">' +
                                            '<p class="font-9 aw-two-line">' + signature + '</p>' +
                                            '</div>' +
                                            '<div class="aw-mod-footer clearfix">' +
                                            '<span>' +
                                            '<a class="text-muted" href="javascript:void(0)" onclick="AWS.User.inbox(\'' + data.nick_name + '\')"><i class="icon icon-inbox"></i> 私信</a>&nbsp;&nbsp;&nbsp;&nbsp;<!--<a  class="text-color-999" href="javascript:void(0)" onclick=""><i class="icon icon-at"></i> 问Ta</a>-->' +
                                            '</span>' +
                                            '<a class="btn btn-primary btn-sm follow float-right ' + focusClass + '" onclick="AWS.User.focus(this,\'user\',' + data.uid + ')"><span>' + focusText + '</span> <em>|</em> <b>' + data.fans_count + '</b></a>' +
                                            '</div>' +
                                            '</div>' +
                                            '</div>';
                                        ajaxBox.html(html).show();
                                        //判断是否为游客or自己
                                        if (userId === data.uid || data.uid < 0) {
                                            $('#aw-card-tips .aw-mod-footer').hide();
                                        }

                                        _init();
                                        //缓存
                                        if (html) {
                                            AWS.config.cacheUserData.push(html);
                                        }
                                    }, 'json');
                                }
                            }
                        }
                    }

                    if (type === 'topic') {
                        url = baseUrl + '/ajax/get_topic_info?id=';
                        //判断用户id or 话题id 是否存在
                        if (uid) {
                            if (AWS.config.cashTopicData.length === 0) {
                                $.get(url + uid, function (result) {
                                    var data = result.data;
                                    var focusText = data.is_focus ? '已关注' : '关注';
                                    var focusClass = data.is_focus ? 'ygz' : 'gz';
                                    //动态插入盒子
                                    html = '<div id="aw-card-tips" class="aw-card-tips aw-card-tips-topic">'+
                                        '<div class="aw-mod">'+
                                        '<div class="aw-mod-head mb-0 border-bottom-0" style="height: auto">'+
                                        '<a href="'+data.url+'" class="img">'+
                                        '<img src="'+data.pic+'" alt="'+data.title+'" onerror="this.src=\'/static/common/image/topic.svg\'" width="50" height="50" title="'+data.title+'"/>'+
                                        '</a>'+
                                        '<p class="title">'+
                                        '<a href="'+data.url+'" class="name" data-id="'+data.id+'">'+data.title+'</a>'+
                                        '</p>'+
                                        '<p class="desc font-9 aw-two-line">'+data.description+'</p>'+
                                        '</div>'+
                                        '<div class="aw-mod-footer">'+
                                        '<span>讨论数: '+data.discuss+'</span>'+
                                        '<a class="btn btn-normal btn-primary btn-sm follow '+focusClass+' float-right" onclick="AWS.User.focus(this, \'topic\','+data.id+');"><span>'+focusText+'</span> <em>|</em> <b>'+data.focus+'</b></a>'+
                                        '</div>'+
                                        '</div>'+
                                        '</div>';

                                    ajaxBox.html(html).show();
                                    if (!userId)
                                    {
                                        $('#aw-card-tips .mod-footer .follow').hide();
                                    }
                                    _init();
                                    //缓存
                                    if(html)
                                    {
                                        AWS.config.cashTopicData.push(html);
                                    }
                                }, 'json');
                            } else {
                                var flag = 0;
                                //遍历缓存中是否含有此id的数据
                                $.each(AWS.config.cashTopicData, function (i, a) {
                                    if (a.match('data-id="' + uid + '"')) {
                                        ajaxBox.html(a);
                                        $('#aw-card-tips').removeAttr('style');
                                        _init();
                                        flag = 1;
                                    }
                                });
                                if (flag === 0) {
                                    $.get(url + uid, function (result) {
                                        var data = result.data;
                                        var focusText = data.is_focus ? '已关注' : '关注';
                                        var focusClass = data.is_focus ? 'ygz' : 'gz';
                                        //动态插入盒子
                                        html = '<div id="aw-card-tips" class="aw-card-tips aw-card-tips-topic">'+
                                            '<div class="aw-mod">'+
                                            '<div class="aw-mod-head mb-0 border-bottom-0" style="height: auto">'+
                                            '<a href="'+data.url+'" class="img" >'+
                                            '<img src="'+data.pic+'" alt="'+data.title+'" onerror="this.src=\'/static/common/image/topic.svg\'" width="50" height="50" title="'+data.title+'"/>'+
                                            '</a>'+
                                            '<p class="title">'+
                                            '<a href="'+data.url+'" class="name" data-id="'+data.id+'">'+data.title+'</a>'+
                                            '</p>'+
                                            '<p class="desc font-9 aw-two-line">'+data.description+'</p>'+
                                            '</div>'+
                                            '<div class="aw-mod-footer">'+
                                            '<span>讨论数: '+data.discuss+'</span>'+
                                            '<a class="btn btn-primary btn-sm follow '+focusClass+' float-right" onclick="AWS.User.focus(this, \'topic\','+data.id+');"><span>'+focusText+'</span> <em>|</em> <b>'+data.focus+'</b></a>'+
                                            '</div>'+
                                            '</div>'+
                                            '</div>';
                                        ajaxBox.html(html).show();
                                        //判断是否为游客or自己
                                        if (!userId) {
                                            $('#aw-card-tips .aw-mod-footer .follow').hide();
                                        }
                                        _init();
                                        //缓存
                                        if(html)
                                        {
                                            AWS.config.cashTopicData.push(html);
                                        }
                                    }, 'json');
                                }
                            }
                        }
                    }

                    //初始化
                    function _init() {
                        var left = _this.offset().left,
                            top = _this.offset().top + _this.height() + 20,
                            nTop = _this.offset().top - $(window).scrollTop();

                        var cardBox = $('#aw-card-tips');
                        //判断下边距离不足情况
                        if (nTop + cardBox.innerHeight() > $(window).height()) {
                            top = _this.offset().top - (cardBox.innerHeight()) - 10;
                        }

                        //判断右边距离不足情况
                        if (left + cardBox.innerWidth() > $(window).width()) {
                            left = _this.offset().left - cardBox.innerWidth() + _this.innerWidth();
                        }
                        cardBox.css({left: left, top: top}).fadeIn();
                    }
                }, time);
            });

            $(document).on('mouseout', selector, function () {
                clearTimeout(AWS.config.card_box_show_timer);
                AWS.config.card_box_hide_timer = setTimeout(function () {
                    $('#aw-card-tips').fadeOut();
                }, 600);
            });
        },

        /**
         * 删除通知
         * @param element
         * @param id
         */
        deleteNotify:function  (element,id)
        {
            AWS.api.post(baseUrl+'/notify/delete',{id:id},function (res){
                if(res.code) {
                    $(element).parents('dl').detach();
                    $(element).parents('.header-inbox-item').detach();
                    $('.header-notify-count').text(parseInt($('.header-notify-count').text())-1);
                }
            });
        },

        /**
         * 折叠回复
         * @param element
         * @param id
         */
        forceFoldAnswer:function (element,id){
            var url = $(element).data('url');
            layer.confirm('是否折叠该回答?<br>折叠的回复是被你或者被大多数用户认为不感兴趣的回复才会被折叠',{
                btn: ['确认', '取消']
            }, function () {
                AWS.api.post(url,{answer_id:id},function (res){
                    if(res.code) {
                        $(element).parents('.aw-answer-item').detach();
                        AWS.events.onAjaxSuccess(res)
                    }else{
                        AWS.events.onAjaxError(res)
                    }
                });
            }, function(){
                layer.closeAll();
            });
        },

        /**
         * 已读通知
         * @param element
         * @param id
         */
        readNotify:function(element,id)
        {
            AWS.api.post(baseUrl+'/notify/read',{id:id},function (res){
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
            AWS.api.get(baseUrl+'/notify/read_all',function (res){
                if(res.code)
                {
                    $('.aw-notify-status').removeClass('unread').addClass('read');
                    $('.header-inbox-item').detach();
                }
            });
        },

        headerNotifyReadAll:function ()
        {
            layer.load();
            AWS.api.get(baseUrl+'/notify/read_all',function (res){
                $('#notifyUnreadTag').text('').detach();
                $('#topNotifyBox').empty();
            });
            layer.closeAll();
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
            }
        },

        /**
         * 收录文章至专栏
         * @param article_id
         * @param column_id
         */
        saveColumnArticle:function (article_id,column_id,element)
        {
            var that = $(element);
            var url = baseUrl+'/ajax.column/collect';
            AWS.api.post(url,{article_id:article_id,column_id:column_id},function (res){
                var msg = res.msg ? res.msg : '收录成功';
                if(res.code==1)
                {
                    that.text('已收录').removeClass('btn-outline-primary').addClass('btn-primary');
                    that.attr('onclick','');
                }

                layer.msg(msg);
            });
        }
    },

    Dropdown:{
        // 下拉菜单功能绑定
        bind_dropdown_list: function(selector, type)
        {
            if (type == 'search')
            {
                $(selector).focus(function()
                {
                    $(selector).parent().find('div.aw-dropdown').show();
                });
            }

            $(selector).bind('compositionstart', function (e) {
                e.target.composing = true
            })

            $(selector).bind('compositionend', function (e) {
                e.target.composing = false
                trigger(e.target, 'input')
            });

            function trigger(el, type) {
                var e = document.createEvent('HTMLEvents')
                e.initEvent(type, true, false)
                el.dispatchEvent(e)
            }

            $(selector).bind('input', function (e)
            {
                if (e.target.composing) {
                    return
                }

                if (type == 'search')
                {
                    $(selector).parent().find('.search').show().children('a').text($(selector).val());
                }

                if ($(selector).val().length >= 1)
                {
                    if (e.which != 38 && e.which != 40 && e.which != 188 && e.which != 13)
                    {
                        AWS.Dropdown.get_dropdown_list($(this), type, $(selector).val());
                    }
                }
                else
                {
                    $(selector).parent().find('.aw-dropdown').hide();
                }

                if (type == 'topic')
                {
                    // 逗号或回车提交
                    if (e.which == 188)
                    {
                        if ($('.aw-edit-topic-box #aw_edit_topic_title').val() != ',')
                        {
                            $('.aw-edit-topic-box #aw_edit_topic_title').val( $('.aw-edit-topic-box #aw_edit_topic_title').val().substring(0,$('.aw-edit-topic-box #aw_edit_topic_title').val().length-1));
                            $('.aw-edit-topic-box .aw-dropdown').hide();
                            $('.aw-edit-topic-box .add').click();
                        }
                        return false;
                    }

                    // 回车提交
                    if (e.which == 13)
                    {
                        $('.aw-edit-topic-box .aw-dropdown').hide();
                        $('.aw-edit-topic-box .add').click();
                        return false;
                    }

                    var lis = $(selector).parent().find('.aw-dropdown-list li');

                    //键盘往下
                    if (e.which == 40 && lis.is(':visible'))
                    {
                        var _index;
                        if (!lis.hasClass('active'))
                        {
                            lis.eq(0).addClass('active');
                        }
                        else
                        {
                            $.each(lis, function (i, e)
                            {
                                if ($(this).hasClass('active'))
                                {
                                    $(this).removeClass('active');
                                    if ($(this).index() == lis.length - 1)
                                    {
                                        _index = 0;
                                    }
                                    else
                                    {
                                        _index = $(this).index() + 1;
                                    }
                                }
                            });
                            lis.eq(_index).addClass('active');
                            $(selector).val(lis.eq(_index).text());
                        }
                    }

                    //键盘往上
                    if (e.which == 38 && lis.is(':visible'))
                    {
                        var _index;
                        if (!lis.hasClass('active'))
                        {
                            lis.eq(lis.length - 1).addClass('active');
                        }
                        else
                        {
                            $.each(lis, function (i, e)
                            {
                                if ($(this).hasClass('active'))
                                {
                                    $(this).removeClass('active');
                                    if ($(this).index() == 0)
                                    {
                                        _index = lis.length - 1;
                                    }
                                    else
                                    {
                                        _index = $(this).index() - 1;
                                    }
                                }
                            });
                            lis.eq(_index).addClass('active');
                            $(selector).val(lis.eq(_index).text());
                        }
                    }
                }
            });

            $(selector).blur(function()
            {
                $(selector).parent().find('.aw-dropdown').delay(500).fadeOut(300);
            });
        },

        // 插入下拉菜单
        set_dropdown_list: function(selector, type)
        {
            switch (type)
            {
                case 'invite' :
                case 'inbox' :
                    $(selector).parent().find('.aw-dropdown .aw-dropdown-list li a').click(function ()
                    {
                        var text = $(this).text();
                        $(selector).val(text);
                    });
                    break;
            }
        },

        /* 下拉菜单数据获取 */
        get_dropdown_list: function(selector, type, data)
        {
            if (AWS.config.dropdown_list_xhr != '')
            {
                AWS.config.dropdown_list_xhr.abort(); // 中止上一次ajax请求
            }
            var url;
            switch (type)
            {
                case 'search' :
                    url = baseUrl + '/ajax.search/ajax/?q=' + encodeURIComponent(data) + '&limit=15';
                    break;
                case 'invite' :
                case 'inbox' :
                    url = baseUrl + '/ajax.search/search_result/?type=users&q=' + encodeURIComponent(data) + '&limit=10';
                    break;
                case 'publish':
                    url = baseUrl + '/ajax.search/search_result/?type=question&q=' + encodeURIComponent(data) + '&limit=10';
                    break;
            }

            AWS.config.dropdown_list_xhr = $.get(url, function (result)
            {
                if (result.length != 0 && AWS.config.dropdown_list_xhr != undefined)
                {
                    $(selector).parent().find('.aw-dropdown-list').html(''); // 清空内容
                    switch (type)
                    {
                        case 'search' :
                            $(selector).parent().find('.aw-dropdown-list').html(result);
                            break;
                        case 'inbox' :
                        case 'invite' :
                            var html = '<ul>';
                            $.each(result.data.list, function (i, a)
                            {
                                html+='<li class="user py-1"><a data-url="'+a.url+'" data-id="'+a.uid+'" style="cursor: pointer"><img class="img" src="'+a.avatar+'" style="width: 16px;height: 16px"/> '+a.nick_name+'</a></li>'
                            });
                            html+='</ul>';
                            $(selector).parent().find('.aw-dropdown-list').html(html);
                            $(selector).parent().find('.aw-dropdown .aw-dropdown-list li a').click(function ()
                            {
                                var text = $(this).text();
                                $(selector).val(text);
                            });
                            break;

                        case 'publish':
                            if(result.data.list)
                            {
                                $(selector).parent().find('.aw-dropdown').show();
                                var html = '<ul>';
                                $.each(result.data.list, function (i, a)
                                {
                                    html+='<li class="user py-1"><a href="'+a.url+'" style="cursor: pointer">'+a.title+'</a></li>'
                                });
                                html+='</ul>';
                                $(selector).parent().find('.aw-dropdown-list').html(html);
                            }else{
                                $(selector).parent().find('.aw-dropdown').hide();
                            }
                            break;
                    }
                    $(selector).parent().find('.aw-dropdown, .aw-dropdown-list').show().children().show();
                    $(selector).parent().find('.title').hide();
                }else
                {
                    $(selector).parent().find('.aw-dropdown').show().end().find('.title').html('没有找到相关结果').show();
                }
            }, 'json');
        },
    },

    init: function () {
        $(function () {
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
                    layer.msg('您还未登录,请登录后再操作!');
                    return false;
                }

                if (options.confirm) {
                    layer.confirm(options.confirm,{
                        btn: ['确认', '取消']
                    }, function () {
                        AWS.api.ajax(options.url, success, error);
                    }, function(){
                        if (typeof options.cancel === 'undefined') {
                            layer.closeAll();
                        }else{
                            AWS.api.ajax(options.cancel, success, error);
                        }
                    });
                } else {
                    AWS.api.ajax(options.url, success, error);
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
                    layer.msg('您还未登录,请登录后再操作!');
                    return false;
                }

                if (typeof options.confirm !== 'undefined') {
                    layer.confirm(options.confirm, function (index) {
                        AWS.api.open(url, title, options);
                        layer.close(index);
                    });
                } else {
                    AWS.api.open(url, title, options);
                }
                return false;
            });

            //post弹窗点击
            $(document).on('click', '.ajax-post-open', function (e) {
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                let url = $(that).data("url") ? $(that).data("url") : $(that).attr('href');
                let title = $(that).attr("title") || $(that).data("title") || $(that).data('original-title');
                let data = $(that).data('data') || {};

                if (typeof options.confirm !== 'undefined') {
                    layer.confirm(options.confirm, function (index) {
                        AWS.api.postOpen(url, title, data, options);
                        layer.close(index);
                    });
                } else {
                    window[$(that).data("window") || 'self'].AWS.api.postOpen(url, title, data, options);
                }
                return false;
            });

            //ajax表单提交带验证
            $(document).on('click', '.ajax-form', function (e) {
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                let form = $($(that).parents('form')[0]);
                let success = typeof options.success === 'function' ? options.success : null;

                if(!verification(form.serializeArray())){
                    return false;
                }
                delete options.success;
                delete options.error;
                $(that).attr('type', 'button');
                $.ajax({
                    url: form.attr('action'),
                    dataType: 'json',
                    type: 'post',
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function(){
                        $(".ajax-form").attr({ disabled: "disabled" });
                    },
                    success: function (result) {
                        if (typeof success !== 'function') {
                            let msg = result.msg ? result.msg : '操作成功';
                            if (result.code > 0) {
                                AWS.api.success(msg, result.url)
                            } else {
                                AWS.api.error(msg, result.url)
                            }
                        } else {
                            success || success(result);
                        }
                    },
                    complete: function () {
                        //移除禁用
                        $(".ajax-form").removeAttr("disabled");
                    },
                    error: function (error) {
                        if ($.trim(error.responseText) !== '') {
                            layer.closeAll();
                            AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                        }
                    }
                });
            });

            //ajax表单提交不带验证
            $(document).on('click', '.aw-ajax-form', function (e) {
                let that = this;
                let options = $.extend({}, $(that).data() || {});
                let form = $($(that).parents('form')[0]);
                $(that).attr('disabled',true);
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
                                AWS.api.success(msg, result.url)
                            } else {
                                $(that).attr('disabled',false);
                                if(msg)
                                {
                                    layer.msg(msg,{},function (){
                                        if (typeof url !== 'undefined' && url) {
                                            window.location.href = url;
                                        }
                                    })
                                }else{
                                    if (typeof url !== 'undefined' && url) {
                                        window.location.href = url;
                                    }
                                }
                            }
                        } else {
                            success || success(result);
                        }
                    },
                    error: function (error) {
                        if ($.trim(error.responseText) !== '') {
                            layer.closeAll();
                            AWS.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                        }
                    }
                });
            });

            //ajax 通用POST提交
            $(document).on('click', '.ajax-post,.aw-ajax-post', function (e) {
                let target, query, form;
                let target_form = $(this).attr('data-target-form');
                let that = this;
                let need_confirm = false;
                if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('data-url'))) {
                    form = $('.' + target_form);

                    if ($(this).attr('hide-data') === 'true') { //无数据时也可以使用的功能
                        form = $('.hide-data');
                        query = form.serialize();
                    } else if (form.get(0) == undefined) {
                        return false;
                    } else if (form.get(0).nodeName == 'FORM') {
                        if ($(this).hasClass('confirm')) {
                            if (!confirm('确认要执行该操作吗?')) {
                                return false;
                            }
                        }
                        if ($(this).attr('url') !== undefined) {
                            target = $(this).attr('url');
                        } else {
                            target = form.get(0).action;
                        }
                        query = form.serialize();
                    } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                        form.each(function (k, v) {
                            if (v.type === 'checkbox' && v.checked === true) {
                                need_confirm = true;
                            }
                        });
                        if (need_confirm && $(this).hasClass('confirm')) {
                            if (!confirm('确认要执行该操作吗?')) {
                                return false;
                            }
                        }
                        query = form.serialize();

                    } else {
                        if ($(this).hasClass('confirm')) {
                            if (!confirm('确认要执行该操作吗?')) {
                                return false;
                            }
                        }
                        query = form.find('input,select,textarea').serialize();
                    }

                    $(that).addClass('disabled').attr('autocomplete', 'off').prop('disabled', true);

                    $.post(target, query).success(function (data) {
                        if (data.code === 1) {
                            AWS.api.success(data.msg);
                            setTimeout(function () {
                                $(that).removeClass('disabled').prop('disabled', false);
                                if (data.url) {
                                    location.href = data.url;
                                } else if ($(that).hasClass('no-refresh')) {
                                    $('#top-alert').find('button').click();
                                } else {
                                    location.reload();
                                }
                            }, 1500);
                        } else {
                            AWS.api.error(data.msg);
                            setTimeout(function () {
                                $(that).removeClass('disabled').prop('disabled', false);
                            }, 1500);
                        }
                    });
                }
                return false;
            });

            $(document).on('click', '[data-delete]', function (t) {
                let url = this.dataset.delete
                let that = $(this);
                layer.confirm('确定要删除吗？', {}, function(){
                    $.ajax({
                        url: url,
                        dataType: 'json',
                        success: function (res) {
                            if(res.code){
                                layer.closeAll();
                                AWS.api.success(res.msg, res.url);
                                that.parents('.post-comments-single').remove();
                            }else{
                                AWS.api.error(res.msg);
                            }
                        },
                    });
                });
            });

            $(document).on('click', '[data-lock]', function (t) {
                let url=this.dataset.lock
                let that=$(this);
                $.ajax({
                    url: url,
                    dataType: 'json',
                    success: function (res) {
                        if(res.code){
                            AWS.api.success(res.msg,res.url);
                        }else{
                            AWS.api.error(res.msg);

                        }
                    },
                })
            });
        })
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
            if(type=='img')
            {
                upload_allowExt = upload_image_ext.replace(/\|/g, ",");
            }else{
                upload_allowExt = upload_file_ext.replace(/\|/g, ",");
            }

            if (type=='img') {
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
                chunked: false,                                             // 是否分片
                chunkSize: size,                                // 分片大小
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
                    layer.msg(response.msg);
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
                    layer.msg('请上传' + upload_allowExt + '格式的文件！');
                } else if (type == "F_EXCEED_SIZE") {
                    layer.msg('单个文件大小不能超过' + size / 1024 + 'kb！');
                } else if (type == "F_DUPLICATE") {
                    layer.msg('请不要重复选择文件');
                } else {
                    layer.msg('上传出错！请检查后重新上传！错误代码' + type);
                }
            });
        },

        /**
         * 通用上传方法
         * @param options
         * @param success
         * @param complete
         * @param error
         */
        commonUpload : function (options,success,complete,error) {
            options.path = options.path || 'common';
            let upload_allowExt,size,url;
            url = options.url || baseUrl+'/upload/index?upload_type=' + options.type+'&path='+options.path+'&access_key='+options.access_key;
            options = $.extend({
                filePicker:'',//文件选择触发器
                preview:'.upload-image-preview',//单图容器
                field:'aw-file',//上传字段
                path:'common',//上传路径
                type:'img',
                more:false,
                progressContainer:'.upload-progress-container',//上传进度条容器
                imagesList:'.upload-images-container',//多图容器
                uploadBtnTitle:'上传图片',
                url:url,
                access_key:''
            },options);

            if(options.type=='img')
            {
                upload_allowExt = upload_image_ext.replace(/\|/g, ",");
            }else{
                upload_allowExt = upload_file_ext.replace(/\|/g, ",");
            }

            if (options.type=='img') {
                size = upload_image_size * 1024;
            } else {
                size = upload_file_size * 1024;
            }

            var $list = $(options.progressContainer);
            var GUID = WebUploader.Base.guid();// 一个GUID
            var uploader = WebUploader.create({
                auto: true,                                        // 选完文件后，是否自动上传。
                swf: '/static/libs/webuploader/uploader.swf',     // 加载swf文件，路径一定要对
                server: options.url, // 文件接收服务端
                pick: options.filePicker,                              // 选择文件的按钮。可选。
                resize: false,                                             // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
                chunked: false,                                             // 是否分片
                chunkSize: size,                                // 分片大小
                threads: 1,                                                // 上传并发数
                formData: {
                    // 由于Http的无状态特征，在往服务器发送数据过程传递一个进入当前页面是生成的GUID作为标示
                    GUID: GUID,                                            // 自定义参数
                },
                compress: false,
                fileSingleSizeLimit: size,                                 // 限制大小200M，单文件
                accept: {
                    title: options.uploadBtnTitle,
                    extensions: upload_allowExt,                           // 允许上传的类型 'gif,jpg,jpeg,bmp,png'
                    mimeTypes: '*',                                        // 默认全部文件，为兼容上传文件功能，如只上传图片可写成img/*
                }
            });
            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $list, $percent = $li.find('.progress .progress-bar');
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
                if (typeof success === 'function') {
                    success(file, response);
                }else{
                    if (response.code === 0) {
                        layer.msg(response.msg);
                    }
                    let url = response.url;
                    if (options.more === true) {
                        var images = '<div class="row"><div class="col-6"><input type="text" name="' + options.field + '[]" value="' + url + '" class="form-control"/></div> <div class="col-3"><input class="form-control input-sm" type="text" name="' + field + '_title[]" value="' + file.name + '" ></div> <div class="col-xs-3"><button type="button" class="btn btn-block btn-warning remove_images">移除</button></div></div>';
                        var images_list = $(options.imagesList).html();
                        $(options.imagesList).html(images + images_list);
                    } else {
                        $("input[name='" + options.field + "']").val(url);
                        $(options.preview).attr('src', url);
                        $(options.preview).parent("a").attr('href', url);
                    }
                }
            });
            uploader.on('uploadComplete', function (file) {
                if (typeof complete === 'function') {
                    complete(file);
                }else{
                    $list.find('.progress').fadeOut();
                }
            });
            // 错误提示
            uploader.on("error", function (type) {
                if (typeof error === 'function') {
                    error(type);
                }else{
                    if (type == "Q_TYPE_DENIED") {
                        layer.msg('请上传' + upload_allowExt + '格式的文件！');
                    } else if (type == "F_EXCEED_SIZE") {
                        layer.msg('单个文件大小不能超过' + size / 1024 + 'kb！');
                    } else if (type == "F_DUPLICATE") {
                        layer.msg('请不要重复选择文件');
                    } else {
                        layer.msg('上传出错！请检查后重新上传！错误代码' + type);
                    }
                }
            });
        },

        /**
         * 删除附件
         * @param obj
         * @param attachId
         * @param access_key
         * @returns {boolean}
         */
        removeAttach:function (obj,attachId,access_key)
        {
            AWS.api.post(baseUrl+'/upload/remove_attach',{id:attachId,access_key:access_key},function (res){
                if(res.code)
                {
                    $(obj).parents('tr').detach();
                }
                layer.msg(res.msg);
            });

            return false;
        },

        /**
         * 附件上传
         * @param elem
         * @param bindAction
         * @param uploadList
         * @param path
         * @param access_key
         * @param id
         */
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
                chunked: false,                                             // 是否分片
                chunkSize: size,                                // 分片大小
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
                    AWS.modal.alertError(response.msg);
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
                    layer.msg('请上传' + upload_allowext + '格式的文件！',{time: 500});
                } else if (type === "F_EXCEED_SIZE") {
                    layer.msg('单个文件大小不能超过' + size / 1024 + 'kb！',{time: 500});
                } else if (type === "F_DUPLICATE",{time: 500}) {
                    layer.msg('请不要重复选择文件',{time: 500});
                } else {
                    layer.msg('上传出错！请检查后重新上传！错误代码' + type,{time: 500});
                }
            });
        }
    },

    // 通用方法封装处理
    common: {
        openNewWindow:function(url) {
            var a = document.createElement('a');
            a.setAttribute('href', url);
            a.setAttribute('target', '_blank');
            var id = Math.random(10000, 99999);
            a.setAttribute('id', id);
            // 防止反复添加
            if (!document.getElementById(id)) {
                document.body.appendChild(a);
            }
            a.click();
        },
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
            layer.msg('复制成功', {type:1})
        },

        //设置cookie
        setCookie:function(name, value, liveMinutes) {
            if (liveMinutes == undefined || liveMinutes == null) {
                liveMinutes = 60 * 2;
            }
            if (typeof (liveMinutes) != 'number') {
                liveMinutes = 60 * 2;//默认120分钟
            }
            var minutes = liveMinutes * 60 * 1000;
            var exp = new Date();
            exp.setTime(exp.getTime() + minutes + 8 * 3600 * 1000);
            //path=/表示全站有效，而不是当前页
            document.cookie = name + "=" + value + ";path=/;expires=" + exp.toUTCString();
        },

        //获取cookie
        getCookie:function(cname) {
            var name = cname + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ')
                    c = c.substring(1);
                if (c.indexOf(name) != -1)
                    return c.substring(name.length, c.length);
            }
            return "";
        },

        //删除cookies
        delCookie:function(name)
        {
            AWS.common.setCookie(name, 1, -1);
        }
    },
};
AWS.init();
window.AWS = AWS;

/*
function L(string, replace)
{
    if (typeof (aws_lang) != 'undefined')
    {
        if (typeof (aws_lang[string]) != 'undefined')
        {
            string = aws_lang[string];
        }
    }
    if (replace)
    {
        string = string.replace('%s', replace);
    }
    return string;
}*/
