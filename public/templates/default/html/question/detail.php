{extend name="$theme_block" /}
{block name="meta_script"}
{if $theme_config['common']['enable_mathjax']=='Y'}
<style>
    .MathJax{outline:0;text-align: unset !important;}
</style>
<script async src="{$cdnUrl}/static/common/js/tex-mml-chtml.js"></script>
{/if}
{/block}

{block name="main"}
<div id="aw-question-fixed" class="bg-white fixed-top d-xs-none" style="display: none">
    <div class="container overflow-hidden">
        <h3 class="mb-0 float-left">{:htmlspecialchars_decode($question_info.title)}</h3>
        <div class="float-right">
            <button onclick="AWS.User.focus(this,'question','{$question_info.id}')" class="btn btn-primary btn-sm px-3 mr-3 {if $question_info['has_focus']}active ygz{/if}">{$question_info['has_focus'] ? L('已关注') : L('关注问题')}</button>
            <button class="btn btn-outline-primary btn-sm px-3 awsAnswerEditor" data-enable="{$user_id && $user_info['permission']['publish_answer_enable']=='N' ? 1 : 0}" data-question-id="{$question_info['id']}" data-answern-id="0">
                {:L("回答问题")}</button>
        </div>
    </div>
</div>

<div class="aw-wrap">
    <div class="container mt-2">
        {if $redirect_message}
        <div class="row">
            <div class="col-sm-12 pl-0">
                {foreach $redirect_message as $key => $message}
                <div class="alert alert-danger">
                    {$message|raw}
                </div>
                {/foreach}
            </div>
        </div>
        {/if}

        {if $question_info['is_lock']}
        <div class="row">
            <div class="col-sm-12 pl-0">
                <div class="alert alert-danger"><i class="icon icon-info"></i> {:L('该问题已被锁定')}！</div>
            </div>
        </div>
        {/if}
        <div class="row">
            <div class="aw-left col-md-9 px-xs-0 mb-1">
                <div class="aw-question-container bg-white pt-3 pb-3 mb-2">
                    <div class="container position-relative">
                        <div class="extend-info position-absolute d-xs-none">
                            <div class="d-flex text-center text-muted">
                                <dl class="flex-fill mb-0 mr-4">
                                    <dt class="font-weight-bold">{$question_info['focus_count']}</dt>
                                    <dd>{:L('关注')}</dd>
                                </dl>
                                <dl class="flex-fill mb-0 mr-4 line"></dl>
                                <dl class="flex-fill mb-0">
                                    <dt>{$question_info['view_count']}</dt>
                                    <dd>{:L('浏览')}</dd>
                                </dl>
                            </div>
                        </div>
                        <div style="min-height: 70px">
                            <h2 class="mb-3 title font-13 font-weight-bold">
                                {if $question_info.set_top}
                                <i class="iconfont icon-zhiding text-warning font-14"></i>
                                {/if}
                                {:htmlspecialchars_decode($question_info.title)}

                                {:hook('extend_title_label',['area'=>'question_detail','info'=>$question_info])}
                            </h2>
                            {if !empty($question_info['topics']) || get_user_permission('edit_content_topic')=='Y'}
                            <div class="page-detail-topic mb-2">
                                {if !empty($question_info['topics'])}
                                <ul id="awTopicList" class="d-inline p-0">
                                    {volist name="question_info['topics']" id="v"}
                                    <li class="d-inline-block aw-tag"><a href="{:url('topic/detail',['id'=>$v['id']])}" class="aw-topic" data-id="{$v.id}"><em class="tag">{$v.title}</em></a></li>
                                    {/volist}
                                </ul>
                                {/if}
                                {if $user_id && (get_user_permission('edit_content_topic')=='Y' || $user_id == $question_info.uid || isSuperAdmin() || isNormalAdmin())}
                                <a href="javascript:;" class="aw-ajax-open d-inline" data-url="{:url('topic/select',['item_type'=>'question','item_id'=>$question_info['id']])}"><i class="icon-edit1"> </i></a>
                                {/if}
                            </div>
                            {/if}
                        </div>
                        <div class="aw-content-info">
                            {:hook('pageDetailTop',['info'=>$question_info])}

                            <div class="aw-content position-relative" id="question-content">
                                <div id="show-all" >{$question_info.detail|raw}</div>
                                {if $question_info.detail}
                                <div class="aw-question-show aw-alpha-hidden" style="display: none">
                                    <span style="cursor: pointer;"><i class="icon-chevrons-down"></i> {:L('阅读全文')}</span>
                                </div>
                                <div class="aw-question-hide aw-alpha-hidden" style="display: none;background:none;position: inherit;height: auto">
                                    <span style="position: unset;float: left;cursor: pointer"><i class="icon-chevrons-up"></i> {:L('收起全文')}</span>
                                </div>
                                {/if}
                            </div>

                            {:hook('pageDetailBottom',['info'=>$question_info])}
                            {if !$log}
                            <div class="actions">
                                {if $isMobile}
                                <label class="mr-3">
                                    <button onclick="AWS.User.focus(this,'question','{$question_info.id}')" class="btn btn-primary btn-sm px-3 {if $question_info['has_focus']}active ygz{/if}">{$question_info['has_focus'] ? L('已关注') : L('关注')}</button>
                                </label>
                                <label class="mr-3">
                                    <button class="btn btn-outline-primary btn-sm px-3 awsAnswerEditor" data-enable="{$user_id && $user_info['permission']['publish_answer_enable']=='N' ? 1 : 0}" data-question-id="{$question_info['id']}" data-answern-id="0">
                                        {:L('回复')}</button>
                                </label>
                                <label class="mr-3">
                                    <a href="javascript:;" class="btn btn-outline-secondary btn-sm {$question_info['vote_value']==1 ? 'active' : ''}" onclick="AWS.User.agree(this,'question','{$question_info.id}');" title="{:L('这是个好问题')}"><i class="fa fa-thumbs-up"></i>
                                        {:L('好问题')}</a>
                                </label>
                                <div class="dropdown d-inline-block float-right">
                                    <a href="javascript:;" data-toggle="dropdown" class="btn btn-default btn-sm" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h d-sm-inline-block"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm">
                                        <div class="text-center d-block py-2" style="min-width: 100px">

                                            <a href="javascript:;" class="dropdown-item"><span class="question-comment-count">{$question_info['comment_count']}</span>{:L('评论')}</a>
                                            <a href="javascript:;" class="dropdown-item" onclick="AWS.User.favorite(this,'question','{$question_info.id}')">{if $checkFavorite} {:L('已收藏')}{else}{:L('收藏')}{/if} </a>
                                            <a href="javascript:;" class="dropdown-item" {if !$checkReport} onclick="AWS.User.report(this,'question','{$question_info.id}')" {/if} >{if $checkReport}{:L('已举报')}{else}{:L('举报')}{/if}</a>
                                            <a href="javascript:;" class="dropdown-item" onclick="AWS.User.invite(this,'{$question_info.id}')">
                                                <span>{:L('邀请回答')}</span>
                                            </a>
                                            <a class="dropdown-item" href="{:url('question/detail',['id'=>$question_info['id'],'log'=>1])}">
                                                <span>{:L('修改记录')}</span>
                                            </a>
                                            {if isSuperAdmin() || isNormalAdmin() || $question_info['uid']==$user_id || get_user_permission('modify_question')=='Y'}
                                            <a href="{:url('question/publish?id='.$question_info['id'])}" class="dropdown-item"><span>{:L('编辑问题')}</span></a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || $question_info['uid']==$user_id || get_user_permission('remove_question')=='Y'}
                                            <a class="aw-ajax-get dropdown-item"  href="javascript:;" data-confirm="{:L('是否删除该问题')}?" data-url="{:url('ajax.Question/remove_question',['id'=>$question_info['id']])}">
                                                <span>{:L('删除问题')}</span>
                                            </a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || get_user_permission('recommend_post')=='Y'}
                                            <a href="javascript:;" class="aw-ajax-get dropdown-item" data-url="{:url('ajax.Question/manager',['id'=>$question_info['id'],'type'=>$question_info['is_recommend'] ? 'un_recommend' : 'recommend'])}">
                                                <span>{$question_info['is_recommend'] ? L('取消推荐') : L('推荐问题')}</span>
                                            </a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || get_user_permission('set_top_post')=='Y'}
                                            <a  href="javascript:;" class="aw-ajax-get dropdown-item" data-url="{:url('ajax.Question/manager',['id'=>$question_info['id'],'type'=> $question_info['set_top'] ? 'unset_top' : 'set_top'])}">
                                                <span>{$question_info['set_top'] ? L('取消置顶') : L('置顶问题')}</span>
                                            </a>
                                            {/if}

                                            {if (isSuperAdmin() || isNormalAdmin() || get_user_permission('lock_question')=='Y') && !$question_info['is_lock']}
                                            <a class="aw-ajax-get dropdown-item" data-title="锁定问题" href="javascript:;" data-url="{:url('ajax.Question/lock_question',['question_id'=>$question_info['id']])}">
                                                <span>{:L('锁定问题')}</span>
                                            </a>
                                            {/if}

                                            {if isSuperAdmin() || isNormalAdmin() || get_user_permission('redirect_question')=='Y'}
                                            <a class="aw-ajax-open dropdown-item" data-title="问题重定向" href="javascript:;" data-url="{:url('ajax.Question/redirect_content',['item_id'=>$question_info['id'],'item_type'=>'question'])}">
                                                <span>{:L('问题重定向')}</span>
                                            </a>
                                            {/if}

                                            {if isSuperAdmin() || isNormalAdmin()}
                                            <a class="aw-ajax-open dropdown-item" data-title="添加到帮助" href="javascript:;" data-url="{:url('ajax.Help/select_chapter',['item_id'=>$question_info['id'],'item_type'=>'question'])}">
                                                <span>{:L('添加到帮助')}</span>
                                            </a>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                {else/}
                                <label class="mr-3">
                                    <button onclick="AWS.User.focus(this,'question','{$question_info.id}')" class="btn btn-primary btn-sm px-3 {if $question_info['has_focus']}active ygz{/if}">{$question_info['has_focus'] ? L('已关注') : L('关注问题')}</button>
                                </label>
                                <label class="mr-3">
                                    <button class="btn btn-outline-primary btn-sm px-3 awsAnswerEditor" data-enable="{$user_id && $user_info['permission']['publish_answer_enable']=='N' ? 1 : 0}" data-question-id="{$question_info['id']}" data-answern-id="0">
                                        {:L('回答问题')}</button>
                                </label>
                                <label class="mr-4">
                                    <button class="btn btn-outline-secondary btn-sm px-3" onclick="AWS.User.invite(this,'{$question_info.id}')">
                                        {:L('邀请回答')}</button>
                                </label>
                                <label class="mr-3">
                                    <a href="javascript:;" class="{$question_info['vote_value']==1 ? 'active' : ''}" onclick="AWS.User.agree(this,'question','{$question_info.id}');" title="{:L('这是个好问题')}"><i class="fa fa-thumbs-up"></i>
                                        {:L('好问题')}</a>
                                </label>
                                <label class="mr-3 questionCommentBtn" data-id="{$question_info['id']}">
                                    <a href="javascript:;"><i class="fa fa-keyboard"></i> <span class="question-comment-count">{$question_info['comment_count']}</span> {:L('评论')}</a>
                                </label>
                                <label class="mr-3">
                                    <a href="javascript:;" onclick="AWS.User.favorite(this,'question','{$question_info.id}')"><i class="fa fa-star"></i>{if $checkFavorite} {:L('已收藏')}{else}{:L('收藏')}{/if} </a>
                                </label>
                                <label class="mr-3">
                                    <a href="javascript:;" {if !$checkReport} onclick="AWS.User.report(this,'question','{$question_info.id}')" {/if} ><i class="fa fa-info-circle"></i> {if $checkReport}{:L('已举报')}{else}{:L('举报')}{/if}</a>
                                </label>
                                <div class="dropdown d-inline-block mr-3">
                                    <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-fw fa-share-alt font-9 d-none d-sm-inline-block"></i>{:L('分享')}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm">
                                        <div class="text-center d-block py-2" style="min-width: 100px">
                                            <a href="javascript:;"  class="dropdown-item aw-clipboard" data-clipboard-text="{:url('question/detail',['id'=>$question_info.id],true,true)}"><i class="icon-link"></i>
                                                {:L('复制链接')}</a>
                                            <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="fab fa-weibo text-warning"></i>
                                                {:L('新浪微博')}</a>
                                            <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="fab fa-qq text-primary"></i>
                                                {:L('腾讯空间')}</a>
                                            <div class="aw-qrcode-container" data-share="{:url('question/detail',['id'=>$question_info.id],true,true)}">
                                                <a href="javascript:;" class="dropdown-item "><i class="fab fa-weixin text-success"></i>
                                                    {:L('微信扫一扫')}</a>
                                                <div class="aw-qrcode text-center py-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <label class="aw-question-show mr-3" style="display: none">
                                    <a href="javascript:;"><i class="fa fa-fw fa-angle-down"></i> {:L('展开')}</a>
                                </label>
                                <label class="aw-question-hide mr-3" style="display: none;">
                                    <a href="javascript:;"><i class="fa fa-fw fa-angle-up"></i> {:L('收起')}</a>
                                </label>
                                {if $user_id}
                                <div class="dropdown d-inline-block">
                                    <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h d-none d-sm-inline-block"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm">
                                        <div class="text-center d-block py-2" style="min-width: 100px">
                                            <a class="dropdown-item" href="{:url('question/detail',['id'=>$question_info['id'],'log'=>1])}">
                                                <span>{:L('修改记录')}</span>
                                            </a>
                                            {if isSuperAdmin() || isNormalAdmin() || $question_info['uid']==$user_id || get_user_permission('modify_question')=='Y'}
                                            <a href="{:url('question/publish?id='.$question_info['id'])}" class="dropdown-item"><span>{:L('编辑问题')}</span></a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || $question_info['uid']==$user_id || get_user_permission('remove_question')=='Y'}
                                            <a class="aw-ajax-get dropdown-item"  href="javascript:;" data-confirm="{:L('是否删除该问题')}?" data-url="{:url('ajax.Question/remove_question',['id'=>$question_info['id']])}">
                                                <span>{:L('删除问题')}</span>
                                            </a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || get_user_permission('recommend_post')=='Y'}
                                            <a href="javascript:;" class="aw-ajax-get dropdown-item" data-url="{:url('ajax.Question/manager',['id'=>$question_info['id'],'type'=>$question_info['is_recommend'] ? 'un_recommend' : 'recommend'])}">
                                                <span>{$question_info['is_recommend'] ? L('取消推荐') : L('推荐问题')}</span>
                                            </a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || get_user_permission('set_top_post')=='Y'}
                                            <a  href="javascript:;" class="aw-ajax-get dropdown-item" data-url="{:url('ajax.Question/manager',['id'=>$question_info['id'],'type'=> $question_info['set_top'] ? 'unset_top' : 'set_top'])}">
                                                <span>{$question_info['set_top'] ? L('取消置顶') : L('置顶问题')}</span>
                                            </a>
                                            {/if}
                                            {if (isSuperAdmin() || isNormalAdmin() || get_user_permission('lock_question')=='Y') && !$question_info['is_lock']}
                                            <a class="aw-ajax-get dropdown-item" data-title="锁定问题" href="javascript:;" data-url="{:url('ajax.Question/lock_question',['question_id'=>$question_info['id']])}">
                                                <span>{:L('锁定问题')}</span>
                                            </a>
                                            {/if}
                                            {if isSuperAdmin() || isNormalAdmin() || get_user_permission('redirect_question')=='Y'}
                                            <a class="aw-ajax-open dropdown-item" data-title="问题重定向" href="javascript:;" data-url="{:url('ajax.Question/redirect_content',['item_id'=>$question_info['id']])}">
                                                <span>{:L('问题重定向')}</span>
                                            </a>
                                            {/if}

                                            {if isSuperAdmin() || isNormalAdmin()}
                                            <a class="aw-ajax-open dropdown-item" data-title="添加到帮助" href="javascript:;" data-url="{:url('ajax.Help/select_chapter',['item_id'=>$question_info['id'],'item_type'=>'question'])}">
                                                <span>{:L('添加到帮助')}</span>
                                            </a>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                {/if}
                                {/if}
                            </div>

                            <!--评论框动态显示-->
                            <div class="answerCommentBox mt-2 border" id="questionCommentBox-{$question_info.id}" style="display: none">
                                <div class="answerCommentHeader clearfix px-3 pt-3">
                                    <h6 class="font-10 float-left mb-1"><span class="question-comment-count">{$question_info.comment_count}</span> {:L('评论')}</h6>
                                </div>
                                <div class="questionCommentList p-3"></div>
                                <div class="pageElement"></div>
                                <div class="commentForm clearfix rounded"></div>
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>

                {if !$log}

                {if $user_id}
                <div id="answerEditor" class="mb-2" style="display: none"></div>
                {/if}
                {if $attach_list}
                {if get_plugins_config('paid_attach','enable')=='Y'}
                {:hook('attachDetail',['info'=>$question_info,'page'=>'question','attach_list'=>$attach_list??[]])}
                {else/}
                <div class="card mb-2 border-0">
                    <div class="card-header border-0 bg-white"><b>{:L('附件列表')}</b></div>
                    <div class="card-body pt-0">
                        {volist name="attach_list" id="v"}
                        <dl class="clearfix mb-0 py-1">
                            <dt class="float-left">
                                <p class="mb-1">{$v.name}</p>
                                <p class="text-muted font-8 mb-0">{:formatBytes($v.size)}</p>
                            </dt>
                            <dd class="float-right mb-0">
                                <a href="javascript:;" class="btn btn-primary btn-sm attach-download" data-name="{$v.auth_key}">{:L('下载')}</a>
                            </dd>
                        </dl>
                        {/volist}
                    </div>
                </div>
                {/if}
                {/if}
                <div id="answer-container">
                    {if $answer_id}
                    <p class="aw-view-all-answer bg-white p-3 text-center mb-2"><a  data-pjax="answer-container" href="{:url('question/detail',['id'=>$question_info['id'],'answer'=>0])}">{:L('查看全部')} <span class="aw-answer-count">{$question_info.answer_count}</span> 个回答</a></p>
                    {else/}
                    {if get_setting('visitor_view_answer_count',0) && !$user_id}
                    <div class="alert alert-danger mt-3">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <p class="text-center mb-2">{:L('您还未登录!暂时最多只可查看 %s 条回答',get_setting('visitor_view_answer_count',0))}</p>
                        <p class="text-center mt-2">{:L('去')} <a href="javascript:;" onclick="AWS.User.login();" class="mr-1 text-primary">{:L('登录')}</a>!
                            {:L('还没有账号?')}{:L('去')}<a <a href="javascript:;" onclick="AWS.User.login();" class="text-primary">{:L('注册')}</a></p>
                    </div>
                    {else/}
                    <div class="{$force_fold?'':'d-none'} clearfix py-2 px-3 my-2 bg-primary rounded" id="load_force_fold_answers">
                        <span class="float-right text-light" style="cursor: pointer" onclick="layer.alert('{:L('被折叠的回复是被你或者被大多数用户不感兴趣的回复')}');">{:L('为什么被折叠')}？</span>
                        <a href="javascript:;" class="text-center text-white">{:L('%s 个回复被折叠', $force_fold)}</a>
                    </div>
                    <div class="d-none" id="force_fold_answers_list"></div>
                    {/if}
                    <div class="aw-mod bg-white px-3 pt-3">
                        <div class="aw-mod-head mb-0 border-bottom clearfix pb-3">
                            <p class="float-left mr-5 font-weight-bold">
                                <span class="aw-answer-count">{$question_info.answer_count}</span> {:L('回答')}
                            </p>

                            <div class="dropdown float-right">
                                <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-fw fa-angle-down d-sm-inline-block"></i><span>{$sort_texts[$sort]}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm">
                                    <div class="text-center d-block py-2 aw-nav aw-dropdown-nav text-center aw-answer-sort" style="min-width: 100px">
                                        <div class="{$sort=='new' ? 'active':''} py-1"><a href="{:url('question/detail',['id'=>$question_info['id'],'answer'=>0,'sort'=>'new'])}"  data-pjax="answer-container">{:L('最新排序')}</a> </div>
                                        <div class="{$sort=='hot' ? 'active':''} py-1"><a href="{:url('question/detail',['id'=>$question_info['id'],'answer'=>0,'sort'=>'hot'])}"  data-pjax="answer-container">{:L('热门排序')} </a></div>
                                        <div class="{$sort=='publish' ? 'active':''} py-1"><a href="{:url('question/detail',['id'=>$question_info['id'],'answer'=>0,'sort'=>'publish'])}"  data-pjax="answer-container">{:L('只看楼主')} </a></div>
                                        {if $user_id}
                                        <div class="{$sort=='focus' ? 'active':''} py-1"><a href="{:url('question/detail',['id'=>$question_info['id'],'answer'=>0,'sort'=>'focus'])}"  data-pjax="answer-container">{:L('关注的人')} </a></div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {/if}
                    <div class="aw-answer-body aw-answer-list" id="aw-answer-list" data-id="{$question_info.id}" data-aid="{$answer_id}">
                        {if !empty($data)}
                        {volist name="data" id="v"}
                        <div class="aw-answer-item p-3 mb-1 bg-white" data-uninterested_count="{$v.uninterested_count}" id="question-answer-{$v.id}" data-answer-id="{$v.id}">
                            <div class="user-details-card pt-0 pb-2 position-relative clearfix">
                                <div class="user-details-card-avatar float-left" style="position: relative">
                                    {if $v.is_anonymous}
                                    <a href="javascript:;">
                                        <img src="/static/common/image/default-avatar.svg" class="rounded" alt="{:L('匿名用户')}" data-toggle="popover" title="{:L('匿名用户')}" style="width: 40px;height: 40px">
                                    </a>
                                    {else/}
                                    <a href="{$v['user_info']['url']}" class="aw-username" data-id="{$v.uid}" title="{$v['user_info']['name']}">
                                        <img src="{$v['user_info']['avatar']}" class="rounded" onerror="this.src='/static/common/image/default-avatar.svg'" alt="{$v['user_info']['name']}" style="width: 40px;height: 40px">
                                        {if $v['user_info']['verified']}
                                        <img src="{$v['user_info']['verified_icon']}" width="16" height="16" class="verifiedInfo position-absolute">
                                        {/if}
                                    </a>
                                    {/if}
                                </div>
                                <div class="user-details-card-name float-left ml-2">
                                    {if $v.is_anonymous}<a href="javascript:;" data-toggle="popover" title="{:L('匿名用户')}">{:L('匿名用户')}</a>{else/}<a href="{$v['user_info']['url']}" data-id="{$v.uid}" class="aw-username" title="{$v['user_info']['name']}">{$v['user_info']['name']}</a> <span class="badge badge-success">{$v['user_info']['group_name']|default=''}</span> {/if} {if $setting.show_answer_user_ip=='Y' && $v.answer_user_local}<span class="ml-2 text-muted font-8">{:L('用户来自于')}: {$v.answer_user_local}</span>{/if} <br><span class="ml-0"> {:date_friendly($v['create_time'])} </span>
                                </div>

                                {if $v['is_best']}
                                <div class="aw-answer-best">
                                    <i class="fa fa-award " title="{:L('最佳回答')}"></i>
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

                            <!--回答操作-->
                            <div class="answer-btn-actions mt-3">
                                <label class="mr-1">
                                    <a href="javascript:;" class="aw-ajax-agree {if $v['vote_value']==1}active{/if}"  onclick="AWS.User.agree(this,'answer','{$v.id}');">
                                        <i class="icon-thumb_up"></i> {:L('赞同')} <span> {$v.agree_count}</span>
                                    </a>
                                </label>

                                <label class="mr-3 ">
                                    <a href="javascript:;" class="aw-ajax-against {if $v['vote_value']==-1}active{/if}"  onclick="AWS.User.against(this,'answer','{$v.id}');">
                                        <i class="icon-thumb_down"></i>
                                    </a>
                                </label>

                                <label class="mr-3">
                                    <a href="javascript:;" class="answerCommentBtn" data-url="{:url('comment/answer',['id'=>$v.id])}" data-id="{$v.id}">
                                        <i class="icon-chat"></i> <span class="answer-comment-count{$v.id}">{$v.comment_count}</span>{:L('评论')}
                                    </a>
                                </label>

                                {if $user_id}
                                <label class="mr-3">
                                    <a href="javascript:;" {if !$v.checkReport} onclick="AWS.User.report(this,'answer','{$v.id}')"{/if} ><i class="icon-warning"></i>{$v.checkReport ? L('已举报') : L('举报')}</a>
                                </label>

                                <label class="mr-3">
                                    <a href="javascript:;" onclick="AWS.User.favorite(this,'answer','{$v.id}')"><i class="icon-turned_in"></i>{if $v.checkFavorite} {:L('已收藏')}{else}{:L('收藏')}{/if}</a>
                                </label>

                                {if $user_id!=$v['uid']}
                                <label class="mr-3">
                                    <a href="javascript:;"  {if $v.has_thanks} class="active" {else/}onclick="AWS.User.thanks(this,'{$v.id}')"{/if}>
                                    <i class="icon-favorite"></i> <span>{$v.has_thanks ? L('已感谢') : L('感谢')}</span>
                                    </a>
                                </label>
                                {/if}

                                {if !$v.has_uninterested}
                                <label class="mr-3">
                                    <a href="javascript:;" onclick="AWS.User.uninterested(this,'answer','{$v.id}')">
                                        <i class="icon-report"></i> {:L('不感兴趣')}
                                    </a>
                                </label>
                                {/if}

                                {if isSuperAdmin() || isNormalAdmin()}
                                <label class="mr-3">
                                    <a href="javascript:;" data-url="{:url('answer/force')}" onclick="AWS.User.forceFoldAnswer(this,'{$v.id}')">
                                        <i class="fa fa-cut "></i> <span>{:L($v.force_fold?'取消折叠':'折叠')}</span>
                                    </a>
                                </label>
                                {/if}

                                {/if}
                                {if ($user_id && get_user_permission('set_best_answer')=='Y' && !$v['is_best'] && !$best_answer_count)}
                                <label class="mr-3">
                                    <a href="javascript:;"  class="aw-ajax-get" data-confirm="{:L('是否把该回答设为最佳')}？" data-url="{:url('answer/set_answer_best?answer_id='.$v['id'])}">
                                        <i class="fa fa-award "></i> {:L('最佳')}
                                    </a>
                                </label>
                                {/if}
                                <div class="dropdown d-inline-block mr-3">
                                    <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-fw fa-share-alt font-9 d-sm-inline-block"></i> {:L('分享')}
                                    </a>
                                    <div class="dropdown-menu p-0 border-0 font-size-sm">
                                        <div class="text-center d-block py-2" style="min-width: 100px">
                                            <a href="javascript:;"  class="dropdown-item aw-clipboard" data-clipboard-text="{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}"><i class="icon-link"></i>
                                                {:L('复制链接')}</a>
                                            <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}','','weibo')" class="dropdown-item "><i class="fab fa-weibo text-warning"></i>
                                                {:L('新浪微博')}</a>
                                            <a href="javascript:;" onclick="AWS.User.share('{$question_info.title}','{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}','','qzone')" class="dropdown-item "><i class="fab fa-qq text-primary"></i>
                                                {:L('腾讯空间')}</a>
                                            <div class="aw-qrcode-container" data-share="{:url('question/detail',['answer'=>$v.id,'id'=>$question_info.id],true,true)}">
                                                <a href="javascript:;" class="dropdown-item "><i class="fab fa-weixin text-success"></i>
                                                    {:L('微信扫一扫')}</a>
                                                <div class="aw-qrcode text-center py-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--问题回答操作栏钩子-->
                                {:hook('question_answer_bottom_action',$v)}

                                <div class="aw-share clearfix d-inline-block">
                                    <div class="social-share" data-disabled="google,twitter,facebook,linkedin,douban"></div>
                                </div>
                                {if $user_id}
                                <div class="dropdown d-inline-block">
                                    <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h d-none d-sm-inline-block"></i>
                                    </a>
                                    <div class="dropdown-menu p-0 border-0 font-size-sm">
                                        <div class="text-center d-block py-2" style="min-width: 100px">
                                            {if $user_info['uid']==$v['uid'] || get_user_permission('modify_answer')=='Y'}
                                            <a href="javascript:;"  class="dropdown-item awsAnswerEditor" data-question-id="{$v.question_id}" data-answer-id="{$v['id']}">{:L('编辑')}</a>
                                            {/if}
                                            {if $user_info['uid']==$v['uid'] || get_user_permission('remove_answer')=='Y'}
                                            <a href="javascript:;" title="{:L('删除回答')}" class="dropdown-item aw-ajax-get" data-confirm="{:L('是否删除该回答')}?" data-url="{:url('answer/delete_answer?answer_id='.$v['id'])}">{:L('删除')}</a>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                                {/if}
                            </div>

                            <!--评论框动态显示-->
                            <div class="answerCommentBox mt-2 border" id="answerCommentBox-{$v.id}" style="display: none;margin-left: 45px">
                                <div class="answerCommentHeader clearfix px-3 pt-3">
                                    <h6 class="font-10 float-left mb-1"><span class="answer-comment-count{$v.id}">{$v.comment_count}</span> {:L('评论')}</h6>
                                </div>
                                <div class="answerCommentList px-3"></div>
                                <div class="pageElement"></div>
                                <div class="commentForm clearfix rounded aw-replay-box"></div>
                            </div>
                        </div>
                        {/volist}
                        {if $page_render}
                        <div class="bg-white p-3">
                            {$page_render|raw}
                        </div>
                        {/if}
                        {else/}
                        <p class="text-center text-muted p-3 bg-white">
                            <img src="{$cdnUrl}/static/common/image/empty.svg">
                            <span class="d-block">{:L('暂无回答')}</span>
                        </p>
                        {/if}
                    </div>
                </div>
                {else/}
                <div class="aw-mod bg-white px-3 pt-3">
                    <div class="aw-mod-head mb-0 border-bottom clearfix pb-3">
                        <p class="float-left mr-5 font-weight-bold">
                            <span class="aw-answer-count">{:L('修改记录')}
                        </p>
                        <a href="{:url('question/detail',['id'=>$question_info.id])}" class="float-right text-muted font-8" data-pjax="wrapMain">{:L('返回问题')} »</a>
                    </div>

                    <div class="aw-mod-body py-3 aw-modify-log">
                        {foreach $logs as $k=>$v}
                        <dl class="border-bottom">
                            <dt>{$v.label|raw}</dt>
                            <dd class="mt-2">{$v.content|raw}</dd>
                            <dd>{$v.create_time}</dd>
                        </dl>
                        {/foreach}
                    </div>
                </div>
                {/if}
            </div>
            <div class="aw-right col-md-3 px-xs-0">
                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('write_nav',get_theme_setting('question_detail.sidebar_show_items'))}
                {:widget('sidebar/writeNav')}
                {/if}

                {:hook('sidebarTop')}

                {if !$question_info['is_anonymous']}
                <div class="r-box mb-1">
                    <div class="r-title">
                        <h4>{:L('关于作者')}</h4>
                    </div>
                    <div class="block-content">
                        <dl class="overflow-hidden mb-0 pb-2 border-bottom">
                            <dt class="float-left">
                                <a href="{$question_info['user_info']['url']}" data-pjax="pageMain" class="aw-username" data-id="{$question_info.uid}">
                                    <img src="{$question_info['user_info']['avatar']|default='/static/common/image/default-avatar.svg'}" class="rounded" width="45" height="45">
                                    {if $question_info['user_info']['verified']}
                                    <img src="{$question_info['user_info']['verified_icon']}" width="16" height="16" class="verifiedInfo">
                                    {/if}
                                </a>
                            </dt>
                            <dd class="float-right" style="width:calc(100% - 55px)">
                                <a href="{$question_info['user_info']['url']}" class="d-block aw-one-line aw-username" data-id="{$question_info.uid}" target="_blank">
                                    <strong>{$question_info['user_info']['name']}</strong> <span class="badge badge-success">{$question_info['user_info']['group_name']|default=''}</span>
                                </a>
                                <p class="mb-0 font-8 text-muted aw-one-line">{$question_info['user_info']['signature']|default=L('这家伙很懒，还没有设置简介')}</p>
                            </dd>
                        </dl>
                        <div class="d-flex text-center pt-3 pb-3 text-muted">
                            <a href="{:get_user_url($question_info['uid'],['type'=>'answer'])}" target="_blank" class="flex-fill mb-0">
                                <dl class="mb-0">
                                    <dt>{$publish_answer_count}</dt>
                                    <dd>{:L('回答')}</dd>
                                </dl>
                            </a>
                            <a href="{:get_user_url($question_info['uid'],['type'=>'article'])}" target="_blank" class="flex-fill mb-0">
                                <dl class="mb-0">
                                    <dt>{$publish_article_count}</dt>
                                    <dd>{:L('文章')}</dd>
                                </dl>
                            </a>
                            <a href="{:get_user_url($question_info['uid'],['type'=>'question'])}" target="_blank" class="flex-fill mb-0">
                                <dl class="mb-0">
                                    <dt>{$publish_question_count}</dt>
                                    <dd>{:L('问题')}</dd>
                                </dl>
                            </a>
                        </div>
                        {if $user_id && $user_id!=$question_info['uid']}
                        <div class="user-btn-group d-flex text-center">
                            <div class="mr-2 flex-fill">
                                <button class="btn btn-primary btn-sm w-100 {$question_info['user_focus'] ? 'active ygz' : ''}" onclick="AWS.User.focus(this,'user','{$question_info.uid}')">{$question_info['user_focus'] ? L('已关注') : L('关注TA')}</button>
                            </div>
                            <div class="ml-2 flex-fill">
                                <button onclick="AWS.User.inbox('{$question_info['user_info']['nick_name']}')" class="btn btn-outline-secondary btn-sm w-100">
                                    {:L('发私信')}</button>
                            </div>
                        </div>
                        {/if}
                    </div>
                </div>
                {/if}

                <div class="r-box mb-2">
                    <div class="r-title">
                        <h4>{:L('问题动态')}</h4>
                    </div>
                    <div class="block-content2 pb-2">
                        <div class="text-center text-muted">
                            <dl class="clearfix mb-1">
                                <dt class="float-left">{:L('发布时间')}</dt>
                                <dd class="float-right">{:date_friendly($question_info['create_time'])}</dd>
                            </dl>
                            <dl class="clearfix mb-1">
                                <dt class="float-left">{:L('更新时间')}</dt>
                                <dd class="float-right">{:date_friendly($question_info['update_time'])}</dd>
                            </dl>
                            <dl class="clearfix mb-1">
                                <dt class="float-left">{:L('关注人数')}</dt>
                                <dd class="float-right">{:L('%s 人关注',$question_info['focus_count'])}</dd>
                            </dl>
                        </div>
                        <div>
                            <ul id="questionFocusUsers">
                                {foreach $question_focus_users as $k=>$v}
                                <li class="px-1 d-inline-block">
                                    <a href="{$v.url}" class="aw-username" data-id="{$v.uid}" title="{$v.nick_name}" >
                                        <img src="{$v.avatar}" width="26" height="26" style="border-radius: 50%" alt="{$v.nick_name}">
                                    </a>
                                </li>
                                {/foreach}
                                {if $question_info['focus_count']>10}
                                <li class="p-1 d-inline-block">
                                    <a href="javascript:;" class="text-center d-inline-block showAllFocusUsers" title="{:L('查看更多')}" style="border-radius: 50%;width: 26px;height: 26px;line-height: 26px;background: #f6f6f6"><i class="icon icon-more-horizontal "></i></a>
                                </li>
                                {/if}
                            </ul>
                        </div>

                    </div>
                </div>

                {if $relation_question && get_theme_setting('question_detail.sidebar_show_relation_question')=='Y'}
                <div class="r-box mb-2">
                    <div class="r-title">
                        <h4>{:L('相关问题')}</h4>
                    </div>
                    <div class="aboutanswer">
                        {volist name="relation_question" id="v"}
                        <dl class="mb-0 py-2">
                            <dt class="d-block aw-one-line font-weight-normal font-9">
                                <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
                                <label class="mr-2 mb-0">{$v.focus_count} {:L('关注')}</label>
                                <label class="mr-2 mb-0">{$v['answer_count']} {:L('回答')}</label>
                                <label class="mb-0">{$v['comment_count']} {:L('评论')}</label>
                            </dd>
                        </dl>
                        {/volist}
                    </div>
                </div>
                {/if}

                {if $recommend_post && get_theme_setting('question_detail.sidebar_show_recommend_post')=='Y'}
                <div class="r-box mb-2">
                    <div class="r-title">
                        <h4>{:L('推荐内容')}</h4>
                    </div>
                    <div  class="aboutanswer">
                        {volist name="recommend_post" id="v"}
                        {if $v['item_type']=='article'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block aw-one-line font-weight-normal font-9">
                                <span class="bg-primary text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">文</span> <a href="{:url('article/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
                                <label class="mb-0">{$v['comment_count']} {:L('评论')}</label>
                            </dd>
                        </dl>
                        {/if}
                        {if $v['item_type']=='question'}
                        <dl class="mb-0 py-2 border-bottom">
                            <dt class="d-block aw-one-line font-weight-normal font-9">
                                <span class="bg-warning text-white font-8 d-inline-block text-center rounded" style="width: 18px;height: 18px">问</span> <a href="{:url('question/detail',['id'=>$v['id']])}">{$v.title}</a>
                            </dt>
                            <dd class="mt-2 font-9 text-color-info mb-0">
                                <label class="mr-2 mb-0">{$v.view_count} {:L('浏览')}</label>
                                <label class="mr-2 mb-0">{$v.focus_count} {:L('关注')}</label>
                                <label class="mr-2 mb-0">{$v['answer_count']} {:L('回答')}</label>
                                <label class="mb-0">{$v['comment_count']} {:L('评论')}</label>
                            </dd>
                        </dl>
                        {/if}
                        {/volist}
                    </div>
                </div>
                {/if}

                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('announce',get_theme_setting('question_detail.sidebar_show_items'))}
                {:widget('sidebar/announce')}
                {/if}

                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('focus_topic',get_theme_setting('question_detail.sidebar_show_items'))}
                {:widget('sidebar/focusTopic',['uid'=>$user_id])}
                {/if}

                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('hot_topic',get_theme_setting('question_detail.sidebar_show_items'))}
                {:widget('sidebar/hotTopic',['uid'=>$user_id])}
                {/if}

                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('column',get_theme_setting('question_detail.sidebar_show_items'))}
                {:widget('sidebar/hotColumn',['uid'=>$user_id,'sort'=>'hot'])}
                {/if}

                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('hot_users',get_theme_setting('question_detail.sidebar_show_items'))}
                {:widget('sidebar/hotUsers',['uid'=>$user_id])}
                {/if}

                {if get_theme_setting('question_detail.sidebar_show_items') && in_array('diy_content',get_theme_setting('question_detail.sidebar_show_items'))}
                {$theme_config['home']['sidebar_diy_content']|raw|htmlspecialchars_decode}
                {/if}

                {:hook('sidebarBottom')}
            </div>
        </div>
    </div>
