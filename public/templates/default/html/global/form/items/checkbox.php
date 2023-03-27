<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <div class="">
        {volist name="form[type].options" id="option"}
        <div class="form-check-inline">
            <input type="checkbox" name="{$form[type].name}[]" class="form-check-input {notempty name="form[type].required"}required{/notempty}" id="{$form[type].name}{$i}" value="{$key}" {in name="key" value="$form[type].value|default=''" }checked{/in} {$form[type].extra_attr|raw|default=''}>
            <label class="form-check-label">{$option|raw}</label>
        </div>
        {/volist}
        {notempty name="form[type].tips"}
        <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
            {$form[type].tips|raw}
        </div>
        {/notempty}
    </div>
</div>