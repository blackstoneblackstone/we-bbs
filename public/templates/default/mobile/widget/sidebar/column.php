{if !empty($list)}
<div class="aui-card mb-1">
    <div class="aui-card-main" style="padding: 0;">
        <div class="aui-lists">
            <div class="aui-list">
                <a href="{:url('column/index')}" data-pjax="pageMain">
                    <div class="aui-list-left font-weight-bold">{:L('热门专栏')}</div>
                    <div class="aui-list-right">
                        <span class="font-8 text-muted">{:L('去专栏广场')}</span>
                        <i class="iconfont aui-btn-right font-8 iconright1"></i>
                    </div>
                </a>
            </div>

            <div class="topicList swiper-container py-3 mx-3">
                <ul class="swiper-wrapper">
                    {volist name="list" id="v"}
                    <li class="swiper-slide text-center mr-3" style="max-width: 80px;">
                        <a href="{:url('column/detail',['id'=>$v['id']])}" class="aw-topic d-block mb-1" data-id="{$v.id}">
                            <img src="{$v['cover']|default='/static/common/image/default-cover.svg'}" onerror="this.src='/static/common/image/default-cover.svg'" style="border-radius: 50%" width="60" height="60">
                        </a>
                        <a href="{:url('column/detail',['id'=>$v['id']])}" class="d-block aw-two-line">{$v.name}</a>
                    </li>
                    {/volist}
                </ul>
            </div>

        </div>
    </div>
</div>
{/if}
