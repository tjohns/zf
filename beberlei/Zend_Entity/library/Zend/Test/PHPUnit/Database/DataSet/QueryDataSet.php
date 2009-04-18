<?php

class Zend_Test_PHPUnit_Database_DataSet_QueryDataSet extends PHPUnit_Extensions_Database_DataSet_QueryDataSet
{
    /**
     * Creates a new dataset using the given database connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection->getConnection() instanceof Zend_Db_Adapter_Abstract) ) {
            throw new Zend_Test_PHPUnit_Database_Exception();
        }
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Add a Table dataset representation by specifiying an arbitrary select query.
     *
     * By default a select * will be done on the given tablename.
     *
     * @param string                $tableName
     * @param string|Zend_Db_Select $query
     */
    public function addTable($tableName, $query = NULL)
    {
        if ($query === NULL) {
            $query = $this->databaseConnection->getConnection()->select();
            $query->from($tableName, Zend_Db_Select::SQL_WILDCARD);
        }

        if($query instanceof Zend_Db_Select) {
            $query = $query->__toString();
        }

        $this->tables[$tableName] = new Zend_Test_PHPUnit_Database_DataSet_QueryTable($tableName, $query, $this->databaseConnection);
    }
}