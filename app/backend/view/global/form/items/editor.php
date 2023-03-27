<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    {:hook('editor',['name'=>$form[type].name,'value'=>$form[type].value,'cat'=>'common','access_key'=>md5($user_id),'cat'=>$form[type].types])}
    {notempty name="form[type].tips"}
    <div class="text-muted mt-1" style="display: block;font-size:0.9rem">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>