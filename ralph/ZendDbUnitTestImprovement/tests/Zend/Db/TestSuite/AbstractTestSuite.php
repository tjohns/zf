<?php

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Loader.php';
require_once 'Zend/Db.php';

abstract class Zend_Db_TestSuite_AbstractTestSuite extends PHPUnit_Framework_TestSuite
{
    
    protected $_driverName = null;
    protected $_suiteName  = null;
    
    protected static function _mainRunner($testSuiteName, $arguments = array())
    {
        $runnerArguments = array();
        if (is_string($arguments) && $arguments != '') {
            parse_str($arguments, $runnerArguments);
        }
        PHPUnit_TextUI_TestRunner::run(new $testSuiteName(), $runnerArguments);
    }
    
    public function __construct()
    {
        parent::__construct();
        
        // set the test name
        $this->setName(get_class($this) . ' - Suite for Zend_Db_Adapter_' . $this->_driverName);
        
        // set the driver and suite name
        $this->_driverName = $this->getDriverName();
        $this->_suiteName = str_replace('_', '', $this->_driverName);
        
        // load the necessary classes
        Zend_Loader::loadClass("Zend_Db_Adapter_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Adapter_{$this->_suiteName}Test");
        
        Zend_Loader::loadClass("Zend_Db_Profiler_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Profiler_{$this->_suiteName}Test");

        Zend_Loader::loadClass("Zend_Db_Statement_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Statement_{$this->_suiteName}Test");

        Zend_Loader::loadClass("Zend_Db_Select_{$this->_suiteName}Test");
        $this->addTestSuite("Zend_Db_Select_{$this->_suiteName}Test");

        Zend_Loader::loadClass("Zend_Db_Table_TestSuite_TableTestSuite");
        $this->addTestSuite(new Zend_Db_Table_TestSuite_TableTestSuite($this->_suiteName));        

    }
    
    abstract public function getDriverName();
    
    public function getSuiteName()
    {
        return $this->_suiteName;
    }
    
    public function setUp()
    {
        $this->sharedFixture = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        $utilityClass = 'Zend_Db_TestSuite_DbUtility_' . $this->_suiteName . 'Utility';
        Zend_Loader::loadClass($utilityClass);
        $this->sharedFixture->dbUtility = new $utilityClass($this);
        
        // create the adapter
        $this->sharedFixture->dbAdapter = $this->sharedFixture->dbUtility->getDbAdapter();
        try {
            $conn = $this->sharedFixture->dbAdapter->getConnection();
        } catch (Zend_Exception $e) {
            unset($this->sharedFixture->dbAdapter);
            $this->markTestSuiteSkipped($e->getMessage());
        }
        $this->sharedFixture->dbUtility->createDefaultResources();
        $this->sharedFixture->dbUtility->setCanManageResources(false);
    }
    
    public function tearDown()
    {
        $this->sharedFixture->dbUtility->setCanManageResources(true);
        $this->sharedFixture->dbUtility->cleanupResources();
        unset($this->sharedFixture->dbAdapter);
        unset($this->sharedFixture->dbUtility);
        unset($this->sharedFixture);
    }
}