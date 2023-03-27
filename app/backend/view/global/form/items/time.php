<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <input class="form-control datetimepicker-input {notempty name="form[type].required"}required{/notempty}" type="text" id="{$form[type].name}"
           name="{$form[type].name}" value="{$form[type].value|default=''}">
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>

<script>
    var endTime=layui.laydate.render({
        elem:'#{$form[type].name}',
        type:"time",
        format: '{$form[type].format}',
        trigger: 'click'
    })
</script>