{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>$user_id])}
            <div class="col-md-10" id="wrapMain">
                <div class="bg-white">
                    <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block">
                        <li class="nav-item"><a class="nav-link {if $type=='question'}active{/if}" data-pjax="tabMain" href="{:url('draft/index',['type'=>'question'])}">{:L('问题草稿')}</a></li>
                        <li class="nav-item"><a class="nav-link {if $type=='article'}active{/if}" data-pjax="tabMain" href="{:url('draft/index',['type'=>'article'])}">{:L('文章草稿')}</a></li>
                        <li class="nav-item"><a class="nav-link {if $type=='answer'}active{/if}" data-pjax="tabMain" href="{:url('draft/index',['type'=>'answer'])}">{:L('回答草稿')}</a></li>
                    </ul>
                </div>
                <div id="tabMain" class="bg-white">
                    {if !empty($list)}
                    <div class="aw-common-list py-2">
                        {volist name="list" id="v"}
                        {switch name="$v['item_type']"}
                        {case value="question" }
                        <dl class="question position-relative">
                            <dt class="mb-2">
                                <a href="{$user_info['url']}" class="aw-user-name">
                                    <img src="{$user_info['avatar']}" alt="{$user_info['name']}">{$user_info['name']}
                                </a>
                                <span>{:L('发起了提问')}</span>
                                <label class="float-right">{:date_friendly($v['create_time'])}</label>
                                {if isset($v['data']['topics']) && !empty($v['data']['topics'])}
                                <div class="aw-tag d-inline">
                                    {volist name="$v['data']['topics']" id="topic"}
                                    <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                                    {/volist}
                                </div>
                                {/if}
                            </dt>
                            <dd class="title">
                                <p class="bold">
                                    {if $v['item_id']}
                                    <a href="{:url('question/detail',['id'=>$v['item_id']])}" class="aw-one-line font-weight-bold">{$v.data.title}</a>
                                    {else/}
                                    <a href="javascript:;" class="aw-one-line font-weight-bold">{$v.data.title}</a>
                                    {/if}
                                </p>
                            </dd>
                            <dd class="my-2 aw-two-line text-muted font-9">
                                {$v['data']['detail']|raw}
                            </dd>

                            <div class="aw-draft-action font-9 text-muted position-absolute" style="bottom: 1rem;right: 0">
                                <a href="javascript:;" class="text-color-info mr-2 aw-ajax-get" data-url="{:url('draft/delete',['type'=>'question','item_id'=>$v['item_id']])}" data-confirm="{:L('是否确认删除草稿')}?">{:L('删除草稿')}</a>
                                <a href="{:url('question/publish',['id'=>$v['item_id']])}" class="text-color-info">{:L('编辑')}</a>
                            </div>
                        </dl>
                        {/case}

                        {case value="article" }
                        <dl class="article position-relative">
                            <dd class="col-sm-12 m-0 px-0" {if !$v['data']['cover']}style="width:100%"{/if}>
                            <h2>
                                {if $v['item_id']}
                                <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['data']['title']}</a>
                                {else/}
                                <a href="javascript:;">{$v['data']['title']}</a>
                                {/if}
                            </h2>
                            <div class="aw-content aw-two-line ncon">
                                {$v.data.message|raw}
                            </div>
                            {if isset($v['data']['topics']) && !empty($v['data']['topics'])}
                            <div class="aw-tag">
                                {volist name="$v['data']['topics']" id="topic"}
                                <a href="{:url('topic/detail',['id'=>$topic['id']])}" target="_blank">{$topic.title}</a>
                                {/volist}
                            </div>
                            {/if}
                            <div class="aw-common-footer n-footer mt-2">
                                <a href="{$user_info['url']}" class="aw-user-name avatar">
                                    <img src="{$user_info['avatar']}" alt="" class="rounded" style="width: 12px;height: 12px">
                                </a>
                                <a href="{$user_info['url']}" class="aw-user-name font-9" style="font-size:12px;">{$user_info['name']}</a>
                                <span style="font-size:12px;"> | {:date_friendly($v['create_time'])}</span>
                                <div class="aw-draft-action d-inline-block ml-3">
                                    <a style="font-size:12px;" href="javascript:;" class="text-color-info mr-2 aw-ajax-get" data-url="{:url('draft/delete',['type'=>'article','item_id'=>$v['item_id']])}" data-confirm="{:L('是否确认删除草稿')"><i class="icon-delete"></i>{:L('删除草稿')}</a>
                                    <a style="font-size:12px;" href="{:url('article/publish',['id'=>$v['item_id']])}" class="text-color-info"><i class="icon-edit"></i>{:L('编辑')}</a>
                                </div>
                            </div>
                            </dd>
                            <div class="clear"></div>
                        </dl>
                        {/case}

                        {case value="answer" }
                        <dl class="question position-relative">
                            <dt class="mb-2">
                                <a href="{$user_info['url']}" class="aw-user-name">
                                    <img src="{$user_info['avatar']}" alt="{$user_info['name']}">{$user_info['name']}
                                </a>
                                <span>{:L('回答了问题')}</span>
                                <label class="float-right">{:date_friendly($v['create_time'])}</label>
                            </dt>
                            <dd class="title">
                                <p class="bold">
                                    <a href="{:url('question/detail',['id'=>$v['data']['question_id']])}" class="aw-one-line font-weight-bold">{$v.title}</a>
                                </p>
                            </dd>
                            <dd class="my-2 aw-two-line text-muted font-9">
                                {$v['data']['content']|raw}
                            </dd>

                            <div class="aw-draft-action font-9 text-muted position-absolute" style="bottom: 1rem;right: 0">
                                <a href="javascript:;" class="text-color-info mr-2 aw-ajax-get" data-url="{:url('draft/delete',['type'=>'answer','item_id'=>$v['item_id']])}" data-confirm="{:L('是否确认删除草稿')}?">{:L('删除草稿')}</a>
                                <a href="{:url('question/detail',['id'=>$v['data']['question_id']])}" class="text-color-info">{:L('编辑')}</a>
                            </div>
                        </dl>
                        {/case}

                        {default /}
                        {/switch}
                        {/volist}
                    </div>
                    {$page|raw}
                    {else/}
                    <p class="text-center pt-4 text-muted">
                        <img src="{$cdnUrl}/static/common/image/empty.svg">
                        <span class="py-3 d-block  ">{:L('暂无内容')}</span>
                    </p>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
