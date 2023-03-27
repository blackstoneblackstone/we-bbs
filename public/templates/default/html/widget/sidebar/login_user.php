{if $user_id}
<div class="aw-card aw-card-default aw-card-small aw-padding-small mb-3">
    <div class="sidebar-user pt-0">
        <dl class="aw-overflow-hidden">
            <dt class="aw-float-left mr-3">
                <a href="{$user_info.url}"><img src="{$user_info.avatar}" alt="{$user_info.name}" class="circle" style="width: 48px;height: 48px"></a>
            </dt>
            <dd class="aw-float-left">
                <a href="{$user_info.url}">{$user_info.name}</a>
                <p class="aw-text-meta">{$user_info.signature|default=L("还没有完善签名哦")}</p>
            </dd>
        </dl>
    </div>
    <ul class="sidebar-user-list">
        <li>
            <a href="{:url('index/explore',['uid'=>$user_info['uid'],'type'=>'question'])}">
                <p><i class="icon-help-with-circle"></i>{:L('我的提问')}</p>
                <em>{$user_info.question_count}</em>
            </a>
        </li>
        <li>
            <a href="{:url('index/explore',['uid'=>$user_info['uid'],'type'=>'article'])}">
                <p><i class="icon-assignment"></i>{:L('我的文章')}</p>
                <em>{$user_info.article_count}</em>
            </a>
        </li>
        <li>
            <a href="{:url('index/explore',['uid'=>$user_info['uid'],'type'=>'answer'])}">
                <p><i class="icon-insert_comment"></i>{:L('我的回答')}</p>
                <em>{$user_info.answer_count}</em>
            </a>
        </li>
    </ul>
</div>
{else/}
<div class="aw-card aw-card-default aw-card-small aw-card-body mb-3 rounded" data-aw-sticky="offset:70 ; media : @m">
    <h3 class="mb-3">{:L('账号登录')}</h3>
    <p>{:get_setting('site_description')}</p>
    <a href="{:url('account/login')}" class="button primary aw-display-block mt-3">{:L('登录')}</a>
    <a class="mt-3 button default aw-display-block" href="{:url('account/register')}">{:L('注册')}</a>
</div>
{/if}