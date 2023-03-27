<div class="form-group {$form[type].extra_class|default=''} clearfix" id="form_group_{$form[type].name}">
    <label class="control-label " for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <div class="more_images">
        <div id="more_images_{$form[type].name}">
            {notempty name="form[type].value"}
            {volist name="form[type]['value']" id="vo"}
            <div class="row my-1">
                <div class="col-6">
                    <input type="text" name="{$form[type].name}[]" value="{$vo}" class="form-control {notempty name="form[type].required"}required{/notempty}">
                </div>
                <div class="col-xs-3">
                    <button type="button" class="btn btn-block btn-warning remove_images"> 移除</button>
                </div>
            </div>
            {/volist}
            {/notempty}
        </div>
    </div>
    <div id="fileList_{$form[type].name}" class="uploader-list"></div>
    <div id="filePicker_{$form[type].name}"><i class="fa fa-upload m-r-10"></i> 选择图片</div>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>
<script type="text/javascript">
    upload('fileList_{$form[type].name}', 'filePicker_{$form[type].name}', '{$form[type].name}_preview', '{$form[type].name}', true, '{$form[type].ext|default=""}', '{$form[type].size|default="0"}','img',"{$form[type].path|default='common'}");
</script>