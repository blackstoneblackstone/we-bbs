{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>input('uid')])}
            <div class="col-md-10" id="wrapMain">
                <div class="bg-white">
                    <div class="aw-nav-container">
                        <ul class="aw-pjax-tabs nav nav-tabs nav-tabs-block px-3">
                            <li class="nav-item"><a class="nav-link {$type=='log'?'active':''}" data-pjax="tabMain" href="{:url('integral/index',['type'=>'log'])}">{:L('积分记录')}</a></li>
                            <!--<li class="{$type=='exchange'?'active':''}"><a data-pjax="tabMain" href="{:url('score/index',['type'=>'exchange'])}">积分兑换</a></li>-->
                        </ul>
                    </div>
                    <div id="tabMain" class="p-3">
                        {if $list}
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>{:L('积分描述')}</th>
                                <th>{:L('积分变化')}</th>
                                <th>{:L('当前余额')}</th>
                                <th>{:L('变动时间')}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="v"}
                            <tr>
                                <td class="text-muted">{$v.remark}</td>
                                <td>{$v.integral}</td>
                                <td>{$v.balance}</td>
                                <td>{:date('Y-m-d H:i:s',$v['create_time'])}</td>
                            </tr>
                            {/volist}
                            </tbody>
                        </table>
                        {$page|raw}
                        {else/}
                        <p class="text-center mt-4 text-meta">
                            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无记录')}">
                            <span class="mt-3 d-block ">{:L('暂无积分记录')}</span>
                        </p>
                        {/if}
                    </div>
                </div>
			</div>
		</div>
	</div>
</div>
{/block}