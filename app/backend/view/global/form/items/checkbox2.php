<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <input type="hidden" name="{$form[type].name}" class="{notempty name="form[type].required"}required{/notempty}" value="{$form[type].value|default=''}">
    <div id="{$form[type].name}" {$form[type].extra_attr|default=''} style="background: #fff;padding: 10px"></div>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>

<div style="display: none" id="{$form[type].name}Data">
    {$form[type].options}
</div>

<script>
    $(function(){
        var treeData = eval('('+$("#{$form[type].name}Data").text()+')');
        $("#{$form[type].name}").jstree({
            "core": {
                "data": treeData
            },
            "checkbox" : {
                "keep_selected_style": false,//是否默认选中
                "three_state": false,//父子级别级联选择
                "tie_selection": true,
                "cascade":'undetermined'
            },
            "plugins" : ["checkbox", "wholerow"]
        })
    });

    $("#{$form[type].name}").on('changed.jstree', function(e,data){
        $("input[name={$form[type].name}]").val(data.selected.join(','));
    })
</script>

