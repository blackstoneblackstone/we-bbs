{extend name="block" /}
{block name="main"}
<style>
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        font-size: 0.7rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__rendered{padding: 0 5px}
    .select2-container--default.select2-container--focus .select2-selection--multiple, .select2-container--default.select2-container--focus .select2-selection--single, .select2-container--default.select2-container--open .select2-selection--multiple, .select2-container--default.select2-container--open .select2-selection--single {
        border: none;
        box-shadow:none
    }
    .select2-container--default .select2-selection--multiple{/*background: none;*/border: none;border-color: unset}
</style>
<section class="p-3">
    <!--额外CSS代码-->
    {$extra_css|raw|default=''}
    <!--额外HTML代码-->
    {$extra_html_top|raw|default=''}
    <!--顶部提示开始-->
    {if $page_tips_top}
    <div class="alert alert-{$tips_type} alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p>{$page_tips_top|raw}</p>
    </div>
    {/if}
    <!--顶部提示结束-->
    <div class="container-fluid">
        <div class="row">
            {if !empty($group)}
            <div class="w-100 bg-white mb-2">
                <ul class="nav nav-tabs nav-tabs-alt js-tabs-enabled">
                    {volist name="group" id="item" key="items_key"}
                    <li class="nav-item">
                        <a class="nav-link {if $item.active}active{/if}" href="{$item.link}" data-pjax="wrapMain">{:L($item.title)}</a>
                    </li>
                    {/volist}
                </ul>
            </div>
            {/if}
            <!--搜索区域开始-->
            {if $search OR $page_tips_search }
            <div class="col-12 search-collapse">
                <fieldset>
                    <legend>{:L('条件选项')}</legend>
                    <form id="search_form" class="mb-0">
                        <div class="select-list">
                            {notempty name="page_tips_search"}{$page_tips_search|raw}{/notempty}
                            {notempty name="search"}
                            <ul class="clearfix mb-0">
                                {volist name="search" id="search"}
                                <li>
                                    <label>{$search.title|default=''}： </label>
                                    {if $search.param }
                                    {if $search.type == 'text' || $search.type == 'textarea' || $search.type == 'number' || $search.type == 'hidden'}
                                    <input type="text" id="search_{$search.name|default=''}" name="{$search.name|default=''}" value="{$search.default|default=''}"/>
                                    {else}
                                    <select id="search_{$search.name|default=''}"  name="{$search.name|default=''}">
                                        <option value="">{:L('所有')}</option>
                                        {notempty name="search.param"}
                                        {volist name="search.param" id="v"}
                                        <option value="{$key}" {if ((string)$search.default == (string)$key)}selected{/if}>{$v}</option>
                                        {/volist}
                                        {/notempty}
                                    </select>
                                    {/if}
                                    {else}
                                    {if $search.type == 'date' OR $search.type == 'time' OR $search.type == 'datetime' }
                                    {// 日期类型的数据 }
                                    <input type="text" id="search_{$search.name|default=''}" name="{$search.name|default=''}" value="{$search.default|default=''}" autocomplete="off"/>
                                    <script>
                                       var endTime=layui.laydate.render({
                                           elem:'#search_{$search.name|default=''}',
                                           type: 'datetime',
                                           range: "至",
                                       })
                                   </script>
                                    {elseif $search.type == 'select2'}
                                    <select class="select2" id="search_{$search.name|default=''}" name="{$search.name|default=''}" data-value="{$search.default|default=''}">
                                        <option value="">{:L('所有')}</option>
                                        {notempty name="search.param"}
                                        {volist name="search.param" id="v"}
                                        <option value="{$key}" {if ((string)$search.default == (string)$key)}selected{/if}>{$v}</option>
                                        {/volist}
                                        {/notempty}
                                    </select>
                                    <script>
                                        $(function () {
                                            var option = {};
                                            {if !$search.param}
                                            // 启用ajax分页查询
                                            option = {
                                                language: "zh-CN",
                                                ajax: {
                                                    delay: 250, // 限速请求
                                                    url: "{$search.param['select_url']}",   //  请求地址
                                                    dataType: 'json',
                                                    data: function (params) {
                                                        return {
                                                            keyWord: params.term || '',    //搜索参数
                                                            page: params.page || 1,        //分页参数
                                                            rows: params.page_size || 10,   //每次查询10条记录
                                                        };
                                                    },
                                                    processResults: function (data, params) {
                                                        params.page = params.page || 1;
                                                        if (params.page === 1) {
                                                            data.data.unshift({id: '', name: "", text: "所有"});
                                                        }
                                                        return {
                                                            results: data.data?:search.param,
                                                            pagination: {
                                                                more: (params.page) < data.last_page
                                                            }
                                                        };
                                                    },
                                                    cache: true
                                                }
                                            };
                                            // 默认值设置
                                            var defaultValue = $("#search_{$search.name|default=''}").data("value");
                                            if (defaultValue) {
                                                $.ajax({
                                                    type: "POST",
                                                    url: "{$search.param['select_url']}",
                                                    data: {value: defaultValue},
                                                    dataType: "json",
                                                    async: false,
                                                    success: function(data){
                                                        $("#search_{$search.name|default=''}").append("<option selected value='" + data.key + "'>" + data.value + "</option>");
                                                    }
                                                });
                                            }
                                            {/if}
                                            $("#search_{$search.name|default=''}").select2(option);
                                        })
                                    </script>
                                    {else}
                                    <input type="text" id="search_{$search.name|default=''}" name="{$search.name|default=''}" value="{$search.default|default=''}"/>
                                    {/if}
                                    {/if}
                                </li>
                                {/volist}
                                <li>
                                    <a class="btn btn-primary btn-rounded btn-sm" onclick="AWS_ADMIN.table.search()"><i class="fa fa-search"></i>&nbsp;{:L('搜索')}</a>
                                    <a class="btn btn-warning btn-rounded btn-sm" onclick="resetPre()"><i class="fas fa-sync-alt"></i>&nbsp;{:L('重置')}</a>
                                </li>
                            </ul>
                            {/notempty}
                        </div>
                    </form>
                </fieldset>
            </div>
            {/if}
            <!--列表区域开始-->
            <div class="col-sm-12 select-table table-striped">
                <div class="btn-group-sm" id="toolbar" role="group">
                {volist name="top_buttons" id="top_button"}
                <a class="{$top_button.class|default=''}" {if isset($top_button['href']) && $top_button['href']}href="{$top_button.href|default=''}"{/if}{if isset($top_button['target']) && $top_button['target']} target="{$top_button.target|default=''}"{/if}{if isset($top_button['onclick']) && $top_button['onclick']} onclick="{$top_button.onclick|default=''}"{/if} title="{$top_button.title|default=''}" {if isset($top_button['url']) && $top_button['url']} data-url="{$top_button.url|default=''}"{/if} >
                    <i class="{$top_button.icon|default=''}"></i> {:L($top_button.title)}
                </a>
                {/volist}
                </div>

                {if !empty($second_group)}
                <div class="w-100 bg-white mb-2">
                    <ul class="nav nav-tabs nav-tabs-alt js-tabs-enabled">
                        {volist name="second_group" id="item" key="items_key"}
                        <li class="nav-item">
                            <a class="nav-link {if $item.active}active{/if}" href="{$item.link}" data-pjax="wrapMain">{:L($item.title)}</a>
                        </li>
                        {/volist}
                    </ul>
                </div>
                {/if}

                <table id="bootstrap-table" data-mobile-responsive="true"></table>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $.fn.editable.defaults.mode = 'inline';
            let columns = eval({:json_encode($columns,JSON_UNESCAPED_UNICODE)});
            let right_buttons = eval({:json_encode($right_buttons,JSON_UNESCAPED_UNICODE)});
            let list=[];
            let tmp =[]
            tmp.push({
                checkbox: {$hide_checkbox ? 'true' : 'false'},
                formatter: function(value, row, index) {
                    if(row.checkbox_disabled =='1'){
                        return {
                            disabled : true
                        }
                    }
                }
            });
            $.each(columns, function(i, item) {
                var sortable = item.name=='{$unique_id}' || item.sortable == 'true';
                var class1 = item.class==='' ? '' : item.class;
                var format;
                var editable;
                if (item.name === 'sort')
                {
                    format = function(value, row, index) {
                        var val = value?value:item.default;
                        return '<input class="form-control input-sm w_40 changeSort" type="text" value="' + val + '" data-field="sort" data-id="' + row.{$unique_id} + '" onblur="AWS_ADMIN.table.sort(this)">';
                    }
                }else{
                    switch (item.type){
                        case 'icon':
                        case 'text':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                return HTMLDecode(val);
                            }
                            break;
                        case 'input':
                        case 'sort':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                return '<input class="form-control input-sm w_40 changeSort" type="text" value="' + val + '" data-field="' + item.name + '" data-id="' + row.{$unique_id} + '" onblur="AWS_ADMIN.table.sort(this)">';
                            }
                            break;
                        case 'datetime':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                return changeDateFormat(val);
                            }
                            break;
                        case 'radio':
                        case 'status':
                            format = function(value, row, index) {
                                if (value === 0) {
                                    return '<i class="fa fa-toggle-off text-primary fa-2x cursor_pointer" onclick="AWS_ADMIN.operate.state(\'' + row.{$unique_id} + '\',\''+item.name+'\',\'{$state_url|raw}\')"></i>';
                                } else {
                                    return '<i class="fa fa-toggle-on text-primary fa-2x cursor_pointer" onclick="AWS_ADMIN.operate.state(\'' + row.{$unique_id} + '\',\''+item.name+'\',\'{$state_url|raw}\')"></i>';
                                }
                            }
                            break;

                        case 'tag':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                if(item.param)
                                {
                                    return '<span class="badge badge-primary">'+HTMLDecode(item.param[val])+'</span>'
                                }else{
                                    return '<span class="badge badge-primary">'+HTMLDecode(val)+'</span>'
                                }
                            }
                            break;

                        case 'label':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                if(item.param)
                                {
                                    return '<span class="badge badge-'+item.param[val].label+'">'+HTMLDecode(item.param[val].text)+'</span>'
                                }else{
                                    return '<span class="badge badge-primary">'+HTMLDecode(val)+'</span>'
                                }
                            }
                            break;

                        case 'bool':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                if (val == 0) {
                                    return '<i class="fa fa-ban text-danger"></i>';
                                } else if (val == 1) {
                                    return '<i class="fa fa-check text-primary"></i>';
                                }
                            }
                            break;

                        case 'link':
                            format = function(value, row, index) {
                                var link = item.default;
                                var reg = /__(.*?)__/g;
                                var result ;
                                var val = value?value:item.default;
                                while (result = reg.exec(link)) {
                                    link = link.replace(result[0], row[result[1]]);
                                }
                                // 拼接
                                link = '<a href="'+link+'" class="'+item.class+'" target="_blank" title="'+item.title+'">' + val + '</a>';
                                return link;
                            }
                            break;

                        case 'dialog':
                            format = function(value, row, index) {
                                var link = item.default;
                                var reg = /__(.*?)__/g;
                                var result ;
                                var val = value?value:item.default;
                                while (result = reg.exec(link)) {
                                    link = link.replace(result[0], row[result[1]]);
                                }
                                // 拼接
                                link = '<a href="javascript:;" data-url="'+link+'" class="aw-ajax-open '+item.class+'" data-title="'+item.title+'" title="'+item.title+'">' + val + '</a>';
                                return link;
                            }
                            break;

                        case 'image':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                if (AWS_ADMIN.common.isNotEmpty(val)) {
                                    return '<a href="' + val + '" data-fancybox="fancybox" data-fancybox-group="aw-thumb"> <img class="image_preview" src="' + val + '"></a>';
                                }
                            }
                            break;

                        case 'color':
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                if (AWS_ADMIN.common.isNotEmpty(value)) {
                                    return '<i class="table_colorpicker" style="background: ' + val + '""></i>';
                                }
                            }
                            break;

                        case 'select':
                            format = function(value, row, index) {
                                var val1 = value?value:item.default;
                                var str = '<select onchange="AWS_ADMIN.operate.select(this,\''+ row.{$unique_id} +'\',\''+item.name+'\')" class="form-control">';
                                str+='<option  value="0">--请选择--</option>'
                                $.each(item.param,function(val,name) {
                                    var selected = '';
                                    if(val1==val)
                                    {
                                        selected ='selected=selected';
                                    }
                                    str+='<option  value="'+val+'" '+ selected+'>'+name+'</option>'
                                });
                                str +='</select>';
                                return str;
                            }

                            /*var param = JSON.stringify(item.param);

                            editable = {
                                type:'select',
                                source:param,
                                formatter : function(value,row,index) {
                                    return value;
                                }
                            }*/
                            break;


                        case 'checkbox':
                            /*format = function(value, row, index) {
                                var param = JSON.stringify(item.param);
                                return  '<a href="javascript:;" class="aw-table-checkbox" data-option=\''+param+'\' data-name="'+item.name+'" data-type="checklist" data-pk="'+row.{$unique_id}+'" data-value="'+value+'" data-url="{$choose_url}" data-title="请选择..."></a>';
                            }
                            break;*/

                        case 'select2':
                            format = function(value, row, index) {
                                var param = JSON.stringify(item.param);
                                var val = value?value:item.default;
                                var  str = '<select style="min-width:150px" class="select2 table-select2" data-id="'+row.{$unique_id}+'" data-name="'+item.name+'" data-options=\''+param+'\' data-value="'+val+'" multiple ></select>';
                                return str;
                            }
                            /*var param = JSON.stringify(item.param);
                            editable = {
                                type:'select2',
                                source: param,
                                select2: {
                                    multiple: true,
                                    width : '150px',//设置宽
                                    id: function (item) {
                                        return item.text;
                                    }

                                }
                            }*/
                            break;

                        case 'btn':
                            format = function(value, row, index) {
                                var actions = [];
                                $.each(right_buttons, function(i2, item2) {
                                    var confirm = '';
                                    if(typeof item2.confirm == 'undefined' || item2.confirm =='')
                                    {
                                        confirm = '';
                                    }else{
                                        confirm = 'data-confirm="'+item2.confirm+'"';
                                    }
                                    if(item2.type == 'edit'){
                                        if(item2.href){
                                            var url = item2.href;
                                            var reg = /__(.*?)__/g;
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="m-1 '+item2.class+'" title="'+item2.title+'" target="'+item2.target+'" href="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }else{
                                            actions.push('<a class="m-1 '+item2.class+'" href="javascript:void(0)" title="'+item2.title+'" onclick="AWS_ADMIN.operate.edit(\'' + row.{$unique_id} + '\')"><i class="'+item2.icon+'"></i>'+item2.title+'</a> ');
                                        }
                                    } else if (item2.type == 'delete'){
                                        if(item2.href){
                                            var url = item2.href;
                                            var reg = /__(.*?)__/g;
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="m-1 '+item2.class+'" target="'+item2.target+'" title="'+item2.title+'" href="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }else{
                                            actions.push('<a class="m-1 '+item2.class+'" href="javascript:void(0)" title="'+item2.title+'" onclick="AWS_ADMIN.operate.remove(\'' + row.{$unique_id} + '\')"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }
                                    }else {
                                        if(item2.href && !item2.url && item2.href!=='javascript:;'){
                                            var url = item2.href;
                                            var reg = /__(.*?)__/g;
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="m-1 '+item2.class+'" target="'+item2.target+'" title="'+item2.title+'" href="'+url+'"><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }else{
                                            var url = item2.url;
                                            var reg = /__(.*?)__/g;
                                            while (result = reg.exec(url)) {
                                                url = url.replace(result[0], row[result[1]]);
                                            }
                                            actions.push('<a class="m-1 '+item2.class+'" title="'+item2.title+'" data-url="'+url+'"' +confirm +'><i class="'+item2.icon+'"></i> '+item2.title+'</a> ');
                                        }
                                    }
                                })
                                return actions.join('');
                            }
                            break;

                        //单列提交
                        case 'submit':
                            format = function(value, row, index) {
                                var url = '"{$submit_url|raw}"';
                                var json = JSON.stringify(row);
                                var title = value?value:item.title;
                                var html = '<a class="m-1 btn btn-sm btn-primary '+item.class+'" title="'+title+'" onclick=\'AWS_ADMIN.api.post('+url+','+ json+')\' href="javascript:;">'+title+'</a> ';
                                return html;
                            }
                            break;

                        default :
                            format = function(value, row, index) {
                                var val = value?value:item.default;
                                return HTMLDecode(val);
                            }
                    }
                }
                var data={field: item.name,
                    title: item.title,
                    sortable:sortable,
                    class:class1,
                    formatter:eval(format),
                    editable: eval(editable)
                }
                tmp.push(data)
            });
            list.push(tmp);
            let column = list.reduce(function(reduced,next){
                    Object.keys(next).forEach(function(key){reduced[key]=next[key];});
                    return reduced;
                }
            );

            let options = {
                uniqueId      : "{$unique_id}",         // 表格主键名称，（默认为id，如表主键不为id必须设置主键）
                url           : "{$data_url|raw}",      // 请求后台的URL
                addUrl        : "{$add_url|raw}",       // 新增的地址
                editUrl       : "{$edit_url|raw}",      // 修改的地址
                delUrl        : "{$del_url|raw}",       // 删除的地址
                exportUrl     : "{$export_url|raw}",    // 导出的地址
                sortUrl       : "{$sort_url|raw}",      // 排序的地址
                chooseUrl     : "{$choose_url|raw}",    // 下拉选择|radio单选
                sortName      : "{$unique_id}",         // 排序列名称
                sortOrder     : "desc",                 // 排序方式  asc 或者 desc
				pagination    : {$pagination},			// 是否进行分页
                parentIdField : "{$parent_id_field}",   // 列表树模式需传递父id字段名（parent_id/pid）
				clickToSelect : false,				    // 默认false不响应，设为true则当点击此行的某处时，会自动选中此行的checkbox/radiobox
                pageSize      : "{$page_size}",         // 每页显示的行数
                editable: true,
                columns:column,
            };

            AWS_ADMIN.table.init(options);
        });

        function select(element,id,field)
        {
            var value = $('#'+element+' option:selected') .val();
            var url = AWS_ADMIN.common.isEmpty(id) ? AWS_ADMIN.table._option.selectUrl : AWS_ADMIN.table._option.selectUrl.replace("__id__", id);
            var data = {"id": id,"field":field,"value":value};
            AWS_ADMIN.operate.submit(url, "post", "json", data);
        }

        // 搜索
        function searchPre() {
            var data = {};
            AWS_ADMIN.table.search('', data);
        }

        // 重置搜索
        function resetPre() {
            AWS_ADMIN.form.reset();
        }

		//HTML反转义
		function HTMLDecode(text) { 
			var temp = document.createElement("div"); 
			temp.innerHTML = text; 
			var output = temp.innerText || temp.textContent; 
			temp = null; 
			return output; 
		}

		function getDefaultValue(value,text)
        {
            return value ? value : text;
        }
    </script>
    <!--底部提示-->
    {if $page_tips_bottom}
    <div class="alert alert-{$tips_type} alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <p>{$page_tips_bottom|raw}</p>
    </div>
    {/if}

    <!--额外HTML代码-->
    {$extra_html_bottom|raw|default=''}

    <!--额外JS代码-->
    {$extra_js|raw|default=''}
</section>
{/block}

