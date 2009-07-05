<?php

Class Zend_Test_PHPUnit_Database_DataSet_DbTableDataSetTest extends PHPUnit_Framework_TestCase
{
    public function testAddTableAppendedToTableNames()
    {
        $fixtureTable = "foo";

        $table = $this->getMock('Zend_Db_Table', array(), array(), '', false);
        $table->expects($this->at(0))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(1))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(2))->method('info')->with('cols')->will($this->returnValue(array()));

        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet();
        $dataSet->addTable($table);

        $this->assertEquals(array($fixtureTable), $dataSet->getTableNames());
    }

    public function testAddTableCreatesDbTableInstance()
    {
        $fixtureTable = "foo";

        $table = $this->getMock('Zend_Db_Table', array(), array(), '', false);
        $table->expects($this->at(0))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(1))->method('info')->with('name')->will($this->returnValue($fixtureTable));
        $table->expects($this->at(2))->method('info')->with('cols')->will($this->returnValue(array()));

        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet();
        $dataSet->addTable($table);

        $this->assertType('Zend_Test_PHPUnit_Database_DataSet_DbTable', $dataSet->getTable($fixtureTable));
    }

    public function testGetUnknownTableThrowsException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet();
        $dataSet->getTable('unknown');
    }
}