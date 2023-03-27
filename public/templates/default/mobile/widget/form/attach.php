<div>
    <div class="more_images dd_ts">
        <div id="more_images_{$form[type].name}">
            {notempty name="$value"}
            {volist name="$value" id="vo"}
            <div class="row py-2">
                <div class="col-6">
                    <input type="text" name="{$field}[]" value="{$vo}" class="form-control">
                </div>
                <div class="col-xs-3">
                    <button type="button" class="btn btn-block btn-warning remove_images">{:L('移除')}</button>
                </div>
            </div>
            {/volist}
            {/notempty}
        </div>
    </div>
    <div id="fileList_{$field}" class="uploader-list"></div>
    <div id="filePicker_{$field}" class="mt-2" style="height: 32px;line-height: 32px"><i class="fa fa-upload m-r-10"></i> {:L('选择文件')}</div>
</div>
<script type="text/javascript">
    AWS.upload.upload('fileList_{$field}', 'filePicker_{$field}}', '{$field}_preview', '{$field}', true, '{$setting.upload_file_ext|default=""}', '{$setting.upload_file_size|default="0"}', 'file');
</script>