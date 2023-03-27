<?php

namespace app\common\library\builder;

use app\common\library\builder\traits\table\Search;
use app\common\library\builder\traits\table\Table;
use think\facade\Config;
use think\facade\Request;
use think\facade\View;

class TableBuilder
{
    use Table;
    use Search;
    /**
     * @var array 列名
     */
    private $_field_name = [];

    /**
     * @var string 模板路径(默认使用系统内置路径，无需设置)
     */
    private $_template = '';

    /**
     * @var array 模板变量
     */
    private $_vars = [
        'page_title'       => '',        // 页面标题
        'page_tips'        => '',        // 页面提示
        'page_tips_top'    => '',        // 页面提示[top]
        'page_tips_search' => '',        // 页面提示[search]
        'page_tips_bottom' => '',        // 页面提示[bottom]
        'page_size'        => '',        // 每页显示的行数
        'tips_type'        => '',        // 页面提示类型
        'extra_js'         => '',        // 额外JS代码
        'extra_css'        => '',        // 额外CSS代码
        'extra_html'       => '',        // 额外HTML代码
        'columns'          => [],        // 表格列集合
        'right_buttons'    => [],        // 表格右侧按钮
        'top_buttons'      => [],        // 顶部栏按钮组[toolbar]
        'unique_id'        => 'id',      // 表格主键名称，（默认为id，如表主键不为id必须设置主键）
        'data_url'         => '',        // 表格数据源
        'add_url'          => '',        // 默认的新增地址
        'edit_url'         => '',        // 默认的修改地址
        'del_url'          => '',        // 默认的删除地址
        'export_url'       => '',        // 默认的导出地址
        'sort_url'         => '',        // 默认的排序地址
        'choose_url'       =>'',         // 默认单选多选地址
        'state_url'       =>'',         // 默认状态地址
        'submit_url'      =>'',          //单列内容提交地址
        'search'           => [],        // 搜索参数
        'pagination'       => 'true',    // 是否进行分页
        'parent_id_field'  => '',        // 列表树模式需传递父id
        'empty_tips'       => '暂无数据', // 空数据提示信息[待完善]
        'hide_checkbox'    => true,     // 是否隐藏第一列多选[待完善]
    ];

    /**
     * @var
     */
    private static $instance;

