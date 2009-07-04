<?php

class Zend_Test_PHPUnit_Database_Operation_DeleteAllTest extends PHPUnit_Framework_TestCase
{
    private $operation = null;

    public function setUp()
    {
        $this->operation = new Zend_Test_PHPUnit_Database_Operation_DeleteAll();
    }

    public function testDeleteAll()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/truncateFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->at(0))
                    ->method('delete')
                    ->with('foo');
        $testAdapter->expects($this->at(1))
                    ->method('delete')
                    ->with('bar');

        $connection = new Zend_Test_PHPUnit_Database_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testDeleteQueryErrorTransformsException()
    {
        $this->setExpectedException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/truncateFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new Exception));

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