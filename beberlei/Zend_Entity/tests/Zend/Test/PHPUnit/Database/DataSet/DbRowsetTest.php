<?php

class Zend_Test_PHPUnit_Database_DataSet_DbRowsetTest extends PHPUnit_Framework_TestCase
{
    protected function getRowSet()
    {
        $config = array(
            'rowClass' => 'stdClass',
            'data'     => array(array('foo' => 'bar'), array('foo' => 'baz')),
        );
        $rowset = new Zend_Db_Table_Rowset($config);
        return $rowset;
    }

    public function testRowsetCountInITableRepresentation()
    {
        $rowsetTable = new Zend_Test_PHPUnit_Database_DataSet_DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals(2, $rowsetTable->getRowCount());
    }

    public function testRowsetGetSpecificValue()
    {
        $rowsetTable = new Zend_Test_PHPUnit_Database_DataSet_DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals("bar", $rowsetTable->getValue(0, "foo"));
    }

    public function testRowsetGetSpecificRow()
    {
        $rowsetTable = new Zend_Test_PHPUnit_Database_DataSet_DbRowset($this->getRowSet(), "fooTable");
        $this->assertEquals(array("foo" => "baz"), $rowsetTable->getRow(1));
    }
}