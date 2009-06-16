<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractPdoUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Oracle.php';

class Zend_Db_TestSuite_DbUtility_PdoOciUtility extends Zend_Db_TestSuite_DbUtility_AbstractPdoUtility 
{

    public function getDriverConfigurationAsParams()
    {
        $constants = array(
            'TESTS_ZEND_DB_ADAPTER_ORACLE_HOSTNAME' => 'host',
            'TESTS_ZEND_DB_ADAPTER_ORACLE_USERNAME' => 'username',
            'TESTS_ZEND_DB_ADAPTER_ORACLE_PASSWORD' => 'password',
            'TESTS_ZEND_DB_ADAPTER_ORACLE_SID'      => 'dbname'
            );
        return parent::_getConstantAsParams($constants);
    }
    
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_Oracle();
    }

}
