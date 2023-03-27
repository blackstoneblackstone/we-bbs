<script src="/static/libs/layui/layui.all.js"></script>
<script src="/static/common/js/core.min.js"></script>
<script src="/static/libs/webuploader/webuploader.js"></script>
<script src="/static/libs/pjax/jquery.pjax.js"></script>
<script src="/static/libs/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="/static/libs/ztree/js/jquery.ztree.core.js"></script>
<script type="text/javascript" src="/static/libs/ztree/js/jquery.ztree.excheck.js"></script>
<script src="/static/libs/jstree/dist/jstree.min.js"></script>

<script src="/static/libs/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/libs/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/libs/bootstrap-table/extensions/mobile/bootstrap-table-mobile.js"></script>
<script src="/static/libs/bootstrap-table/extensions/toolbar/bootstrap-table-toolbar.min.js"></script>
<script src="/static/libs/bootstrap-table/extensions/fixed-columns/bootstrap-table-fixed-columns.min.js"></script>
<script src="/static/libs/bootstrap-table/extensions/treegrid/bootstrap-table-treegrid.js"></script>
<script src="/static/libs/jquery-treegrid/js/jquery.treegrid.js"></script>
<script src="/static/libs/bootstrap-table/extensions/export/tableExport.js"></script>
<script src="/static/libs/bootstrap-table/extensions/export/bootstrap-table-export.js"></script>

<script src="/static/libs/codemirror/codemirror.js"></script>
<script src="/static/libs/codemirror/mode/css/css.js"></script>
<script src="/static/libs/codemirror/mode/xml/xml.js"></script>
<script src="/static/libs/codemirror/mode/javascript/javascript.js"></script>
<script src="/static/libs/codemirror/mode/htmlmixed/htmlmixed.js"></script>

<script src="/static/libs/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script>
    window.G_BASE_URL = '{$base_url}'
    layui.use('layer',
        function () {
            var layer = layui.layer;
        })
</script>
<script src="/static/common/js/tools.js"></script>
<script src="/static/admin/js/aws-admin.js"></script>
<script>
    window.userId = parseInt("{$user_id|default='0'}");
    window.baseUrl = '{$base_url}';
    window.cdnUrl = '{$cdnUrl}';
    window.thisController ="{$thisController|default=''}";
    window.thisAction ="{$thisAction|default=''}";
    window.staticUrl = cdnUrl + '/static/';
    window.upload_image_ext = "{$setting.upload_image_ext}" ;
    window.upload_file_ext = "{$setting.upload_file_ext}" ;
    window.upload_image_size = "{$setting.upload_image_size}" ;
    window.upload_file_size = "{$setting.upload_file_size}" ;
    window.isAjax = "{$_ajax}" ;
    window.isAjaxOpen = "{$_ajax_open}" ;
    //代码高亮
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('pre').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });
</script>