{extend name="$theme_block" /}
{block name="main"}
<div class="aw-wrap mt-2">
    <div class="container">
        <div class="row">
            {:widget('member/userNav',['uid'=>$user_id])}
            <div class="col-md-10" id="wrapMain">
                <div class="bg-white">
                    <div class="aw-nav-container">
                        <ul class="nav nav-tabs nav-tabs-block js-tabs-enabled aw-pjax-tabs">
                            <li class="nav-item"><a class="nav-link {if $type=='my'}active{/if}" data-pjax="tabMain" href="{:url('favorite/index',['type'=>'my'])}">{:L('我创建的')}</a></li>
                            <!--<li class="nav-item"><a class="nav-link {if $type=='focus'}active{/if}" data-pjax="tabMain" href="{:url('favorite/index',['type'=>'focus'])}">{:L('我关注的')}</a></li>-->
                        </ul>
                    </div>
                    <div id="tabMain" class="p-3">
                        {if $list}
                        {volist name="list" id="v"}
                        <div class="favorite-tag-item py-2 border-bottom">
                            <a href="{:url('favorite/detail',['id'=>$v['id']])}" class="font-weight-bold font-10">{$v.title}</a>
                            <div class="mt-1 text-color-info overflow-hidden">
                                <span>{:date_friendly($v['update_time'] ? : $v['create_time'])}{:L('更新')} · {:L('%s 条内容',$v.post_count)} · {:L('%s 人关注',$v.focus_count)}</span>
                                <a href="javascript:;" class="ml-3 aw-ajax-get text-color-info float-right" data-confirm="{:L('是否删除该收藏夹')}?" data-url="{:url('favorite/delete',['id'=>$v['id']])}"><i class="fa fa-trash-alt"></i></a>
                            </div>
                        </div>
                        {/volist}
                        {$page|raw}
                        {else/}
                        <p class="text-center mt-4 text-meta">
                            <img src="{$cdnUrl}/static/common/image/empty.svg" alt="{:L('暂无记录')}">
                            <span class="mt-3 d-block ">{:L('暂无记录')}</span>
                        </p>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}
