{if !empty($list)}
{foreach $list as $v}
<div class="aui-chat-item {$v.uid == $user_id ? 'aui-chat-right' : 'aui-chat-left'}" data-total="{$total}">
    <div class="aui-chat-header">
        {:date('Y-m-d H:i:s',$v['send_time'])}
        {if $user_id==$v['uid']}
            {if $v.read_time}
                ({:L('对方于')} {:date('Y-m-d H:i:s',$v['read_time'])} {:L('已读')})
            {else/}
                <span class="text-danger">{:L('未读')}</span>
            {/if}
        {/if}
    </div>
    <div class="aui-chat-media">
        <a href="{$v.user.url}" class="d-block">
            <img src="{$v['user']['avatar']}" alt="{$v['user']['name']}" style="width: 2rem;height: 2rem"/>
        </a>
    </div>
    <div class="aui-chat-inner">
        <div class="aui-chat-name">{$v.user.name}</div>
        <div class="aui-chat-content">
            <div class="aui-chat-arrow"></div>
            {$v.message}
        </div>
        <div class="aui-chat-status aui-chat-status-refresh">
            <i class="aui-iconfont aui-icon-correct aui-text-success"></i>
        </div>
    </div>
</div>
{/foreach}
{/if}