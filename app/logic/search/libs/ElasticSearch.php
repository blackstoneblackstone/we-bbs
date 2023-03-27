<?php

namespace app\logic\search\libs;
use Exception;
use think\helper\Str;

/**
 * ElasticSearch全文索引类
 * @ElasticSearch  7.15.0
 */
class ElasticSearch 
{
    /**
     * @var object 对象实例
     */
    protected static $instance = null;
    // 全局对象
    protected  $_client;
    // 索引标识
    protected  $_index = '';
    // 索引字段
    private $_app_field = [];
    // 高亮字段
    public $_highlight_field = [];
    // 查询规则
    public $_where = [];
    // 查询参数
    protected $_option = ['page' => 0];
    // 查询毫秒
    protected $_seconds = 0;
    protected $_count;
    // 字段排序
    protected $_sort = null;
    // 索引状态
    protected $_status = false;
    // 错误信息
    protected $_error = '';

    /**
     * 类构造函数
     * class constructor.
     */
    public function __construct()
    {
        if(get_setting('search_handle')!='ElasticSearch' || !(get_setting('search_engine_user') && get_setting('search_engine_password'))) return false;
        // 链接服务器
        require app_path('logic').'search/libs/elasticsearch/src/autoload.php';
        if(get_setting('search_engine_user') && get_setting('search_engine_password'))
        {
            $this->_client = \Elasticsearch\ClientBuilder::create()
                ->setBasicAuthentication(get_setting('search_engine_user'),get_setting('search_engine_password'))
                ->setConnectionPool('\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool')
                ->setHosts([get_setting('search_engine_host')])
                ->setSelector('\Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector')
                ->build();
        }else{
            $this->_client =\Elasticsearch\ClientBuilder::create()
                ->setConnectionPool('\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool')
                ->setHosts([get_setting('search_engine_host')])
                ->setSelector('\Elasticsearch\ConnectionPool\Selectors\StickyRoundRobinSelector')
                ->build();
        }
        $this->_index = get_setting('search_engine_app') ?:'we';
        $types = db('search_engine')->where('status',1)->column('name,result_field');
        //需要同步的字段
        if (!empty($types)) {
            foreach ($types as $key => $value) {
                if($value['result_field'])
                {
                    $field = $value['result_field'];
                    $this->_app_field[$value['name']] = explode(',', $field);
                }
            }
        }
    }

