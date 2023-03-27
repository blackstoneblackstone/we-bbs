{extend name="$theme_block" /}
{block name="main"}
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

    <div class="row justify-content-between">
        <div class="aw-left radius col-md-9 mb-2">
            <div class="bg-white px-3 rounded">
                <dl class="topic-detail-top py-3 overflow-hidden mb-0">
                    <dt class="float-left">
                        <div style='background-image:url("{$topic_info.pic|default='/static/common/image/topic.svg'}") ;background-size: cover;width:100px;height:100px' class="rounded"></div>
                    </dt>
                    <dd class="float-right">
                        <h4 class="mb-0 font-12 clearfix">
                            {$topic_info.title}
                            {if $user_id}
                            <a href="javascript:;" style="height: 26px;line-height: 1.2" class="btn btn-primary btn-sm float-right px-4 cursor-pointer {if $topic_info['has_focus']}active ygz {/if}" onclick="AWS.User.focus(this,'topic','{$topic_info.id}')">{$topic_info['has_focus'] ? '<span>'.L('已关注').'</span>' : '<span>+ '.L('关注').'</span>'}</a>
                            {/if}
                        </h4>
                        {if $topic_info['description']}
                        <p class="text-muted aw-two-line mt-2">{:str_cut(strip_tags(htmlspecialchars_decode($topic_info['description'])),0,100)}
                            {if mb_strlen(strip_tags($topic_info['description']))>=100}
                            <a href="{:url('topic/detail',['type'=>'about','id'=>$topic_info['id']])}"  data-pjax="aw-index-main" class="pl-3 text-primary">{:L('查看详情')}></a>
                            {/if}
                        </p>
                        {/if}
                        <p class="text-muted mt-1 font-8">{:L('共 %s 讨论',$topic_info['discuss'])},{:L('7天新增 %s 个讨论',$topic_info['discuss_week'])},{:L('30天新增 %s 个讨论',$topic_info['discuss_month'])} </p>
                    </dd>
                </dl>
            </div>
            <div class="bg-white mt-2" id="wrapMain">
                <div class="clearfix border-bottom">
                    <div class="nav nav-tabs px-4 aw-pjax-a border-0 float-left">
                        <a class="nav-item nav-link {if !$type}active {/if}" data-pjax="wrapMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'sort'=>$sort])}">{:L('综合')}</a>
                        <a class="nav-item nav-link {if $type=='question'}active{/if}" data-pjax="wrapMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'question','sort'=>$sort])}">{:L('问题')}</a>
                        <a class="nav-item nav-link {if $type=='article'}active{/if}" data-pjax="wrapMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'article','sort'=>$sort])}">{:L('文章')}</a>
                        {volist name=":config('aws.tabs')" id="v"}
                        <a class="nav-link nav-item " href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$key,'sort'=>$sort])}" data-pjax="wrapMain">{$v.title}</a>
                        {/volist}
                        <a class="nav-link nav-item {if $type=='about'}active{/if}" data-pjax="wrapMain" href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>'about'])}">
                            {:L('简介')} </a>
                    </div>
                    {if $type!='about'}
                    <div class="dropdown float-right pr-3 py-3">
                        <a href="javascript:;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-fw fa-angle-down d-sm-inline-block"></i><span>{$sort_texts[$sort]}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm">
                            <div class="text-center d-block py-2 aw-nav aw-dropdown-nav text-center aw-answer-sort" style="min-width: 100px">
                                <div class="{$sort=='new' ? 'active':''} py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'new'])}"  data-pjax="wrapMain">{:L('最新排序')}</a> </div>
                                <div class="{$sort=='hot' ? 'active':''} py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'hot'])}"  data-pjax="wrapMain">{:L('热门排序')} </a></div>
                                <div class="{$sort=='recommend' ? 'active':''} py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'recommend'])}"  data-pjax="wrapMain">{:L('推荐内容')} </a></div>
                                {if $type=='question'}
                                <div class="{$sort=='unresponsive' ? 'active':''} py-1"><a href="{:url('topic/detail',['id'=>$topic_info['id'],'type'=>$type,'sort'=>'unresponsive'])}"  data-pjax="wrapMain">{:L('等待回答')} </a></div>
                                {/if}
                            </div>
                        </div>
                    </div>
                    {/if}
                </div>

                <div class="tab-content" id="tabMain">
                    <div class="tab-pane fade show active">
                        <div class="aw-common-list">
                            {if $type!='about'}
                            {:widget('common/lists',['item_type'=>$type,'sort'=>$sort,'topic_ids'=>[$topic_info['id']]])}
                            {else/}
                            <div class="py-3 bg-white aw-content">
                                {:htmlspecialchars_decode($topic_info['description'])}
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="aw-right radius col-md-3 px-xs-0">
            <!--侧边栏顶部钩子-->
            {:hook('sidebarTop')}

            <div class="r-box w-fun mb-2">
                <div class="row">
                    <a class="col-4" href="{:url('question/publish',['topic_id'=>$topic_info['id']])}" target="_blank">
                        <i class="fa icon-help-with-circle fa-2x d-inline-block" style="color:#ff9d08;background: #f7f9da;border-radius: 50px;height: 56px;line-height: 56px;margin-bottom: 10px;width: 56px"></i>
                        <h6>{:L('发讨论')}</h6>
                    </a>
                    <a class="col-4 text-center" href="{:url('article/publish',['topic_id'=>$topic_info['id']])}" target="_blank">
                        <i class="far fa-file-alt fa-2x d-inline-block" style="color:#fd5e5e;background: #ffdfdf;border-radius: 50px;height: 56px;line-height: 56px;margin-bottom: 10px;width: 56px"></i>
                        <h6>{:L('写文章')}</h6>
                    </a>
                    <a class="col-4 aw-ajax-open" href="javascript:;" data-title="话题日志" data-url="{:url('ajax.topic/logs',['id'=>$topic_info['id']])}">
                        <i class="icon-book circle fa-2x d-inline-block" style="color:#6f5a90;background: #ece7f3;border-radius: 50px;height: 56px;line-height: 56px;margin-bottom: 10px;width: 56px"></i>
                        <h6>{:L('话题日志')}</h6>
                    </a>
                </div>
            </div>

            {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['permission']['topic_manager']=='Y')}
            <div class="r-box mb-2">
                <div class="r-title">
                    <h4>{:L('话题管理')}</h4>
                </div>
                <div>
                    <ul class="py-0 row">
                        {if $user_info['permission']['topic_manager']=='Y'}
                        <li class="col-3 mb-3">
                            <a href="{:url('topic/manager',['id'=>$topic_info['id']])}" class="text-center d-block">
                                <i class="icon-edit d-block font-15"></i>
                                <span class="d-block mt-2">{:L('编辑')}</span>
                            </a>
                        </li>
                        {/if}
                        {if $user_id && get_user_permission('lock_topic')=='Y'}
                        <li class="col-3 mb-3">
                            <a href="javascript:;" data-confirm="{$topic_info['lock'] ? L('是否取消锁定该话题?') : L('是否锁定该话题?锁定话题后其他用户将无法对该话题进行编辑')}" data-url="{:url('ajax/lock',['id'=>$topic_info['id']])}" class="aw-ajax-get text-center d-block">
                                <i class="icon-lock d-block font-15"></i>
                                <span class="d-block mt-2">{$topic_info['lock'] ? L('取消'):L('锁定')}</span>
                            </a>
                        </li>
                        {/if}
                        {if $user_id && get_user_permission('remove_topic')=='Y'}
                        <li class="col-3 mb-3">
                            <a href="javascript:;" data-confirm="{:L('是否删除该话题')}？" data-url="{:url('ajax.topic/remove_topic',['id'=>$topic_info['id']])}" class="aw-ajax-get text-center d-block">
                                <i class="fa fa-trash d-block font-15"></i>
                                <span class="d-block mt-2">{:L('删除')}</span>
                            </a>
                        </li>
                        {/if}

                        {if isSuperAdmin() || isNormalAdmin() || get_user_permission('merge_topic')=='Y'}
                        <li class="col-3 mb-3">
                            <a href="javascript:;" data-title="{:L('话题合并')}" data-url="{:url('ajax.Topic/merge_topic',['item_id'=>$topic_info['id']])}" class="aw-ajax-open text-center d-block">
                                <i class="fa fa-link d-block font-15"></i>
                                <span class="d-block mt-2">{:L('合并')}</span>
                            </a>
                        </li>
                        {/if}
                    </ul>
                </div>
            </div>
            {/if}

            {if $topic_info['relation_topics'] || ($user_id && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['permission']['topic_manager']=='Y'))}
            <div class="r-box mb-2">
                <div class="r-title clearfix">
                    <h4 class="float-left">{:L('相关话题')}</h4>
                    {if $user_id && ($user_info['group_id']==1 || $user_info['group_id']==2 || $user_info['permission']['topic_manager']=='Y')}
                    <a href="javascript:;" class="text-primary font-9 aw-ajax-open float-right" data-url="{:url('ajax/merge_topic',['item_id'=>isset($topic_info['id']) ? $topic_info['id'] : 0])}" data-title="{:L('相关话题')}"><i class="icon-edit font-8"></i>
                        {:L('修改')}</a>&nbsp;&nbsp;<span class="font-9 text-muted"></span>
                    {/if}
                </div>
                <div class="page-detail-topic pb-3">
                    <ul class="d-block py-1" id="awTopicList">
                        {volist name="$topic_info['relation_topics']" id="v"}
                        <li class="d-inline-block aw-tag my-1"><a href="{:url('topic/detail',['id'=>$v['id']])}"><em class="tag">{$v.title}</em></a></li>
                        {/volist}
                        {if $topic_info['relation_topics']}
                        <input type="hidden" name="topics" value="{:implode(',',array_column($topic_info['relation_topics'],'id'))}">
                        {/if}
                    </ul>
                </div>
            </div>
            {/if}

            {if $top_parent_topic || $parent_topic || $child_topics}
            <div class="r-box mb-2 py-2">
                {if $top_parent_topic}
                <dl class="mb-0">
                    <dt class="clearfix r-title" style="height: 36px;line-height: 36px">{:L('根话题')}
