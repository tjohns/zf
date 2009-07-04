<?php

require_once "PHPUnit/Extensions/Database/DataSet/QueryTable.php";

class Zend_Test_PHPUnit_Database_DataSet_QueryTable extends PHPUnit_Extensions_Database_DataSet_QueryTable
{
    /**
     * Creates a new database query table object.
     *
     * @param string $table_name
     * @param string $query
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct($tableName, $query, PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection->getConnection() instanceof Zend_Db_Adapter_Abstract) ) {
            require_once "Zend/Test/PHPUnit/Database/Exception.php";
            throw new Zend_Test_PHPUnit_Database_Exception();
        }
        parent::__construct($tableName, $query, $databaseConnection);
    }

    protected function loadData()
    {
        if($this->data === null) {
            $stmt = $this->databaseConnection->getConnection()->query($this->query);
            $this->data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        }
    }
}