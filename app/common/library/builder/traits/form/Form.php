<?php
namespace app\common\library\builder\traits\form;

use app\common\library\builder\TableBuilder;

trait Form
{
    /**
     * 添加单行文本框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addText($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'text',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => '',
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多行文本框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @param int    $rows        高度（以行数计）
     * @return $this|array
     */
    public function addTextarea($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false, $rows = 3)
    {
        $item = [
            'type'        => 'textarea',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
            'rows'        => $rows,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单选
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     单选数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addRadio($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'radio',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];
        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加复选框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addCheckbox($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'checkbox',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加树形复选框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addCheckbox2($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'checkbox2',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加字典
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     复选框数据
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addArray($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'array',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options == '' ? [] : $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDate($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'yyyy-MM-dd',
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($item['value'] == 'now') {
            $item['value'] = date($item['format']);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加时间
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      时间格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addTime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'time',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'HH:mm',
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($item['value'] == 'now') {
            $item['value'] = date($item['format']);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期时间
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDatetime($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'date',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'yyyy-MM-dd HH:mm',
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($item['value'] == 'now') {
            $item['value'] = date($item['format']);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加日期范围
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $format      日期格式
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addDaterange($name = '', $title = '', $tips = '', $default = '', $format = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'daterange',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default ?: '',
            'format'      => $format ?: 'yyyy-MM-dd',
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => !empty($placeholder) ? $placeholder : '请选择或输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加标签
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addTag($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'tags',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => is_array($default) ? implode(',', $default) : $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加图标
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addIcon($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'icon',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => is_array($default) ? implode(',', $default) : $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加数字输入框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $min         最小值
     * @param string $max         最大值
     * @param string $step        步进值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addNumber($name = '', $title = '', $tips = '', $default = '', $min = '', $max = '', $step = '', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'number',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default == '' ? 0 : $default,
            'min'         => $min,
            'max'         => $max,
            'step'        => $step,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加密码框
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addPassword($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'password',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加普通下拉菜单
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     选项
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param string $placeholder 占位符
     * @param bool   $required    是否必选
     * @return $this|array
     */
    public function addSelect($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'select',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请选择',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加下拉菜单select2
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param array  $options     选项
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addSelect2($name = '', $title = '', $tips = '', $options = [], $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false, $url = '',$multiple=0)
    {
        $item = [
            'type'        => 'select2',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'options'     => $options,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请选择',
            'required'    => $required,
            'url'    => $url,
            'multiple'=>$multiple
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单图片上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addImage($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false,$path='common',$ext='',$size=0)
    {
        $item = [
            'type'        => 'image',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
            'path'=>$path, //上传文件夹目录
            'ext'=>$ext?:get_setting('upload_image_ext'),//上传格式
            'size'=>$size?:get_setting('upload_image_size')//上传大小
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多图片上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addImages($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false,$path='common',$ext='',$size=0)
    {
        $item = [
            'type'        => 'images',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
            'path'=>$path,
            'ext'=>$ext?:get_setting('upload_image_ext'),//上传格式
            'size'=>$size?:get_setting('upload_image_size')//上传大小
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加单文件上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addFile($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false,$path='common',$ext='',$size=0)
    {
        $item = [
            'type'        => 'file',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
            'path'=>$path,
            'ext'=>$ext?:get_setting('upload_file_ext'),//上传格式
            'size'=>$size?:get_setting('upload_file_size')//上传大小
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加多文件上传
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addFiles($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false,$path='common',$ext='',$size=0)
    {
        $item = [
            'type'        => 'files',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请点击按钮上传或手动输入地址',
            'required'    => $required,
            'path'=>$path,
            'ext'=>$ext?:get_setting('upload_file_ext'),//上传格式
            'size'=>$size?:get_setting('upload_file_size')//上传大小
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加编辑器
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $types      附加配置
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addEditor($name = '', $title = '', $tips = '', $default = '', $types ='common', $extra_attr = '', $extra_class = '', $required = false)
    {
        $item = [
            'type'        => 'editor',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'types'      => $types,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加隐藏表单项
     * @param string $name        字段名称
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @return $this|array
     */
    public function addHidden($name = '', $default = '', $extra_attr = '', $extra_class = '')
    {
        $item = [
            'type'        => 'hidden',
            'name'        => $name,
            'value'       => $default,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加取色器
     * @param string $name        字段名称
     * @param string $title       字段别名
     * @param string $tips        提示信息
     * @param string $default     默认值
     * @param string $extra_attr  额外属性
     * @param string $extra_class 额外css类名
     * @param string $placeholder 占位符
     * @param bool   $required    是否必填
     * @return $this|array
     */
    public function addColor($name = '', $title = '', $tips = '', $default = '', $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $item = [
            'type'        => 'color',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'placeholder' => $placeholder ?: '请选择或输入颜色',
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加代码编辑器
     * @param string $name 字段名称
     * @param string $title 字段别名
     * @param string $tips 提示信息
     * @param string $default 默认值
     * @param string $height 高度
     * @param string $extra_attr 额外属性
     * @param string $extra_class 额外css类名
     * @param bool $required 是否必填
     * @param string $mode 编程语言（htmlmixed/javascript/css）
     * @param string $theme 主题
     * @return $this|array
     */
    public function addCode($name = '', $title = '', $tips = '', $default = '', $height = '', $extra_attr = '', $extra_class = '', $required = false, $mode = 'htmlmixed', $theme = 'monokai')
    {
        if ($mode == 'html') {
            $mode = 'htmlmixed';
        } else if ($mode == 'js') {
            $mode = 'javascript';
        }
        $item = [
            'type'        => 'code',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'height'      => $height ?: '500',
            'extra_class' => $extra_class,
            'extra_attr'  => $extra_attr,
            'required'    => $required,
            'mode'        => $mode,
            'theme'       => $theme,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 添加自定义Html
     * @param string $name
     * @param string $title
     * @param string $tips
     * @param string $default
     * @param array $group
     * @param string $extra_attr
     * @param string $extra_class
     * @param string $placeholder
     * @param bool $required
     * @return $this|array
     */
    public function addHtml($name = '', $title = '', $tips = '', $default = '', $group = [], $extra_attr = '', $extra_class = '', $placeholder = '', $required = false)
    {
        $default = htmlspecialchars_decode($default);
        preg_match_all('/(?<=href=")[\w\d\.:\/]*/', $default, $href_links, PREG_SET_ORDER);
        preg_match_all('/(?<=data-url=")[\w\d\.:\/]*/',$default, $url_links, PREG_SET_ORDER);

        if($href_links)
        {
            foreach ($href_links as $k=>$v)
            {
                $url = $v[0];
                if(!strstr($url,'http') && !strstr($url,'https') && $url!='javascript:;')
                {
                    $default = str_replace($url,(string)url($url),$default);
                }
            }
        }

        if($url_links)
        {
            foreach ($url_links as $k=>$v)
            {
                $url = $v[0];
                if(!strstr($url,'http') && !strstr($url,'https'))
                {
                    $default = str_replace($url,(string)url($url),$default);
                }
            }
        }

        $item = [
            'type'        => 'html',
            'name'        => $name,
            'title'       => $title,
            'tips'        => $tips,
            'value'       => $default,
            'group'       => $group,
            'extra_attr'  => $extra_attr,
            'extra_class' => $extra_class,
            'placeholder' => $placeholder ?: '请输入' . $title,
            'required'    => $required,
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    public function addAjaxHtml($name = '', $tips = '',$url = '', $trigger_field = '',$value='')
    {
        $item = [
            'type'        => 'ajax_html',
            'name'        => $name,
            'title'       => '',
            'tips'        => $tips,
            'options'     => [],
            'value'       => $value,
            'extra_attr'  => '',
            'extra_class' => '',
            'placeholder' => '',
            'required'    => false,
            'url'    => $url,
            'trigger'=>$trigger_field
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }
}