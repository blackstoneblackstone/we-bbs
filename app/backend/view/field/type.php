<style>
    .typeTable{width: 100%}
    .typeTable tr td {padding: 5px;}
    .typeTable tr td:first-child{text-align: right}
</style>
{switch name="type"}
{case value="text" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/></td>
    </tr>
</table>
{/case}
{case value="textarea"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td>
            <input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>占位符</td>
        <td>
            <input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/>
        </td>
    </tr>
</table>
{/case}
{case value="radio"}
<table class="typeTable" cellpadding="2" cellspacing="1" width="100%">
    <tr>
        <td>默认值</td>
        <td>
            <input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>字段选项</td>
        <td>
            <textarea type="text" placeholder="填写字段选项,填写格式如：键|值" class="form-control" name="settings[param]">{$fieldInfo.settings.param ?: ''}</textarea>
        </td>
    </tr>
</table>
{/case}
{case value="checkbox"}
<table class="typeTable" cellpadding="2" cellspacing="1" width="100%">
    <tr>
        <td>默认值</td>
        <td>
            <input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>字段选项</td>
        <td>
            <textarea type="text" placeholder="填写字段选项,填写格式如：键|值" class="form-control" name="settings[param]">{$fieldInfo.settings.param ?: ''}</textarea>
        </td>
    </tr>
</table>
{/case}
{case value="date"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td>
            <input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/>
        </td>
    </tr>
    <tr>
        <td>日期格式 <a href="https://www.php.net/manual/zh/function.date.php" target="_blank" title="点击查看php 日期格式"><i class="far fa-question-circle"></i></a></td>
        <td>
            <input type="text" class="form-control" name="settings[format]" value="{$fieldInfo.settings.format ?: 'Y-m-d'}"/>
        </td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td>
            <input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/>
        </td>
    </tr>
    <tr>
        <td>占位符</td>
        <td>
            <input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/>
        </td>
    </tr>
</table>
{/case}
{case value="time"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>时间格式 <a href="https://www.php.net/manual/zh/function.date.php" target="_blank" title="点击查看php 日期格式"><i class="far fa-question-circle"></i></a></td>
        <td>
            <input type="text" class="form-control" name="settings[format]" value="{$fieldInfo.settings.format ?: 'H:i:s'}"/>
        </td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/></td>
    </tr>
</table>
{/case}
{case value="datetime"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>日期时间格式</td>
        <td><input type="text" class="form-control" name="settings[format]" value="{$fieldInfo.settings.format ?: 'Y-m-d H:i:s'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/></td>
    </tr>
</table>
{/case}
{case value="daterange"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>日期格式</td>
        <td><input type="text" class="form-control" name="settings[format]" value="{$fieldInfo.settings.format ?: 'Y-m-d'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
</table>
{/case}
{case value="tag" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
</table>
{/case}
{case value="number"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>步进值</td>
        <td><input type="text" class="form-control" name="settings[step]" value="{$fieldInfo.settings.step ?: '1'}"/></td>
    </tr>
</table>
{/case}
{case value="password"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/></td>
    </tr>
</table>
{/case}
{case value="select" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>字段选项</td>
        <td>
            <textarea type="text" placeholder="填写字段选项,填写格式如：键|值" class="form-control" name="settings[param]">{$fieldInfo.settings.param ?: ''}</textarea>
        </td>
    </tr>
</table>
{/case}
{case value="select2" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ?: ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ?: ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ?: ''}"/></td>
    </tr>

    <tr>
        <td>异步地址</td>
        <td><input type="text" class="form-control" name="settings[url]" value="{$fieldInfo.settings.url ?: ''}"/></td>
    </tr>
    <tr>
        <td>是否多选</td>
        <td>
            <label class="dd_radio_lable">
                <input type="radio" name="multiple" value="1" class="dd_radio" {$fieldInfo.settings.multiple ?: ''} ?'checked' : ''}><span>多选</span>
            </label>
            <label class="dd_radio_lable">
                <input type="radio" name="multiple" value="0" class="dd_radio" {!$fieldInfo.settings.multiple ?'' : 'checked'}><span>单选</span>
            </label>
        </td>
    </tr>
</table>
{/case}
{case value="image"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ? $fieldInfo.settings.placeholder : ''}"/></td>
    </tr>
</table>
{/case}
{case value="images"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ? $fieldInfo.settings.placeholder : ''}"/></td>
    </tr>
</table>
{/case}
{case value="file"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ? $fieldInfo.settings.placeholder : ''}"/></td>
    </tr>
</table>
{/case}
{case value="files"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ? $fieldInfo.settings.placeholder : ''}"/></td>
    </tr>
</table>
{/case}
{case value="editor"}
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
</table>
{/case}
{case value="hidden" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? '0'}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
</table>
{/case}
{case value="color" }
<table class="typeTable" cellpadding="2" cellspacing="1">
    <tr>
        <td>默认值</td>
        <td><input type="text" class="form-control" name="settings[default]" value="{$fieldInfo.settings.default ?? ''}"/></td>
    </tr>
    <tr>
        <td>额外属性</td>
        <td><input type="text" class="form-control" name="settings[extra_attr]" value="{$fieldInfo.settings.extra_attr ? $fieldInfo.settings.extra_attr : ''}"/></td>
    </tr>
    <tr>
        <td>额外css类</td>
        <td><input type="text" class="form-control" name="settings[extra_class]" value="{$fieldInfo.settings.extra_class ? $fieldInfo.settings.extra_class : ''}"/></td>
    </tr>
    <tr>
        <td>占位符</td>
        <td><input type="text" class="form-control" name="settings[placeholder]" value="{$fieldInfo.settings.placeholder ? $fieldInfo.settings.placeholder : ''}"/></td>
    </tr>
</table>
{/case}

{default /}
{/switch}