<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2014 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|
+---------------------------------------------------------------------------
*/

class Db
{
    private static $dbObject;
    private static $prefix;
    private static $config;
    private static $table;
    private static $select;
    private static $instance;
    protected $lastSqlString='';
    /**
     * @param string $driver
     * @param array $db_config
     * @param null $prefix
     * @throws Zend_Db_Exception
     * @throws Zend_Exception
     */
    public static function instance($driver='MySQLi',$db_config=[],$prefix=null)
    {
        if(!empty($db_config))
        {
            if(!isset($db_config['host']) || !isset($db_config['username']) || !isset($db_config['password']) || !isset($db_config['dbname']))
            {
                show_error('数据库配置不正确');
            }
            self::$config['driver'] = $driver;
            self::$config['prefix'] = $prefix;
            self::$config['db_config'] = $db_config;
        }else{
            self::$config['driver'] = load_class('core_config')->get('database')->driver;
            self::$config['prefix'] = load_class('core_config')->get('database')->prefix;
            self::$config['db_config'] = load_class('core_config')->get('database')->master;
        }

        self::$dbObject = Zend_Db::factory(self::$config['driver'], self::$config['db_config']);
        Zend_Registry::set('dbAdapter', self::$dbObject);
        Zend_Db_Table_Abstract::setDefaultAdapter(self::$dbObject);
        self::$select = self::$dbObject->select();

        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }


    /**
     * @param $table
     * @return $this
     */
    public function name($table)
    {
        self::$table = strstr($table,self::$config['prefix']) ? $table : self::$config['prefix'].$table;
        return $this;
    }

    /**
     * @param $cond
     * @param null $value
     * @param null $type
     * @return $this
     */
    public function where($cond, $value = null, $type = null){
        if(is_array($cond))
        {
            $tmp = array();
            foreach ($cond as $key=>$val)
            {
                $tmp[] = $key.'='.$val;
            }

            $cond = implode(' AND ',$tmp);
        }
        self::$select->where($cond, $value,$type);
        return $this;
    }

    public function orWhere($cond, $value = null, $type = null)
    {
        if(is_array($cond))
        {
            $cond = implode(' OR ',$cond);
        }
        self::$select->orWhere($cond, $value,$type);
        return $this;
    }

    public function group($spec)
    {
        self::$select->group($spec);
        return $this;
    }

    public function having($cond, $value = null, $type = null)
    {
        self::$select->having($cond, $value,$type);
        return $this;
    }

    public function orHaving($cond, $value = null, $type = null)
    {
        self::$select->orHaving($cond, $value,$type);
        return $this;
    }

    public function order($spec)
    {
        self::$select->order($spec);
        return $this;
    }

    public function begin_transaction(){
        self::$dbObject->beginTransaction();
    }

    public function roll_back()
    {
        self::$dbObject->rollBack();
    }

    /**
     * 事务处理提交
     * 此功能只在 Pdo 数据库驱动下有效
     */
    public function commit()
    {
        self::$dbObject->commit();
    }

    public function limit($count = null, $offset = null)
    {
        self::$select->limit($count,$offset);
        return $this;
    }

    /**
     * @param int $page
     * @param int $rowCount
     * @return $this
     */
    public function page($page=1, $rowCount=10)
    {
        self::$select->limitPage($page, $rowCount);
        return $this;
    }

    public function forUpdate($flag = true)
    {
        self::$select->forUpdate($flag);
        return $this;
    }

    public function query($sql, $bind = array())
    {
        self::$dbObject->query($sql,$bind);
        return $this;
    }

    public function bind($bind)
    {
        self::$select->bind($bind);
        return $this;
    }

    public function fetchRow($sql, $bind = array(), $fetchMode = null)
    {
        return self::$dbObject->fetchRow($sql,$bind,$fetchMode);
    }

    public function fetchAll($sql, $bind = array(), $fetchMode = null)
    {
        return self::$dbObject->fetchAll($sql,$bind,$fetchMode);
    }

    public function fetchAssoc($sql, $bind = array())
    {
        return self::$dbObject->fetchAssoc($sql,$bind);
    }

    public function fetchCol($sql, $bind = array())
    {
        return self::$dbObject->fetchCol($sql,$bind);
    }

    public function fetchPairs($sql, $bind = array())
    {
        return self::$dbObject->fetchPairs($sql,$bind);
    }

    public function fetchOne($sql, $bind = array())
    {
        return self::$dbObject->fetchOne($sql,$bind);
    }

