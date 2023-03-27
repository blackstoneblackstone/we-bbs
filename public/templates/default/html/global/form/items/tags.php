<link rel="stylesheet" href="/static/admin/plugins/jquery-tags-input/jquery.tagsinput.css">
<script src="/static/admin/plugins/jquery-tags-input/jquery.tagsinput.js"></script>
<style>
    .tags{ display: inline-block; margin: 10px 0 5px 0;}
</style>
<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <input class="form-control tags {notempty name="form[type].required"}required{/notempty}" type="text" id="{$form[type].name}" name="{$form[type].name}" value="{$form[type].value}" placeholder="{:L('输入后请回车确认')}" {$form[type].extra_attr|raw}>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>

<script>
    // tag 标签
    if ($(".tags").length > 0) {
        $('.tags').tagsInput({
            'width': 'auto',
            'height': 'auto',
            'placeholderColor': '#666666',
            'defaultText': '{:L("添加标签")}',
        });
    }
</script>

