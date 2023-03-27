{if $list}
{volist name="list" id="v"}
<div class="bg-white p-3 mb-1">
    <a href="{:url('member/favorite/detail',['id'=>$v['id']])}" class="font-weight-bold font-10">{$v.title}</a>
    <div class="mt-1 text-color-info overflow-hidden">
        <span>{:date_friendly($v['update_time'] ? : $v['create_time'])}{:L('更新')} · {$v.post_count} {:L('条内容')} · {$v.focus_count} {:L('关注')} </span>
        <a href="javascript:;" class="ml-3 aw-ajax-get text-color-info float-right" data-confirm="{:L('是否删除该收藏夹')}?" data-url="{:url('member/favorite/delete',['id'=>$v['id']])}"><i class="fa fa-trash-alt"></i></a>
    </div>
</div>
{/volist}
{/if}