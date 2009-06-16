<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Oracle.php';

class Zend_Db_TestSuite_DbUtility_OracleUtility extends Zend_Db_TestSuite_DbUtility_AbstractUtility 
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

    protected function _executeRawQuery($sql)
    {
        $conn = $this->_dbAdapter->getConnection();
        $stmt = oci_parse($conn, $sql);
        if (!$stmt) {
            $e = oci_error($conn);
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL parse error for \"$sql\": ".$e['message']);
        }
        $retval = oci_execute($stmt);
        if (!$retval) {
            $e = oci_error($conn);
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL execute error for \"$sql\": ".$e['message']);
        }
    }

}
