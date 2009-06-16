<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Db2.php';

class Zend_Db_TestSuite_DbUtility_Db2Utility extends Zend_Db_TestSuite_DbUtility_AbstractUtility 
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

    protected function _executeRawQuery($sql)
    {
        $conn = $this->_dbAdapter->getConnection();
        $result = db2_exec($conn, $sql);

        if (!$result) {
            $e = db2_stmt_errormsg();
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL error for \"$sql\": $e");
        }
    }

}
