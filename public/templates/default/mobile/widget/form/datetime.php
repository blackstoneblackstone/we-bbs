<input type="text" class="form-control" id="{$field}" name="{$field}" value="{$value|default=time()|date='Y-m-d H:i:s',###}" readonly size="15">
<script>
    layer.laydate.render({
        elem: '#{$field}', //指定元素
        format: 'yyyy-mm-dd hh:ii:ss'
    });
</script>