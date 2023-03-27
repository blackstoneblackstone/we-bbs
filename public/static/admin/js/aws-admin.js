const AWS_ADMIN = {
    api: {
        /**
         * 发送Ajax请求
         * @param options
         * @param success
         * @param error
         * @returns {*|jQuery}
         */
        ajax: function (options, success, error) {
            options = typeof options === 'string' ? {url: options} : options;
            options.url = options.url + (options.url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            options = $.extend({
                type: "POST",
                dataType: "json",
                success: function (ret) {
                    if (typeof success === 'function') {
                        success(ret);
                    } else {
                        AWS_ADMIN.events.onAjaxSuccess(ret, success);
                    }
                },
                error: function (xhr) {
                    if (typeof error === 'function') {
                        error(xhr);
                    } else {
                        var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                        AWS_ADMIN.events.onAjaxError(ret, error);
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
                success: function (ret) {
                    if (ret.code === 1) {
                        if (typeof success === 'function') {
                            success(ret);
                        } else {
                            AWS_ADMIN.events.onAjaxSuccess(ret, success);
                        }
                    } else {
                        if (typeof error === 'function') {
                            error(ret);
                        } else {
                            AWS_ADMIN.events.onAjaxError(ret, error);
                        }
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_ADMIN.events.onAjaxError(ret, error);
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
                headers: {"Access-Control-Allow-Headers": "X-Requested-With"},
                success: function (result) {
                    if (typeof success != 'function') {
                        let msg = typeof result.msg !== 'undefined' ? result.msg : '操作成功';
                        if (result.code > 0) {
                            AWS_ADMIN.api.success(msg, result.url)
                        } else {
                            AWS_ADMIN.api.error(msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (xhr) {
                    //let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_ADMIN.api.error(error);
                }
            });
        },

        /**
         * ajax表单提交
         * @param element 表单标识
         * @param success 成功回调
         */
        ajaxForm: function (element, success) {
            let url = $(element).attr('action');
            $.ajax({
                url: url,
                dataType: 'json',
                type: 'post',
                data: $(element).serialize(),
                success: function (result) {
                    if (typeof success != 'function') {
                        let msg = result.msg ? result.msg : '操作成功';
                        if (result.code > 0) {
                            AWS_ADMIN.api.success(msg, result.url)
                        } else {
                            AWS_ADMIN.api.error(msg, result.url)
                        }
                    } else {
                        success || success(result);
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        AWS_ADMIN.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                }
            });
        },

        success: function (message, url)
        {
            if(message)
            {
                layer.msg(message,{},function (){
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
            layer.msg(message, {time: 500}, function () {
                if (typeof url !== 'undefined' && url) {
                    parent.layer.closeAll() || layer.closeAll();
                    AWS_ADMIN.common.jump(url);
                } else {
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
            let width = options.width ? options.width : $(window).width() > 800 ? '800px' : '95%';
            let height = options.height ? options.height : $(window).height() > 600 ? '600px' : '95%';
            let area = [width, height];
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
                    const that = this;
                    $(layero).data("callback", that.callback);
                    layer.setTop(layero);
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

        //post方式打开
        postOpen: function (url, title, data, options) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'html',
                success: function () {
                    return AWS_ADMIN.api.open(url, title, options);
                }
            });
        }
    },

    events: {
        //请求成功的回调
        onAjaxSuccess: function (ret, onAjaxSuccess) {
            let data = typeof ret.data !== 'undefined' ? ret.data : null;
            let url = typeof ret.url !== 'undefined' ? ret.url : null;
            let msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作完成';
            if (typeof onAjaxSuccess === 'function') {
                var result = onAjaxSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            AWS_ADMIN.api.success(msg, url);
        },
        //请求错误的回调
        onAjaxError: function (ret, onAjaxError) {
            let data = typeof ret.data !== 'undefined' ? ret.data : null;
            let url = typeof ret.url !== 'undefined' ? ret.url : null;
            let msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作完成';
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            AWS_ADMIN.api.error(msg, url);
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            response = typeof response === 'object' ? response : JSON.parse(response);
            return response;
        }
    },

    btTable: {},  // bootstrapTable
    tableParam:{},

    // 表格封装处理
    table: {
        _option: {},
        // 初始化表格参数
        init: function (options) {
            // 默认参数
            const defaults = {
                id: "bootstrap-table",
                height: undefined,            // 表格的高度,一般不需要设置
                sidePagination: "server",     // server启用服务端分页client客户端分页
                sortName: "id",               // 排序列名称
                sortOrder: "desc",            // 排序方式  asc 或者 desc
                escape: true,                 // 转义HTML字符串
                pagination: true,             // 是否显示分页
                pageSize: 10,                 // 每页的记录行数
                showRefresh: true,            // 是否显示刷新按钮
                showToggle: true,             // 是否显示详细视图和列表视图的切换按钮
                showFullscreen: true,         // 是否显示全屏按钮
                showColumns: true,            // 是否显示隐藏某列下拉框
                search: false,				  // 是否显示自带的搜索框功能
                showSearchButton: false,      // 是否显示自带的搜索按钮
                pageList: [15, 30, 50, 100],  // 每页显示的数量选择
                toolbar: "toolbar",           // 自定义工具栏
                toolbarAlign: "left",         // 工具栏左对齐
                buttonsClass: "",             // 按钮样式
                showFooter: false,			  // 显示页脚
                showExport: false,			  // 显示导出按钮
                clickToSelect: false,         // 是否启用点击选中行
                fixedColumns: false,          // 是否启用固定列功能
                rowStyle: {},                 // 设置行样式
                classes: 'table table-hover', // 设置表样式
                queryParams: AWS_ADMIN.table.queryParams,
            };
            options = $.extend(defaults, options);

            AWS_ADMIN.table._option = options;
            AWS_ADMIN.btTable = $('#bootstrap-table');
            // 初始化新事件对象的属性
            AWS_ADMIN.table.initEvent();
            // 构建bootstrap数据
            var option = {
                url: options.url,                                   // 请求后台的URL（*）
                height: options.height,                             // 表格的高度
                sortable: true,                                     // 是否启用排序
                sortName: options.sortName,                         // 排序列名称
                sortOrder: options.sortOrder,                       // 排序方式  asc 或者 desc
                sortStable: true,                                   // 设置为 true 将获得稳定的排序
                method: 'post',                                     // 请求方式（*）
                cache: false,                                       // 是否使用缓存
                contentType: "application/json",   					// 内容类型
                dataType: 'json',                                   // 数据类型
                responseHandler: AWS_ADMIN.table.responseHandler,           // 在加载服务器发送来的数据之前处理函数
                pagination: options.pagination,                     // 是否显示分页（*）
                paginationLoop: true,                               // 是否禁用分页连续循环模式
                sidePagination: options.sidePagination,             // server启用服务端分页client客户端分页
                pageNumber: 1,                                      // 初始化加载第一页，默认第一页
                pageSize: options.pageSize,                         // 每页的记录行数（*）
                pageList: options.pageList,                         // 可供选择的每页的行数（*）
                search: options.search,                             // 是否显示搜索框功能
                showSearchButton: options.showSearchButton,         // 是否显示检索信息
                showColumns: options.showColumns,                   // 是否显示隐藏某列下拉框
                showRefresh: options.showRefresh,                   // 是否显示刷新按钮
                showToggle: options.showToggle,                     // 是否显示详细视图和列表视图的切换按钮
                showFullscreen: options.showFullscreen,             // 是否显示全屏按钮
                showFooter: options.showFooter,                     // 是否显示页脚
                escape: options.escape,                             // 转义HTML字符串
                clickToSelect: options.clickToSelect,				// 是否启用点击选中行
                toolbar: '#' + options.toolbar,                     // 指定工作栏
                detailView: options.detailView,                     // 是否启用显示细节视图
                iconSize: 'undefined',                              // 图标大小：undefined默认的按钮尺寸 xs超小按钮sm小按钮lg大按钮
                rowStyle: options.rowStyle,                         // 通过自定义函数设置行样式
                showExport: true,                     // 是否支持导出文件
                exportTypes:['json', 'xml', 'csv', 'txt', 'sql', 'excel'],
                uniqueId: options.uniqueId,                         // 唯 一的标识符
                fixedColumns: options.fixedColumns,                 // 是否启用冻结列（左侧）
                detailFormatter: options.detailFormatter,           // 在行下面展示其他数据列表
                columns: options.columns,                           // 显示列信息（*）
                classes: options.classes,                           // 设置表样式
                queryParams: options.queryParams,                   // 传递参数（*）
                showMultiSort: false,
                onLoadSuccess:AWS_ADMIN.table.onLoadSuccess,
                onEditableSave: AWS_ADMIN.table.onEditableSave
            };
            // 将tree合并到option[关闭分页且传递父id字段才可以看到tree]
            if (option.pagination === false && AWS_ADMIN.common.isNotEmpty(options.parentIdField)) {
                // 构建tree
                var tree = {
                    idField: options.uniqueId,
                    treeShowField: options.uniqueId,
                    parentIdField: options.parentIdField,
                    rowStyle: function (row, index) {
                        return classes = [
                            'bg-blue',
                            'bg-green',
                            'bg-red'
                        ];
                    },
                    onPostBody: function onPostBody() {
                        var columns = AWS_ADMIN.btTable.bootstrapTable('getOptions').columns;
                        if (columns) {
                            AWS_ADMIN.btTable.treegrid({
                                initialState: 'collapsed',// 所有节点都折叠
                                treeColumn: 1, // 默认为第三个
                                onChange: function () {
                                    AWS_ADMIN.btTable.bootstrapTable('resetWidth');
                                }
                            });
                        }
                    },
                };
                $.extend(option, tree);
            }
            AWS_ADMIN.btTable.bootstrapTable(option);
        },

        onEditableSave: function (field, row, oldValue, $el) {
            var uniqueId= AWS_ADMIN.table._option.uniqueId;
            var id= row[uniqueId];
            var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.chooseUrl : AWS_ADMIN.table._option.chooseUrl.replace("__id__", id);
            var data = {"id": id,"field":field,"value":row[field]};
            $.ajax({
                type: "post",
                url: url,
                data: data,
                dataType: 'JSON',
                success: function (result) {
                    let msg = typeof result.msg !== 'undefined' ? result.msg : '操作成功';
                    if (result.code > 0) {
                        AWS_ADMIN.api.success(msg, result.url)
                    } else {
                        AWS_ADMIN.api.error(msg, result.url)
                    }
                },
                error: function (error) {
                    if ($.trim(error.responseText) !== '') {
                        layer.closeAll();
                        AWS_ADMIN.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                    }
                },
                complete: function () {

                }
            });
        },

        //处理select2数据表格
        onLoadSuccess:function (){
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

            $.each($('.aw-table-checkbox'),function(j,val){
                var that = $(val);
                var value = that.data('value');
                var option = that.data('option');
                that.editable({
                    value: [value],
                    source: option
                });
            })
            $.each($('.table-select2'),function(j,val){
                var obj = $(val);
                var id = obj.data('id');
                var field = obj.data('name');
                var value = obj.data('value');
                var param = obj.data('options');
                var options = [];
                value = value.split(',');
                $.each(param,function(index,item){
                    var item1={};
                    if(value.indexOf(item.id)!=-1){
                        item1={"id":item.id, "text":item.text, "selected":true}
                    }else{
                        item1={"id":item.id, "text":item.text, "selected":false}
                    }
                    options.push(item1);
                })

                obj.select2({data:options});

                obj.on('select2:select', function (e) {
                    var data = e.params.data;
                    if(value.indexOf(data.id)==-1)
                    {
                        value.push(data.id);
                        obj.data('value',value.join(','));
                        AWS_ADMIN.operate.select2(obj,id,field,value.join(','));
                    }
                });
                obj.on('select2:unselect', function (e) {
                    var data = e.params.data;
                    $.each(value,function(index,item){
                        if(item==data.id){
                            value.splice(index,1);
                        }
                    })
                    obj.data('value',value.join(','));
                    AWS_ADMIN.operate.select2(obj,id,field,value.join(','));
                });
            })
        },

        // 查询条件
        queryParams: function (params) {
            var curParams = {
                // 传递参数查询参数
                pageSize: params.limit,
                page: params.offset / params.limit + 1,
                searchValue: params.search,
                orderByColumn: params.sort,
                isAsc: params.order,
                multiSort:params.multiSort
            };
            if(null != params.multiSort && undefined != params.multiSort){
                params.sort = params.multiSort.map(item => {
                    return item.sortName;
                }).join(',');
                params.order = params.multiSort.map(item => {
                    return item.sortOrder;
                }).join(',');
                params.multiSort = null;
            }
            AWS_ADMIN.tableParam = curParams;
            var currentId = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.formId) ? 'search_form' : AWS_ADMIN.table._option.formId;
            return $.extend(curParams, AWS_ADMIN.common.formToJSON(currentId));
        },

        // 请求获取数据后处理回调函数
        responseHandler: function (res) {
            if (typeof AWS_ADMIN.table._option.responseHandler == "function") {
                AWS_ADMIN.table._option.responseHandler(res);
            }
            return {rows: res.data, total: res.total};
        },

        // 初始化事件
        initEvent: function (data) {
            // 触发行点击事件 加载成功事件
            AWS_ADMIN.btTable.on("check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table load-success.bs.table", function () {
                // 工具栏按钮控制
                var rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
                // 非多个禁用
                $('#' + AWS_ADMIN.table._option.toolbar + ' .multiple').toggleClass('disabled', !rows.length);
                // 非单个禁用
                $('#' + AWS_ADMIN.table._option.toolbar + ' .single').toggleClass('disabled', rows.length != 1);
            });
            // 绑定选中事件、取消事件、全部选中、全部取消
            AWS_ADMIN.btTable.on("check.bs.table check-all.bs.table uncheck.bs.table uncheck-all.bs.table", function (e, rows) {
                // 复选框分页保留保存选中数组
                var rowIds = AWS_ADMIN.table.affectedRowIds(rows);
                if (AWS_ADMIN.common.isNotEmpty(AWS_ADMIN.table._option.rememberSelected) && AWS_ADMIN.table._option.rememberSelected) {
                    func = $.inArray(e.type, ['check', 'check-all']) > -1 ? 'union' : 'difference';
                    selectionIds = _[func](selectionIds, rowIds);
                }
            });
        },

        // 表格销毁
        destroy: function (tableId) {
            var currentId = AWS_ADMIN.common.isEmpty(tableId) ? AWS_ADMIN.table._option.id : tableId;
            $("#" + currentId).bootstrapTable('destroy');
        },

        // 图片预览
        imageView: function (value, height, width, target) {
            if (AWS_ADMIN.common.isEmpty(width)) {
                width = 'auto';
            }
            if (AWS_ADMIN.common.isEmpty(height)) {
                height = 'auto';
            }
            // blank or self
            var _target = AWS_ADMIN.common.isEmpty(target) ? 'self' : target;
            if (AWS_ADMIN.common.isNotEmpty(value)) {
                return AWS_ADMIN.common.sprintf("<img class='img-circle img-xs' data-height='%s' data-width='%s' data-target='%s' src='%s'/>", width, height, _target, value);
            } else {
                return AWS_ADMIN.common.nullToStr(value);
            }
        },

        // 搜索-默认为 search_form
        search: function (formId, data) {
            var currentId = AWS_ADMIN.common.isEmpty(formId) ? 'search_form' : formId;
            var params = AWS_ADMIN.btTable.bootstrapTable('getOptions');
            params.queryParams = function (params) {
                // 获取所有搜索的form元素
                var search = AWS_ADMIN.common.formToJSON(currentId);

                // 如传递data则追加进search中
                if (AWS_ADMIN.common.isNotEmpty(data)) {
                    $.each(data, function (key) {
                        search[key] = data[key];
                    });
                }
                search.pageSize = params.limit;
                search.page = params.offset / params.limit + 1;
                search.searchValue = params.search;
                search.orderByColumn = params.sort;
                search.isAsc = params.order;
                return search;
            }
            AWS_ADMIN.btTable.bootstrapTable('refresh', params);
        },

        // 导出数据
        export: function (param) {
            if(param)
            {
                param = '&'+$.param(param);
            }else{
                param='';
            }
            layer.confirm('确定导出所有数据吗？<br><font color="red">导出全部：导出全部数据</font><br><font color="green">导出当前页：导出当前页数据</font><br><strong>提示</strong>：导出全部可能由于数据过大无法直接导出，建议分页导出', {
                icon: 3,
                title: "系统提示",
                btn: ['导出全部', '导出当前页']
            }, function (index) {
                layer.close(index);
                window.open(AWS_ADMIN.table._option.exportUrl + '?type=all'+param);
            },function (index){
                layer.close(index);
                if(param)
                {
                    window.open(AWS_ADMIN.table._option.exportUrl+ '?'+$.param(AWS_ADMIN.tableParam)+param);
                }else{
                    window.open(AWS_ADMIN.table._option.exportUrl+ '?'+$.param(AWS_ADMIN.tableParam));
                }
            });

            /*AWS_ADMIN.modal.confirm("确定导出所有数据吗？", function () {
                var currentId = AWS_ADMIN.common.isEmpty(formId) ? 'search_form' : formId;
                window.open(AWS_ADMIN.table._option.exportUrl + '?' + $("#" + currentId).serialize());
            });*/
        },

        // 设置排序
        sort: function (obj) {
            var url = AWS_ADMIN.table._option.sortUrl;
            var data = {"id": $(obj).data('id'), "sort": $(obj).val(),"field":$(obj).data('field')};
            AWS_ADMIN.operate.submit(url, "post", "json", data);
        },

        // 刷新表格
        refresh: function () {
            if(( AWS_ADMIN.btTable.length > 0 ))
            {
                AWS_ADMIN.btTable.bootstrapTable('refresh', {
                    silent: true
                });
            }
        },

        // 显示表格指定列
        showColumn: function (column) {
            AWS_ADMIN.btTable.bootstrapTable('showColumn', column);
        },

        // 隐藏表格指定列
        hideColumn: function (column) {
            AWS_ADMIN.btTable.bootstrapTable('hideColumn', column);
        },

        // 查询表格指定列值
        selectColumns: function (column) {
            var rows = $.map(AWS_ADMIN.btTable.bootstrapTable('getSelections'), function (row) {
                return row[column];
            });
            if (AWS_ADMIN.common.isNotEmpty(AWS_ADMIN.table._option.rememberSelected) && AWS_ADMIN.table._option.rememberSelected) {
                rows = rows.concat(selectionIds);
            }
            return AWS_ADMIN.common.uniqueFn(rows);
        },

        // 获取当前页选中或者取消的行ID
        affectedRowIds: function (rows) {
            var column = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table._option.columns[1].field : AWS_ADMIN.table._option.uniqueId;
            var rowIds;
            if ($.isArray(rows)) {
                rowIds = $.map(rows, function (row) {
                    return row[column];
                });
            } else {
                rowIds = [rows[column]];
            }
            return rowIds;
        },

        // 查询表格首列值
        selectFirstColumns: function () {
            var rows = $.map(AWS_ADMIN.btTable.bootstrapTable('getSelections'), function (row) {
                return row[AWS_ADMIN.table._option.columns[1].field];
            });
            if (AWS_ADMIN.common.isNotEmpty(AWS_ADMIN.table._option.rememberSelected) && AWS_ADMIN.table._option.rememberSelected) {
                rows = rows.concat(selectionIds);
            }
            return AWS_ADMIN.common.uniqueFn(rows);
        },
    },

    // 表单封装处理
    form: {
        // 表单重置
        reset: function (formId) {
            var currentId = AWS_ADMIN.common.isEmpty(formId) ? 'search_form' : formId;
            $("#" + currentId)[0].reset();
            // 重置select2
            $('select.select2').val(null).trigger("change");
            // 刷新表格
            AWS_ADMIN.btTable.bootstrapTable('refresh');
        },
    },

    // 弹出层封装处理
    modal: {
        // 消息提示前显示图标(通常不会单独前台调用)
        icon: function (type) {
            var icon = "";
            if (type === "warning") {
                icon = 0;
            } else if (type === "success") {
                icon = 1;
            } else if (type === "error") {
                icon = 2;
            } else {
                icon = 3;
            }
            return icon;
        },
        // 消息提示(第一个参数为内容，第二个为类型，通过类型调用不同的图标效果) [warning/success/error]
        msg: function (content, type) {
            if (type != undefined) {
                layer.msg(content, {icon: AWS_ADMIN.modal.icon(type), time: 500, anim: 5, shade: [0.3]});
            } else {
                layer.msg(content,{time: 500});
            }
        },
        // 错误消息
        msgError: function (content) {
            AWS_ADMIN.modal.msg(content, "error");
        },
        // 成功消息
        msgSuccess: function (content) {
            AWS_ADMIN.modal.msg(content, "success");
        },
        // 警告消息
        msgWarning: function (content) {
            AWS_ADMIN.modal.msg(content, "warning");
        },
        // 弹出提示
        alert: function (content, type, callback) {
            layer.msg(content, {
                icon: AWS_ADMIN.modal.icon(type),
                time: 500
            }, callback);
        },
        // 错误提示
        alertError: function (content, callback) {
            AWS_ADMIN.modal.alert(content, "error", callback);
        },
        // 成功提示
        alertSuccess: function (content, callback) {
            AWS_ADMIN.modal.alert(content, "success", callback);
        },
        // 警告提示
        alertWarning: function (content, callback) {
            AWS_ADMIN.modal.alert(content, "warning", callback);
        },
        // 确认窗体
        confirm: function (content, callBack) {
            layer.confirm(content, {
                icon: 3,
                title: "系统提示",
                btn: ['确认', '取消']
            }, function (index) {
                layer.close(index);
                callBack(true);
            });
        },
        // 消息提示并刷新父窗体
        msgReload: function (msg, type) {
            layer.msg(msg, {
                    icon: AWS_ADMIN.modal.icon(type),
                    time: 500,
                    shade: [0.1, '#8F8F8F']
                },
                function () {
                    AWS_ADMIN.modal.reload();
                });
        },
        // 弹出层指定宽度
        open: function (title, url, width, height, callback) {
            // 如果是移动端，就使用自适应大小弹窗
            if (navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                width = 'auto';
                height = 'auto';
            }
            if (AWS_ADMIN.common.isEmpty(title)) {
                title = false;
            }
            if (AWS_ADMIN.common.isEmpty(width)) {
                width = 600;
            }
            if (AWS_ADMIN.common.isEmpty(height)) {
                height = ($(window).height() - 50);
            }
            if (AWS_ADMIN.common.isEmpty(callback)) {
                // 当前层索引参数（index）、当前层的DOM对象（layero）
                callback = function (index, layero) {
                    var iframeWin = layero.find('iframe')[0];
                    iframeWin.contentWindow.submitHandler(index, layero);
                }
            }
            layer.open({
                // iframe层
                type: 2,
                // 宽高
                area: [width + 'px', height + 'px'],
                // 固定
                fix: false,
                // 最大最小化
                maxmin: true,
                // 遮罩
                shade: 0.3,
                // 标题
                title: title,
                // 内容
                content: url,
                // 按钮
                btn: ['确定', '关闭'],
                // 是否点击遮罩关闭
                shadeClose: true,
                // 确定按钮回调方法
                yes: callback,
                // 右上角关闭按钮触发的回调
                cancel: function (index) {
                    return true;
                }
            });
        },

        postOpen: function (url, title, data, options) {
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                dataType: 'json',
                success: function () {
                    return AWS_ADMIN.modal.open(title, url, options.width, options.height);
                }
            });
        },

        // 弹出层指定参数选项
        openOptions: function (options) {
            var _url = AWS_ADMIN.common.isEmpty(options.url) ? "/404.html" : options.url;
            var _title = AWS_ADMIN.common.isEmpty(options.title) ? "系统窗口" : options.title;
            var _width = AWS_ADMIN.common.isEmpty(options.width) ? "800" : options.width;
            var _height = AWS_ADMIN.common.isEmpty(options.height) ? ($(window).height() - 50) : options.height;
            var _btn = ['<i class="fa fa-check"></i> 确认', '<i class="fa fa-close"></i> 关闭'];
            if (AWS_ADMIN.common.isEmpty(options.yes)) {
                options.yes = function (index, layero) {
                    options.callBack(index, layero);
                }
            }
            layer.open({
                type: 2,
                maxmin: true,
                shade: 0.3,
                title: _title,
                fix: false,
                area: [_width + 'px', _height + 'px'],
                content: _url,
                shadeClose: AWS_ADMIN.common.isEmpty(options.shadeClose) ? true : options.shadeClose,
                skin: options.skin,
                btn: AWS_ADMIN.common.isEmpty(options.btn) ? _btn : options.btn,
                yes: options.yes,
                cancel: function () {
                    return true;
                }
            });
        },
        // 弹出层全屏
        openFull: function (title, url, width, height) {
            //如果是移动端，就使用自适应大小弹窗
            if (navigator.userAgent.match(/(iPhone|iPod|Android|ios)/i)) {
                width = 'auto';
                height = 'auto';
            }
            if (AWS_ADMIN.common.isEmpty(title)) {
                title = false;
            }
            if (AWS_ADMIN.common.isEmpty(url)) {
                url = "/404.html";
            }
            if (AWS_ADMIN.common.isEmpty(width)) {
                width = 800;
            }
            if (AWS_ADMIN.common.isEmpty(height)) {
                height = ($(window).height() - 50);
            }
            var index = layer.open({
                type: 2,
                area: [width + 'px', height + 'px'],
                fix: false,
                //不固定
                maxmin: true,
                shade: 0.3,
                title: title,
                content: url,
                btn: ['确定', '关闭'],
                // 弹层外区域关闭
                shadeClose: true,
                yes: function (index, layero) {
                    var iframeWin = layero.find('iframe')[0];
                    iframeWin.contentWindow.submitHandler(index, layero);
                },
                cancel: function (index) {
                    return true;
                }
            });
            layer.full(index);
        },
        // 重新加载
        reload: function () {
            parent.location.reload();
        },
        // 关闭窗体
        close: function () {
            var index = parent.layer.getFrameIndex(window.name);
            parent.layer.close(index);
        },
    },

    // 操作封装处理
    operate: {
        // 修改信息
        edit: function (id) {
            // 当前窗口打开要修改的地址
            var url = AWS_ADMIN.operate.editUrl(id)
            AWS_ADMIN.request.open(url, '编辑', {});
        },

        // 修改访问的地址
        editUrl: function (id) {
            var url = "";
            if (AWS_ADMIN.common.isNotEmpty(id)) {
                url = AWS_ADMIN.table._option.editUrl.replace("__id__", id);
            } else {
                var id = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
                if (id.length == 0) {
                    AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                    return;
                }
                url = AWS_ADMIN.table._option.editUrl.replace("__id__", id);
            }
            // 获取搜索信息
            var back = AWS_ADMIN.common.serializeRemoveNull($("#search_form").serialize());
            back = back ? '&back_url=' + encodeURIComponent(back) : '';
            return url + back;
        },

        // 添加信息
        add: function (id) {
            // 当前窗口打开要添加的地址
            var url = AWS_ADMIN.operate.addUrl(id)
            AWS_ADMIN.request.open(url, '添加', {});
        },

        // 添加访问的地址
        addUrl: function (id) {
            var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.addUrl.replace("__id__", "") : AWS_ADMIN.table._option.addUrl.replace("__id__", id);
            // 获取搜索信息
            var back = AWS_ADMIN.common.serializeRemoveNull($("#search_form").serialize());
            if (url.indexOf('?') != -1) {
                back = back ? '&back_url=' + encodeURIComponent(back) : '';
            } else {
                back = back ? '?back_url=' + encodeURIComponent(back) : '';
            }
            return url + back;
        },

        // 删除信息
        remove: function (id) {
            AWS_ADMIN.modal.confirm("确定删除该条数据吗？", function () {
                var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.delUrl : AWS_ADMIN.table._option.delUrl.replace("__id__", id);
                var data = {"id": id};
                AWS_ADMIN.operate.submit(url, "post", "json", data);
            });
        },

        // 批量删除信息
        removeAll: function () {
            var rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
            if (rows.length === 0) {
                AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                return;
            }
            AWS_ADMIN.modal.confirm("确认要删除选中的" + rows.length + "条数据吗?", function () {
                var url = AWS_ADMIN.table._option.delUrl.replace("__id__", rows.join());
                var data = {"id": rows.join()};
                AWS_ADMIN.operate.submit(url, "post", "json", data);
            });
        },

        //代码生成
        build: function (id, url) {
            AWS_ADMIN.modal.confirm("确定要生成代码吗？生成代码会覆盖原有的控制器、模型和验证器文件<br>注意：原有文件会被重命名留做备份", function () {
                if (AWS_ADMIN.common.isEmpty(id)) {
                    var id = AWS_ADMIN.common.isEmpty($.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
                    if (id.length == 0) {
                        AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                        return;
                    }
                }
                var data = {"id": id[0]};
                AWS_ADMIN.modal.submit(url, "post", "json", data);
            });
        },

        //自定义表格选择
        selectAll: function (url, title, type) {
            let rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
            if (rows.length === 0) {
                AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                return;
            }
            AWS_ADMIN.modal.confirm("确认要" + title + "选中的" + rows.length + "条数据吗?", function () {
                url = url.replace("__id__", rows.join());
                var data = {"id": rows.join(), "type": type};
                AWS_ADMIN.operate.submit(url, "post", "json", data);
            });
        },

        //顶部ajax请求操作
        topAjax: function (url, title) {
            let rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
            if (rows.length === 0) {
                AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                return;
            }
            AWS_ADMIN.modal.confirm("确认要" + title + "选中的" + rows.length + "条数据吗?", function () {
                url = url.replace("__id__", rows.join());
                var data = {"id": rows.join()};
                AWS_ADMIN.operate.submit(url, "post", "json", data);
            });
        },

        //顶部ajax弹窗操作
        topOpen: function (url, title) {
            let rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
            if (rows.length === 0) {
                AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                return;
            }
            AWS_ADMIN.modal.confirm("确认要" + title + "选中的" + rows.length + "条数据吗?", function () {
                url = url.replace("__id__", rows.join());
                url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1&id=" + rows.join();
                AWS_ADMIN.api.open(url,title,{});
            });
        },

        rightAjax: function (url, id) {
            if (AWS_ADMIN.common.isNotEmpty(id)) {
                url = url.replace("__id__", id);
            }
            AWS_ADMIN.request.ajax(url);
        },

        rightOpen: function (url, id, title) {
            if (AWS_ADMIN.common.isNotEmpty(id)) {
                url = url.replace("__id__", id);
            }
            AWS_ADMIN.request.open(url, title);
        },

        //自定义表格弹窗选择
        selectDialog: function (url, title) {
            let rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
            if (rows.length === 0) {
                AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                return;
            }
            AWS_ADMIN.modal.confirm("确认要" + title + "选中的" + rows.length + "条数据吗?", function () {
                url = url.replace("__id__", rows.join());
                url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1&id=" + rows.join();
                AWS_ADMIN.modal.open(title, url);
            });
        },

        // 修改状态
        state: function (id,field, url) {
            AWS_ADMIN.modal.confirm("确认要更改状态吗?", function () {
                var data = {"id": id,"field":field};
                AWS_ADMIN.operate.submit(url, "post", "json", data);
            });
        },

        select:function (element,id,field)
        {
            var value = $(element).val();
            var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.chooseUrl : AWS_ADMIN.table._option.chooseUrl.replace("__id__", id);
            var data = {"id": id,"field":field,"value":value};
            AWS_ADMIN.operate.submit(url, "post", "json", data);
        },

        select2:function (element,id,field,value)
        {
            var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.chooseUrl : AWS_ADMIN.table._option.chooseUrl.replace("__id__", id);
            var data = {"id": id,"field":field,"value":value};
            AWS_ADMIN.operate.submit(url, "post", "json", data);
        },

        radio:function (value,id,field)
        {
            var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.chooseUrl : AWS_ADMIN.table._option.chooseUrl.replace("__id__", id);
            var data = {"id": id,"field":field,"value":value};
            AWS_ADMIN.operate.submit(url, "post", "json", data);
        },

        // 数据库备份+优化+修复
        database: function (url, title) {
            var rows = AWS_ADMIN.common.isEmpty(AWS_ADMIN.table._option.uniqueId) ? AWS_ADMIN.table.selectFirstColumns() : AWS_ADMIN.table.selectColumns(AWS_ADMIN.table._option.uniqueId);
            if (rows.length === 0) {
                AWS_ADMIN.modal.alertWarning("请至少选择一条记录");
                return;
            }
            AWS_ADMIN.modal.confirm("确认要" + title + "选中的" + rows.length + "条数据吗?", function () {
                var data = {"id": rows.join()};
                AWS_ADMIN.operate.submit(url, "post", "json", data);
            });
        },

        // 提交数据
        submit: function (url, type, dataType, data, callback) {
            var config = {
                url: url,
                type: type,
                dataType: dataType,
                data: data,
                beforeSend: function () {
                    // "正在处理中，请稍后..."
                },
                success: function (result) {
                    if (typeof callback == "function") {
                        callback(result);
                    }
                    AWS_ADMIN.operate.ajaxSuccess(result);
                }
            };
            $.ajax(config)
        },

        // 保存信息 刷新表格
        save: function (url, data, callback) {
            var config = {
                url: url,
                type: "post",
                dataType: "json",
                data: data,
                success: function (result) {
                    if (typeof callback == "function") {
                        callback(result);
                    }
                    AWS_ADMIN.operate.successCallback(result);
                }
            };
            $.ajax(config)
        },

        // 成功回调执行事件（父窗体静默更新）
        successCallback: function (result) {
            if (result.code === 1) {
                var parent = window.parent;
                AWS_ADMIN.modal.close();
                parent.AWS_ADMIN.modal.msgSuccess(result.msg);
                parent.AWS_ADMIN.table.refresh();
            } else {
                AWS_ADMIN.modal.alertError(result.msg);
            }
        },

        // 保存结果弹出msg刷新table表格
        ajaxSuccess: function (result) {
            if (result.error === 0 || result.code === 1) {
                parent.layer.closeAll();
                AWS_ADMIN.modal.msgSuccess(result.msg);
                AWS_ADMIN.table.refresh();
            } else {
                AWS_ADMIN.modal.alertError(result.msg);
            }
        },

        // 展开/折叠列表树
        treeStatus: function (result) {
            if ($('.treeStatus').hasClass('expandAll')) {
                AWS_ADMIN.btTable.treegrid('collapseAll');
                $('.treeStatus').removeClass('expandAll')
            } else {
                AWS_ADMIN.btTable.treegrid('expandAll');
                $('.treeStatus').addClass('expandAll')
            }
        },
    },

    // 通用方法封装处理
    common: {
        // 判断字符串是否为空
        isEmpty: function (value) {
            return value == null || this.trim(value) === "";
        },
        // 判断一个字符串是否为非空串
        isNotEmpty: function (value) {
            return !AWS_ADMIN.common.isEmpty(value);
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
            if (AWS_ADMIN.common.isEmpty(str)) {
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
            $.pjax({url: url, container: '#wrapMain'})
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
    },

    //通用请求
    request: {
        ajax: function (options, success, error) {
            options = typeof options === 'string' ? {url: options} : options;
            options.url = options.url + (options.url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            options = $.extend({
                type: "POST",
                dataType: "json",
                success: function (ret) {
                    if (typeof success === 'function') {
                        success(ret);
                    } else {
                        AWS_ADMIN.events.onAjaxSuccess(ret, success);
                    }
                },
                error: function (xhr) {
                    if (typeof error === 'function') {
                        error(xhr);
                    } else {
                        var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                        AWS_ADMIN.events.onAjaxError(ret, error);
                    }
                }
            }, options);
            return $.ajax(options);
        },

        post: function (url, data, success, error) {
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            return $.ajax({
                type: 'POST',
                url: url,
                data: data,
                dataType: 'json',
                success: function (ret) {
                    if (ret.code === 1) {
                        if (typeof success === 'function') {
                            success(ret);
                        } else {
                            AWS_ADMIN.events.onAjaxSuccess(ret, success);
                        }
                    } else {
                        if (typeof error === 'function') {
                            error(ret);
                        } else {
                            AWS_ADMIN.events.onAjaxError(ret, error);
                        }
                    }
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_ADMIN.events.onAjaxError(ret, error);
                }
            });
        },

        get: function (url, success, error) {
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax=1";
            return $.ajax({
                type: 'GET',
                url: url,
                dataType: 'json',
                headers: {"Access-Control-Allow-Headers": "X-Requested-With"},
                success: function (ret) {
                    ret = AWS_ADMIN.events.onAjaxResponse(ret);
                    if (ret.code === 1) {
                        AWS_ADMIN.events.onAjaxSuccess(ret, success);
                    } else {
                        AWS_ADMIN.events.onAjaxError(ret, error);
                    }
                },
                error: function (xhr) {
                    var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS_ADMIN.events.onAjaxError(ret, error);
                }
            });
        },

        open: function (url, title, options) {
            title = options && options.title ? options.title : (title ? title : "");
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "_ajax_open=1";
            let width = options.width ? options.width : $(window).width() > 800 ? '800px' : '95%';
            let height = options.height ? options.height : $(window).height() > 600 ? '600px' : '95%';
            let area = [width, height];
            options = $.extend({
                type: 2,
                title: title,
                shadeClose: true,
                scrollbar: false,
                shade: 0.7,
                maxmin: true,
                moveOut: true,
                area: area,
                content: url,
                success: function (layero, index) {
                    const that = this;
                    //检测弹窗是否是提示信息
                    var text = window["layui-layer-iframe" + index].document.body.innerText;
                    if (text.indexOf('"code":0') != -1 || text.indexOf('"code":1') != -1) {
                        var result = JSON.parse(text);
                        parent.layer.close(index);
                        layer.msg(result.msg,{time: 500});
                    }

                    //存储callback事件
                    $(layero).data("callback", that.callback);
                    layer.setTop(layero);
                    if ($(layero).height() > $(window).height()) {
                        //当弹出窗口大于浏览器可视高度时,重定位
                        layer.style(index, {
                            top: 0,
                            height: $(window).height()
                        });
                    }
                }
            }, options ? options : {});
            return layer.open(options);
        },
    },

    lang: function () {
        var args = arguments,
            string = args[0],
            i = 1;
        string = string.toLowerCase();
        if (string.indexOf('.') !== -1 && false) {
            var arr = string.split('.');
            var current = Lang[arr[0]];
            for (var i = 1; i < arr.length; i++) {
                current = typeof current[arr[i]] != 'undefined' ? current[arr[i]] : '';
                if (typeof current != 'object')
                    break;
            }
            if (typeof current == 'object')
                return current;
            string = current;
        } else {
            string = args[0];
        }
        return string.replace(/%((%)|s|d)/g, function (m) {
            var val = null;
            if (m[2]) {
                val = m[2];
            } else {
                val = args[i];
                switch (m) {
                    case '%d':
                        val = parseFloat(val);
                        if (isNaN(val)) {
                            val = 0;
                        }
                        break;
                }
                i++;
            }
            return val;
        });
    },

    init:function (){
        //全局pjax方法
        if($.support.pjax)
        {
            $.pjax.defaults.timeout = 1200;
            $(document).on('click', 'a[data-pjax],a[target!=_blank]', function(event) {
                let container = $(this).attr('data-pjax')
                let containerSelector = '#' + container;
                $.pjax.defaults.fragment = containerSelector;
                $.pjax.click(event, {container: containerSelector,scrollTo:container})
            })

            $(document).on('pjax:timeout', function(event) {
                event.preventDefault()
            })
        }

        //pjax标签导航点击
        $(document).on('click','.aw-pjax-tabs li',function (){
            $(this).parent('.aw-pjax-tabs').find('li a').removeClass('active');
            $(this).find('a').addClass('active');
        });

        //ajax表单提交带验证
        $(document).on('click', '.ajax-form,.aw-ajax-form', function (e) {
            let that = this;
            let options = $.extend({}, $(that).data() || {});
            let form = $($(that).parents('form')[0]);
            let success = typeof options.success === 'function' ? options.success : null;
            if(!form.serializeArray()){
                return false;
            }
            delete options.success;
            delete options.error;

            $(that).attr('type', 'button');

            if (options.confirm) {
                layer.confirm(options.confirm,{
                    btn: ['确认', '取消']
                }, function () {
                    $.ajax({
                        url: form.attr('action'),
                        dataType: 'json',
                        type: 'post',
                        data: form.serialize(),
                        beforeSend: function(){
                            $(".ajax-form").attr({ disabled: "disabled" });
                        },
                        success: function (result) {
                            if (typeof success !== 'function') {
                                let msg = result.msg ? result.msg : '操作成功';
                                if (result.code > 0) {
                                    parent.AWS_ADMIN.table.refresh();
                                    AWS_ADMIN.api.success(msg, result.url)
                                } else {
                                    AWS_ADMIN.api.error(msg, result.url)
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
                                AWS_ADMIN.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                            }
                        }
                    });
                }, function(){
                    layer.closeAll();
                });
            } else {
                $.ajax({
                    url: form.attr('action'),
                    dataType: 'json',
                    type: 'post',
                    data: form.serialize(),
                    beforeSend: function(){
                        $(".ajax-form").attr({ disabled: "disabled" });
                    },
                    success: function (result) {
                        if (typeof success !== 'function') {
                            let msg = result.msg ? result.msg : '操作成功';
                            if (result.code > 0) {
                                parent.AWS_ADMIN.table.refresh();
                                AWS_ADMIN.api.success(msg, result.url)
                            } else {
                                AWS_ADMIN.api.error(msg, result.url)
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
                            AWS_ADMIN.api.error('发生错误, 返回的信息:' + ' ' + error.responseText);
                        }
                    }
                });
            }
        });

        //ajax获取
        $(document).on('click', '.aw-ajax-get,.ajax-get', function (e) {
            var that = this;
            var options = $.extend({}, $(that).data() || {});
            if (typeof options.url === 'undefined' && $(that).attr("data-url")) {
                options.url = $(that).attr("data-url");
            }
            let success = typeof options.success === 'function' ? options.success : null;
            let error = typeof options.error === 'function' ? options.error : null;
            delete options.success;
            delete options.error;

            if(options.login && !userId)
            {
                layer.msg('您还未登录,请登录后再操作!',{time: 500});
                return false;
            }

            if (options.confirm) {
                layer.confirm(options.confirm,{
                    btn: ['确认', '取消']
                }, function () {
                    AWS_ADMIN.api.get(options.url, success, error);
                }, function(){
                    if (typeof options.cancel === 'undefined') {
                        layer.closeAll();
                    }else{
                        AWS_ADMIN.api.get(options.cancel, success, error);
                    }
                });
            } else {
                AWS_ADMIN.api.get(options.url, success, error);
            }
        });

        //ajax 通用POST提交
        $(document).on('click', '.ajax-post', function (e) {
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
                        AWS_ADMIN.api.success(data.msg);
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
                        AWS_ADMIN.api.error(data.msg);
                        setTimeout(function () {
                            $(that).removeClass('disabled').prop('disabled', false);
                        }, 1500);
                    }
                });
            }
            return false;
        });

        //弹窗点击
        $(document).on('click', '.aw-ajax-open', function (e) {
            e.preventDefault();
            e.target.blur();
            var that = this;
            var options = $(that).data();
            var url = $(that).data("url") ? $(that).data("url") : $(that).attr('href');
            var title = $(that).attr("title") || $(that).data("title") || $(that).data('original-title');
            if (typeof options.confirm !== 'undefined') {
                layer.confirm(options.confirm,{
                    btn: ['确认', '取消']
                }, function () {
                    AWS_ADMIN.request.open(url, title, options);
                }, function(){
                    layer.closeAll();
                });
            } else {
                AWS_ADMIN.request.open(url, title, options);
            }
            return false;
        });

        //ajax获取
        /*$(document).on('click', '.aw-table-checkbox', function (e) {
            var that = $(this);
            var value = that.data('value');
            var option = that.data('option');
            that.editable({
                value: [value],
                source: option
            });
        });*/


        function changeDateFormat(value) {
            if (value == '') {
                return '-';
            }
            if(value != null && value != undefined){
                if (value.toString().indexOf("-") >= 0) {
                    return value;
                }
            }
            var dateVal = value * 1000;
            if (value != null) {
                var date = new Date(dateVal);
                var month = date.getMonth() + 1 < 10 ? "0" + (date.getMonth() + 1) : date.getMonth() + 1;
                var currentDate = date.getDate() < 10 ? "0" + date.getDate() : date.getDate();

                var hours = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
                var minutes = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
                var seconds = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();

                return date.getFullYear() + "-" + month + "-" + currentDate + " " + hours + ":" + minutes + ":" + seconds;
            }
        }
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
                server: '/upload/index?upload_type=' + type+'&path='+path, // 文件接收服务端
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
                    layer.msg(response.msg,{time: 500});
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
                    layer.msg('请上传' + upload_allowExt + '格式的文件！',{time: 500});
                } else if (type == "F_EXCEED_SIZE") {
                    layer.msg('单个文件大小不能超过' + size / 1024 + 'kb！',{time: 500});
                } else if (type == "F_DUPLICATE") {
                    layer.msg('请不要重复选择文件',{time: 500});
                } else {
                    layer.msg('上传出错！请检查后重新上传！错误代码' + type,{time: 500});
                }
            });
        },

        removeAttach:function (obj,attachId,access_key)
        {
            AWS.api.post(baseUrl+'/upload/remove_attach',{id:attachId,access_key:access_key},function (res){
                if(res.code)
                {
                    $(obj).parents('tr').detach();
                }
                layer.msg(res.msg,{time: 500});
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
        }
    },
};

AWS_ADMIN.init();
window.AWS_ADMIN = AWS_ADMIN;
// 多图删除
$(document).on('click','.remove_images',function()
{
    var remove = $(this).parent().parent();
    remove.remove();
})

function upload(list, filePicker_image, image_preview, image, more, upload_allowext, size, type, path) {
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

/*! 异步任务状态监听与展示 */
$(document).on('click', '.do-queue', function (action) {
    action = this.dataset.url || '';
    if (action.length < 1) return $.msg.tips('任务地址不能为空！');
    this.doRuntime = function (index) {
        $.ajax({
            url: action,
            dataType: 'json',
            type: 'post',
            success: function (result) {
                if (result.code > 0) {
                    return $.loadQueue(result.data, true);
                }
            },
            error: function (error) {
                if ($.trim(error.responseText) !== '') {
                    layer.closeAll();
                    layer.msg('发生错误, 返回的信息:' + ' ' + error.responseText,{time: 500});
                }
            }
        });
        layer.close(index);
    };
    this.dataset.confirm ? layer.confirm(this.dataset.confirm, this.doRuntime) : this.doRuntime(0);
});

$.loadQueue = function (code, doScript, doAjax) {
    layer.open({
        type: 1, title: false, area: ['560px', '315px'], anim: 2, shadeClose: false, end: function () {
            doAjax = false;
        }, content: '' +
            '<div class="padding-30 padding-bottom-0" style="width:100%;height:100%;" id="' + code + '">' +
            '   <div class="layui-elip nowrap" data-message-title></div>' +
            '   <div class="margin-top-15 layui-progress layui-progress-big" lay-showPercent="yes"><div class="layui-progress-bar transition" lay-percent="0.00%"></div></div>' +
            '   <div class="margin-top-15" style="height:90%;"><textarea class="layui-textarea layui-bg-black border-0" disabled style="resize:none;overflow:hidden;height:100%"></textarea></div>' +
            '</div>'
    });
    (function loadProcess(code, that) {
        that = this, this.$box = $('#' + code);
        if (doAjax === false || that.$box.length < 1) return false;
        this.$area = that.$box.find('textarea'), this.$title = that.$box.find('[data-message-title]');
        this.$percent = that.$box.find('.layui-progress div'), this.runCache = function (code, index, value) {
            this.ckey = code + '_' + index, this.ctype = 'admin-queue-script';
            return value !== undefined ? layui.data(this.ctype, {
                key: this.ckey,
                value: value
            }) : layui.data(this.ctype)[this.ckey] || 0;
        };
        this.setState = function (status, message) {
            if (message.indexOf('javascript:') === -1) if (status === 1) {
                that.$title.html('<b class="color-text">' + message + '</b>').addClass('text-center');
                that.$percent.addClass('layui-bg-blue').removeClass('layui-bg-green layui-bg-red');
            } else if (status === 2) {
                if (message.indexOf('>>>') > -1) {
                    that.$title.html('<b class="color-blue">' + message + '</b>').addClass('text-center');
                } else {
                    that.$title.html('<b class="color-blue">正在处理：</b>' + message).removeClass('text-center');
                }
                that.$percent.addClass('layui-bg-blue').removeClass('layui-bg-green layui-bg-red');
            } else if (status === 3) {
                that.$title.html('<b class="color-green">' + message + '</b>').addClass('text-center');
                that.$percent.addClass('layui-bg-green').removeClass('layui-bg-blue layui-bg-red');
            } else if (status === 4) {
                that.$title.html('<b class="color-red">' + message + '</b>').addClass('text-center');
                that.$percent.addClass('layui-bg-red').removeClass('layui-bg-blue layui-bg-green');
            }
        };
        AWS_ADMIN.api.post('progress', {code: code}, function (ret) {
            if (ret.code) {
                that.lines = [];
                for (this.lineIndex in ret.data.history) {
                    this.line = ret.data.history[this.lineIndex], this.percent = '[ ' + this.line.progress + '% ] ';
                    if (this.line.message.indexOf('javascript:') === -1) {
                        that.lines.push(this.line.message.indexOf('>>>') > -1 ? this.line.message : this.percent + this.line.message);
                    } else if (!that.runCache(code, this.lineIndex) && doScript !== false) {
                        that.runCache(code, this.lineIndex, 1), location.href = this.line.message;
                        // 跳转
                        var str = this.line.message;
                        str = str.replace('>>> javascript:', '');
                        str = str.replace(' <<<', '');
                        location.href = str;
                    }
                }
                that.$area.val(that.lines.join("\n")), that.$area.animate({scrollTop: that.$area[0].scrollHeight + 'px'}, 200);
                that.$percent.attr('lay-percent', (parseFloat(ret.data.progress || '0.00').toFixed(2)) + '%');
                if (ret.data.status > 0) that.setState(parseInt(ret.data.status), ret.data.message);
                else return that.setState(4, '获取任务详情失败！');
                if (parseInt(ret.data.status) === 3 || parseInt(ret.data.status) === 4) return false;
                return setTimeout(function () {
                    loadProcess(code);
                }, Math.floor(Math.random() * 200)), false;
            }
        });
    })(code)
};