{if isset($data)}
{volist name="data" id="v"}
<div class="invite-recommend-user bg-white overflow-auto border-bottom py-2" data-total="{$total}">
    <div class="float-left">
        <a href="javascript:;"><img src="{$v.avatar}" alt="" style="border-radius:50%;width: 50px;height: 50px;" /></a>
    </div>
    <div class="float-left ml-2">
        <a href="javascript:;"><b> {$v.nick_name} </b></a>
        <p class="text-muted font-9 mb-0"> {$v.remark|raw} </p>
    </div>
    <div class="float-right">
        <a href="javascript:;" data-uid="{$v.uid}" data-invite="{$v['has_invite']}" data-id="{$question_id}" class="px-4 btn btn-primary btn-sm {if $v['has_invite']}active{else/}question-invite{/if}">{:L($v['has_invite'] ? '已邀请':'邀请回答')}</a>
    </div>
</div>
{/volist}
{/if}