</div>
{/block}
{block name="script"}
<script>
    $(document).on('click', '.showAllFocusUsers', function(event) {
        $.ajax({
            type: 'GET',
            url: "{:url('ajax/get_question_focus_users',['question_id'=>$question_info['id']])}",
            dataType:'json',
            success: function (ret) {
                var html='';
                $.each(ret.data, function(i,val){
                    html +='<li class="px-1 d-inline-block"> <a href="'+val.url+'" class="aw-username" data-id="'+val.uid+'" title="'+val.nick_name+'" > <img src="'+val.avatar+'" width="26" height="26" style="border-radius: 50%" alt="'+val.nick_name+'"> </a> </li>';
                });
                $('#questionFocusUsers').html(html);
            },
            error: function (xhr) {
                let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                AWS.events.onAjaxError(ret, error);
            }
        });
    })

    $(document).on('click', '#load_force_fold_answers a', function(event) {
        if($('#force_fold_answers_list').hasClass('d-none'))
        {
            $.ajax({
                type: 'GET',
                url: "{:url('ajax.Question/force_fold_answers',['question_id'=>$question_info['id']])}",
                dataType:'json',
                success: function (ret) {
                    $('#force_fold_answers_list').html(ret.data.html).removeClass('d-none');
                },
                error: function (xhr) {
                    let ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    AWS.events.onAjaxError(ret, error);
                }
            });
        }else{
            $('#force_fold_answers_list').html('').addClass('d-none');
        }
    })

    $(document).ready(function ()
    {
        var openEnable="{$setting.open_content_enable}";
        var answerId = parseInt("{$answer_id ? $answer_id : 0}");
        var showAll = $('#show-all');
        if(openEnable==='Y')
        {
            $('.aw-question-show').click();
            $('.aw-answer-show').click();
            if(showAll.height() >= 120)
            {
                $('.aw-question-hide').show();
            }
        }else{
            if(showAll.height() >= 120)
            {
                showAll.show().css('height','120px');
                $('.aw-question-show').show();
            }
        }
        $(document).on('click', '.aw-question-show', function (e) {
            $('.aw-question-show').hide();
            showAll.show().css('height','auto');
            $('.aw-question-hide').show();
        });
        $(document).on('click', '.aw-question-hide', function (e) {
            $('.aw-question-hide').hide();
            showAll.show().css('height','120px');
            $('.aw-question-show').show();
        });
        $('.aw-answer-item .aw-answer-content').each(function(){
            if($(this).height() >= 200)
            {
                if(openEnable==='Y')
                {
                    $(this).css('height','auto');
                    $(this).parents('.aw-answer-item').find('.aw-answer-hide').show();
                }else{
                    if(answerId)
                    {
                        $(this).css('height','auto');
                        $(this).parents('.aw-answer-item').find('.aw-answer-hide').show();
                    }else{
                        $(this).css('height','200px');
                        $(this).parents('.aw-answer-item').find('.aw-answer-show').show();
                    }
                }
            }
        });

        $(document).on('click', '.aw-answer-show', function (e) {
            $(this).hide();
            $(this).parents('.aw-answer-item').find('.aw-answer-content').show().css('height','auto');
            $(this).parents('.aw-answer-item').find('.aw-answer-hide').show();
        });

        $(document).on('click', '.aw-answer-hide', function (e) {
            $(this).hide();
            $(this).parents('.aw-answer-item').find('.aw-answer-content').show().css('height','200px');
            $(this).parents('.aw-answer-item').find('.aw-answer-show').show();
        });
    });
</script>
{/block}