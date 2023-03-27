<?php

namespace app\common\library\builder;
use app\common\library\helper\ArrayHelper;
use app\common\library\helper\DateHelper;
use app\common\library\helper\FileHelper;
use app\common\library\helper\TreeHelper;
use app\model\admin\AdminAuth;
use think\facade\Request;
use think\helper\Str;

class MakeBuilder
{
    protected static $instance = null;
    protected static $prefix = '';
    /**
     * @param bool $refresh
     * @return MakeBuilder
     */
    final public static function getInstance(bool $refresh = false): MakeBuilder
    {
        if (is_null(self::$instance) || $refresh) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    public static function getPrefix()
    {
        return self::$prefix ?: self::$prefix = app()->db->getConfig('connections.mysql.prefix');
    }

    /**
     * 所有数据库表详情
     * @param string $table
     * @return array
     */
    public function tablesInfo(string $table = ''): array
    {
        $prefix = self::getPrefix();
        $tableName = $table;
        if ($table) {
            $table = static::sqlFilter($table);
            $table = " AND TABLE_NAME = '{$prefix}{$table}'";
        }
        $sql = "SELECT TABLE_NAME as `table`,TABLE_COMMENT as `comment`,TABLE_ROWS as `rows`,ENGINE as `engine` FROM information_schema.TABLES WHERE TABLE_SCHEMA = :table " . $table;
        $list = db()->query($sql, ['table' => app()->db->getConfig('connections.mysql.database')]);
        foreach ($list as $k => $v) {
            $list[$k] = [
                'table' => $prefix && 0 === strpos($v['table'], $prefix) ? substr($v['table'], strlen($prefix)) : $v['table'],
                'title' => $v['comment'],
                'remark' => $v['comment'],
                'top_button' => 'add,delete,export',
                'right_button' => 'edit,delete',
                'status' => 0,
                'page' => 1,
                'pk' => $this->getPrimary($tableName),
                'pid_field'=>'pid',
                'menu_pid' => 0
            ];
        }
        return $table ? $list[0]:$list;
    }

    /**
     * 所有表中字段详情
     * @param string $table
     * @param mixed $field
     * @return array
     */
    public function fieldsInfo(string $table,$field = null): array
    {
        $fields = db()->query("SHOW full columns FROM " . self::getPrefix() . $table);
        $lists = [];
        is_string($field) && $field = (array)$field;
        $weight = 10;
        foreach ($fields as $info) {
            if (null === $field || in_array($info['Field'], $field)) {
                $auto_increment = strpos($info['Extra'], 'auto_increment') !== false;
                $list = [
                    'table' => $table,
                    'name' => $info['Comment'] ?: $info['Field'],
                    'field' => $info['Field'],
                    'sort' => $weight,
                    "is_list" => 1,
                    "is_add" => 1,
                    "is_edit" => 1,
                    "is_search" => 0,
                    "is_sort" => 0,
                    'is_pk' => 0,
                    'relation_db' => '',      //关联表
                    'relation_field' => '',    //关联表字段
                    "search_type" => "=",
                    'settings' => json_encode([
                        'default' => $info['Default'],
                        'extra_attr' => '',
                        'extra_class' => '',
                        'placeholder' => '',
                        'param' => [],
                        'format'=>'Y-m-d',
                        'step'=>1,
                        'fieldType'=>$info['Type']
                    ],JSON_UNESCAPED_UNICODE),
                    "type" => "text",
                    'tips' => '',           // 表单提示
                    'required' => 0,//是否必填
                    'minlength' => 1,//最小长度
                    'maxlength' => 255,//最大长度
                ];

                if ($info['Key'] === 'PRI') { // 主键默认值
                    $list['sort'] = 1;
                    $list['is_pk'] = 1;
                    if ($auto_increment) {
                        $list['type'] = 'hidden';
                    }
                }

                if ($list['field'] == 'update_time' || $list['field'] == 'create_time') {
                    $list['type'] = 'datetime';
                }

                if ($list['field'] == 'sort') {
                    $list['type'] = 'sort';
                }

                if ($list['field'] == 'icon') {
                    $list['type'] = 'icon';
                }

                if ($list['field'] == 'status') {
                    $list['type'] = 'status';
                }

                $lists[] = $list;
            }
            $weight += 5;
        }
        return $lists;
    }

    public function registerCurdInfos(): array
    {
        $tables = $this->tablesInfo();
        $allTables = array_column($tables,'table');
        $databaseTables = db('curd')->column('table');
        $diffTables = array_diff($allTables,$databaseTables);
        if($diffTables)
        {
            foreach ($diffTables as $diffTable) {
                $tableInfo = $this->tablesInfo($diffTable);
                $fieldInfo = $this->fieldsInfo($diffTable);
                db('curd')->insertAll($tableInfo);
                if($fieldInfo)
                {
                    db('curd_field')->insertAll($fieldInfo);
                }
            }
        }
        return $diffTables;
    }

    /**
     * sql注入过滤
     * @param string $str
     * @return string
     */
    protected static function sqlFilter(string $str): string
    {
        $str = addslashes($str);
        $str = str_replace("%", "\%", $str);
        $str = nl2br($str);
        return htmlspecialchars($str);
    }

    public function getRelations($table)
    {
        return db('curd_field')
            ->where([['table','=',$table],['relation_db','<>',''],['relation_field','<>','']])
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->column('field');
    }

    /**
     * 获取列表显示字段
     * @param $table
     * @return mixed
     */
    public function getListField($table)
    {
        return db('curd_field')
            ->where(['table'=>$table,'is_list'=>1])
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->column('field');
    }

    /**
     * 返回单表信息
     * @param string $table
     * @return mixed
     */
    public function table(string $table)
    {
        $info = db('curd')->where(['name'=>$table])->find();
        if(!$info) $info = $this->tablesInfo($table);
        return $info;
    }

    /**
     * 返回单条字段信息
     * @param string $table
     * @param string $field
     * @return array
     */
    public function field(string $table, string $field): array
    {
        $fieldInfo = db('curd_field')->where(['table'=>$table,'name'=>$field])->find();
        if($fieldInfo) $fieldInfo['settings'] = wc_unserialize($fieldInfo['settings']);
        if(!$fieldInfo) $fieldInfo = $this->fieldsInfo($table,$field);
        return $fieldInfo ?? [];
    }

    /**
     * 获取表字段信息
     * @param $table
     * @return array
     */
    public function fields($table): array
    {
        $tableInfo = $this->table($table);
        // 非空判断
        if (!$tableInfo) {
            return [];
        }
        // 根据模块ID获取所有字段
        $fields = db('curd_field')
            ->where('table', $table)
            ->order(['sort' => 'asc', 'id' => 'asc'])
            ->select()
            ->toArray();

        if(!$fields) $fields = $this->fieldsInfo($table);
        foreach ($fields as &$field) {
            // 给每个字段增加一个属性：是否主键
            $field['is_pk'] = $field['name'] == $tableInfo['pk'] ? 1 : 0;
            // 格式化字段的其他设置
            $field['settings'] = json_decode($field['settings'],true);
        }
        return $fields;
    }

    /**
     * 获取表的主键
     * @param string $table 表名称
     * @return mixed|string
     */
    public function getPrimaryKey(string $table = '')
    {
        return db('curd')->where('name',$table)->value('pk') ?? 'id';
    }

    /**
     * 获取列表页面可展示的字段信息
     * @param string $table 表名称
     * @return array
     */
    public function getListColumns(string $table = ''): array
    {
        $columns = [];
        $fields = $this->fields($table);
        foreach ($fields as &$field) {
            if ($field['is_list']) {
                // 默认值
                $default = $field['settings']['default'] ??'';
                // 排序
                $sortable = $field['is_sort'] ? 'true' : 'false';
                $param = $field['settings']['param'] ?? [];
                $class = $field['settings']['extra_class'] ?? '';
                // 添加到返回数组中
                $columns[] = [$field['field'], $field['name'], $field['type'], $default, $param, $class, $sortable];
            }
        }
        return $columns;
    }

    /**
     * 获取搜索字段
     * @param $table
     * @return array
     */
    public function getSearchColumn($table): array
    {
        $columns = [];
        $fields = $this->fields($table);
        foreach ($fields as &$field)
        {
            if ($field['is_search']) {
                $param = isset($field['settings']['param']) ? ArrayHelper::strToArr($field['settings']['param']) : '';
                $item['type'] = $field['table_type'] ?? '';  // 字段类型
                $item['name'] = $field['field'] ?? '';  // 字段名称
                $item['title'] = $field['title'] ?? '';  // 字段别名
                $item['option'] = $field['search_type'] ?? '='; // 匹配方式
                $item['default'] = $field['default'] ?? '';  // 默认值
                $item['param'] = $param ?? [];  // 额外参数
                $item['data_source'] = $field['data_source'] ?? 0;   // 数据源
                $item['relation_db'] = $field['relation_db'] ?? '';  // 模型关联
                $item['relation_field'] = $field['relation_field'] ?? '';  // 关联字段
                $item['field_id'] = $field['weight'] ?? 0;   // 字段编号
                // 添加到返回数组中
                $columns[] = array_values($item);
            }
        }
        return $columns;
    }

    /**
     * 获取添加页面可展示的字段信息
     * @param string $table 表名称
     * @param array $info
     * @return array
     */
    public function getAddColumns(string $table = '', $info = []): array
    {
        $fields = $this->fields($table);
        $columns = [];
        foreach ($fields as &$field) {
            // 非主键字段判断是否可添加
            if (strpos(strtolower(Request::action()), 'add') !== false && $field['is_add'] != 1) {
                continue;
            }

            // 非主键字段判断是否可修改
            if ($field['is_pk'] != 1 && strpos(strtolower(Request::action()), 'edit') !== false && $field['is_edit'] != 1) {
                continue;
            }

            // 状态为0的字段不可新增或修改
            if ($field['is_pk'] != 1 && isset($field['status']) && $field['status'] == 0) {
                continue;
            }

            if ($info) {
                if (isset($info[$field['field']])) {
                    $field['settings']['default'] = $info[$field['field']] ?? $field['default'];
                    // 禁止修改
                    if ($field['is_edit'] != 1) {
                        $field['settings']['extra_attr'] = isset($field['settings']['extra_attr']) ? $field['settings']['extra_attr'] . ' readonly = "readonly"' : '';
                    }
                }
            }
            $options = $this->getFieldOptions($field);

            $field['options'] = $options ?? [];

            // 必填项转换
            $field['required'] = $field['required'] == 1;
            // 添加到返回数组中,注意form构建器和table构建器的不一致
            if ($field['type'] == 'text') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['settings']['default']??'',    // 默认值
                    '',               // 标签组，可以在文本框前后添加按钮或者文字
                    $field['settings']['extra_attr']??'', // 额外属性
                    $field['settings']['extra_class']??'',// 额外CSS
                    $field['settings']['placeholder']??'',// 占位符
                    $field['required'],            // 是否必填
                ];
            } elseif ($field['type'] == 'icon') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['settings']['default'] ?? '',    // 默认值
                    '',               // 标签组，可以在文本框前后添加按钮或者文字
                    $field['settings']['extra_attr'] ?? '', // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],            // 是否必填
                ];
            } elseif ($field['type'] == 'textarea' || $field['type'] == 'password') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default']??'',           // 默认值
                    $field['settings']['extra_attr']??'',        // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'radio' || $field['type'] == 'checkbox') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['options'],             // 选项（数组）
                    $field['settings']['default']??'',    // 默认值
                    $field['settings']['extra_attr']??'', // 额外属性 extra_attr
                    $field['settings']['extra_class']??'',                            // 额外CSS extra_class
                    $field['required'],            // 是否必填
                ];
            } elseif ($field['type'] == 'select') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['options'],                    // 选项（数组）
                    $field['settings']['default']??'',           // 默认值
                    $field['settings']['extra_attr']??'',        // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'select2') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['options'],                    // 选项（数组）
                    $field['settings']['default']??'',           // 默认值
                    $field['settings']['extra_attr']??'',        // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                    $field['settings']['ajax']??'',                             // ajax请求地址
                ];
            } elseif ($field['type'] == 'number') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default'],           // 默认值
                    $field['minlength'],                                   // 最小值
                    $field['maxlength'],                                   // 最大值
                    $field['settings']['step']??1,              // 步进值
                    $field['settings']['extra_attr']??'',        // 额外属性
                    $field['settings']['extra_class']??'',       // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'hidden') {
                $columns[] = [
                    $field['type'],                      // 类型
                    $field['field'],                     // 字段名称
                    $field['settings']['default'] ?? '',    // 默认值
                    $field['settings']['extra_attr'] ?? '', // 额外属性 extra_attr
                ];
            } elseif ($field['type'] == 'date' || $field['type'] == 'time' || $field['type'] == 'datetime') {
                // 使用每个字段设定的格式
                if ($field['type'] == 'time') {
                    $format = $field['settings']['format'] ?: 'H:i:s';
                } else {
                    $format = $field['settings']['format'] ?: 'Y-m-d H:i:s';
                }
                $field['settings']['default'] = (int)$field['settings']['default'] > 0 && is_int($field['settings']['default']) ? date($format, $field['settings']['default']) : $field['settings']['default'];
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                // 提示信息
                    $field['settings']['default']??'',    // 默认值
                    $field['settings']['format']??'',     // 日期格式
                    $field['settings']['extra_attr']??'', // 额外属性 extra_attr
                    $field['settings']['extra_class']??'',                            // 额外CSS extra_class
                    $field['settings']['placeholder']??'',// 占位符
                    $field['required'],            // 是否必填
                ];
            } elseif ($field['type'] == 'daterange') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default']??'',           // 默认值
                    $field['settings']['format']??'',            // 日期格式
                    $field['settings']['extra_attr'] ?? '',  // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'tag') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default']??'',           // 默认值
                    $field['settings']['extra_attr'] ?? '',  // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'image' || $field['type'] == 'images' || $field['type'] == 'file' || $field['type'] == 'files') {
                // 多(图/文件)上传执行解析操作
                if ($field['type'] == 'images' || $field['type'] == 'files') {
                    $field['settings']['default'] = json_decode($field['settings']['default'], true);
                }

                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default'],           // 默认值
                    $field['settings']['extra_attr'] ?? '',  // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'editor') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default'],           // 默认值
                    $field['settings']['height'] ?? 0,       // 高度
                    $field['settings']['extra_attr'] ?? '',  // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['required'],                   // 是否必填
                ];
            } elseif ($field['type'] == 'color') {
                $columns[] = [
                    $field['type'],                // 类型
                    $field['field'],               // 字段名称
                    $field['name'],                // 字段别名
                    $field['tips'],                       // 提示信息
                    $field['settings']['default'],           // 默认值
                    $field['settings']['extra_attr'] ?? '',  // 额外属性
                    $field['settings']['extra_class'] ?? '', // 额外CSS
                    $field['settings']['placeholder'] ?? '', // 占位符
                    $field['required'],                   // 是否必填
                ];
            }
            // Button
        }
        return $columns;
    }

    /*获取验证*/
    public function getValidateRule($table): array
    {
        $fields = db('curd_field')
            ->where(['table'=> $table,'required'=>1])
            ->column('field,name');

        if(!$fields) return [];
        $rule = $message = [];
        foreach ($fields as $field) {
            $rule[$field['field']] = 'require';
            $message[$field['field'].'.require'] = $field['name'].'必填';
        }
        return ['rule'=>$rule,'message'=>$message];
    }
    /**
     * 获取列表需要的搜索字段
     * @param string $table 表名称
     * @return array
     */
    public function getListSearch(string $table = ''): array
    {
        $fields = $this->fields($table);
        $items = [];
        foreach ($fields as &$field) {
            // 默认参数传递
            if (Request::param($field['field']) || Request::param($field['field']) === "0") {
                $field['default_value'] = Request::param($field['field']);
            }

            // 添加到返回数组中(注意顺序不可变)
            $items[] = [
                $field['type'],                // 字段类型
                $field['field'],               // 字段名称
                $field['name'],                // 字段别名
                $field['search_type'] ?? '=',  // 匹配方式
                $field['default_value'] ?? '', // 默认值
                $field['param'] ?? [],         // 额外参数
                $field['data_source'] ?? 0,    // 数据源 [0 字段本身, 1 关联数据]
                $field['relation_db'] ?? '',// 模型关联
                $field['relation_field'] ?? '',// 关联字段
                $field['id'] ?? 0,             // 字段编号
            ];
        }
        return $items;
    }

    /**
     * 获取搜索条件
     * @param array $search
     * @return array
     */
    public function getWhere(array $search = []): array
    {
        $searchWhere = [];
        foreach ($search as $key => $val) {
            $searchWhere[$key] = [
                $val[0],
                $val[1],
                $val[2],
                $val[3] ?? '=',
                $val[4] ?? '',
                $val[5] ?? [],
                $val[6] ?? 0,
            ];
        }
        //全局查询条件
        $where = [];
        // 循环所有搜索字段，看是否有传递
        foreach ($searchWhere as $k => $v) {
            if (Request::param($v[1]) || Request::param($v[1]) === "0") {
                $searchKeywords = Request::param($v[1]);
                // 判断字段类型，默认为=
                if (isset($v[3]) && !empty($v[3])) {
                    $option = $v[3];
                } else {
                    $option = '=';
                }
                switch ($v[0]) {
                    case 'select':
                    case 'text':
                        if (strtoupper($option) == 'LIKE') {
                            $where[] = [$v[1], $option, '%' . $searchKeywords . '%'];
                        } else {
                            $where[] = $searchKeywords!='' ? [$v[1], $option, $searchKeywords] : [];
                        }
                        break;
                    case 'time':
                    case 'datetime':
                    case 'date':
                        $getDateRange = DateHelper::dateRange($searchKeywords);
                        $where[] = [$v[1], 'between', $getDateRange];
                        break;
                    // 默认都当作文本框
                    default:
                        if (strtoupper($option) == 'LIKE') {
                            $where[] = [$v[1], $option, '%' . $searchKeywords . '%'];
                        } else {
                            $where[] = $searchKeywords ? [$v[1], $option, $searchKeywords] : [];
                        }
                }
            }
        }
        return $where;
    }

    //创建菜单及控制器
    public function build($table)
    {
        $info = $this->table($table);
        if ($info) {
            $baseUrl = 'build.' . Str::studly($info['name']);
            $data = [
                'pid' => $info['menu_pid'] ?? 5,
                'name' => $baseUrl . '/index',
                'title' => $info['title'],
                'sort' => 50,
                'status' => 1,
                'icon' => 'fa fa-folder'
            ];

            // 查询是否已存在，存在的不再处理
            $rule = db('admin_auth')->where('name', $baseUrl . '/index')->find();
            if (!$rule) {
                $rule = AdminAuth::create($data)->toArray();
            }

            $data = [];
            if ($rule) {
                // 添加规则
                if (strpos($info['top_button'], 'add') !== false) {
                    $data[] = [
                        'pid' => $rule['id'],
                        'name' => $baseUrl . '/add',
                        'title' => '操作-添加',
                        'sort' => 1,
                        'status' => 0,
                        'icon' => 'fa fa-plus'
                    ];
                }
                // 修改规则
                if (strpos($info['top_button'], 'edit') !== false) {
                    $data[] = [
                        'pid' => $rule['id'],
                        'name' => $baseUrl . '/edit',
                        'title' => '操作-修改',
                        'sort' => 3,
                        'status' => 0,
                        'icon' => 'fa fa-edit'
                    ];
                }
                // 删除规则
                if (strpos($info['top_button'], 'delete') !== false) {
                    $data[] = [
                        'pid' => $rule['id'],
                        'name' => $baseUrl . '/delete',
                        'title' => '操作-删除',
                        'sort' => 5,
                        'status' => 0,
                        'icon' => 'fa fa-times'
                    ];
                }
                // 导出规则
                if (strpos($info['top_button'], 'export') !== false) {
                    $data[] = [
                        'pid' => $rule['id'],
                        'name' => $baseUrl . '/export',
                        'title' => '操作-导出',
                        'sort' => 7,
                        'status' => 0,
                        'icon' => 'fa fa-download'
                    ];
                }

                $data[] = [
                    'pid' => $rule['id'],
                    'name' => $baseUrl . '/state',
                    'title' => '操作-状态',
                    'sort' => 9,
                    'status' => 0,
                ];

                $data[] = [
                    'pid' => $rule['id'],
                    'name' => $baseUrl . '/sort',
                    'title' => '操作-排序',
                    'sort' => 8,
                    'status' => 0,
                ];

                $data[] = [
                    'pid' => $rule['id'],
                    'name' => $baseUrl . '/choose',
                    'title' => '操作-选择',
                    'sort' => 9,
                    'status' => 0,
                ];

                $newFile = root_path() .'app'. DS.'backend'.DS . 'build' . DS . Str::studly($info['name']) . '.php';

                if(!is_dir(root_path() .'app'. DS.'backend'.DS . 'build' . DS))
                {
                    FileHelper::mkDirs(root_path() .'app'. DS.'backend'.DS . 'build' . DS);
                }

                $fileBase = root_path().'app'. DS.'common'. DS . 'tpl' . DS . 'Controller.tpl';

                try {
                    $fh = fopen($fileBase, "r");
                    $contents = fread($fh, filesize($fileBase));
                    $contents = str_replace('{$tableName}' , Str::studly($info['name']), $contents);
                    $contents = str_replace('{$table}' ,$info['name'], $contents);

                    if ($contents) {
                        $this->checkFile($newFile);
                        $myFile = fopen($newFile, "w");
                        fwrite($myFile, $contents);
                        fclose($myFile);
                    }
                    fclose($fh);
                    $authRule = new AdminAuth();
                    $authRule->saveAll($data);
                    return true;
                }catch (\Exception $e){
                    return false;
                }
            }
        }
        return true;
    }

    //检查控制器文件
    private function checkFile(string $file)
    {
        if (file_exists($file)) {
            rename($file, $file . '_' . time() . '_back');
        }
    }

    public function getFieldOptions(array $field): array
    {
        // 0 字段本身，1 系统字典，2 关联表
        if ($field['data_source'] == 1) {
            // 获取字典列表
            $result = db('dict')
                ->where('dict_id', $field['dict_code'])
                ->field('value,name')
                ->order('id DESC')
                ->select()
                ->toArray();
            $result = $this->changeSelect($result);
        } elseif ($field['data_source'] == 2) {
            if ( $field['type'] == 'select2') {
                $result = [];
            } else {
                // 取出对应模型的所有数据
                $module = db($field['relation_db']);
                // 根据模型名称获取select的排序
                $order = $this->getOrder($field['relation_db']);
                // 主键
                $pk = $this->getPk($field['relation_db']);

                // 当模块中包含pid/parent_id时格式化展示效果
                $fieldPid = '';
                $moduleName = db('curd')->where('name',  $field['relation_db'])->value('name');
                if ($moduleName) {
                    // 查询字段名称
                    $fieldArr = db('curd_field')->where(['field'=>'pid','table'=>$moduleName])->find('field');
                    if ($fieldArr) {
                        $fieldPid = ',' . $fieldArr;
                    }
                }
                // 获取数据列表
                $result = $module->field($pk . ',' . $field['relation_field'] . $fieldPid)
                    ->order($order)
                    ->select()
                    ->toArray();
                $result = $this->changeSelect($result);
            }
        } else {
            $result = [];
        }

        return $result;
    }

    private function getPk(string $table)
    {
        // 取出对应模块信息
        $model = db('curd')->where('name', $table)->find();
        // 获取主键
        return $model['pk'] ?? 'id';
    }

    private function changeSelect(array $array): array
    {
        $result = [];
        // 当元素个数为3时执行tree操作
        if ($array && count(($array[0])) == 3) {
            $array = TreeHelper::treeThree($array);
        }

        foreach ($array as &$arr) {
            if (count($arr) == 2) {
                $result[current($arr)] = end($arr);
            } else {
                $keys = array_keys($arr);
                $result[$arr[$keys[0]]] = $arr[$keys[1]];
            }
        }
        return $result;
    }

    private function getOrder(string $table): string
    {
        // 取出对应模型信息
        $model = db('curd')->where('name', $table)->find();
        // 获取主键
        $pk = $model['pk'] ?? 'id';
        // 是否有排序字段
        if(!$model) return '';
        $sortField = db('curd_field')->where('table', $table)->where('field', 'sort')->find();
        if ($sortField) {
            $order = 'sort ASC,' . $pk . ' DESC';
        } else {
            $order = $pk . ' DESC';
        }
        return $order;
    }

    /**
     * 获取表主键
     * @param $table
     * @return mixed
     */
    public function getPrimary($table){
        $database = config('database.connections.mysql.database');
        $table = get_table($table);
        $sql = "SELECT k.column_name  FROM
             information_schema.table_constraints t
         JOIN
             information_schema.key_column_usage k
         USING
             (constraint_name,table_schema,table_name)
         WHERE
             t.constraint_type='PRIMARY KEY'
         AND
             t.table_schema='$database'
         AND
             t.table_name='$table'";
        return db()->query($sql)[0]["column_name"];    // 数据库查询语句根据情况而定

    }
}