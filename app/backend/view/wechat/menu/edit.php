{extend name="block" /}
{block name="main"}
<div class="p-3 bg-white">
    <div class="mb-2">
        <form method="post">
            <input type="hidden" name="id" value="{$info.id|default=0}"/>
            <div class="form-group">
                <label class="control-label">公众号</label>
                <select class="form-control" id="wechat_account_id" name="wechat_account_id">
                    {volist name="account_list" id="v"}
                    <option value="{$key}" {if isset($info['wechat_account_id']) && $info.wechat_account_id == $key }selected{/if}>{$v}</option>
                    {/volist}
                </select>
            </div>

            <div class="form-group">
                <label class="control-label">父级菜单</label>
                <select class="form-control" id="pid" name="pid">
                    <option value="0">一级菜单</option>
                    {volist name="parent_list" id="v"}
                    <option value="{$v.id}" {if (isset($info['pid']) && $info.pid == $v.id) || $pid == $v.id}selected{/if}>{$v.l_cate_name}</option>
                    {/volist}
                </select>
            </div>

            <div class="form-group">
                <label class="control-label">菜单名称</label>
                <div class="">
                    <input class="form-control" type="text" name="menu_name" value="{$info['menu_name']|default=''}" placeholder="请输入菜单名称">
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">菜单类型</label>
                <div class="">
                    <label class="form-check-inline">
                        <input type="radio" name="menu_event_type" class="form-check-input" value="1" {if isset($info['menu_event_type']) && $info.menu_event_type==1}checked{/if}>
                        <label class="form-check-label font-weight-normal"> 普通URL</label>
                    </label>

                    <label class="form-check-inline">
                        <input type="radio" name="menu_event_type" class="form-check-input" value="2" {if isset($info['menu_event_type']) && $info.menu_event_type==2 }checked{/if}>
                        <label class="form-check-label font-weight-normal"> 图文素材</label>
                    </label>

                    <label class="form-check-inline">
                        <input type="radio" name="menu_event_type" class="form-check-input" value="3" {if isset($info['menu_event_type']) && $info.menu_event_type==3 }checked{/if}>
                        <label class="form-check-label font-weight-normal"> 功能</label>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-flat btn-primary aw-ajax-form">提 交</button>
            </div>
        </form>
    </div>
</div>
{/block}