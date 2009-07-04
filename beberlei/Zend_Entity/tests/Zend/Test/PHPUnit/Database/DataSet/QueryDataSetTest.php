<?php

require_once "Zend/Test/PHPUnit/Database/DataSet/DataSetTestCase.php";

class Zend_Test_PHPUnit_Database_DataSet_QueryDataSetTest extends Zend_Test_PHPUnit_Database_DataSet_DataSetTestCase
{
    public function testCreateQueryDataSetWithoutZendDbAdapterThrowsException()
    {
        $this->setExpectedException('Zend_Test_PHPUnit_Database_Exception');
        $queryDataSet = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->connectionMock);
    }

    public function testCreateQueryDataSetWithZendDbAdapter()
    {
        $this->decorateConnectionMockWithZendAdapter();
        $queryDataSet = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->connectionMock);
    }

    public function testAddTableWithoutQueryParameterCreatesSelectWildcardAll()
    {
        $fixtureTableName = "foo";

        $adapterMock = $this->getMock('Zend_Test_DbAdapterMock');
        $selectMock = $this->getMock('Zend_Db_Select', array(), array($adapterMock));

        $adapterMock->expects($this->once())
                    ->method('select')
                    ->will($this->returnValue($selectMock));
        $this->decorateConnectionGetConnectionWith($adapterMock);

        $selectMock->expects($this->once())
                   ->method('from')
                   ->with($fixtureTableName, Zend_Db_Select::SQL_WILDCARD);
        $selectMock->expects($this->once())
                   ->method('__toString')
                   ->will($this->returnValue('SELECT * FOM foo'));

        $queryDataSet = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->connectionMock);
        $queryDataSet->addTable('foo');
    }
}