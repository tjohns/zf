<?php

class Zend_Test_PHPUnit_Database_DataSet_DbTable extends PHPUnit_Extensions_Database_DataSet_QueryTable
{
    /**
     * Zend_Db_Table object
     * 
     * @var Zend_Db_Table_Abstract
     */
    protected $_table = null;

    /**
     * Construct Dataset Table from Zend_Db_Table object
     *
     * @param Zend_Db_Table_Abstract        $table
     * @param string|Zend_Db_Select|null    $where
     * @param string|null                   $order
     * @param int                           $count
     * @param int                           $offset
     */
    public function __construct(Zend_Db_Table_Abstract $table, $where=null, $order=null, $count=null, $offset=null)
    {
        $this->_table = $table;
    }

    /**
     * Lazy load data via table fetchAll() method.
     *
     * @return void
     */
    protected function loadData()
    {
        if ($this->data === null) {
            $this->data = $this->_table->fetchAll($this->_where, $this->_order, $this->_count, $this->_offset);
        }
    }
}