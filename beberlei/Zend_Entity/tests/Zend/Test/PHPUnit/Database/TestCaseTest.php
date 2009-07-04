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
    protected $_connectionMock = null;

    /**
     * Returns the test database connection.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected function getConnection()
    {
        if($this->_connectionMock == null) {
            $this->_connectionMock = $this->getMock(
                'Zend_Test_PHPUnit_Database_Connection', array(), array(new Zend_Test_DbAdapterMock(), "schema")
            );
        }
        return $this->_connectionMock;
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
        $this->assertTrue($this->createZendDbConnection($mock, "test") instanceof Zend_Test_PHPUnit_Database_Connection);
    }

    public function testCreateDbTableDataSetConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table', array(), array(), "", false);
        $tableDataSet = $this->createDbTableDataSet(array($mock));
        $this->assertTrue($tableDataSet instanceof Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet);
    }

    public function testCreateDbTableConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table', array(), array(), "", false);
        $table = $this->createDbTable($mock);
        $this->assertTrue($table instanceof Zend_Test_PHPUnit_Database_DataSet_DbTable);
    }

    public function testCreateDbRowsetConvenienceMethodReturnType()
    {
        $mock = $this->getMock('Zend_Db_Table_Rowset', array(), array(array()));
        $rowset = $this->createDbRowset($mock);

        $this->assertTrue($rowset instanceof Zend_Test_PHPUnit_Database_DataSet_DbRowset);
    }

    public function testGetAdapterConvenienceMethod()
    {
        $this->_connectionMock->expects($this->once())
                              ->method('getConnection');
        $this->getAdapter();
    }
}