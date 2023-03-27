{extend name="$theme_block" /}
{block name="header"}
<header class="aui-header">
    <div class="aui-header-left"> <a href="{:url('creator/index')}" data-pjax="pageMain"><i class="fa fa-angle-left "></i></a></div>
    <div class="aui-header-title">{:L('积分规则')}</div>
</header>
{/block}
{block name="main"}
<div class="main-container">
    <div class="bg-white aw-score-rule p-3">

        {volist name="list" id="v"}
        <div class="aui-list">
            <div class="aui-list-left aw-one-line" style="line-height: unset;max-width: calc(100% - 4rem)">
                <span class="d-block mb-1 aw-one-line">{$v.title}</span>
                <div class="d-block font-8">
                    {if $v['cycle_type']=='day' && $v['cycle']}
                    {:L('每')} <span class="font-weight-bold text-danger"> {$v['cycle']} </span> {:L('天可获得')} <span class="font-weight-bold text-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                    {/if}

                    {if $v['cycle_type']=='week' && $v['cycle']}
                    {:L('每')} <span class="font-weight-bold text-danger"> {$v['cycle']} </span> {:L('周可获得')} <span class="font-weight-bold text-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                    {/if}

                    {if $v['cycle_type']=='month月' && $v['cycle']}
                    {:L('每')} <span class="font-weight-bold text-danger"> {$v['cycle']} </span> {:L('月可获得')} <span class="font-weight-bold text-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                    {/if}

                    {if $v['cycle_type']=='hour' && $v['cycle']}
                    {:L('每')} <span class="font-weight-bold text-danger"> {$v['cycle']} </span> {:L('小时可获得')} <span class="font-weight-bold text-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                    {/if}

                    {if $v['cycle_type']=='minute' && $v['cycle']}
                    {:L('每')} <span class="font-weight-bold text-danger"> {$v['cycle']} </span> {:L('分钟可获得')} <span class="font-weight-bold text-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                    {/if}

                    {if $v['cycle_type']=='second' && $v['cycle']}
                    {:L('每')} <span class="font-weight-bold text-danger"> {$v['cycle']} </span> {:L('秒可获得')} <span class="font-weight-bold text-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                    {/if}

                    {if !$v['cycle']}
                    <span class="font-weight-bold">{:L('不限次数')}</span>
                    {/if}
                </div>
            </div>
            <div class="aui-list-right text-danger font-12">
                {$v.integral} {$setting.score_unit}
            </div>
        </div>
        {/volist}
    </div>
</div>
{/block}

{block name="footer"}{/block}