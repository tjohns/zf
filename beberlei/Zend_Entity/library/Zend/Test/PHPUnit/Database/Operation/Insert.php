<?php

require_once "PHPUnit/Extensions/Database/Operation/IDatabaseOperation.php";

class Zend_Test_PHPUnit_Database_Operation_Insert implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
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

        $databaseDataSet = $connection->createDataSet();

        $dsIterator = $dataSet->getIterator();

        foreach($dsIterator as $table) {
            $tableName = $table->getTableMetaData()->getTableName();

            $db = $connection->getConnection();
            for($i = 0; $i < $table->getRowCount(); $i++) {
                $values = $this->buildInsertValues($table, $i);
                try {
                    $db->insert($tableName, $values);
                } catch (Exception $e) {
                    throw new PHPUnit_Extensions_Database_Operation_Exception("INSERT", "INSERT INTO ".$tableName." [..]", $values, $table, $e->getMessage());
                }
            }
        }
    }

    protected function buildInsertValues(PHPUnit_Extensions_Database_DataSet_ITable $table, $rowNum)
    {
        $values = array();
        foreach($table->getTableMetaData()->getColumns() as $columnName) {
            $values[$columnName] = $table->getValue($rowNum, $columnName);
        }
        return $values;
    }
}