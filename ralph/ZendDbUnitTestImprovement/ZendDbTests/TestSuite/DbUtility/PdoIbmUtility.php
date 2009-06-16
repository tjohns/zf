<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractPdoUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Db2.php';

class Zend_Db_TestSuite_DbUtility_PdoIbmUtility extends Zend_Db_TestSuite_DbUtility_AbstractPdoUtility 
{

    public function getDriverConfigurationAsParams()
    {
        $constants = array(
            'TESTS_ZEND_DB_ADAPTER_DB2_HOSTNAME' => 'host',
            'TESTS_ZEND_DB_ADAPTER_DB2_USERNAME' => 'username',
            'TESTS_ZEND_DB_ADAPTER_DB2_PASSWORD' => 'password',
            'TESTS_ZEND_DB_ADAPTER_DB2_DATABASE' => 'dbname',
            'TESTS_ZEND_DB_ADAPTER_DB2_PORT'     => 'port'
            );
        return parent::_getConstantAsParams($constants);
    }
    
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_Db2();
    }


}
