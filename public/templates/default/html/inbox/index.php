{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>$user_id])}
            <div class="col-10" id="wrapMain">
                <div class="block new-block mb-0">
                    <div class="block-header">
                        <b>{:L('对话列表')}</b>
                        <div class="float-right notefun">
                            <a href="javascript:;" onclick="AWS.User.inbox()" class="text-muted mr-3">{:L('新私信')}</a>
                            <a href="{:url('setting/notify')}" class="text-muted"><i class="icon-settings"></i> {:L('私信设置')}</a>
                        </div>
                    </div>
                </div>
                <div class="bg-white mb-2 mt-1 rounded p-3">
                    {if isset($list)}
                    {volist name="list" id="v"}
                    <dl class="mb-0 py-3 border-bottom overflow-hidden position-relative">
                        <dt class="float-left">
                            <a href="{$v['user']['url']}">
                                <img src="{$v['user']['avatar']}" alt="" class="rounded" style="width: 46px;height: 46px">
                            </a>
                            <span class="aw-online-status {$v['unread']  ? 'unread' : 'read'}"></span>
                        </dt>
                        <dd class="float-right" style="width: calc(100% - 61px)">
                            <p class="text-muted font-9">{$v['user']['name']} · {:date_friendly($v['update_time'])}</p>
                            <p class="aw-one-line cursor-pointer {$v['unread']  ? 'text-primary' : 'text-muted'}" onclick="AWS.User.inbox('{$v.user.nick_name}')">{:get_username($v['last_message_uid'])}:{$v['last_message']}</p>
                        </dd>
                        <div class="font-8 position-absolute" style="top: 1rem;right: 0">
                            <a href="javascript:;" class="text-primary" onclick="AWS.User.inbox('{$v.user.nick_name}')">{:L('共 %s 条对话',$v.count)}</a>
                       </div>
                    </dl>
                    {/volist}
                    {$page}
                    {else/}
                    <p class="text-center mt-4 text-meta">
                        <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无私信记录')}">
                        <span class="mt-3 d-block ">{:L('暂无私信记录')}</span>
                    </p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}