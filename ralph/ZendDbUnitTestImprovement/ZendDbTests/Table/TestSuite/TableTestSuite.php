<?php

require_once 'Zend/Db/Table/TestSuite/TableUtility.php';

class Zend_Db_Table_TestSuite_TableTestSuite extends PHPUnit_Framework_TestSuite
{
    
    protected $_suiteName  = null;
    
    public function __construct($suiteName)
    {
        $this->_suiteName = $suiteName;
        parent::__construct();
        
        $this->setName(get_class($this) . ' - Suite for Zend_Db_Adapter_' . $this->_suiteName);

        Zend_Loader::loadClass("Zend_Db_Table_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Table_{$this->_suiteName}Test");
        
        Zend_Loader::loadClass("Zend_Db_Table_Select_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Table_Select_{$this->_suiteName}Test");
        
        Zend_Loader::loadClass("Zend_Db_Table_Rowset_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Table_Rowset_{$this->_suiteName}Test");
        
        Zend_Loader::loadClass("Zend_Db_Table_Row_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Table_Row_{$this->_suiteName}Test");

        Zend_Loader::loadClass("Zend_Db_Table_Relationships_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Table_Relationships_{$this->_suiteName}Test");
        
    }
        
    public function setup()
    {
        $this->sharedFixture->tableUtility = new Zend_Db_Table_TestSuite_TableUtility(
            $this->sharedFixture->dbAdapter
            );
    }
    
    public function teardown()
    {
        unset($this->sharedFixture->tableUtility);
    }

}



