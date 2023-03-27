<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <input class="form-control aw-form-icon {notempty name="form[type].required"}required{/notempty}" type="text" name="{$form[type].name}" value="{$form[type].value}" placeholder="{:L('点击右侧选择图标')}" style="width: calc(100% - 70px);display: inline-block">
    <div style="display: inline-block;margin-left: 10px;height: 38px;width: 38px" class="aw-ajax-open" data-url="{:url('Index/icons')}" data-title="{:L('选择图标')}"><i class="{$form[type].value | default='fa fa-cogs'} aw-form-icon-select" style="font-size: 30px;cursor: pointer;height: 38px;width: 38px" ></i></div>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>