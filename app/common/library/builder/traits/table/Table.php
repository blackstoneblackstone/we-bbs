<?php
namespace app\common\library\builder\traits\table;
use app\common\library\builder\TableBuilder;

trait Table
{
    /**
     * 添加一列
     * @param string $name 字段名称
     * @param string $title 字段别名
     * @param string $type 单元格类型
     * @param string $default 默认值
     * @param string $param 额外参数
     * @param string $class css类名
     * @param string $sortable 是否排序
     * @return TableBuilder
     */
    public function addColumn($name = '', $title = '', $type = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => $type,
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];
        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 一次性添加多列
     * @param array $columns 数据列
     * @return TableBuilder
     */
    public function addColumns(array $columns = []): TableBuilder
    {
        if (!empty($columns)) {
            foreach ($columns as $column) {
                call_user_func_array([$this, 'addColumn'], $column);
            }
        }
        return $this;
    }

    /**
     * 添加一个右侧按钮
     * @param string $type 按钮类型：edit/delete/default
     * @param array $attribute 按钮属性
     * @return $this
     */
    public function addRightButton($type = '', $attribute = []): TableBuilder
    {
        switch ($type) {
            // 编辑按钮
            case 'edit':
                // 默认属性
                $btn_attribute = [
                    'type'   => 'edit',
                    'title'  => '编辑',
                    'icon'   => 'fa fa-edit',
                    'class'  => 'btn btn-primary btn-sm',
                ];
                break;
            // 删除按钮(不可恢复)
            case 'delete':
                // 默认属性
                $btn_attribute = [
                    'type'  => 'delete',
                    'title' => '删除',
                    'icon'  => 'far fa-trash-alt',
                    'class' => 'btn btn-danger btn-sm confirm',
                ];
                break;
            // 自定义按钮
            default:
                // 默认属性
                $btn_attribute = [
                    'title' => '自定义按钮',
                    'icon'  => 'fa fa-smile-o',
                    'class' => 'btn btn-flat btn-default btn-sm',
                    'href'  => 'javascript:void(0);'
                ];
                break;
        }
        // 合并自定义属性
        if ($attribute && is_array($attribute)) {
            $btn_attribute = array_merge($btn_attribute, $attribute);
        }

        $this->_vars['right_buttons'][] = $btn_attribute;
        return $this;
    }

    /**
     * 添加多个右侧按钮
     * @param array|string $buttons 按钮类型
     * 例如：
     * $builder->addRightButtons('edit');
     * $builder->addRightButtons('edit,delete');
     * $builder->addRightButtons(['edit', 'delete']);
     * $builder->addRightButtons(['edit' => ['title' => '查看'], 'delete']);
     * @return $this
     */
    public function addRightButtons($buttons = []): TableBuilder
    {
        if (!empty($buttons)) {
            $buttons = is_array($buttons) ? $buttons : explode(',', $buttons);
            foreach ($buttons as $key => $value) {
                if (is_numeric($key)) {
                    $this->addRightButton($value);
                } else {
                    $this->addRightButton(trim($key), $value);
                }
            }
        }
        return $this;
    }

    /**
     * 添加一个顶部按钮
     * @param string $type      按钮类型：add/edit/delete/export
     * @param array  $attribute 按钮属性
     * @return $this
     */
    public function addTopButton($type = '', $attribute = []): TableBuilder
    {
        switch ($type) {
            // 新增按钮
            case 'add':
                // 默认属性
                $btn_attribute = [
                    'title'   => L('新增'),
                    'icon'    => 'fa fa-plus',
                    'class'   => 'btn btn-success',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.operate.add()',
                ];
                break;

            // 修改按钮
            case 'edit':
                // 默认属性
                $btn_attribute = [
                    'title'   => L('修改'),
                    'icon'    => 'fa fa-edit',
                    'class'   => 'btn btn-primary single disabled',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.operate.edit()',
                ];
                break;

            // 删除按钮
            case 'delete':
                // 默认属性
                $btn_attribute = [
                    'title'   => L('删除'),
                    'icon'    => 'fa fa-times',
                    'class'   => 'btn btn-danger multiple disabled',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.operate.removeAll()'
                ];
                break;

            // 导出按钮
            case 'export':
                // 默认属性
                $btn_attribute = [
                    'title'   => L('导出'),
                    'icon'    => 'fa fa-download',
                    'class'   => 'btn btn-warning',
                    'href'    => '',
                    'onclick' => 'AWS_ADMIN.table.export()'
                ];
                break;

            // 自定义按钮
            default:
                // 默认属性
                $btn_attribute = $attribute;
                break;
        }

        // 合并自定义属性
        if ($attribute && is_array($attribute)) {
            $btn_attribute = array_merge($btn_attribute, $attribute);
        }
        $this->_vars['top_buttons'][] = $btn_attribute;
        return $this;
    }

