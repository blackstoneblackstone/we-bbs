<div class="form-group {$form[type].extra_class|default=''}" id="form_group_{$form[type].name}">
    <label class="control-label" for="{$form[type].name}">{$form[type].title|htmlspecialchars}:{notempty name="form[type].required"}(<span class="text-danger ">*</span>){/notempty}</label>
    <textarea class="form-control" id="{$form[type].name}" name="{$form[type].name}" rows="{$form[type].rows|default='3'}" style="display: none">{$form[type].value}</textarea>
    <table class="table table-bordered array-table">
        <thead>
        <tr>
            <th>键名</th>
            <th>键值</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody class="{$form[type].name}-container">
        {volist name="form[type].options" id="option"}
        <tr>
            <td><input type="text" class="form-control form-control-sm" name="{$form[type].name}[key][]" value="{$key}"></td>
            <td><input type="text" class="form-control form-control-sm" name="{$form[type].name}[value][]" value="{$option}"></td>
            <td><a href="javascript:;" class="btn btn-danger btn-remove" ><i class="fa fa-trash"></i></a> </td>
        </tr>
        {/volist}
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3"><a href="javascript:;" class="btn btn-sm btn-primary btn-append" data-name="{$form[type].name}"><i class="fa fa-plus"></i> 追加</a></td>
        </tr>
        </tfoot>
    </table>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>
<script>
    $(document).on('click','.btn-append',function() {
        var name = $(this).data('name');
        var html = '<tr><td><input type="text" class="form-control form-control-sm" name="'+name+'[key][]"></td><td><input type="text" class="form-control form-control-sm" name="'+name+'[value][]"></td><td><a href="javascript:;" class="btn-remove btn btn-danger"><i class="fa fa-trash"></i></a> </td> </tr>';
        $(this).parents('table').find('.{$form[type].name}-container').append(html);
    })

    $(document).on('click','.btn-remove',function() {
        $(this).parents('tr').remove();
    })
</script>