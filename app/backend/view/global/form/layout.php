{extend name="block" /}
{block name="main"}
<div class="p-3 bg-white">
    {$extra_css|raw|default=''}
    <div class="container-fluid">
        {$extra_html_content_top|raw|default=''}
        {notempty name="page_tips_top"}
        <div class="alert alert-{$tips_type} alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p>{$page_tips_top|raw}</p>
        </div>
        {/notempty}

        <form class="form-horizontal" method="{$form_method}" action="{$form_url}" id="awFormBuilder">
            {if $form_items}
            {volist name="form_items" id="form"}
            {switch form.type}
            {case value="group"}
            {// 分组 }
            {include file="global/form/items/group" type='' /}
            {/case}
            {case value="link_group"}
            {// 链接分组 }
            {include file="global/form/items/link_group" type='' /}
            {/case}
            {case text}
            {// 单行文本框 }
            {include file="global/form/items/text" type='' /}
            {/case}
            {case icon}
            {// 图标 }
            {include file="global/form/items/icon" type='' /}
            {/case}
            {case value="textarea"}
            {// 多行文本框 }
            {include file="global/form/items/textarea" type='' /}
            {/case}
            {case value="radio"}
            {// 单选 }
            {include file="global/form/items/radio" type='' /}
            {/case}
            {case value="checkbox"}
            {// 多选 }
            {include file="global/form/items/checkbox" type='' /}
            {/case}
            {case value="checkbox2"}
            {// 树形多选 }
            {include file="global/form/items/checkbox2" type='' /}
            {/case}
            {case value="date"}
            {// 日期 }
            {include file="global/form/items/date" type='' /}
            {/case}
            {case value="time"}
            {// 时间 }
            {include file="global/form/items/time" type='' /}
            {/case}
            {case value="datetime"}
            {// 日期时间 }
            {include file="global/form/items/datetime" type='' /}
            {/case}
            {case value="daterange"}
            {// 日期范围 }
            {include file="global/form/items/daterange" type='' /}
            {/case}
            {case value="tags"}
            {// 标签 }
            {include file="global/form/items/tags" type='' /}
            {/case}
            {case value="number"}
            {// 数字 }
            {include file="global/form/items/number" type='' /}
            {/case}
            {case value="password"}
            {// 密码 }
            {include file="global/form/items/password" type='' /}
            {/case}
            {case value="select"}
            {// 下拉菜单 }
            {include file="global/form/items/select" type='' /}
            {/case}
            {case value="select2"}
            {// 下拉菜单2 }
            {include file="global/form/items/select2" type='' /}
            {/case}
            {case value="image"}
            {// 单图片 }
            {include file="global/form/items/image" type='' /}
            {/case}
            {case value="images"}
            {// 多图片 }
            {include file="global/form/items/images" type='' /}
            {/case}
            {case value="file"}
            {// 单文件 }
            {include file="global/form/items/file" type='' /}
            {/case}
            {case value="files"}
            {// 多文件 }
            {include file="global/form/items/files" type='' /}
            {/case}
            {case value="editor"}
            {// 编辑器 }
            {include file="global/form/items/editor" type='' /}
            {/case}
            {case value="button"}
            {// 按钮 }
            {include file="global/form/items/button" type='' /}
            {/case}
            {case value="hidden"}
            {// 隐藏域 }
            {include file="global/form/items/hidden" type='' /}
            {/case}
            {case value="html"}
            {// 自定义html }
            {include file="global/form/items/html" type='' /}
            {/case}
            {case value="color"}
            {// 取色器 }
            {include file="global/form/items/color" type='' /}
            {/case}
            {case value="code"}
            {// 代码编辑器 }
            {include file="global/form/items/code" type='' /}
            {/case}

            {case value="array"}
            {include file="global/form/items/array" type='' /}
            {/case}

            {case value="ajax_html"}
            {include file="global/form/items/ajax_html" type='' /}
            {/case}

            {default /}

            {/switch}
            {/volist}
            <div class="form-group">
                {php}if(isset($btn_hide) && !in_array('submit', $btn_hide)):{/php}
                <button type="{$form_method=='post'?'button':'submit'}" class="btn btn-flat btn-primary {$form_method=='post'?'aw-ajax-form':''}" {if $submit_confirm} data-confirm="{:L('是否确认提交该表单')}？"{/if}>{$btn_title['submit']|default=L('提交')}</button>
                {php}endif;{/php}
                {// 额外按钮}
                {foreach $btn_extra as $key=>$vo }
                {$vo|raw|default=''}
                {/foreach}
            </div>
            {else /}
            <div class="box box-body">
                {$empty_tips|raw}
            </div>
            {/if}
        </form>

        {notempty name="page_tips_bottom"}
        <div class="alert alert-{$tips_type} alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <p>{$page_tips_bottom|raw}</p>
        </div>
        {/notempty}
        {$extra_html_content_bottom|raw|default=''}
        {$extra_js|raw|default=''}
    </div>
</div>
{/block}