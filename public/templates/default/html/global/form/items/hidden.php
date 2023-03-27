<div class="form-group d-none {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <input type="hidden" name="{$form[type].name}"  class="{notempty name="form[type].required"}required{/notempty}" value="{$form[type].value|default=''}" id="{$form[type].name}" {$form[type].extra_attr|raw|default=''}>
</div>

