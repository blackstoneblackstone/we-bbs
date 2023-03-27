window.wangEditor.attach = {
    init: function (editorSelector) {
        $(editorSelector + " .w-e-toolbar").append('<div class="w-e-menu"><input id="upload-file" type="file" style="display:none;" onchange="editorPluginsExt.attach.callback()" /><a class="we-upload-file" href="javascript:;" onclick="editorPluginsExt.attach.upFile()"><i title="上传附件" class="w-e-icon-upload2"></i></a></div>');
    },
    upFile: function () {
        $("#upload-file").click();
    },
    callback: function () {
        const $file = $('#upload-file');
        const fileElem = $file[0];
        const fileList = fileElem.files;
        const uploadImg = editor.uploadImg;
        const url = '';
        uploadImg.uploadFile(fileList, url);
    }
};