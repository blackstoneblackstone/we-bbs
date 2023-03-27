<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <textarea id="{$form[type].name}" class="{notempty name="form[type].required"}required{/notempty}" rows="3" name="{$form[type].name}" {$form[type].extra_attr|raw}>{$form[type].value}</textarea>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>
<script>
    $(function () {
        var codeEditor = CodeMirror.fromTextArea(document.getElementById("{$form[type].name}"), {
            mode: "{$form[type].mode}",     // 编辑器语言
            theme: "material",   // 编辑器主题
            lineNumbers: true,              // 显示行号
            showCursorWhenSelecting: true,  // 文本选中时显示光标
            lineWrapping: true,             // 代码折叠
            foldGutter: true,
            gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
            matchBrackets: true,    //括号匹配
        });
        //codeEditor.setSize('90%',"{$form[type].height}px");
        //提交表单获取内容
        $('.aw-ajax-form').click(function(){
            $('#{$form[type].name}').val(codeEditor.getValue());
        })
    })
</script>