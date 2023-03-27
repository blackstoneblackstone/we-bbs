<?php
namespace app\common\library\builder;
use app\common\library\builder\traits\form\Form;
use think\facade\View;

class FormBuilder
{
    use Form;
    /**
     * @var string 模板路径(默认使用系统内置路径，无需设置)
     */
    private $_template = '';

    /**
     * @var array 模板变量
     */
    private $_vars = [
        'page_title'     => '',        // 页面标题
        'page_tips'      => '',        // 页面提示
        'tips_type'      => '',        // 提示类型
        'form_url'       => '',        // 表单提交地址 [默认为当前方法 + Post]
        'form_method'    => 'post',    // 表单提交方式
        'empty_tips'     => '暂无数据', // 没有表单项时的提示信息
        'btn_hide'       => [],        // 要隐藏的按钮
        'btn_title'      => [],        // 按钮标题
        'btn_extra'      => [],        // 额外按钮
        'extra_html'     => '',        // 额外HTML代码
        'extra_js'       => '',        // 额外JS代码
        'extra_css'      => '',        // 额外CSS代码
        'submit_confirm' => false,     // 提交确认
        'form_items'     => [],        // 表单项目
        'form_data'      => [],        // 表单数据
    ];

    /**
     * @var bool 是否分组数据 [分组时不再需要传递其他参数]
     */
    private $_is_group = false;

    /**
     * @var
     */
    private static $instance;

