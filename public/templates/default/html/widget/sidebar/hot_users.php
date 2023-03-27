{if !empty($people_list)}
<div class="r-box mb-2">
    <div class="r-title">
        <h4>{:L('热门用户')}</h4>
        <a href="{:url('people/lists')}" target="_blank"><label class="iconfont">&#xe660;</label></a>
    </div>
    <div class="hot-list hot-yh-list">
        {volist name="people_list" id="v"}
        <dl class="row">
            <dt>
                <a href="{$v.url}" class="aw-username" data-id="{$v.uid}">
                    <img src="{$v['avatar']|default='/static/common/image/default-avatar.svg'}" onerror="this.src='/static/common/image/default-avatar.svg'">
                </a>
            </dt>
            <dd  class="">
                <h3><a href="{$v.url}" class="aw-username aw-one-line" data-id="{$v.uid}">{$v.nick_name}</a></h3>
                <label>{:L('提问')}:<i>{$v.question_count}</i></label>
                <label>{:L('获赞')}:<i>{$v.agree_count}</i></label>
            </dd>
        </dl>
        {/volist}
    </div>
</div>
{/if}
