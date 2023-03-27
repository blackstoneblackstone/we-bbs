<div class="aw-editor aw-overflow-auto">
    <textarea style="display: none;" id="{$name}" name="{$name}">{$value|raw}</textarea>
    <div id="editor_{$name}" class="aw-overflow-auto"></div>
</div>
{if $isMobile}
<input type="file" id="inputFile" style="display: none;" accept="image/*">
<link rel="stylesheet" href="/static/plugins/editor/css/mobile.css">
<style>
    .zx-editor .zx-editor-content-wrapper{
        margin-bottom: 0 !important;
        min-height: 350px !important;
        max-height: 60vh;
    }
    .zxeditor-container.fixed .zxeditor-content-wrapper{position: relative !important;}
    .zxeditor-container .zxeditor-toolbar-wrapper dl dd i{font-size: 24px;color: #999}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-polyfills/0.1.42/polyfill.min.js"></script>
<script type="text/javascript" src="/static/plugins/editor/js/mobile.js"></script>
<script type="text/javascript">
    var mediaType='img';
    // 实例化 ZxEditor
    var zxEditor = new ZxEditor('#editor_{$name}', {
        fixed: true
    });

    zxEditor.addFooterButton([
        {
            name: 'video',
            class: '',
            icon: 'icon icon-video',
            on: 'add-video'
        },
        {
            name: 'file',
            class: '',
            icon: 'icon icon-file',
            on: 'add-file'
        }
    ]);

    var editContent = $('#{$name}').val();
    zxEditor.setContent(editContent);

    //回显编辑器内容到表单
    zxEditor.on('change', function () {
        $('#{$name}').val(zxEditor.getContent());
    })

    var $inputFile = document.querySelector('#inputFile');
    zxEditor.on('select-picture', function () {
        $('#inputFile').attr('accept','image/*');
        mediaType = 'img';
        $inputFile.click();
    });
    zxEditor.on('add-video', function () {
        // 触发input点击事件
        $('#inputFile').attr('accept','video/*');
        mediaType = 'video';
        $inputFile.click();
    });

    zxEditor.on('add-file', function () {
        // 触发input点击事件
        $('#inputFile').attr('accept','*');
        mediaType = 'file';
        $inputFile.click();
    });

    $inputFile.addEventListener('change', function (e) {
        var files = e.target.files; // 或者 $inputFile.files
        var file = files[0];
        var xhr, formData;
        if(mediaType=='video' || mediaType=='file')
        {
            var url = "{:get_url('upload/index',['upload_type'=>'file','path'=>$cat,'access_key'=>$access_key])}";
        }else{
            var url = "{:get_url('upload/index',['upload_type'=>'img','path'=>$cat,'access_key'=>$access_key])}";
        }
        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', url);
        xhr.onload = function() {
            var json;
            if (xhr.status != 200) {
                return;
            }
            json = JSON.parse(xhr.responseText);
            if(json.code==0)
            {
                zxEditor.dialog.alert(json.msg, function () {
                    return;
                })

                return;
            }

            if(mediaType=='video')
            {
                zxEditor.addMedia(json.url,'video');
            }else if(mediaType=='file')
            {
                zxEditor.addLink(json.url,json.file_name,1);
            }else{
                zxEditor.addImage(json.url);
            }
        };
        formData = new FormData();
        formData.append('file', file, file.name );
        xhr.send(formData);
    });
</script>

{else/}
<link rel="stylesheet" href="/static/plugins/editor/css/editor.css">
<link rel="stylesheet" href="/static/plugins/editor/fonts/fonts.css">
<script src="/static/plugins/editor/js/editor.min.js"></script>
<script src="/static/plugins/editor/js/plugin.js"></script>
<script type="text/javascript">
    var E = wangEditor;
    window.wangEditor = wangEditor;
    var $text1 = $('#{$name}');
    var content=$text1.val();
    var editor = new E('#editor_{$name}');
    WEditor(editor,$text1,content);
    function WEditor (editor,$text1,content){
        editor.customConfig.codeType={
            title:"选择代码类型:",
            type:[
                "Bash/Shell","C/C++","PHP","C#","JAVA","CSS","SQL","HTML"
            ]
        };
        editor.customConfig.onchangeTimeout = 1;
        editor.customConfig.uploadImgTimeout = parseInt("{$config['timeout']}");
        editor.customConfig.uploadImgMaxSize = upload_image_size>0 ? upload_image_size*1024*1024 : 1024*1024*1024;
        editor.customConfig.customAlert = function (info) {
            layer.msg(info)
        };

        //上传字段
        editor.customConfig.uploadFileName = 'aw-upload-file';

        //上传图片
        editor.customConfig.uploadImgServer = "{:get_url('upload/index',['upload_type'=>'img','path'=>$cat,'access_key'=>$access_key])}";
        editor.customConfig.uploadImgHooks = {
            fail: function (xhr, editor, result) {
                if(result.error){
                    layer.msg(result.msg);
                    return false;
                }
            },
            error: function error(xhr, editor) {},
        };

        //上传视频
        editor.customConfig.uploadVideoServer = "{:get_url('upload/index',['upload_type'=>'file','path'=>$cat,'access_key'=>$access_key])}";
        editor.customConfig.uploadVideoHooks = {
            customInsert: function (insertVideo, result) {
                if(result.code)
                {
                    $.each(result.data, function(i,n) {
                        insertVideo(n);
                    });
                    layer.msg(result.msg);
                }else{
                    layer.msg(result.msg);
                }
            }
        };
        editor.customConfig.onchange = function (html)
        {
            $text1.val(html);
        };
        editor.create();
        editor.txt.html(content);
        editorPluginsExt.attach.init('#editor_{$name}',editor);
        editorPluginsExt.fullscreen.init(editor);
        editorPluginsExt.viewSource.init(editor);
    }
</script>
{/if}