    /**
     * 获取句柄
     * @param string $_template
     * @return FormBuilder
     */
    public static function getInstance($_template=''): FormBuilder
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
        // 设置默认模版
        $this->_template = $_template?:'backend@global/form/layout';
        // 设置默认表单提交地址 [默认为当前方法 + Post]
        $plugin =request()->plugin;
        $this->_vars['form_url'] =  $plugin ? '/'.config('app.admin').'/plugins/'.$plugin.'/'.request()->controller().'/'.request()->action() : (string)url(request()->action());
    }

    /**
     * 私有化clone函数
     */
    private function __clone()
    {

    }

    /**
     * 设置页面标题
     * @param string $title 页面标题
     * @return $this
     */
    public function setPageTitle(string $title = ''): FormBuilder
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
    public function setPageTips(string $tips = '', string $type = 'info', string $pos = 'top'): FormBuilder
    {
        if ($tips != '') {
            $this->_vars['page_tips_' . $pos] = $tips;
            $this->_vars['tips_type']         = trim($type);
        }
        return $this;
    }

    /**
     * 设置表单提交地址
     * @param string $form_url 提交地址
     * @return $this
     */
    public function setFormUrl(string $form_url = ''): FormBuilder
    {
        if ($form_url != '') {
            $this->_vars['form_url'] = trim($form_url);
        }
        return $this;
    }

    /**
     * 设置表单提交方式
     * @param string $value 提交方式
     * @return $this
     */
    public function setFormMethod(string $value = ''): FormBuilder
    {
        if ($value != '') {
            $this->_vars['form_method'] = $value;
        }
        return $this;
    }

    /**
     * 模板变量赋值
     * @param mixed  $name  要显示的模板变量
     * @param string $value 变量的值
     * @return $this
     */
    public function assign($name, string $value = ''): FormBuilder
    {
        if (is_array($name)) {
            $this->_vars = array_merge($this->_vars, $name);
        } else {
            $this->_vars[$name] = $value;
        }
        return $this;
    }

    /**
     * 隐藏按钮
     * @param array|string $btn 要隐藏的按钮，如：['submit']，其中'submit'->确认按钮，'back'->返回按钮
     * @return $this
     */
    public function hideBtn($btn = []): FormBuilder
    {
        if (!empty($btn)) {
            $this->_vars['btn_hide'] = is_array($btn) ? $btn : explode(',', $btn);
        }
        return $this;
    }

    /**
     * 添加按钮
     * @param $name
     * @param $title
     * @param $attr
     * @param $btn_type
     * @return $this|array
     */
    public function addButton($name = '', $title = '', $attr = [], $btn_type = 'button')
    {
        $item = [
            'type'     => 'button',
            'name'     => $name,
            'title'    => $title,
            'id'       => $name,
            'btn_type' => $btn_type,
            'data'     => '',
        ];
        if ($attr) {
            foreach ($attr as $key => $value) {
                if (substr($key, 0, 5) == 'data-') {
                    $item['data'] .= $key . '="' . $value . '" ';
                }
            }
            $item = array_merge($item, $attr);
        }

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 设置按钮标题
     * @param string|array $btn   按钮名 'submit' -> “提交”，'back' -> “返回”
     * @param string       $title 按钮标题
     * @return $this
     */
    public function setBtnTitle($btn = '', string $title = ''): FormBuilder
    {
        if (!empty($btn)) {
            if (is_array($btn)) {
                $this->_vars['btn_title'] = $btn;
            } else {
                $this->_vars['btn_title'][trim($btn)] = trim($title);
            }
        }
        return $this;
    }

    /**
     * 添加额外按钮
     * @param string $btn 按钮内容
     * @return $this
     */
    public function addBtn(string $btn = ''): FormBuilder
    {
        if ($btn != '') {
            $this->_vars['btn_extra'][] = $btn;
        }
        return $this;
    }

    /**
     * 设置额外HTML代码
     * @param string $extra_html 额外HTML代码
     * @param string $pos        位置 [top和bottom]
     * @return $this
     */
    public function setExtraHtml(string $extra_html = '', string $pos = ''): FormBuilder
    {
        if ($extra_html != '') {
            $pos != '' && $pos = '_' . $pos;
            $this->_vars['extra_html' . $pos] = $extra_html;
        }
        return $this;
    }

    /**
     * 设置额外JS代码
     * @param string $extra_js 额外JS代码
     * @return $this
     */
    public function setExtraJs(string $extra_js = ''): FormBuilder
    {
        if ($extra_js != '') {
            $this->_vars['extra_js'] .= $extra_js;
        }
        return $this;
    }

    /**
     * 设置额外CSS代码
     * @param string $extra_css 额外CSS代码
     * @return $this
     */
    public function setExtraCss(string $extra_css = ''): FormBuilder
    {
        if ($extra_css != '') {
            $this->_vars['extra_css'] = $extra_css;
        }
        return $this;
    }

    /**
     * select 触发器
     * @param string $field
     * @param string $value 选中的值
     * @param array|string $changeFields
     * @param string $showHide 默认当等于选择值后是显示还是隐藏
     * @param string $defaultShowHide 默认待触发的字段是显示还是隐藏
     * @return FormBuilder
     */
    public function setSelectTrigger(string $field,string $value,$changeFields=[],string $showHide='show',string $defaultShowHide='show'): FormBuilder
    {
        if(!$field || !$changeFields) return $this;
        $changeFields = is_array($changeFields) ? $changeFields : explode(',',$changeFields);
        $htmlHide = $htmlShow = '';
        foreach ($changeFields as $fieldName)
        {
            $htmlHide.='$("#form_group_'.$fieldName.'").hide();';
            $htmlShow.='$("#form_group_'.$fieldName.'").show();';
        }
        $default = "if($('#form_group_{$field} select option:selected').val()=='".$value."'){".($showHide=='show'?$htmlShow:$htmlHide) ."}else{".($showHide=='show'?$htmlHide:$htmlShow) ."}";
        $script = '<script type="application/javascript">'.($defaultShowHide=='show'?$htmlShow:$htmlHide).$default.';$("#form_group_'.$field.' select").change(function (){ if($(this).val()=="'.$value.'"){'.($showHide=='show'?$htmlShow:$htmlHide).' }else{ '.($showHide=='show'?$htmlHide:$htmlShow).'}})</script>';
        $this->_vars['extra_js'] .= $script;
        return $this;
    }

    /**
     * radio触发器
     * @param string $field
     * @param string $value 选中的值
     * @param array|string $changeFields
     * @param string $showHide 默认当等于选择值后是显示还是隐藏
     * @param string $defaultShowHide 默认待触发的字段是显示还是隐藏
     * @return FormBuilder
     */
    public function setRadioTrigger(string $field,string $value,$changeFields=[],string $showHide='show',string $defaultShowHide='show'): FormBuilder
    {
        if(!$field || !$changeFields) return $this;
        $changeFields = is_array($changeFields) ? $changeFields : explode(',',$changeFields);
        $htmlHide = $htmlShow = '';
        foreach ($changeFields as $fieldName)
        {
            $htmlHide.='$("#form_group_'.$fieldName.'").hide();';
            $htmlShow.='$("#form_group_'.$fieldName.'").show();';
        }
        $default = "if($('#form_group_{$field} input[type=radio]:checked').val()=='".$value."'){".($showHide=='show'?$htmlShow:$htmlHide) ."}else{".($showHide=='show'?$htmlHide:$htmlShow) ."}";
        $script = '<script type="application/javascript">'.($defaultShowHide=='show'?$htmlShow:$htmlHide).$default.';$("#form_group_'.$field.' input[type=radio]").change(function (){ if($(this).val()=="'.$value.'"){'.($showHide=='show'?$htmlShow:$htmlHide).' }else{ '.($showHide=='show'?$htmlHide:$htmlShow).'}})</script>';
        $this->_vars['extra_js'] .= $script;
        return $this;
    }

    /**
     * 设置提交表单时显示确认框
     * @return $this
     */
    public function submitConfirm($bool=true): FormBuilder
    {
        $this->_vars['submit_confirm'] = $bool;
        return $this;
    }

    /**
     * 添加表单项 [别名方法]
     * @param string $type 表单项类型
     * @param string $name 表单项名，与各自方法中的参数一致
     * @return $this
     */
    public function addFormItem(string $type = '', string $name = ''): FormBuilder
    {
        if ($type != '') {
            // 获取所有参数值
            $args = func_get_args();
            // 删除数组中的第一个元素（type），并返回被删除元素的值
            array_shift($args);
            // 首字符转换为大写并拼接为方法名
            $method = 'add' . ucfirst($type);
            // 调用回调函数
            call_user_func_array([$this, $method], $args);
        }
        return $this;
    }

    /**
     * 一次性添加多个表单项
     * @param array $items 表单项
     * @return $this
     */
    public function addFormItems(array $items = []): FormBuilder
    {
        if (!empty($items)) {
            foreach ($items as $item) {
                call_user_func_array([$this, 'addFormItem'], $item);
            }
        }
        return $this;
    }

    /**
     * 设置表单数据
     * @param array $form_data 表单数据
     * @return $this
     */
    public function setFormData(array $form_data = []): FormBuilder
    {
        if (!empty($form_data)) {
            $this->_vars['form_data'] = $form_data;
        }
        return $this;
    }

    /***
     * 设置表单项的值
     */
    private function setFormValue()
    {
        if ($this->_vars['form_data']) {
            foreach ($this->_vars['form_items'] as &$item) {
                // 判断是否为分组
                if ($item['type'] == 'group') {
                    foreach ($item['options'] as &$group) {
                        foreach ($group as $key => $value) {
                            if (isset($value['name'])) {
                                if (isset($this->_vars['form_data'][$value['name']])) {
                                    $group[$key]['value'] = $this->_vars['form_data'][$value['name']];
                                }
                            }
                        }
                    }
                } else {
                    if (isset($item['name'])) {
                        if (isset($this->_vars['form_data'][$item['name']])) {
                            $item['value'] = $this->_vars['form_data'][$item['name']];
                        }
                    }
                }
            }
        }
    }

    /**
     * 添加分组
     * @param array $groups 分组数据
     * @return array|FormBuilder
     */
    public function addGroup(array $groups = [])
    {
        if (is_array($groups) && !empty($groups)) {
            $this->_is_group = true;
            foreach ($groups as &$group) {
                foreach ($group as $key => $item) {
                    // 删除数组中的第一个元素（type）
                    $type = array_shift($item);
                    // 转换首字母大写，找到对应方法并调用
                    $group[$key] = call_user_func_array([$this, 'add' . ucfirst($type)], $item);
                }
            }
            $this->_is_group = false;
        }

        $item = [
            'type'    => 'group',
            'options' => $groups
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }


    /**
     * 添加链接分组
     * @param array $configs 配置数据
     * @return array|FormBuilder
     */
    public function addLinkGroup(array $configs = [],$tab_links=array())
    {
        if (is_array($configs) && !empty($configs)) {
            $this->_is_group = true;
            foreach ($configs as $key => $item) {
                // 删除数组中的第一个元素（type）
                $type = array_shift($item);
                // 转换首字母大写，找到对应方法并调用
                $configs[$key] = call_user_func_array([$this, 'add' . ucfirst($type)], $item);
            }
            $this->_is_group = false;
        }

        $item = [
            'type'    => 'link_group',
            'configs' => $configs,
            'tabs'    =>$tab_links
        ];

        if ($this->_is_group) {
            return $item;
        }

        $this->_vars['form_items'][] = $item;
        return $this;
    }

    /**
     * 渲染模版
     * @param string $template 模板文件名或者内容
     * @return string
     */
    public function fetch(string $template = ''): string
    {
        // 设置表单值
        $this->setFormValue();

        // 单独设置模板
        if ($template != '') {
            $this->_template = $template;
        }
        View::assign($this->_vars);
        return View::fetch($this->_template);
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