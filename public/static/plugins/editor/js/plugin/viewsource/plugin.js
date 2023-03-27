window.wangEditor.attach = {
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
};