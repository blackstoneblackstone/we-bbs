{if isset($keyList) /}
{volist name="keyList" id="field"}
<div class="form-group">
    <label class="control-label">{$field['title']|htmlspecialchars} </label>
    <div>
        {:widget('form/show',array($field,$info))}
    </div>
</div>
{/volist}
{/if}
<script>
    var isVerify = parseInt("{$user_info['verified'] ? 1 : 0}");

    if(isVerify)
    {
        $('#verifyMain input').attr('readonly','readonly').attr('disabled','disabled');
        $('#verifyMain textarea').attr('readonly','readonly').attr('disabled','disabled');
        $('#verifyMain select').attr('readonly','readonly').attr('disabled','disabled');
    }
</script>