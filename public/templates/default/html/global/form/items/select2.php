<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <select class="select2 form-control {notempty name="form[type].required"}required{/notempty}" id="{$form[type].name}" name="{$form[type].name}" data-value="{$form[type].value}" {$form[type].extra_attr|default=''}>
        <option value="">{$form[type].placeholder}</option>
        {volist name="form[type].options" id="option"}
        <option value="{$key}" {in name="key" value="$form[type].value"}selected{/in}>{$option}</option>
        {/volist}
    </select>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>
<script>
    $(function () {
        var option = {};
        {if !$form[type].options}
        if('{$form[type].url}' !== ''){
            // 启用ajax分页查询
            option = {
                language: "zh-CN",
                //allowClear: true,
                ajax: {
                    delay: 250, // 限速请求
                    url: "{$form[type].url}",   //  请求地址
                    dataType: 'json',
                    data: function (params) {
                        return {
                            keyWord: params.term || '',    //搜索参数
                            page: params.page || 1,        //分页参数
                            rows: params.pagesize || 10,   //每次查询10条记录
                        };
                    },
                    processResults: function (data, params) {
                        params.page = params.page || 1;
                        if (params.page == 1) {
                            data.data.unshift({id: '', name: "", text: "{$form[type].placeholder}"});
                        }
                        return {
                            results: data.data,
                            pagination: {
                                more: (params.page) < data.last_page
                            }
                        };
                    },
                    cache: true
                }
            };
            // 默认值设置
            var defaultValue = $('#{$form[type].name}').data("value");
            if (defaultValue) {
                $.ajax({
                    type: "POST",
                    url: "{$form[type].url}",
                    data: {value:defaultValue},
                    dataType: "json",
                    success: function(data){
                        $('#{$form[type].name}').append("<option selected value='" + data.key + "'>" + data.value + "</option>");
                    }
                });
            }
        }
        {/if}
        $('#{$form[type].name}').select2(option);
    })
</script>
