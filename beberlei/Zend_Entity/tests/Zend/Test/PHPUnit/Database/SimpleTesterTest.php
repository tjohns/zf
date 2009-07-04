<?php

require_once "Zend/Test/PHPUnit/Database/SimpleTester.php";
require_once "Zend/Test/PHPUnit/Database/Connection.php";
require_once "Zend/Test/DbAdapterMock.php";
require_once "PHPUnit/Extensions/Database/DataSet/IDataSet.php";
require_once "Zend/Test/PHPUnit/Database/Exception.php";

class Zend_Test_PHPUnit_Database_SimpleTesterTest extends PHPUnit_Framework_TestCase
{
    public function testGetConnection()
    {
        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new Exception));

        $connection = new Zend_Test_PHPUnit_Database_Connection($testAdapter, "schema");

        $databaseTester = new Zend_Test_PHPUnit_Database_SimpleTester($connection);

        $this->assertSame($connection, $databaseTester->getConnection());
    }

    public function testSetupDatabase()
    {
        $testAdapter = $this->getMock('Zend_Test_DbAdapterMock');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new Exception));

        $connection = new Zend_Test_PHPUnit_Database_Connection($testAdapter, "schema");

        $databaseTester = new Zend_Test_PHPUnit_Database_SimpleTester($connection);

        $dataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $dataSet->expects($this->any())->method('getIterator')->will($this->returnValue($this->getMock('Iterator')));
        $databaseTester->setUpDatabase($dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->setExpectedException("Zend_Test_PHPUnit_Database_Exception");

        $connection = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $databaseTester = new Zend_Test_PHPUnit_Database_SimpleTester($connection);
    }
}