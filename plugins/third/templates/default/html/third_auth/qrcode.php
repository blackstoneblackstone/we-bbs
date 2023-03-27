{extend name="$theme_block" /}
{block name="header"}{/block}
{block name="main"}
<div class="bg-white p-2">
    <p class="text-muted text-center mb-1">请用微信扫码</p>
   <img src="{$img}" width="100%">
</div>

<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $.get(baseUrl + '/ThirdAuth/wechat_login?token={$token}', function (response) {
                if (response.code == 1)
                {
                    if (response.returnUrl)
                    {
                        window.open(response.returnUrl);
                    }
                    else
                    {
                        window.location.reload();
                    }

                }
            }, 'json');
        }, 1500);
    });

    setInterval(function () {
        window.location.reload();
    }, 300000);
</script>
{/block}