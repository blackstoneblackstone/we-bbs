{if $update}
<div class="user-details-card pt-0 pb-2 position-relative clearfix">
    <div class="user-details-card-avatar float-left" style="position: relative">
        {if $info.is_anonymous}
        <a href="javascript:;">
            <img src="/static/common/image/default-avatar.svg" alt="匿名用户" data-toggle="popover" title="匿名用户" style="width: 40px;height: 40px">
        </a>
        {else/}
        <a href="{$info['user_info']['url']}" class="aw-username" data-id="{$info.uid}" data-toggle="popover" title="{$info['user_info']['name']}">
            <img src="{$info['user_info']['avatar']}" alt="{$info['user_info']['name']}" style="width: 40px;height: 40px">
        </a>
        {/if}
    </div>
    <div class="user-details-card-name float-left ml-2">
        {if $info.is_anonymous}<a href="javascript:;" title="匿名用户">匿名用户</a>{else/}<a href="{$info['user_info']['url']}" data-id="{$info.uid}" class="aw-username" title="{$info['user_info']['name']}">{$info['user_info']['name']}</a>  <span class="badge badge-success">{$info['user_info']['group_name']}</span>{/if} {if $setting.show_answer_user_ip=='Y' && $info.answer_user_local}<span class="ml-2 text-muted font-8">用户来自于: {$info.answer_user_local}</span>{/if}<br><span class="ml-0"> {:date_friendly($info['create_time'])} </span>
    </div>

    {if $info['is_best']}
    <div class="aw-answer-best">
        <i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>
    </div>
    {/if}
</div>
<div class="aw-content">
    <div class="aw-answer-content overflow-hidden">
        {:html_entity_decode($info.content)}
    </div>
    {if $info.content}
    <div class="aw-answer-show aw-alpha-hidden" style="display: none">
        <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> 阅读全文</span>
    </div>
    <div class="aw-answer-hide aw-alpha-hidden mt-3" style="display: none;background:none;position: inherit;height: auto">
        <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> 收起全文</span>
    </div>
    {/if}
</div>
<div class="answer-btn-actions mt-3">
    <label class="mr-1">
        <a href="javascript:;" class="aw-ajax-agree {if $info['vote_value']==1}active{/if}"  onclick="AWS.User.agree(this,'answer','{$info.id}');">
            <i class="icon-thumb_up"></i> 赞同 <span> {$info.agree_count}</span>
        </a>
    </label>

    <label class="mr-3 ">
        <a href="javascript:;" class="aw-ajax-against {if $info['vote_value']==-1}active{/if}"  onclick="AWS.User.against(this,'answer','{{$info.id}}');">
            <i class="icon-thumb_down"></i>
        </a>
    </label>

    <label class="mr-3">
        <a href="javascript:;" data-title="评论回答" class="answerCommentBtn" data-id="{$info.id}">
            <i class="icon-chat"></i> <span class="answer-comment-count{$info.id}">{$info.comment_count}</span>评论
        </a>
    </label>

    {if $user_id}
    <label class="mr-3">
        <a href="javascript:;" onclick="AWS.User.report(this,'answer','{$info.id}')" ><i class="icon-warning"></i> 举报</a>
    </label>

    <label class="mr-3">
        <a href="javascript:;" onclick="AWS.User.favorite(this,'answer','{$info.id}')"><i class="icon-turned_in"></i> 收藏</a>
    </label>

    <label class="mr-3">
        <a href="javascript:;"  {if $info.has_thanks} class="active" {else/}onclick="AWS.User.thanks(this,'{$info.id}')"{/if}>
        <i class="icon-favorite"></i> <span>{$info.has_thanks ? '已喜欢' : '喜欢'}</span>
        </a>
    </label>

    {if !$info.has_uninterested}
    <label class="mr-3">
        <a href="javascript:;" onclick="AWS.User.uninterested(this,'answer','{$info.id}')">
            <i class="icon-report"></i> 不感兴趣
        </a>
    </label>
    {/if}
    {/if}

    {if ($user_id && get_user_permission('set_best_answer')=='Y' && !$info['is_best'] && !$best_answer_count)}
    <label class="mr-3">
        <a href="javascript:;"  class="aw-ajax-get" data-confirm="是否把该回答设为最佳?" data-url="{:url('question/set_answer_best?answer_id='.$info['id'])}">
            <i class="fa fa-award "></i> 最佳
        </a>
    </label>
    {/if}

    <div class="dropdown d-inline-block mr-3">
        <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-share-alt font-9 d-none d-sm-inline-block"></i>分享
        </a>
        <div class="dropdown-menu p-0 border-0 font-size-sm">
            <div class="text-center d-block py-2" style="min-width: 100px">
                <a href="javascript:;"  class="dropdown-item aw-clipboard" data-clipboard-text="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="fab fa-weibo text-warning"></i> 新浪微博</a>
                <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="fab fa-qq text-primary"></i> 腾讯空间</a>
                <div class="aw-qrcode-container" data-share="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}">
                    <a href="javascript:;" class="dropdown-item "><i class="fab fa-weixin text-success"></i> 微信扫一扫</a>
                    <div class="aw-qrcode text-center py-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!--问题回答操作栏钩子-->
    {:hook('question_answer_bottom_action',$info)}

    {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$info['uid'])}
    <div class="dropdown d-inline-block">
        <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-ellipsis-h d-none d-sm-inline-block"></i>
        </a>
        <div class="dropdown-menu p-0 border-0 font-size-sm">
            <div class="text-center d-block py-2" style="min-width: 100px">
                <a href="javascript:;"  class="dropdown-item aw-answer-editor" data-question-id="{$info.question_id}" data-answer-id="{$info['id']}">编辑</a>
                <a href="javascript:;" data-toggle="popover" title="删除回答" class="dropdown-item aw-ajax-get" data-confirm="是否删除该回答?" data-url="{:url('question/delete_answer?answer_id='.$info['id'])}">删除</a>
            </div>
        </div>
    </div>
    {/if}
