window.wangEditor.attach = {
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
};