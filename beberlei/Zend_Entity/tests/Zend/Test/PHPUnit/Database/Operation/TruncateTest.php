<?php

class Zend_Test_PHPUnit_Database_Operation_TruncateTest extends PHPUnit_Framework_TestCase
{
    private $operation = null;

    public function setUp()
    {
        $this->operation = new Zend_Test_PHPUnit_Database_Operation_Truncate();
    }

    public function testTruncateTablesExecutesAdapterQuery()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/truncateFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->at(0))
                    ->method('quoteIdentifier')
                    ->with('foo')->will($this->returnValue('foo'));
        $testAdapter->expects($this->at(1))
                    ->method('query')
                    ->with('TRUNCATE foo');
        $testAdapter->expects($this->at(2))
                    ->method('quoteIdentifier')
                    ->with('bar')->will($this->returnValue('bar'));
        $testAdapter->expects($this->at(3))
                    ->method('query')
                    ->with('TRUNCATE bar');

        $connection = new Zend_Test_PHPUnit_Database_Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testTruncateTableInvalidQueryTransformsException()
    {
        $this->setExpectedException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(dirname(__FILE__)."/_files/insertFixture.xml");

        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->any())->method('query')->will($this->throwException(new Exception()));

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