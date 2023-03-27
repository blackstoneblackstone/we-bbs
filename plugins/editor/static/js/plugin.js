var editorPluginsExt = {
    attach: {
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
            const url = editor.customConfig.uploadVideoServer;
            uploadImg.uploadFile(fileList, url);
        }
    },
    fullscreen: {
        FullEditor: {},
        init: function (editor) {
            let id = editor.id;
            editorPluginsExt.fullscreen.FullEditor[id] = editor;
            toolbar = editor.$toolbarElem[0];
            $(toolbar).append('' +
                '<div class="w-e-menu btn-fullscreen" title="全屏" onclick="editorPluginsExt.fullscreen.run(\'' + id + '\')">' +
                '<i class="icon-fullscreen"></i> ' +
                '</div>'
            );
        },
        run: function (id) {
            let editor = editorPluginsExt.fullscreen.FullEditor[id];
            let container = $(editor.toolbarSelector);
            container.toggleClass('fullscreen-editor');
            $('#main_header').toggle();
        }
    },
    viewSource: {
        SourceEditor: {},
        init: function (editor) {
            let id = editor.id;
            editor.isHTML = false;
            editorPluginsExt.viewSource.SourceEditor[id] = editor;
            toolbar = editor.$toolbarElem[0];
            $(toolbar).append("" +
                "<div class='w-e-menu btn-viewSource' title='查看源码' onclick='editorPluginsExt.viewSource.run(\"" + id + "\")'>" +
                "<i class='icon-code'></i>" +
                "</div>"
            );
        },
        run: function (id) {
            let editor = editorPluginsExt.viewSource.SourceEditor[id];
            let container = $(editor.toolbarSelector);
            editor.isHTML = !editor.isHTML;
            let _source = editor.txt.html();
            toolbar = editor.$toolbarElem[0];
            if (editor.isHTML) {
                _source = _source.replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/ /g, "&nbsp;");
                $(toolbar).find('.btn-viewSource').css({"display": ""});
            } else {
                _source = editor.txt.text().replace(/&lt;/ig, "<").replace(/&gt;/ig, ">").replace(/&nbsp;/ig, " ");
                editor.change && editor.change();
            }
            container.toggleClass('view-source-editor');
            editor.txt.html(_source);
        }
    }
};