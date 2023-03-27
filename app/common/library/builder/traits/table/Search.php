<?php
namespace app\common\library\builder\traits\table;

use app\common\library\builder\TableBuilder;

trait Search
{
    /**
     * 添加文本搜索项
     * @param string $name 字段名称
     * @param string $title 字段标题
     * @param string $default 默认值
     */
    public function addTextSearch(string $name = '', string $title = '', string $default = ''): TableBuilder
    {
        $column = [
            'name'     => $name,
            'title'    => L($title),
            'type'     => 'text',
            'option'    => [],
            'default'  => $default,
            'param'    => [],
        ];
        $this->_vars['search'][] = $column;
        return $this;
    }
}