    public function quote($string)
    {
        if (is_object(self::$dbObject))
        {
            $_quote = self::$dbObject->quote($string);

            if (substr($_quote, 0, 1) == "'")
            {
                $_quote = substr(substr($_quote, 1), 0, -1);
            }

            return $_quote;
        }

        if (function_exists('mysql_escape_string'))
        {
            $string = @mysql_escape_string($string);
        }
        else
        {
            $string = addslashes($string);
        }
        return $string;
    }

    public function distinct($flag = true)
    {
        self::$select->distinct($flag);
        return $this;
    }

    public function columns($cols = '*', $correlationName = null)
    {
        self::$select->columns($cols,$correlationName);
        return $this;
    }

    public function union($select = array(), $type = Zend_Db_Select::SQL_UNION)
    {
        self::$select->union($select,$type);
        return $this;
    }

    public function join($name, $cond,$type='inner', $cols = Zend_Db_Select::SQL_WILDCARD, $schema = null)
    {
        switch ($type)
        {
            case 'inner':
                self::$select->joinInner($name, $cond, $cols, $schema);
                break;

            case 'left':
                self::$select->joinLeft($name, $cond, $cols, $schema);
                break;

            case 'right':
                self::$select->joinRight($name, $cond, $cols, $schema);
                break;

            case 'full':
                self::$select->joinFull($name, $cond, $cols, $schema);
                break;

            case 'cross':
                self::$select->joinCross($name, $cond, $cols, $schema);
                break;

            case 'natural':
                self::$select->joinNatural($name, $cond, $cols, $schema);
                break;
        }
        return $this;
    }

    //查询全部数据
    public function select($where = null, $order = null, $limit = 0, $offset = 10)
    {
        self::$select->from(self::$table);
        if ($where)
        {
            $this->where($where);
        }
        if ($order)
        {
            $this->order($order);
        }
        if ($limit)
        {
            if (strstr($limit, ','))
            {
                $limit = explode(',', $limit);

                $this->limit(intval($limit[1]), intval($limit[0]));
            }
            else if ($offset)
            {
                $this->limit($limit, $offset);
            }
            else
            {
                $this->limit($limit);
            }
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchAll($sql);
    }

    //查询一组数据
    public function column($column=null,$where = null, $order = null)
    {
        self::$select->from(self::$table,$column ? $column : '*');
        if ($where)
        {
            $this->where($where);
        }
        if ($order)
        {
            $this->order($order);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchAll($sql);
    }

    //查询一行数据
    public function find($where = null, $order = null)
    {
        self::$select->from(self::$table);
        if ($where)
        {
            $this->where($where);
        }
        if ($order)
        {
            $this->order($order);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchRow($sql);
    }

    //查询单个数据
    public function value($field,$where = null, $order = null)
    {
        self::$select->from(self::$table,$field ? $field : '*');
        if ($where)
        {
            $this->where($where);
        }
        if ($order)
        {
            $this->order($order);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchOne($sql);
    }

    //插入数据
    public function insert($data)
    {
        return self::$dbObject->insert(self::$table,$data);
    }

    //插入拿数据并获取插入id
    public function insertGetId($data)
    {
        self::$dbObject->insert(self::$table,$data);
        return self::$dbObject->lastInsertId();
    }

    public function update($data,$where)
    {
        return self::$dbObject->update(self::$table,$data,$where);
    }

    public function delete($where = '')
    {
        return self::$dbObject->delete(self::$table, $where);
    }

    public function min($column, $where = ''){
        self::$select->from(self::$table,'MIN(' . $column . ') AS n');
        if ($where)
        {
            $this->where($where);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchOne($sql);
    }

    public function max($column, $where = ''){

        self::$select->from(self::$table,'MAX(' . $column . ') AS n');
        if ($where)
        {
            $this->where($where);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchOne($sql);
    }

    public function sum($column, $where = '')
    {
        self::$select->from(self::$table,'SUM(' . $column . ') AS n');
        if ($where)
        {
            $this->where($where);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;
        return self::$dbObject->fetchOne($sql);
    }

    public function count($where = '')
    {
        self::$select->from(self::$table,'COUNT(*) AS n');
        if ($where)
        {
            $this->where($where);
        }
        $sql = self::$select->__toString();
        $this->lastSqlString = $sql;

        $this->lastSqlString = $sql;
        return self::$dbObject->fetchOne($sql);
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array(self::$dbObject, $method), $args);
    }
}