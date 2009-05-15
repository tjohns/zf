<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";
require_once "PHPUnit/Extensions/Database/DataSet/CompositeDataSet.php";

class Zend_Test_PHPUnit_Database_TestCaseTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     * Contains a Database Connection
     * 
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $_connection = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        $mock = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');
        return $mock;
    }

    /**
     * Returns the test dataset.
     *
     * @return PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    protected function getDataSet()
    {
        return new PHPUnit_Extensions_Database_DataSet_CompositeDataSet(array());
    }

    public function testDatabaseTesterIsInitialized()
    {
        $this->assertTrue($this->databaseTester instanceof PHPUnit_Extensions_Database_ITester);
    }

    public function testDatabaseTesterNestsDefaultConnection()
    {
        $this->assertTrue($this->databaseTester->getConnection() instanceof PHPUnit_Extensions_Database_DB_IDatabaseConnection);
    }

    public function testCheckZendDbConnectionConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Adapter_Pdo_Sqlite', array('delete'), array(), "Zend_Db_Adapter_Mock", false);
        $this->assertTrue($this->createZendDbConnection($mock, "test") instanceof PHPUnit_Extensions_Database_DB_IDatabaseConnection);
    }

    public function testCreateDbTableDataSetConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table', array(), array(), "Zend_Db_Table_Mock", false);
        $tableDataSet = $this->createDbTableDataSet(array($mock));
        $this->assertTrue($tableDataSet instanceof Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet);
    }

    public function testCreateDbTableConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table', array(), array(), "Zend_Db_Table_Mock2", false);
        $table = $this->createDbTable($mock);
        $this->assertTrue($table instanceof Zend_Test_PHPUnit_Database_DataSet_DbTable);
    }
}