    /**
     * 获取句柄
     */
    public static function getInstance($_template=''): TableBuilder
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($_template);
        }
        return self::$instance;
    }

    /**
     * 私有化构造函数
     */
    private function __construct($_template='')
    {
        // 初始化
        $this->initialize($_template);
    }

    /**
     * 初始化
     */
    protected function initialize($_template='')
    {
        // 每页显示的行数
        $this->_vars['page_size']   = get_setting("contents_per_page",15);
        // 设置默认模版

        $this->_template = $_template ?:'backend@global/table/layout';
        $plugin =request()->plugin;
        // 设置默认URL
        $this->_vars['data_url']   = Request::baseUrl().'?_list=1';
        $this->_vars['add_url']    = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/add' : (string)url('add');
        $this->_vars['edit_url']   = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/edit?id=__id__' : (string)url('edit', ['id' => '__id__']);
        $this->_vars['del_url']    = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/delete' : (string)url('delete');
        $this->_vars['export_url'] = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/export' : (string)url('export');
        $this->_vars['sort_url']   = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/sort' : (string)url('sort');
        $this->_vars['choose_url']   = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/choose' : (string)url('choose');
        $this->_vars['state_url']   = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/state' : (string)url('state');
        $this->_vars['submit_url']   = $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/submit' : (string)url('submit');

    }

    /**
     * 私有化clone函数
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 渲染模版
     * @param string $template 模板文件名或者内容
     * @return string
     */
    public function fetch(string $template = ''): string
    {
        // 单独设置模板
        if ($template != '') {
            $this->_template = $template;
        }
        View::assign($this->_vars);
        return View::fetch($this->_template);
    }

    /**
     * 设置表格主键
     * @param string $key 主键名称
     * @return $this
     */
    public function setUniqueId(string $key = ''): TableBuilder
    {
        if ($key != '') {
            $this->_vars['unique_id'] = $key;
        }
        return $this;
    }

    /**
     * 设置链接分组
     */
    public function setLinkGroup($group=[]): TableBuilder
    {
        if ($group) {
            $this->_vars['group'] = $group;
        }
        return $this;
    }

    /**
     * 设置二级分组
     */
    public function setSecondLinkGroup($group=[]): TableBuilder
    {
        if ($group) {
            $this->_vars['second_group'] = $group;
        }
        return $this;
    }

    /**
     * 设置页面标题
     * @param string $title 页面标题
     * @return $this
     */
    public function setPageTitle(string $title = ''): TableBuilder
    {
        if ($title != '') {
            $this->_vars['page_title'] = trim($title);
        }
        return $this;
    }

    /**
     * 设置表单页提示信息
     * @param string $tips 提示信息
     * @param string $type 提示类型：danger,info,warning,success
     * @param string $pos  提示位置：top,search,bottom
     * @return $this
     */
    public function setPageTips(string $tips = '', string $type = 'info', string $pos = 'top'): TableBuilder
    {
        if ($tips != '') {
            $this->_vars['page_tips_' . $pos] = $tips;
            $this->_vars['tips_type']         = trim($type);
        }
        return $this;
    }

    /**
     * 设置额外JS代码
     * @param string $extra_js 额外JS代码
     * @return $this
     */
    public function setExtraJs(string $extra_js = ''): TableBuilder
    {
        if ($extra_js != '') {
            $this->_vars['extra_js'] = $extra_js;
        }
        return $this;
    }

    /**
     * 设置额外CSS代码
     * @param string $extra_css 额外CSS代码
     * @return $this
     */
    public function setExtraCss(string $extra_css = ''): TableBuilder
    {
        if ($extra_css != '') {
            $this->_vars['extra_css'] = $extra_css;
        }
        return $this;
    }

    /**
     * 设置额外HTML代码
     * @param string $extra_html 额外HTML代码
     * @param string $pos        位置 [top和bottom]
     * @return $this
     */
    public function setExtraHtml(string $extra_html = '', string $pos = ''): TableBuilder
    {
        if ($extra_html != '') {
            $pos != '' && $pos = '_' . $pos;
            $this->_vars['extra_html' . $pos] = $extra_html;
        }
        return $this;
    }

    /**
     * 设置是否显示分页
     * @param string $value 是否显示分页 true|false
     * @return $this
     */
    public function setPagination(string $value = ''): TableBuilder
    {
        if ($value != '') {
            $this->_vars['pagination'] = $value;
        }
        return $this;
    }

    /**
     * 设置列表树父ID
     * @param string $value 字段
     * @return $this
     */
    public function setParentIdField(string $value = ''): TableBuilder
    {
        if ($value != '') {
            $this->_vars['parent_id_field'] = $value;
        }
        return $this;
    }

    /**
     * 设置每页显示的行数
     * @param mixed $value 数量
     * @return $this
     */
    public function setPageSize($value = ''): TableBuilder
    {
        if ($value != '') {
            $this->_vars['page_size'] = $value;
        }
        return $this;
    }

    /**
     * 隐藏第一列多选框(默认显示,多选列多用于批量删除等操作)
     * @return $this
     */
    public function hideCheckbox(): TableBuilder
    {
        $this->_vars['hide_checkbox'] = false;
        return $this;
    }

    /**
     * 设置提示语
     * @param $empty_tips
     * @return $this
     */
    public function setEmptyTips($empty_tips): TableBuilder
    {
        $this->_vars['empty_tips'] = $empty_tips;
        return $this;
    }

    /**
     * 设置单列表单提交url
     * @param string $url
     * @return $this
     */
    public function setSubmitUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['submit_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置表格URL
     * @param string $url url地址
     * @return $this
     */
    public function setDataUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['data_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置表格默认的新增地址
     * @param string $url url地址
     * @return $this
     */
    public function setAddUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['add_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置表格默认的修改地址
     * @param string $url url地址
     * @return $this
     */
    public function setEditUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['edit_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置表格默认的删除地址
     * @param string $url url地址
     * @return $this
     */
    public function setDelUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['del_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置表格默认的导出地址
     * @param string $url url地址
     * @return $this
     */
    public function setExportUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['export_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置表格默认的更改排序地址
     * @param string $url url地址
     * @return $this
     */
    public function setSortUrl(string $url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['sort_url'] = $url;
        }
        return $this;
    }

    //设置单选或下拉选择url
    public function setChooseUrl($url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['choose_url'] = $url;
        }
        return $this;
    }

    public function setStateUrl($url = ''): TableBuilder
    {
        if ($url != '') {
            $this->_vars['state_url'] = $url;
        }
        return $this;
    }

    /**
     * 设置搜索参数
     * @param array $items
     * @return $this
     * 第一个参数：类型
     * 第二个参数：字段名称
     * 第三个参数：字段别名
     * 第四个参数：匹配方式（默认为'='，也可以是'<>，>，>=，<，<=，LIKE'等等）
     * 第五个参数：默认值
     * 第六个参数：额外参数（不同类型，用途不同）['select_url'=>'',]
     */
    public function setSearch(array $items = []): TableBuilder
    {
        if (!empty($items)) {
            foreach ($items as &$item) {
                $item['type']           = $item[0] ?? '';  // 字段类型
                $item['name']           = $item[1] ?? '';  // 字段名称
                $item['title']          = $item[2] ?L($item[2]):'';  // 字段别名
                $item['option']         = $item[3] ?? '='; // 匹配方式
                $item['default']        = $item[4] ?? '';  // 默认值
                $item['param']          = $item[5] ?? [];  // 额外参数
            }
            $this->_vars['search'] = $items;
        }
        return $this;
    }

    /**
     * 设置模板路径
     * @param $template
     * @return void
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }
}
