<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    {$form[type].value|raw}
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>