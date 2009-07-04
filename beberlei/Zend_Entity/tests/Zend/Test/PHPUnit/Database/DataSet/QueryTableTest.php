<?php

require_once "Zend/Test/PHPUnit/Database/DataSet/DataSetTestCase.php";

class Zend_Test_PHPUnit_Database_DataSet_QueryTableTest extends Zend_Test_PHPUnit_Database_DataSet_DataSetTestCase
{
    public function testCreateQueryTableWithoutZendDbConnectionThrowsException()
    {
        $connectionMock = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $this->setExpectedException('Zend_Test_PHPUnit_Database_Exception');
        $queryTable = new Zend_Test_PHPUnit_Database_DataSet_QueryTable("foo", "SELECT * FROM foo", $connectionMock);
    }

    public function testCreateQueryTableWithZendDbConnection()
    {
        $this->decorateConnectionMockWithZendAdapter();
        $queryTable = new Zend_Test_PHPUnit_Database_DataSet_QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
    }

    public function testLoadDataExecutesQueryOnZendAdapter()
    {
        $statementMock = new Zend_Test_DbStatementMock();
        $statementMock->appendToFetchStack(array('foo' => 'bar'));
        $adapterMock = new Zend_Test_DbAdapterMock();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new Zend_Test_PHPUnit_Database_DataSet_QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
        $data = $queryTable->getRow(0);

        $this->assertEquals(
            array("foo" => "bar"), $data
        );
    }

    public function testGetRowCountLoadsData()
    {
        $statementMock = new Zend_Test_DbStatementMock();
        $statementMock->appendToFetchStack(array('foo' => 'bar'));
        $adapterMock = new Zend_Test_DbAdapterMock();
        $adapterMock->appendStatementToStack($statementMock);

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new Zend_Test_PHPUnit_Database_DataSet_QueryTable("foo", "SELECT * FROM foo", $this->connectionMock);
        $count = $queryTable->getRowCount();

        $this->assertEquals(1, $count);
    }

    public function testDataIsLoadedOnlyOnce()
    {
        $fixtureSql = "SELECT * FROM foo";

        $statementMock = new Zend_Test_DbStatementMock();
        $statementMock->appendToFetchStack(array('foo' => 'bar'));
        $adapterMock = $this->getMock('Zend_Test_DbAdapterMock');
        $adapterMock->expects($this->once())
                    ->method('query')
                    ->with($fixtureSql)
                    ->will($this->returnValue($statementMock));

        $this->decorateConnectionGetConnectionWith($adapterMock);

        $queryTable = new Zend_Test_PHPUnit_Database_DataSet_QueryTable("foo", $fixtureSql, $this->connectionMock);
        $queryTable->getRowCount();
        $queryTable->getRowCount();
        $row = $queryTable->getRow(0);
    }
}