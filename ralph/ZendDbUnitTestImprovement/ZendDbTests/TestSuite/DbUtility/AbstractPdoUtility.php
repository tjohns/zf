<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractUtility.php';

abstract class Zend_Db_TestSuite_DbUtility_AbstractPdoUtility extends Zend_Db_TestSuite_DbUtility_AbstractUtility
{
    
    protected function _executeRawQuery($sql)
    {
        $conn = $this->_dbAdapter->getConnection();
        $retval = $conn->exec($sql);
        if ($retval === false) {
            $e = $conn->error;
            require_once 'Zend/Db/Exception.php';
            throw new Zend_Db_Exception("SQL error for \"$sql\": $e");
        }
    }
    
}