<?php

class Zend_Test_PHPUnit_Database_DataSet_DbTableTest extends PHPUnit_Framework_TestCase
{
    public function testLoadDataSetDelegatesWhereLimitOrderBy()
    {
        $fixtureWhere = "where";
        $fixtureLimit = "limit";
        $fixtureOffset = "offset";
        $fixtureOrderBy = "order";

        $table = $this->getMock('Zend_Db_Table', array(), array(), '', false);
        $table->expects($this->once())
              ->method('fetchAll')
              ->with($fixtureWhere, $fixtureOrderBy, $fixtureLimit, $fixtureOffset)
              ->will($this->returnValue(array()));

        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTable($table, $fixtureWhere, $fixtureOrderBy, $fixtureLimit, $fixtureOffset);
        $count = $dataSet->getRowCount();
    }

    public function testGetTableMetadata()
    {
        $fixtureTableName = "foo";

        $table = $this->getMock('Zend_Db_Table', array(), array(), '', false);
        $table->expects($this->at(0))
              ->method('info')
              ->with($this->equalTo('name'))
              ->will($this->returnValue($fixtureTableName));
        $table->expects($this->at(1))
              ->method('info')
              ->with($this->equalTo('cols'))
              ->will($this->returnValue( array("foo", "bar") ));
        $table->expects($this->once())
              ->method('fetchAll')
              ->will($this->returnValue(array( array("foo" => 1, "bar" => 2) )));

        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTable($table);

        $this->assertEquals($fixtureTableName, $dataSet->getTableMetaData()->getTableName());
        $this->assertEquals(array("foo", "bar"), $dataSet->getTableMetaData()->getColumns());
    }

    public function testLoadDataOnlyCalledOnce()
    {
        $table = $this->getMock('Zend_Db_Table', array(), array(), '', false);
        $table->expects($this->once())
              ->method('fetchAll')
              ->will($this->returnValue(array( array("foo" => 1, "bar" => 2) )));

        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTable($table);
        $dataSet->getRow(0);
        $dataSet->getRow(0);
    }
}