    /**
     * 初始化
     * @access public
     * @return self
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        // 返回实例
        return self::$instance;
    }

    /**
     * 创建索引
     * @param $name
     * @return string|void
     */
    public function indices($name)
    {
        try {
            $params = [
                'index' => $name,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 3,
                        'number_of_replicas' => 2,
                    ]
                ]
            ];
            // 创建索引
            if (!$this->_client->indices()->exists(['index' => $name])) {
                $this->_client->indices()->create($params);
            }
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    /**
     * 删除索引
     * @param string $name 索引名称
     * @return void
     * @throws Exception
     */
    public function deleteIndex(string $name)
    {
        try {
            $params = [
                'index' => $name,
            ];
            // 删除索引
            if ($this->_client->indices()->exists($params)) {
                $this->_client->indices()->delete($params);
            }
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * 创建Mapping
     * @param array $properties
     * @return array
     * @throws Exception
     */
    public function putMapping(array $properties = []): array
    {
        try {
            $properties = $this->parElasticSearch($properties);
            $params = [
                'index' => $this->_index,
                'body' => [
                    'properties' => $properties
                ]
            ];
            $this->_client->indices()->putMapping($params);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }

        return $properties;
    }

    /**
     * 解析ES配置
     * @param array $post
     * @return array
     */
    protected function parElasticSearch(array $post = []): array
    {
        // 循环配置
        foreach ($post['field_name'] as $key => $value) {
            $field[$value]['type'] = $post['field_type'][$key];
            if ($post['field_type'][$key] == 'date') {
                $field[$value]['format'] = 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis';
            }
            if ($post['field_type'][$key] == 'text') {
                if ($post['field_index'][$key] !== 'none') {
                    $field[$value]['index'] = $post['field_index'][$key];
                }
                if ($post['field_analyzer'][$key] !== 'defalut') {
                    $field[$value]['analyzer'] = $post['field_analyzer'][$key];
                }
                if ($post['field_search_analyzer'][$key] !== 'defalut') {
                    $field[$value]['search_analyzer'] = $post['field_search_analyzer'][$key];
                }
            }
        }
        return $field ?? [];
    }

    /**
     * 添加文档
     * @param string $name 文档索引类型
     * @param array $data
     * @return array|callable|false|string
     */
    public function create(string $name,array $data = [])
    {
        if (!empty($data)) {
            //$data = $this->_app_field[$name] ? array_intersect_key($this->_app_field[$name],$data) : $data;
            //$data = $this->queryTimestamp($data);
            $params = [
                'index' => $this->_index,
                'id' => 0,
                'body' => [],
            ];
            $params['body']['search_type'] = strtolower($name);
            foreach ($data as $key => $value)
            {
                if($key == 'id')
                {
                    if($name == 'answer')
                    {
                        $params['id'] = $data['question_id'].'_'.$value;
                    }else if($name == 'question')
                    {
                        $params['id'] = $data['id'].'_0';
                    }else
                    {
                        $params['id'] = $value;
                    }
                }

                $params['body'][$key] = $value;
            }
            try {
                return $this->_client->index($params);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }

        return false;
    }

    /**
     * 更新文档
     * @param array|null $data
     * @return array|callable|false
     */
    public function update(string $name,array $data = [])
    {
        if (!empty($data) && is_array($data))
        {
            //$data = array_intersect_key($data,$this->_app_field[$name]);
            //$data = $this->queryTimestamp($data);
            $params = [
                'index' => $this->_index,
                'id' => 0,
                'body' => [
                    'doc' => []
                ],
            ];
            $params['body']['search_type'] = strtolower($name);
            foreach ($data as $key => $value) {
                if($key == 'id')
                {
                    if($name == 'answer')
                    {
                        $params['id'] = $data['question_id'].'_'.$value;
                    }else if($name == 'question')
                    {
                        $params['id'] = $data['id'].'_0';
                    }else
                    {
                        $params['id'] = $value;
                    }
                }
                $params['body']['doc'][$key] = $value;
            }

            try {
                return $this->_client->update($params);
            } catch (\Throwable $th) {
                $this->setError($th->getMessage());
                return $th->getMessage();
            }
        }
        return false;
    }

    /**
     * 删除文档
     * @param $name
     * @param array $data 为空删除全部
     * @return void
     */
    public function delete($name,array $data = [])
    {
        $params['index'] = $name;
        $params['body']['search_type'] = strtolower($name);
        if (!empty($data)) {

            if($name == 'answer')
            {
                $params['id'] = $data['question_id'].'_'.$data['id'];
            }else if($name == 'question')
            {
                $params['id'] = $data['id'].'_0';
            }else
            {
                $params['id'] = $data['article_id'];
            }
        }

        $this->_client->delete($params);
    }

    /**
     * 格式化时间戳
     * @param array $data
     * @return void
     */
    private function queryTimestamp(array $data = [])
    {
        if (!empty($data) && is_array($data)) {
            if (isset($data['create_time'])) {
                if (is_numeric($data['create_time'])) {
                    $data['create_time'] = date('Y-m-d H:i:s',$data['create_time']);
                }
            }
            if (isset($data['update_time'])) {
                if (is_numeric($data['update_time'])) {
                    $data['update_time'] = date('Y-m-d H:i:s',$data['update_time']);
                }
            }
        }
        return $data;
    }

     /**
      * 魔术方法
      * @param string $method
      * @param mixed $arguments
      * @return void|array
      */
    public function __call(string $method, $arguments)
    {
        if (strtolower(substr($method, 0, 5)) == 'getby') {
            $field = Str::snake(substr($method, 5));
            $params = [
                'index' => $this->_index,
                'body' => [
                    'query' => [
                        'match' => [
                            $field => $arguments[0]
                        ]
                    ]
                ]
            ];
            return $this->_client->search($params);
        }
    }

    /**
     * 搜索数据源
     * @param string $name
     * @param null $query 关键词
     * @param string|array $field 查询字段
     * @return array
     */
    public function search(string $name='',$query = null, $field = []): array
    {
        $length = $this->_option['length'] ?? 10;
        if ($this->_option['page'] == 0) {
            $offset = $this->_option['offset'] ?? 0;
        } else {
            $offset = $this->_option['page'] ? $this->_option['length'] * ($this->_option['page']-1) : 0;
        }
        // 封装查询体
        $queryParam = [
            'index' => $this->_index,
            'from' => $offset,
            'size' => $length
        ];

        // 封装查询规则
        if (is_array($query)) {
            $queryParam['body'] = $query;
        } else {
            $conditions = [];
            $field = !empty($field) ? $field : 'search_text';
            if($name)
            {
                $conditions['bool']['must'] = array("match" => array("search_type" => $name));
            }

            if (!is_array($field)) {
                $field = explode(',',$field);
            }

            foreach ($field as $key => $value) {
                $conditions['bool']['should'][]['term'][$value] = $query;
            }
            if (!empty($this->_where)) {
                $conditions['bool']['must'] = array_merge_recursive($conditions['bool']['must'],$this->_where);
                $conditions['bool']['must'] = array_values(array_unique($conditions['bool']['must'],SORT_REGULAR));
            }
            $queryParam['body'] = ['query' => $conditions];
        }

        if (!$this->_highlight_field) {
            $this->highlight($field);
        }

        $queryParam['body']['highlight'] = [
            'pre_tags'  => [ "<span class=\"text-danger\">" ],
            "post_tags" => [ "</span>" ],
            'fields'    => $this->_highlight_field,
        ];

        if (!empty($this->_sort)) {
            $queryParam['body']['sort'] = $this->_sort;
        }

        // 查询索引
        $result = $this->_client->search($queryParam);

        // 查询总数
        $this->_count = $result['hits']['total']['value'];

        // 查询耗时
        $this->_seconds = ($result['took'] / 1000);

        $list = [];
        foreach ($result['hits']['hits'] as $key => $value) {
            $list[$key] = $value['_source'];
            if (isset($value['highlight'])) {
                foreach ($value['highlight'] as $field => $fragment) {
                    $list[$key][$field] = $fragment[0];
                }
            }
        }
        return $list;
    }

    /**
     * 指定查询条件
     * @access public
     * @param $fields
     * @param mixed $op 查询表达式
     * @param mixed $condition 查询条件
     * @return ElasticSearch
     */
    public function where($fields, $op = null, $condition = null): ElasticSearch
    {
        if (!is_array($fields) && $op !== null) {
            if (!$condition) {
                $this->_where['term'] = [
                    $fields => $op
                ];
            } else {
                $this->_where = $this->parseQueryWhere($fields,$op,$condition);
            }
        } else {
            foreach ($fields as $key => $meta) {
                list ($field,$op,$condition) = $meta;
                // 循环处理字段集
                if (array_key_exists($field,$this->_app_field)) {
                    $this->_where[$key] = $this->parseQueryWhere($field,$op,$condition);
                }
            }
        }

       return $this;
    }

    /**
     * 解析语句
     * @param string $field
     * @param string $op
     * @param string $condition
     * @return array
     */
    public function parseQueryWhere(string $field,string $op, string $condition): array
    {
        switch (strtolower($op)) {
            case '>':
                $array['term'] = [
                    $field => [
                        'gt' => $condition
                    ]
                ];
                break;
            case '<':
                $array['term'] = [
                    $field => [
                        'lt' => $condition
                    ]
                ];
                break;
            case '>=':
                $array['term'] = [
                    $field => [
                        'gte' => $condition
                    ]
                ];
                break;
            case '<=':
                $array['term'] = [
                    $field => [
                        'lte' => $condition
                    ]
                ];
                break;
            case '!=':
            case '!==':
                $array['term'] = [
                    $field => [
                        'neq' => $condition
                    ]
                ];
                break;
            case 'in':
                if (!is_array($condition)) {
                    $condition = explode(',',(string)$condition);
                }
                $array['terms'] = [$field => $condition];
                break;
            case 'like':
            default:
                $condition = str_replace('%','',(string)$condition);
                $array['term'] = [$field => $condition];
                break;
        }

        return $array;
    }

    /**
     * 查询字段高亮
     * @param mixed $fields
     * @return ElasticSearch
     */
    public function highlight($fields): ElasticSearch
    {
        if (!is_array($fields)) {
            $fields = str_replace(array('，','|','-'),',',$fields);
            $fields = explode(',',$fields);
        }
        foreach ($fields as $value) {
            $this->_highlight_field[$value] = new \stdClass();
        }
        return $this;
    }

    /**
     * 分页查询
     * @param integer $offset   数据偏移
     * @param integer $length   查询条数
     * @return ElasticSearch
     */
    public function limit(int $offset = 0, int $length = 0): ElasticSearch
    {
        if (empty($length)) {
            $this->_option['length'] = $offset;
            $this->_option['offset'] = 0;
        } else {
            $this->_option['length'] = $length;
            $this->_option['offset'] = $offset;
        }
        return $this;
    }

    /**
     * 搜索分页
     * @param integer $page
     * @return ElasticSearch
     */
    public function page(int $page = 1): ElasticSearch
    {
        $this->_option['page'] = $page == 0 ? 1 : $page;
        return $this; 
    }

    /**
     * 搜索排序
     * @param string  $field     // 字段
     * @param string    $asc       // 排序方式
     * @return ElasticSearch
     */
    public function order(string $field = 'id', string $asc = 'asc'): ElasticSearch
    {
        $this->_sort = [$field => $asc];
        return $this;
    }

    /**
     * 搜索匹配数
     * @return int 
     */
    public function getCount(): int
    {
        return $this->_count;
    }

    /**
     * 获取搜索耗时
     * 单位/秒
     * @return int
     */
    public function getSecond(): int
    {
        return $this->_seconds;
    }

    /**
     * 获取字段集
     * @param string $name
     * @return array
     */
    public function getField(string $name=''): array
    {
        return $name ? $this->_app_field[$name] : $this->_app_field;
    }

    /**
     * 获取最后产生的错误
     * @return string
     */
    public function getError(): string
    {
        return $this->_error;
    }

    /**
     * 设置错误
     * @param string $error 信息信息
     */
    protected function setError(string $error)
    {
        $this->_error = $error;
    }
}