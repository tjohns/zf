<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/MySQL.php';

class Zend_Db_TestSuite_DbUtility_MysqliUtility extends Zend_Db_TestSuite_DbUtility_AbstractUtility 
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

    protected function _executeRawQuery($sql)
    {
        $mysqli = $this->_dbAdapter->getConnection();
        $retval = $mysqli->query($sql);
        if (!$retval) {
            $e = $mysqli->error;
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL error for \"$sql\": $e");
        }
    }

}
