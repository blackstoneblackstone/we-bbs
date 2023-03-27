{if isset($keyList) /}
{volist name="keyList" id="field"}
<div class="form-group">
    <label class="control-label">{$field['title']|htmlspecialchars} </label>
    <div >
        {:widget('form/show',array($field,$info))}
    </div>
</div>
{/volist}
{/if}
<script>
    let isVerify = parseInt("{$info.enable}");
    if(isVerify)
    {
        $('#tabMain input').attr('readonly','readonly').attr('disabled','disabled');
        $('#tabMain textarea').attr('readonly','readonly').attr('disabled','disabled');
        $('#tabMain select').attr('readonly','readonly').attr('disabled','disabled');
    }
</script>