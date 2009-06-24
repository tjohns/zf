<?php

require_once 'Zend/Db/TestSuite/DbUtility/Db2Utility.php';

class Zend_Db_TestSuite_DbUtility_PdoIbmUtility extends Zend_Db_TestSuite_DbUtility_Db2Utility
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
    
    public function getServer()
    {
        return substr($this->_db->getConnection()->getAttribute(PDO::ATTR_SERVER_INFO), 0, 3);
    }

}
