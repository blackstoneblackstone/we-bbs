<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <select class="form-control {notempty name="form[type].required"}required{/notempty}" id="{$form[type].name}" name="{$form[type].name}" {$form[type].extra_attr|default=''}>
        <option value="">{$form[type].placeholder}</option>
        {volist name="form[type].options" id="option"}
        <option value="{$key}" {if ((string)$form[type].value == (string)$key)}selected{/if}>{$option}</option>
        {/volist}
    </select>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>

