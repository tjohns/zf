<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractPdoUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/MySQL.php';

class Zend_Db_TestSuite_DbUtility_PdoMysqlUtility extends Zend_Db_TestSuite_DbUtility_AbstractPdoUtility 
{

    public function getDriverConfigurationAsParams()
    {
        $constants = array(
            'TESTS_ZEND_DB_ADAPTER_MYSQL_HOSTNAME' => 'host',
            'TESTS_ZEND_DB_ADAPTER_MYSQL_USERNAME' => 'username',
            'TESTS_ZEND_DB_ADAPTER_MYSQL_PASSWORD' => 'password',
            'TESTS_ZEND_DB_ADAPTER_MYSQL_DATABASE' => 'dbname',
            'TESTS_ZEND_DB_ADAPTER_MYSQL_PORT'     => 'port'
            );
        return parent::_getConstantAsParams($constants);
    }
    
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_MySQL();
    }


}
