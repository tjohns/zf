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

    public function testLoadDataOnlyCalledOnce()
    {
        
    }
}