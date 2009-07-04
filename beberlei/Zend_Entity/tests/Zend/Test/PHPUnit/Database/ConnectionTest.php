<?php

require_once "Zend/Test/DbAdapterMock.php";
require_once "Zend/Test/PHPUnit/Database/Connection.php";

class Zend_Test_PHPUnit_Database_ConnectionTest extends PHPUnit_Framework_TestCase
{
    protected $adapterMock;

    public function setUp()
    {
        $this->adapterMock = $this->getMock('Zend_Test_DbAdapterMock');
    }

    /**
     * @return Zend_Test_PHPUnit_Database_Connection
     */
    public function createConnection()
    {
        $connection = new Zend_Test_PHPUnit_Database_Connection($this->adapterMock, "schema");
        return $connection;
    }

    public function testCloseConnection()
    {
        $this->adapterMock->expects($this->once())
                    ->method('closeConnection');

        $connection = $this->createConnection();
        $connection->close();
    }

    public function testCreateQueryTable()
    {
        $connection = $this->createConnection();
        $ret = $connection->createQueryTable("foo", "foo");

        $this->assertType('Zend_Test_PHPUnit_Database_DataSet_QueryTable', $ret);
    }

    public function testGetSchema()
    {
        $fixtureSchema = "schema";
        $connection = new Zend_Test_PHPUnit_Database_Connection($this->adapterMock, $fixtureSchema);

        $this->assertEquals($fixtureSchema, $connection->getSchema());
    }

    public function testGetMetaData()
    {
        $connection = $this->createConnection();
        $metadata = $connection->getMetaData();

        $this->assertType('Zend_Test_PHPUnit_Database_Metadata_Generic', $metadata);
    }

    public function testGetTruncateCommand()
    {
        $connection = $this->createConnection();

        $this->assertEquals("DELETE", $connection->getTruncateCommand());
    }
}