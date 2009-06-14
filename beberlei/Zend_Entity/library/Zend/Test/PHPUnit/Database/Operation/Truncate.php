<?php

class Zend_Test_PHPUnit_Database_Operation_Truncate extends Zend_Test_PHPUnit_Database_Operation_DeleteAll
{
    public function execute(PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection, PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet)
    {
        foreach ($dataSet as $table) {
            try {
                $tableName = $table->getTableMetaData()->getTableName();
                $connection->getConnection()->query("TRUNCATE $tableName");
            } catch (Zend_Db_Exception $e) {
                throw new PHPUnit_Extensions_Database_Operation_Exception('TRUNCATE', 'TRUNCATE '.$tableName.'', array(), $table, $e->getMessage());
            }
        }
    }
}