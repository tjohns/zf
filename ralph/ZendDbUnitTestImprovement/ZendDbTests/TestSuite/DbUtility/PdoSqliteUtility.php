<?php

require_once 'Zend/Db/TestSuite/DbUtility/AbstractPdoUtility.php';

require_once 'Zend/Db/TestSuite/DbUtility/SQLDialect/Sqlite.php';

class Zend_Db_TestSuite_DbUtility_PdoSqliteUtility extends Zend_Db_TestSuite_DbUtility_AbstractPdoUtility 
{

    public function getDriverConfigurationAsParams()
    {
        $constants = array(
            'TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_DATABASE' => 'dbname',
            );
        return parent::_getConstantAsParams($constants);
    }
    
    public function getSchema()
    {
        return null;
    }
    
    public function getSQLDialect()
    {
        return new Zend_Db_TestSuite_DbUtility_SQLDialect_Sqlite();
    }


}
