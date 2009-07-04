<?php

require_once "PHPUnit/Extensions/Database/DataSet/FlatXmlDataSet.php";

class Zend_Test_PHPUnit_Database_Operation_InsertTest extends PHPUnit_Framework_TestCase
{
    private $operation = null;

    public function setUp()
    {
        $this->operation = new Zend_Test_PHPUnit_Database_Operation_Insert();
    }

    public function testInsertDataSetUsingAdapterInsert()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/insertFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->at(0))
                    ->method('insert')
                    ->with('foo', array('foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'));
        $testAdapter->expects($this->at(1))
                    ->method('insert')
                    ->with('foo', array('foo' => 'bar', 'bar' => 'bar', 'baz' => 'bar'));
        $testAdapter->expects($this->at(2))
                    ->method('insert')
                    ->with('foo', array('foo' => 'baz', 'bar' => 'baz', 'baz' => 'baz'));

        $connection = new Zend_Test_PHPUnit_Database_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testInsertExceptionIsTransformed()
    {
        $this->setExpectedException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/insertFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->any())->method('insert')->will($this->throwException(new Exception()));

        $connection = new Zend_Test_PHPUnit_Database_Connection($testAdapter, "schema");
        $this->operation->execute($connection, $dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->setExpectedException("Zend_Test_PHPUnit_Database_Exception");

        $dataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $connection = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');
        
        $this->operation->execute($connection, $dataSet);
    }
}