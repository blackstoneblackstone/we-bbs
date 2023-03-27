<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <div class="">
        {notempty name="form[type].group"}
        <div class="input-group">
        {/notempty}
            {notempty name="form[type].group.0"}
            <div class="input-group-prepend">
                <span class="input-group-text">{$form[type].group.0|raw}</span>
            </div>
            {/notempty}
            <input class="form-control {notempty name="form[type].required"}required{/notempty}" type="text" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value|htmlspecialchars_decode}" placeholder="{$form[type].placeholder|htmlspecialchars_decode}" {$form[type].extra_attr|raw}>
            {notempty name="form[type].group.1"}
            <div class="input-group-append">
                <span class="input-group-text">{$form[type].group.1|raw}</span>
            </div>
            {/notempty}
        {notempty name="form[type].group"}
        </div>
        {/notempty}

        {notempty name="form[type].tips"}
        <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
            {$form[type].tips|raw}
        </div>
        {/notempty}
    </div>
</div>

