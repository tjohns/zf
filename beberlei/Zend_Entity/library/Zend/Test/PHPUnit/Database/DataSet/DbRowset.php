<?php

class Zend_Test_PHPUnit_Database_DataSet_DbRowset extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * Construct Table object from a Zend_Db_Table_Rowset
     * 
     * @param Zend_Db_Table_Rowset_Abstract $rowset
     */
    public function __construct(Zend_Db_Table_Rowset_Abstract $rowset)
    {
        $this->data = $rowset->toArray();
    }
}