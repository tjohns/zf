<?php

class Zend_Test_PHPUnit_Database_Metadata_GenericTest extends PHPUnit_Framework_TestCase
{
    private $adapterMock = null;

    private $metadata = null;

    public function setUp()
    {
        $this->adapterMock = $this->getMock('Zend_Test_DbAdapterMock');
        $this->metadata = new Zend_Test_PHPUnit_Database_Metadata_Generic($this->adapterMock, "schema");
    }

    public function testGetSchema()
    {
        $this->assertEquals("schema", $this->metadata->getSchema());
    }

    public function testGetColumnNames()
    {
        $fixtureTableName = "foo";

        $this->adapterMock->expects($this->once())
                          ->method('describeTable')
                          ->with($fixtureTableName)
                          ->will($this->returnValue(array("foo" => 1, "bar" => 2)));
        $data = $this->metadata->getTableColumns($fixtureTableName);

        $this->assertEquals(array("foo", "bar"), $data);
    }

    public function testGetTableNames()
    {
        $this->adapterMock->expects($this->once())
                          ->method('listTables')
                          ->will($this->returnValue(array("foo")));
        $tables = $this->metadata->getTableNames();

        $this->assertEquals(array("foo"), $tables);
    }

    public function testGetTablePrimaryKey()
    {
        $fixtureTableName = "foo";

        $tableMeta = array(
            array('PRIMARY' => false, 'COLUMN_NAME' => 'foo'),
            array('PRIMARY' => true, 'COLUMN_NAME' => 'bar'),
            array('PRIMARY' => true, 'COLUMN_NAME' => 'baz'),
        );

        $this->adapterMock->expects($this->once())
                          ->method('describeTable')
                          ->with($fixtureTableName)
                          ->will($this->returnValue($tableMeta));

        $primaryKey = $this->metadata->getTablePrimaryKeys($fixtureTableName);
        $this->assertEquals(array("bar", "baz"), $primaryKey);
    }

    public function testGetAllowCascading()
    {
        $this->assertFalse($this->metadata->allowsCascading());
    }

    public function testQuoteIdentifierIsDelegated()
    {
        $fixtureValue = "foo";

        $this->adapterMock->expects($this->once())
                          ->method('quoteIdentifier')
                          ->with($fixtureValue)
                          ->will($this->returnValue($fixtureValue));

        $actualValue = $this->metadata->quoteSchemaObject($fixtureValue);

        $this->assertEquals($fixtureValue, $actualValue);
    }
}