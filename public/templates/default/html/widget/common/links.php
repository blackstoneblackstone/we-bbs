{if $links}
<!--友情链接-->
{if get_theme_setting('home.links_show_type')=='image'}
<div class="mt-2 bg-white py-3" style="margin-bottom: -10px">
    <div class="container">
        <h6 class="mb-3">{:L('友情链接')}：</h6>
        <ul class="row">
            {volist name="links" id="v"}
            {if $v.logo}
            <li class="col-md-2">
                <a href="{$v.url}" target="_blank"><img src="{$v.logo}" class="rounded w-100" style="max-height: 40px"></a>
            </li>
            {/if}
            {/volist}
        </ul>
    </div>
</div>
{else/}
<div class="mt-2 bg-white py-3" style="margin-bottom: -10px">
    <div class="container">
        <dl class="mb-0">
            <dt class="d-block mb-2">{:L('友情链接')}：</dt>
            {volist name="links" id="v"}
            <dd class="d-inline-block mr-3 mb-0">
                <a href="{$v.url}" target="_blank">{$v.name}</a>
            </dd>
            {/volist}
        </dl>
    </div>
</div>
{/if}
{/if}