    /**
     * 一次性添加多个顶部按钮
     * @param mixed $buttons 按钮组
     *                              例如：
     *                              addTopButtons('add')
     *                              addTopButtons('add, edit, del')
     *                              addTopButtons(['add', 'del'])
     *                              addTopButtons(['add' => ['title' => '增加'], 'del'])
     * @return $this
     */
    public function addTopButtons($buttons = []): TableBuilder
    {
        if (!empty($buttons)) {
            $buttons = is_array($buttons) ? $buttons : explode(',', $buttons);
            foreach ($buttons as $key => $value) {
                if (is_numeric($key)) {
                    // key为数字则直接添加一个按钮
                    $this->addTopButton($value);
                } else {
                    // key不为数字则需设置属性，去除前后空格
                    $this->addTopButton(trim($key), $value);
                }
            }
        }
        return $this;
    }

    /**
     * 添加文本列
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addText($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'text',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加排序/自定义文本框
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param array $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addSort($name = '', $title = '', $default = '', $param = [], $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'sort',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];
        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 可编辑文本框
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addInput($name = '', $title = '', $default = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'input',
            'default'  => $default,
            'param'    => '',
            'class'    => $class,
            'sortable' => $sortable,
        ];
        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 可编辑文本框
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $class 默认类
     * @return $this
     */
    public function addColor($name = '', $title = '', $default = '', $class = ''): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'color',
            'default'  => $default,
            'param'    => '',
            'class'    => $class,
            'sortable' => 'false',
        ];
        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加图标列
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addIcon($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'icon',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加状态
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param array $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addStatus($name = '', $title = '', $default = '', $param = [], $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'status',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加链接
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $link 链接
     * @param string $target 打开方式
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addLink($name = '', $title = '', $link = '', $target = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'link',
            'default'  => $link,
            'param'    => $target,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加弹窗
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $link 链接
     * @param string $param 附属选项
     * @param string $class 默认类
     * @return $this
     */
    public function addDialog($name = '', $title = '', $link = '', $param = '', $class = ''): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'dialog',
            'default'  => $link,
            'param'    => $param,
            'class'    => $class,
            'sortable' => 'false',
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加图片
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addImage($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'image',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加选择
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addSelect($name = '', $title = '', $default = '', $param = [], $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'select',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加选择
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param array|string $default 默认值
     * @param array $param 默认参数[url=>'',]
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addSelect2($name = '', $title = '', $default = '', $param = [], $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'select2',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加单选
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param array $param 默认参数 [0=>'否',1=>'是']
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addRadio($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'radio',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加日期
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addDate($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'date',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加时间
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addTime($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'time',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];
        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加日期时间
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param string $param 默认参数
     * @param string $class 默认类
     * @param string $sortable 是否排序
     * @return $this
     */
    public function addDateTime($name = '', $title = '', $default = '', $param = '', $class = '', $sortable = 'false'): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'datetime',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => $sortable,
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加标签
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param array $param 默认参数
     * @param string $class 默认类
     * @return $this
     */
    public function addTag($name = '', $title = '', $default = '', $param = '', $class = ''): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'tag',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => 'false',
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }

    /**
     * 添加自定义label标签
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     * @param array $param 默认参数 [1=>['text'=>'测试','label'=>'info']]
     * @param string $class 默认类
     * @return $this
     */
    public function addLabel($name = '', $title = '', $default = '', $param = [], $class = ''): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'label',
            'default'  => $default,
            'param'    => $param,
            'class'    => $class,
            'sortable' => 'false',
        ];

        $this->_vars['columns'][] = $column;
        $this->_field_name[$name] = $title;
        return $this;
    }
}