</div>
{else/}
<div class="aw-answer-item p-3 mb-1 bg-white" id="question-answer-{$info.id}" data-answer-id="{$info.id}">
    <div class="user-details-card pt-0 pb-2 position-relative clearfix">
        <div class="user-details-card-avatar float-left" style="position: relative">
            {if $info.is_anonymous}
            <a href="javascript:;">
                <img src="/static/common/image/default-avatar.svg" alt="匿名用户" data-toggle="popover" title="匿名用户" style="width: 40px;height: 40px">
            </a>
            {else/}
            <a href="{$info['user_info']['url']}" class="aw-username" data-id="{$info.uid}" data-toggle="popover" title="{$info['user_info']['name']}">
                <img src="{$info['user_info']['avatar']}" alt="{$info['user_info']['name']}" style="width: 40px;height: 40px">
            </a>
            {/if}
        </div>
        <div class="user-details-card-name float-left ml-2">
            {if $info.is_anonymous}<a href="javascript:;" title="匿名用户">匿名用户</a>{else/}<a href="{$info['user_info']['url']}" data-id="{$info.uid}" class="aw-username" title="{$info['user_info']['name']}">{$info['user_info']['name']}</a>{/if}<br><span class="ml-0"> {:date_friendly($info['create_time'])} </span>
        </div>
        {if $info['is_best']}
        <div class="aw-answer-best">
            <i class="iconfont" data-toggle="popover" title="最佳回答">&#xe6f7;</i>
        </div>
        {/if}
    </div>
    <div class="aw-content">
        <div class="aw-answer-content overflow-hidden">
            {:html_entity_decode($info.content)}
        </div>
        {if $info.content}
        <div class="aw-answer-show aw-alpha-hidden" style="display: none">
            <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> 阅读全文</span>
        </div>
        <div class="aw-answer-hide aw-alpha-hidden mt-3" style="display: none;background:none;position: inherit;height: auto">
            <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> 收起全文</span>
        </div>
        {/if}
    </div>
    <div class="answer-btn-actions mt-3">
        <label class="mr-1">
            <a href="javascript:;" class="aw-ajax-agree {if $info['vote_value']==1}active{/if}"  onclick="AWS.User.agree(this,'answer','{$info.id}');">
                <i class="icon-thumb_up"></i> 赞同 <span> {$info.agree_count}</span>
            </a>
        </label>

        <label class="mr-3 ">
            <a href="javascript:;" class="aw-ajax-against {if $info['vote_value']==-1}active{/if}"  onclick="AWS.User.against(this,'answer','{{$info.id}}');">
                <i class="icon-thumb_down"></i>
            </a>
        </label>

        <label class="mr-3">
            <a href="javascript:;" data-title="评论回答" class="answerCommentBtn" data-id="{$info.id}">
                <i class="icon-chat"></i> <span class="answer-comment-count{$info.id}">{$info.comment_count}</span>评论
            </a>
        </label>

        {if $user_id}
        <label class="mr-3">
            <a href="javascript:;" onclick="AWS.User.report(this,'answer','{$info.id}')" ><i class="icon-warning"></i> 举报</a>
        </label>

        <label class="mr-3">
            <a href="javascript:;" onclick="AWS.User.favorite(this,'answer','{$info.id}')"><i class="icon-turned_in"></i> 收藏</a>
        </label>

        <label class="mr-3">
            <a href="javascript:;"  {if $info.has_thanks} class="active" {else/}onclick="AWS.User.thanks(this,'{$info.id}')"{/if}>
            <i class="icon-favorite"></i> <span>{$info.has_thanks ? '已喜欢' : '喜欢'}</span>
            </a>
        </label>

        {if !$info.has_uninterested}
        <label class="mr-3">
            <a href="javascript:;" onclick="AWS.User.uninterested(this,'answer','{$info.id}')">
                <i class="icon-report"></i> 不感兴趣
            </a>
        </label>
        {/if}
        {/if}

        {if ($user_id && ($info['uid']==$user_id || $user_info['group_id']==1 || $user_info['group_id']==2)  && !$info['is_best'] && !$best_answer_count)}
        <label class="mr-3">
            <a href="javascript:;"  class="aw-ajax-get" data-confirm="是否把该回答设为最佳?" data-url="{:url('question/set_answer_best?answer_id='.$info['id'])}">
                <i class="fa fa-award "></i> 最佳
            </a>
        </label>
        {/if}

        <div class="dropdown d-inline-block mr-3">
            <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-fw fa-share-alt font-9 d-none d-sm-inline-block"></i>分享
            </a>
            <div class="dropdown-menu p-0 border-0 font-size-sm">
                <div class="text-left d-block py-2" style="min-width: 100px">
                    <a href="javascript:;"  class="dropdown-item aw-clipboard" data-clipboard-text="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}"><i class="icon-link"></i> 复制链接</a>
                    <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="fab fa-weibo text-warning"></i> 新浪微博</a>
                    <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="fab fa-qq text-primary"></i> 腾讯空间</a>
                    <div class="aw-qrcode-container" data-share="{:url('question/detail',['answer'=>$info.id,'id'=>$question_info.id],true,true)}">
                        <a href="javascript:;" class="dropdown-item "><i class="fab fa-weixin text-success"></i> 微信扫一扫</a>
                        <div class="aw-qrcode text-center py-2"></div>
                    </div>
                </div>
            </div>
        </div>
        <!--问题回答操作栏钩子-->
        {:hook('question_answer_bottom_action',$info)}
        <div class="aw-share clearfix d-inline-block">
            <div class="social-share" data-disabled="google,twitter,facebook,linkedin,douban"></div>
        </div>
        {if isset($user_info) && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['uid']==$info['uid'])}
        <div class="dropdown d-inline-block">
            <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-h d-none d-sm-inline-block"></i>
            </a>
            <div class="dropdown-menu p-0 border-0 font-size-sm">
                <div class="text-center d-block py-2" style="min-width: 100px">
                    <a href="javascript:;"  class="dropdown-item aw-answer-editor" data-question-id="{$info.question_id}" data-answer-id="{$info['id']}">编辑</a>
                    <a href="javascript:;" title="删除回答" class="dropdown-item aw-ajax-get" data-confirm="是否删除该回答?" data-url="{:url('question/delete_answer?answer_id='.$info['id'])}">删除</a>
                </div>
            </div>
        </div>
        {/if}
        <!--评论框动态显示-->
        <div class="answerCommentBox mt-2 border" id="answerCommentBox-{$info.id}" style="display: none;margin-left: 45px">
            <div class="answerCommentHeader clearfix px-3 pt-3">
                <h6 class="font-10 float-left mb-1"><span class="answer-comment-count{$info.id}">{$info.comment_count}</span> {:L('评论')}</h6>
            </div>
            <div class="answerCommentList px-3"></div>
            <div class="pageElement"></div>
            <div class="commentForm clearfix rounded aw-replay-box"></div>
        </div>
    </div>
</div>
{/if}