<!--                        <a href="javascript:;" class="aw-ajax-open" data-title="{:L('话题树')}" data-url="{:url('ajax.topic/tree',['pid'=>$top_parent_topic['id']])}" ><label class="iconfont font-weight-light">&#xe660;</label></a>
-->                 </dt>
                    <dd><a href="{:url('topic/detail',['id'=>$top_parent_topic.id])}" class="aw-topic" data-id="{$top_parent_topic.id}" target="_blank"><em class="tag">{$top_parent_topic.title}</em></a> </dd>
                </dl>
                {/if}
                {if $parent_topic}
                <dl class="mb-0">
                    <dt class="r-title" style="height: 36px;line-height: 36px">{:L('父话题')}</dt>
                    <dd><a href="{:url('topic/detail',['id'=>$parent_topic.id])}" class="aw-topic" data-id="{$parent_topic.id}" target="_blank"><em class="tag">{$parent_topic.title}</em></a> </dd>
                </dl>
                {/if}

                {if $child_topics}
                <dl class="mb-0">
                    <dt class="r-title" style="height: 36px;line-height: 36px">{:L('子话题')}</dt>
                    <dd>
                        {foreach $child_topics as $topic}
                        <a href="{:url('topic/detail',['id'=>$topic.id])}" class="aw-topic my-2" data-id="{$topic.id}" target="_blank" style="display: inline-block;"><em class="tag">{$topic.title}</em></a>
                        {/foreach}
                    </dd>
                </dl>
                {/if}
            </div>
            {/if}

            {if $focus_user}
            <div class="r-box mb-2">
                <div class="r-title">
                    <h4>{:L('%s 人关注该话题',$topic_info['focus'])}</h4>
                </div>
                <div class="hot-list hot-yh-list pb-3" style="margin: 0 -20px">
                    <ul class="row">
                        {foreach $focus_user as $k=>$v}
                        <li class="col-2"><a href="{$v.url}" class="aw-username text-center" target="_blank" data-id="{$v.uid}"><img src="{$v.avatar}" style="width: 32px;he 32px;border-radius: 50%"></a> </li>
                        {/foreach}
                    </ul>
                </div>
            </div>
            {/if}
            {:widget('sidebar/hotTopic',['uid'=>$user_id])}

            <!--侧边栏底部钩子-->
            {:hook('sidebarBottom')}
        </div>
    </div>
</div>
{/block}