<div class="form-group" id="form_group_{$form[type].name}">
    <div id="ajaxHtml"></div>
    {notempty name="form[type].tips"}
    <div class="mt-1" style="font-size:0.9rem;display: block;color: #dc3545;">
        {$form[type].tips|raw}
    </div>
    {/notempty}
</div>

<script>
    $(function(){
        var url = '{$form[type].url}';
        {if $form[type].trigger}
        $("#form_group_{$form[type].trigger} select").change(function (){
            var value = $(this).val();
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "{$form[type].trigger}="+value;
            $.ajax({
                type : "GET",
                url  : url,
                beforeSend: function () {
                    $('#ajaxHtml').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
                },
                success: function (html) {
                    $('#ajaxHtml').html(html);
                }
            });
        })
        {/if}
       {if $form[type].value && $form[type].trigger}
            var value = '{$form[type].value}';
            url = url + (url.indexOf("?") > -1 ? "&" : "?") + "{$form[type].trigger}="+value;
            $.ajax({
                type : "GET",
                url  : url,
                beforeSend: function () {
                    $('#ajaxHtml').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
                },
                success: function (html) {
                    $('#ajaxHtml').html(html);
                }
            });
       {/if}

       {if !$form[type].value && !$form[type].trigger}
       $.ajax({
           type : "GET",
           url  : url,
           beforeSend: function () {
               $('#ajaxHtml').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
           },
           success: function (html) {
               $('#ajaxHtml').html(html);
           }
       });
       {/if}
   })
</script>