<?php

class Zend_Test_PHPUnit_Database_Operation_DeleteAll extends PHPUnit_Extensions_Database_Operation_Truncate
{
    /**
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     */
    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        if(!($connection instanceof Zend_Test_PHPUnit_Database_Connection)) {
            require_once "Zend/Test/PHPUnit/Database/Exception.php";
            throw new Zend_Test_PHPUnit_Database_Exception("Not a valid Zend_Test_PHPUnit_Database_Connection instance, ".get_class($connection)." given!");
        }

        foreach ($dataSet as $table) {
            try {
                $tableName = $table->getTableMetaData()->getTableName();
                $connection->getConnection()->delete($tableName);
            } catch (Exception $e) {
                throw new PHPUnit_Extensions_Database_Operation_Exception('DELETEALL', 'DELETE FROM '.$tableName.'', array(), $table, $e->getMessage());
            }
        }
    }
}