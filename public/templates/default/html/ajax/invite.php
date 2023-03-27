{extend name="$theme_block" /}
{block name="main"}
<div class="card" >
    <div class="card-header border-bottom bg-white">
        <h6 class="mb-0 float-left" style="line-height: 31px">{:L('你可以邀请下面用户')}，{:L('快速获得回答')}</h6>
        <div class="float-right">
            <form class="d-none d-sm-inline-block ml-5" action="" method="POST">
                <input type="hidden" name="question_id" id="question-id" value="{$question_id}">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-alt" placeholder="{:L('请输入您想搜索的内容')}..." name="username" id="invite-users">
                    <div class="input-group-append">
                        <span class="input-group-text bg-body border-0 invite-btn">
                            <i class="si si-magnifier"></i>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {if $invite_list}
    <div class="pt-3 px-3">
        <dl class="bg-white overflow-auto mb-1">
            <dt class="float-left">{:L('已邀请用户')}:</dt>
            {volist name="$invite_list" id="v"}
            <dd class="float-left ml-2 mb-0">
                <a href="javascript:;">
                    <img src="{$v.avatar}" style="border-radius:50%;width: 18px;height: 18px;" title="{$v.nick_name}" alt="{$v.nick_name}"/>
                </a>
            </dd>
            {/volist}
        </dl>
    </div>
    {/if}
    <div class="card-body" id="ajaxList">
        {if isset($data) && $data}
        {volist name="data" id="v"}
        <div class="invite-recommend-user bg-white overflow-auto p-3 mb-1" data-total="{$total}">
            <div class="float-left">
                <a href="javascript:;">
                    <img src="{$v.avatar}" alt="" style="border-radius:50%;width: 50px;height: 50px;" />
                </a>
            </div>
            <div class="float-left ml-2">
                <a href="javascript:;"><b> {$v.nick_name} </b></a>
                <p class="text-muted font-9"> {$v.remark|raw} </p>
            </div>
            <div class="float-right">
                <a href="javascript:;" data-uid="{$v.uid}" data-invite="{$v['has_invite']}" data-id="{$question_id}" class="px-4 btn btn-primary btn-sm {if $v['has_invite']}active{else/}question-invite{/if}">{:L($v['has_invite'] ? '已邀请':'邀请回答')}</a>
            </div>
        </div>
        {/volist}

        {else/}
        <p class="p-4 text-center">
            <i class="text-danger fa fa-info"></i>
            {:L('没有相关领域作者')}? {:L('您可以搜索用户参与回答')}
        </p>
        {/if}
    </div>
</div>
{/block}