{extend name="block" /}
{block name="main"}
<!--内容开始-->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!--数据表开始-->
            <form class="col-12 form_builder" method="post" action="{:url('change')}">
                <input type="hidden" name="table" value="{$table}"/>
                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">对应字段</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="field" readonly class="form-control"  value="{$field}">
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">表单类型</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <select id="type" name="form_type" class="form-control">
                            <option value=''>请选择表单类型</option>
                            {foreach $fieldTypes as $type=>$title}
                            <option value="{$type}" {if $info}{$info.form_type == $type ? 'selected="selected"' : ''}{/if}>{$title}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">表格类型</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <select name="form_type" class="form-control">
                            <option value=''>请选择表格类型</option>
                            {foreach $tableFieldType as $type=>$title}
                            <option value="{$type}" {if $info}{$info.table_type == $type ? 'selected="selected"' : ''}{/if}>{$title}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">提示信息</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="tips" class="form-control" placeholder="字段右侧提示信息" value="{$info.tips?$info.tips:''}">
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem"> 新增/修改页面字段右侧的提示信息</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">是否必填</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                            <label class="dd_radio_lable">
                                <input type="radio" name="required" value="1" class="dd_radio" {if $info}{$info.required==1?'checked':''}{/if}><span>是</span>
                            </label>
                            <label class="dd_radio_lable">
                                <input type="radio" name="required" value="0" class="dd_radio" {if $info}{$info.required==0?'checked':''}{else /}checked{/if}><span>否</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">列表展示</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_list" value="1" class="dd_radio" {$info.is_list ?'checked' : ''}><span>是</span>
                        </label>
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_list" value="0" class="dd_radio" {$info.is_list==0 ?'checked' : ''}><span>否</span>
                        </label>
                        </div>
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 是否在列表页显示</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">添加字段</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_add" value="1" class="dd_radio" {$info.is_add ?'checked' : ''}><span>是</span>
                        </label>
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_add" value="0" class="dd_radio" {$info.is_add==0 ?'checked' : ''}><span>否</span>
                        </label>
                        </div>
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 是否在添加页面显示</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">编辑字段</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_edit" value="1" class="dd_radio" {$info.is_edit ?'checked' : ''}><span>是</span>
                        </label>
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_edit" value="0" class="dd_radio" {$info.is_edit==0 ?'checked' : ''}><span>否</span>
                        </label>
                        </div>
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 是否在编辑页面显示</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">搜索字段</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_search" value="1" class="dd_radio" {$info.is_search ?'checked' : ''}><span>是</span>
                        </label>
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_search" value="0" class="dd_radio" {$info.is_search==0 ?'checked' : ''}><span>否</span>
                        </label>
                        </div>
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 是否作为搜索选项</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">排序字段</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_sort" value="1" class="dd_radio" {$info.is_sort ?'checked' : ''}><span>是</span>
                        </label>
                        <label class="dd_radio_lable">
                            <input type="radio" name="is_sort" value="0" class="dd_radio" {$info.is_sort==0 ?'checked' : ''}><span>否</span>
                        </label>
                        </div>
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 是否作为列表页排序字段</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">搜索类型</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <select name="search_type" class="form-control">
                            <option value=''>请选择搜索类型</option>
                            <option value="=" {if $info}{$info.form_type == '=' ? 'selected="selected"' : ''}{/if}>等于</option>
                            <option value="<>" {if $info}{$info.form_type == '<>' ? 'selected="selected"' : ''}{/if}>不等于</option>
                            <option value=">" {if $info}{$info.form_type == '>' ? 'selected="selected"' : ''}{/if}>大于</option>
                            <option value="<" {if $info}{$info.form_type == '>' ? 'selected="selected"' : ''}{/if}>小于</option>
                            <option value="LIKE" {if $info}{$info.form_type == 'LIKE' ? 'selected="selected"' : ''}{/if}>LIKE</option>
                        </select>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">关联表</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="relation_db" class="form-control" placeholder="请填写关联的表" value="{$info.param.relation_db?:''}">
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 填写完整的关联表，如 users</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">展示字段</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="relation_field" class="form-control" placeholder="请填写关联表对应的字段" value="{$info.param.relation_field?:''}">
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem">* 填写完整的关联表字段名称，如 type_name</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">字段设置</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left" id="field_setup"></div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">是否启用</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <div class="dd_radio_lable_left">
                            {if $info}
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="1" class="dd_radio" {$info.status ?'checked' : ''}><span>启用</span>
                            </label>
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="0" class="dd_radio" {$info.status ?'' : 'checked'}><span>禁用</span>
                            </label>
                            {else /}
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="1" class="dd_radio" checked><span>启用</span>
                            </label>
                            <label class="dd_radio_lable">
                                <input type="radio" name="status" value="0" class="dd_radio"><span>禁用</span>
                            </label>
                            {/if}
                        </div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <label class="col-4 col-md-2 col-lg-1 control-label dd_input_l">排序权重</label>
                    <div class="col-8 col-md-10 col-lg-10">
                        <input type="text" name="weight" class="form-control" placeholder="请输入排序" value="{$info.weight ? $info.weight : '50'}">
                        <div class="text-muted mt-1" style="display: block;font-size:0.9rem"> * 排序为从小到大排序</div>
                    </div>
                </div>

                <div class="row dd_input_group">
                    <div class="form-group">
                        <div class="col-12 col-md-6 col-lg-5 text-center">
                            <button type="submit" class="btn btn-flat btn-primary ">提 交</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(function(){
        // 字段变更时触发
        $("#type").change(function(){
            var type = $(this).val();
            var url = "{:url('extend.Curd/manager')}?field={$field}&table={$table}&type=" + type;
            field_setting(type, url);
        });
        // 编辑字段时触发
        {if $info}
        var type  = '{$info.form_type}';
        var field = '{$field}';
        var url   = "{:url('extend.Curd/manager')}?table={$table}&type=" + type + "&field=" + field;
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
