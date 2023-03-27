{if !empty($people_list)}
<div class="aui-card mb-1">
    <div class="aui-card-main" style="padding: 0;">
        <div class="aui-lists">
            <div class="aui-list">
                <a href="{:url('people/lists')}" data-pjax="pageMain">
                    <div class="aui-list-left font-weight-bold">{:L('热门用户')}</div>
                    <div class="aui-list-right">
                        <span class="font-8 text-muted">{:L('发现大咖')}</span>
                        <i class="iconfont aui-btn-right font-8 iconright1"></i>
                    </div>
                </a>
            </div>
            <div class="topicList swiper-container py-3 mx-3">
                <ul class="swiper-wrapper">
                    {volist name="people_list" id="v"}
                    <li class="swiper-slide text-center mr-3" style="max-width: 80px;">
                        <a href="{$v.url}" class="aw-username d-block mb-1" data-id="{$v.uid}">
                            <img src="{$v['avatar']|default='/static/common/image/default-avatar.svg'}" class="rounded" width="60" height="60" onerror="this.src='/static/common/image/default-avatar.svg'">
                        </a>
                        <a href="{$v.url}" class="aw-username d-block aw-two-line" data-id="{$v.uid}">{$v.nick_name}</a>
                    </li>
                    {/volist}
                </ul>
            </div>
        </div>
    </div>
</div>
{/if}
