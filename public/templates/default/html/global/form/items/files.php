<div class="row dd_input_group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l" for="{$form[type].name}">{notempty name="form[type].required"} *{/notempty}{$form[type].title}</label>
    <div class="col-8 col-md-10 col-lg-10">
        <div class="more_images dd_ts">
            <div id="more_images_{$form[type].name}">
                {notempty name="form[type].value"}
                {volist name="form[type]['value']" id="vo"}
                <div class="row py-2">
                    <div class="col-6">
                        <input type="text" name="{$form[type].name}[]" value="{$vo}" class="form-control {notempty name="form[type].required"}required{/notempty}">
                    </div>
                    <div class="col-xs-3">
                        <button type="button" class="btn btn-block btn-warning remove_images">{:L('移除')}</button>
                    </div>
                </div>
                {/volist}
                {/notempty}
            </div>
        </div>
        <div id="fileList_{$form[type].name}" class="uploader-list"></div>
        <div id="filePicker_{$form[type].name}" class="mt-2" style="height: 32px;line-height: 32px"><i class="fa fa-upload m-r-10"></i> {:L('选择文件')}</div>
        <!--上传图片-->
        {notempty name="form[type].tips"}
        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">
            {$form[type].tips|raw}
        </div>
        {/notempty}
    </div>
</div>
<script type="text/javascript">
    upload('fileList_{$form[type].name}', 'filePicker_{$form[type].name}', '{$form[type].name}_preview', '{$form[type].name}', true, '{$form[type].ext|default=""}', '{$form[type].size|default=""}','file',"{$form[type].path|default='common'}");
</script>