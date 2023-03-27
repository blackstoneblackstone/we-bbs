{extend name="$theme_block" /}
{block name="main"}
<div class="{if !$_ajax_open}aw-wrap mt-2{/if}">
    <div class="{if !$_ajax_open}container{/if}">
        <div class="bg-white aw-score-rule p-3">
            <h1 class="text-center mb-3 font-15">{$setting.site_name}{:L($setting.score_unit)}{:L('规则')}</h1>
            {volist name="list" id="v"}
            <dl class="overflow-auto">
                <dt class="float-left">{$v.title}</dt>
                <dd class="float-right">
                    <span class="{$v.integral >0 ? 'text-success' : 'text-danger'}">{$v.integral} {:L($setting.score_unit)}</span>
                    <span class="ml-4 text-danger">
                        {if $v['cycle_type']=='day' && $v['cycle']}
                        {:L('每')} <span class="font-weight-bold badge badge-danger"> {$v['cycle']} </span> {:L('天可获得')} <span class="font-weight-bold badge badge-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                        {/if}

                        {if $v['cycle_type']=='week' && $v['cycle']}
                        {:L('每')} <span class="font-weight-bold badge badge-danger"> {$v['cycle']} </span> {:L('周可获得')} <span class="font-weight-bold badge badge-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                        {/if}

                        {if $v['cycle_type']=='month' && $v['cycle']}
                        {:L('每')} <span class="font-weight-bold badge badge-danger"> {$v['cycle']} </span> {:L('月可获得')} <span class="font-weight-bold badge badge-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                        {/if}

                        {if $v['cycle_type']=='hour' && $v['cycle']}
                        {:L('每')} <span class="font-weight-bold badge badge-danger"> {$v['cycle']} </span> {:L('小时可获得')} <span class="font-weight-bold badge badge-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                        {/if}

                        {if $v['cycle_type']=='minute' && $v['cycle']}
                        {:L('每')} <span class="font-weight-bold badge badge-danger"> {$v['cycle']} </span> {:L('分钟可获得')} <span class="font-weight-bold badge badge-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                        {/if}

                        {if $v['cycle_type']=='second' && $v['cycle']}
                        {:L('每')} <span class="font-weight-bold badge badge-danger"> {$v['cycle']} </span> {:L('秒可获得')} <span class="font-weight-bold badge badge-primary">{$v.max>0 ? $v.max : L('不限')}</span> {:L('次')}
                        {/if}

                        {if !$v['cycle']}
                        <span class="font-weight-bold">{:L('不限次数')}</span>
                        {/if}
                    </span>
                </dd>
            </dl>
            {/volist}
        </div>
    </div>
</div>
{/block}