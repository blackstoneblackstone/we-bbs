<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <div class="input-group {$form[type].name}-colorpicker">
        <input class="form-control {notempty name="form[type].required"}required{/notempty}" type="text" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value}" placeholder="{$form[type].placeholder}" {$form[type].extra_attr|raw}>
        <div class="input-group-append">
                <span class="input-group-text">
                    <i class="fas fa-square" {if $form[type].value}style="color: {$form[type].value}"{/if}></i>
                </span>
        </div>
    </div>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>
<script>
    $(function () {
        $('.{$form[type].name}-colorpicker').colorpicker()
        $('.{$form[type].name}-colorpicker').on('colorpickerChange', function(event) {
            $('.{$form[type].name}-colorpicker .fa-square').css('color', event.color.toString());
        });
    })
</script>

