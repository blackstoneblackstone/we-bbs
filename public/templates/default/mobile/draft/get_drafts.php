{if !empty($list)}
{volist name="list" id="v"}
{switch name="$v['item_type']"}
{case value="question" }
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        <a href="{$user_info['url']}" class="aw-user-name">
                            <img src="{$user_info['avatar']}" alt="{$user_info['name']}" class="aw-user-img circle" width="40" height="40">
                        </a>
                    </div>
                    <div class="float-left ml-2">
                        <a href="{$user_info['url']}" class="aw-user-name">
                            {$user_info['name']}
                        </a>
                        <span class="d-block text-muted font-9">{:L('发起了问题')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['create_time'])}</em>
                    </div>
                </div>

                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        {if $v['item_id']}
                        <a href="{:url('question/detail',['id'=>$v['item_id']])}" class="aw-one-line font-weight-bold">{$v.data.title}</a>
                        {else/}
                        <a href="javascript:;" class="aw-one-line font-weight-bold">{$v.data.title}</a>
                        {/if}
                    </div>
                </div>
                <div class="desc">
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v['data']['detail']|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                <div class="aui-btn">
                    <a href="javascript:;" class="text-color-info mr-2 aw-ajax-get"
                       data-url="{:url('draft/delete',['type'=>'question','item_id'=>$v['item_id']])}" data-confirm="{:L('是否确认删除草稿')}？">{:L('删除草稿')}</a>
                </div>
                <div class="aui-btn">
                    <a href="{:url('question/publish',['id'=>$v['item_id']])}" class="text-color-info">{:L('编辑')}</a>
                </div>
            </div>
        </div>
    </div>
</div>
{/case}

{case value="article" }
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        <a href="{$user_info['url']}" class="aw-user-name">
                            <img src="{$user_info['avatar']}" alt="{$user_info['name']}" class="aw-user-img circle" width="40" height="40">
                        </a>
                    </div>
                    <div class="float-left ml-2">
                        <a href="{$user_info['url']}" class="aw-user-name">
                            {$user_info['name']}
                        </a>
                        <span class="d-block text-muted font-9">{:L('发起了问题')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['create_time'])}</em>
                    </div>
                </div>

                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        {if $v['item_id']}
                        <a href="{:url('article/detail',['id'=>$v['id']])}">{$v['data']['title']}</a>
                        {else/}
                        <a href="javascript:;">{$v['data']['title']}</a>
                        {/if}
                    </div>
                </div>
                <div class="desc">
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v.data.message|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                <div class="aui-btn">
                    <a href="javascript:;" class="text-color-info mr-2 aw-ajax-get" data-url="{:url('draft/delete',['type'=>'article','item_id'=>$v['item_id']])}" data-confirm="{:L('是否确认删除草稿')}？">{:L('删除草稿')}</a>
                </div>
                <div class="aui-btn">
                    <a href="{:url('article/publish',['id'=>$v['item_id']])}" class="text-color-info">{:L('编辑')}</a>
                </div>
            </div>
        </div>
    </div>
</div>
{/case}

{case value="answer" }
<div class="aui-card" style="background: none">
    <div class="aui-card-main pb-1 px-0 pt-0">
        <div class="aui-card aui-card-image">
            <div class="aui-card-main">
                <div class="aui-card-title pt-3 bg-white px-2 clearfix position-relative pb-2">
                    <div class="float-left">
                        <a href="{$user_info['url']}" class="aw-user-name">
                            <img src="{$user_info['avatar']}" alt="{$user_info['name']}" class="aw-user-img circle" width="40" height="40">
                        </a>
                    </div>
                    <div class="float-left ml-2">
                        <a href="{$user_info['url']}" class="aw-user-name">
                            {$user_info['name']}
                        </a>
                        <span class="d-block text-muted font-9">{:L('回复了问题')}</span>
                        <em class="time position-absolute" style="right: 0.5rem;top: 1rem;">{:date_friendly($v['create_time'])}</em>
                    </div>
                </div>

                <div class="img" style="min-height: 24px;height: auto">
                    <div class="img-mask font-weight-bold aw-one-line" style="background: none;height: auto;line-height: unset">
                        <a href="{:url('question/detail',['id'=>$v['data']['question_id']])}" class="aw-one-line">{$v.title}</a>
                    </div>
                </div>
                <div class="desc">
                    <div class="aw-content aw-two-line text-muted font-9">
                        {$v['data']['content']|raw}
                    </div>
                </div>
            </div>
            <div class="aui-card-down row-before">
                <div class="aui-btn">
                    <a href="javascript:;" class="text-color-info mr-2 aw-ajax-get" data-url="{:url('draft/delete',['type'=>'answer','item_id'=>$v['item_id']])}" data-confirm="{:L('是否确认删除草稿')}？">{:L('删除草稿')}</a>
                </div>
                <div class="aui-btn">
                    <a href="{:url('question/detail',['id'=>$v['data']['question_id']])}" class="text-color-info">{:L('编辑')}</a>
                </div>
            </div>
        </div>
    </div>
</div>
{/case}

{default /}
{/switch}
{/volist}
{/if}