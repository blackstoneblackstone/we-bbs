{if !empty($list)}
{volist name="list" id="v"}
<div class="aw-answer-item p-3 mb-1 bg-white" id="question-answer-{$v.id}" data-answer-id="{$v.id}">
    <div class="user-details-card pt-0 pb-2 position-relative clearfix">
        <div class="user-details-card-avatar float-left" style="position: relative">
            {if $v.is_anonymous}
            <a href="javascript:;">
                <img src="/static/common/image/default-avatar.svg" alt="{:L('匿名用户')}" class="rounded" data-toggle="popover" title="{:L('匿名用户')}" style="width: 40px;height: 40px">
            </a>
            {else/}
            <a href="{$v['user_info']['url']}" class="aw-username" data-id="{$v.uid}" title="{$v['user_info']['name']}">
                <img src="{$v['user_info']['avatar']}" onerror="this.src='/static/common/image/default-avatar.svg'" class="rounded" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
            {/if}
        </div>
        <div class="user-details-card-name float-left ml-2">
            {if $v.is_anonymous}
            <a href="javascript:;" data-toggle="popover" title="{:L('匿名用户')}">{:L('匿名用户')}</a>
            {else/}
            <a href="{$v['user_info']['url']}" data-id="{$v.uid}" class="aw-username" title="{$v['user_info']['name']}">{$v['user_info']['name']}</a>
            <span class="badge badge-success">{$v['user_info']['group_name']}</span>
            {/if}
            <br>
            {if $setting.show_answer_user_ip=='Y' && $v.answer_user_local}
            <span class="font-8 text-muted mr-2">(IP : {$v.answer_user_local})</span>
            {/if}
            <span class="ml-0"> {:date_friendly($v['create_time'])} </span>
        </div>

        {if $v['is_best']}
        <div class="aw-answer-best float-right">
            <img src="{$static_url}mobile/img/best.png" height="20">
            <!--<i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>-->
        </div>
        {/if}
    </div>
    <div class="aw-content">
        <div class="aw-answer-content overflow-hidden">
            {:html_entity_decode($v.content)}
        </div>
        {if $v.content}
        <div class="aw-answer-show aw-alpha-hidden" style="display: none">
            <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> {:L('阅读全文')}</span>
        </div>
        <div class="aw-answer-hide aw-alpha-hidden mt-3" style="display: none;background:none;position: inherit;height: auto">
            <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> {:L('收起全文')}</span>
        </div>
        {/if}
    </div>
    <div class="answer-btn-actions mt-3 pt-3 border-top">
        <label class="mr-3">
            <a href="javascript:;" class="text-muted aw-ajax-agree {if $v['vote_value']==1}active{/if}"  onclick="AWS_MOBILE.User.agree(this,'answer','{$v.id}');">
                <i class="icon-thumb_up"></i> <span> {$v.agree_count}</span>
            </a>
        </label>

        <label class="mr-3">
            <a href="javascript:;" class="text-muted aw-ajax-against {if $v['vote_value']==-1}active{/if}"  onclick="AWS_MOBILE.User.against(this,'answer','{$v.id}');">
                <i class="icon-thumb_down"></i>
            </a>
        </label>

        <label class="mr-3">
            <a href="javascript:;" class="text-muted aw-ajax-open" data-title="评论回答" data-url="{:url('comment/answer?answer_id='.$v['id'])}">
                <i class="icon-chat"></i> <span class="answer-comment-count{$v.id}">{$v.comment_count}</span>
            </a>
        </label>
        <label class="mr-3">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.shareBox('{$question_info.title}','{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}')">
                <i class="fa fa-fw fa-share-alt"></i> {:L('分享')}
            </a>
        </label>

        {if $user_id}
        <script type="text/html" class="answerAction">
            <div class="row text-center mt-3">
                <div class="col-4 mb-3">
                    <a href="javascript:;" class="text-muted" {if !$v.checkReport} onclick="AWS_MOBILE.User.report(this,'answer','{$v.id}')"{/if} >
                        <i class="icon-warning font-14"></i> <br>
                        <span class="d-block font-9 mt-1">{$v.checkReport ? L('已举报') : L('举报')}</span>
                    </a>
                </div>

                <div class="col-4 mb-3">
                    <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.favorite(this,'answer','{$v.id}')">
                        <i class="icon-turned_in font-14"></i><br>
                        <span class="d-block font-9 mt-1">{if $v.checkFavorite} {:L('已收藏')}{else}{:L('收藏')}{/if}</span>
                    </a>
                </div>

                {if $user_id!=$v['uid']}
                <div class="col-4 mb-3">
                    <a href="javascript:;"  {if $v.has_thanks} class="active text-muted" {else/}onclick="AWS_MOBILE.User.thanks(this,'{$v.id}')" class="text-muted"{/if}>
                    <i class="icon-favorite font-14"></i> <br>
                    <span class="d-block font-9 mt-1">{$v.has_thanks ? L('已感谢') : L('感谢')}</span>
                    </a>
                </div>
                {/if}

                {if !$v.has_uninterested}
                <div class="col-4 mb-3">
                    <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.User.uninterested(this,'answer','{$v.id}')">
                        <i class="icon-report font-14"></i> <br>
                        <span class="d-block font-9 mt-1">{:L('不感兴趣')}</span>
                    </a>
                </div>
                {/if}

                {if (get_user_permission('set_best_answer')=='Y' && !$v['is_best'] && !$best_answer_count)}
                <div class="col-4 mb-3">
                    <a href="javascript:;"  class="aw-ajax-get text-muted" data-confirm="{:L('是否把该回答设为最佳')}？" data-url="{:url('ajax.question/set_answer_best?answer_id='.$v['id'])}">
                        <i class="fa fa-award font-14"></i> <br>
                        <span class="d-block font-9 mt-1">{:L('最佳')}</span>
                    </a>
                </div>
                {/if}

                {if $user_info['uid']==$v['uid'] || get_user_permission('modify_answer')=='Y'}
                <div class="col-4 mb-3">
                    <a href="javascript:;"  class="aw-ajax-open text-muted" data-title="修改回答" data-url="{:url('ajax/editor',['question_id'=>$v['question_id'],'answer_id'=>$v['id']])}">
                        <i class="fa fa-edit font-14"></i><br>
                        <span class="d-block font-9 mt-1">{:L('修改回答')}</span>
                    </a>
                </div>
                {/if}

                {if $user_info['uid']==$v['uid'] || get_user_permission('remove_answer')=='Y'}
                <div class="col-4 mb-3">
                    <a href="javascript:;" title="{:L('删除回答')}" class="aw-ajax-get text-muted" data-confirm="{:L('是否删除该回答')}?" data-url="{:url('ajax.question/delete_answer?answer_id='.$v['id'])}">
                        <i class="fa fa-trash-alt font-14"></i><br>
                        <span class="d-block font-9 mt-1">{:L('删除回答')}</span>
                    </a>
                </div>
                {/if}
            </div>
        </script>
        {/if}
        {if $user_id}
        <label class="float-right">
            <a href="javascript:;" class="text-muted" onclick="AWS_MOBILE.api.dialog('{:L(\'回答操作\')}',$(this).parents('.answer-btn-actions').find('.answerAction').html())"><i class="fa fa-ellipsis-h"></i></a>
        </label>
        {/if}
    </div>
</div>
{/volist}
{/if}
