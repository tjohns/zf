<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractPdoUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/MSSQL.php';

class Zend_Db_TestSuite_DbUtility_PdoMssqlUtility extends Zend_Db_TestSuite_DbUtility_AbstractPdoUtility 
{

    public function getDriverConfigurationAsParams()
    {
        $constants = array(
            'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_HOSTNAME' => 'host',
            'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_USERNAME' => 'username',
            'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PASSWORD' => 'password',
            'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_DATABASE' => 'dbname',
            'TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_PORT'     => 'port'
            );
        return parent::_getConstantAsParams($constants);
    }
    
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_MSSQL();
    }


}
