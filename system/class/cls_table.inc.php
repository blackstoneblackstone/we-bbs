<?php
class Table extends Zend_Db_Table
{
    public function setTableName($table)
    {
        $this->_name = $table;
    }

    public function setPrimaryKey($primaryKey)
    {
        $this->_primary = $primaryKey;
    }
}