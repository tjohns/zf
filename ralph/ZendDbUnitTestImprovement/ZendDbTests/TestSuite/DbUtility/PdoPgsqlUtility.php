<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractPdoUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/PostgreSQL.php';

class Zend_Db_TestSuite_DbUtility_PdoPgsqlUtility extends Zend_Db_TestSuite_DbUtility_AbstractPdoUtility 
{

    public function getDriverConfigurationAsParams()
    {
        $constants = array(
            'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_HOSTNAME' => 'host',
            'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_USERNAME' => 'username',
            'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_PASSWORD' => 'password',
            'TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_DATABASE' => 'dbname'
            );
        return parent::_getConstantAsParams($constants);
    }
    
    public function getSchema()
    {
        return 'public';
    }
    
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_PostgreSQL();
    }


}
