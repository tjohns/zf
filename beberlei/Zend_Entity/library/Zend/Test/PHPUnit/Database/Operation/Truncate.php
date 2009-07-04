<?php

require_once "PHPUnit/Extensions/Database/Operation/IDatabaseOperation.php";

class Zend_Test_PHPUnit_Database_Operation_Truncate implements PHPUnit_Extensions_Database_Operation_IDatabaseOperation
{
    /**
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $connection
     * @param PHPUnit_Extensions_Database_DataSet_IDataSet $dataSet
     * @return void
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
                $this->truncate($connection->getConnection(), $tableName);
            } catch (Exception $e) {
                throw new PHPUnit_Extensions_Database_Operation_Exception('TRUNCATE', 'TRUNCATE '.$tableName.'', array(), $table, $e->getMessage());
            }
        }
    }

    /**
     * Truncate a given table.
     * 
     * @param Zend_Db_Adapter_Abstract $db
     * @param string $tableName
     * @return void
     */
    private function truncate(Zend_Db_Adapter_Abstract $db, $tableName)
    {
        $tableName = $db->quoteIdentifier($tableName);
        if($db instanceof Zend_Db_Adapter_Pdo_Sqlite) {
            $db->query('DELETE FROM '.$tableName);
        } else if($db instanceof Zend_Db_Adapter_Db2) {
            if(strstr(PHP_OS, "WIN")) {
                $file = tempnam(sys_get_temp_dir(), "zendtestdbibm_");
                file_put_contents($file, "");
                $db->query('IMPORT FROM '.$file.' OF DEL REPLACE INTO '.$tableName);
                unlink($file);
            } else {
                $db->query('IMPORT FROM /dev/null OF DEL REPLACE INTO '.$tableName);
            }
        } else {
            $db->query('TRUNCATE '.$tableName);
        }
    }
}