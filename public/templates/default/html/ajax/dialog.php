{if isset($list)}
{volist name="list" id="v"}
<div class="mx-3 py-2" style="border-radius: 10px" data-total="{$total}">
    <span class="d-block font-8 text-center" style="margin: 1rem auto;">
        {:date('Y-m-d H:i:s',$v['send_time'])}
        {if $user_id==$v['uid']}
            {if $v.read_time}
            ({:L('对方于')} {:date('Y-m-d H:i:s',$v['read_time'])} {:L('已读')})
            {else/}
            <span class="text-danger">{:L('未读')}</span>
            {/if}
        {/if}
    </span>
    <div class="message-bubble {if $user_id==$v['uid']}me{/if}">
        <div class="message-bubble-inner overflow-hidden">
            <div class="message-avatar">
                <a href="{$v['user']['url']}">
                    <img src="{$v['user']['avatar']}" alt="{$v['user']['name']}" style="width: 32px;height: 32px"/>
                </a>
            </div>
            <div class="message-text">
                <p class="font-9">{$v.message}</p>
            </div>
        </div>
    </div>
</div>
{/volist}
{/if}