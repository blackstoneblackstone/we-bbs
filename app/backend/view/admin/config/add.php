{extend name="block" /}
{block name="main"}
<!--内容开始-->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!--数据表开始-->
            <form class="col-12" method="post" action="">
                <input type="hidden" name="id" value=" {$info ? $info.id : 0}"/>
                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段分组</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <select id="group" name="group" class="form-control">
                            {volist name="$config_group" id="v" key="k"}
                            <option value="{$k}" {if $info}{$info.group == $k ? 'selected="selected"' : ''}{/if}>{$v}</option>
                            {/volist}
                        </select>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段类型</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <select id="type" name="type" class="form-control">
                            <option value="text" {if $info}{$info.type == 'text' ? 'selected="selected"' : ''}{/if}>单行文本</option>
                            <option value="icon" {if $info}{$info.type == 'icon' ? 'selected="selected"' : ''}{/if}>选择图标</option>
                            <option value="textarea" {if $info}{$info.type == 'textarea' ? 'selected="selected"' : ''}{/if}>多行文本</option>
                            <option value="radio" {if $info}{$info.type == 'radio' ? 'selected="selected"' : ''}{/if}>单选按钮</option>
                            <option value="checkbox" {if $info}{$info.type == 'checkbox' ? 'selected="selected"' : ''}{/if}>多选按钮</option>
                            <option value="date" {if $info}{$info.type == 'date' ? 'selected="selected"' : ''}{/if}>日期</option>
                            <option value="time" {if $info}{$info.type == 'time' ? 'selected="selected"' : ''}{/if}>时间</option>
                            <option value="datetime" {if $info}{$info.type == 'datetime' ? 'selected="selected"' : ''}{/if}>日期时间</option>
                            <option value="daterange" {if $info}{$info.type == 'daterange' ? 'selected="selected"' : ''}{/if}>日期范围</option>
                            <option value="tag" {if $info}{$info.type == 'tag' ? 'selected="selected"' : ''}{/if}>标签</option>
                            <option value="number" {if $info}{$info.type == 'number' ? 'selected="selected"' : ''}{/if}>数字</option>
                            <option value="password" {if $info}{$info.type == 'password' ? 'selected="selected"' : ''}{/if}>密码</option>
                            <option value="select" {if $info}{$info.type == 'select' ? 'selected="selected"' : ''}{/if}>普通下拉菜单</option>
                            <option value="select2" {if $info}{$info.type == 'select2' ? 'selected="selected"' : ''}{/if}>高级下拉菜单</option>
                            <option value="image" {if $info}{$info.type == 'image' ? 'selected="selected"' : ''}{/if}>单图上传</option>
                            <option value="images" {if $info}{$info.type == 'images' ? 'selected="selected"' : ''}{/if}>多图上传</option>
                            <option value="file" {if $info}{$info.type == 'file' ? 'selected="selected"' : ''}{/if}>单文件上传</option>
                            <option value="files" {if $info}{$info.type == 'files' ? 'selected="selected"' : ''}{/if}>多文件上传</option>
                            <option value="editor" {if $info}{$info.type == 'editor' ? 'selected="selected"' : ''}{/if}>编辑器</option>
                            <option value="hidden" {if $info}{$info.type == 'hidden' ? 'selected="selected"' : ''}{/if}>隐藏域</option>
                            <option value="color" {if $info}{$info.type == 'color' ? 'selected="selected"' : ''}{/if}>取色器</option>
                            <option value="code" {if $info}{$info.type == 'code' ? 'selected="selected"' : ''}{/if}>代码编辑器</option>
                            <option value="html" {if $info}{$info.type == 'html' ? 'selected="selected"' : ''}{/if}>自定义HTML</option>
                        </select>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段标识</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="name" class="form-control" placeholder="字段标识如:'register_type'" value="{$info.name?$info.name:''}">
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段名称</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="title" class="form-control" placeholder="字段名称如：'注册类型'" value="{$info.title?$info.title:''}">
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">默认值</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <textarea name="value" class="form-control" placeholder="字段默认值,当选择html自定义时在此填写HTML">{$info.value?$info.value:''}</textarea>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">提示信息</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="tips" class="form-control" placeholder="字段提示信息" value="{$info.tips?$info.tips:''}">
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段设置</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left" id="field_setup"></div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">排序</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="sort" class="form-control" placeholder="请输入排序" value="{$info.sort ? $info.sort : '0'}">
                    </div>
                </div>

                <div class="row dd_input_group">
                    <div class="form-group">
                        <div class="col-12 col-md-6 col-lg-5 text-center">
                            <button type="button" class="btn btn-flat btn-primary aw-ajax-form">提 交</button>
                        </div>
                    </div>
                </div>
            </form>
            <!--数据表结束-->
        </div>
    </div>
</section>
<!--内容结束-->

<script>
    $(function(){
        // 字段变更时触发
        $("#type").change(function(){
            var type = $(this).val();
            var url = "{:url('admin.Config/state')}?type=" + type;
            field_setting(type, url);
        });
        // 编辑字段时触发
        {if $info}
        var type  = '{$info.type}';
        var id = '{$info.id}';
        var url   = "{:url('admin.Config/state')}?type=" + type + "&id=" + id;
        field_setting(type, url);
        {/if}
        })
        //选择后变更
        function field_setting(type, url, data) {
            $.ajax({
                type : "GET",
                url  : url,
                beforeSend: function () {
                    $('#field_setup').html('<i class="fa fa-spinner fa-spin fa-fw"></i>');
                },
                success: function (msg) {
                    $('#field_setup').html(msg);
                }
            });
        }
</script>
{/block}
