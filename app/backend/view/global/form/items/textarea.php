<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>

    <textarea class="form-control {notempty name="form[type].required"}required{/notempty}" id="{$form[type].name}" name="{$form[type].name}" rows="{$form[type].rows|default='3'}" placeholder="{$form[type].placeholder}" {$form[type].extra_attr|raw}>{$form[type].value|htmlspecialchars_decode}</textarea>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>