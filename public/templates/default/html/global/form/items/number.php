<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <input class="form-control {notempty name="form[type].required"}required{/notempty}" type="number" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value|htmlspecialchars_decode}" {php}if(isset($form[type]['min']) && $form[type]['min'] !== ''):{/php}min="{$form[type].min}"{php}endif;{/php} {php}if(isset($form[type]['max']) && $form[type]['max'] !== ''):{/php}max="{$form[type].max}"{php}endif;{/php} {php}if(isset($form[type]['step']) && $form[type]['step'] !== ''):{/php}step="{$form[type].step}"{php}endif;{/php} {$form[type].extra_attr|raw}>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>

