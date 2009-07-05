<?php

abstract class Zend_Test_PHPUnit_Database_Integration_AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $dbAdapter;

    public function testZendDbTableDataSet()
    {
        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet();
        $dataSet->addTable($this->createFooTable());
        $dataSet->addTable($this->createBarTable());

        $this->assertEquals(
            "foo", $dataSet->getTableMetaData('foo')->getTableName()
        );
        $this->assertEquals(
            "bar", $dataSet->getTableMetaData("bar")->getTableName()
        );

        $this->assertEquals(array("foo", "bar"), $dataSet->getTableNames());
    }

    public function testZendDbTableEqualsXmlDataSet()
    {
        $fooTable = $this->createFooTable();
        $fooTable->insert(array("id" => null, "foo" => "foo", "bar" => "bar", "baz" => "baz"));
        $fooTable->insert(array("id" => null, "foo" => "bar", "bar" => "bar", "baz" => "bar"));
        $fooTable->insert(array("id" => null, "foo" => "baz", "bar" => "baz", "baz" => "baz"));

        $dataSet = new Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet();
        $dataSet->addTable($fooTable);

        $xmlDataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__)."/_files/sqliteIntegrationFixture.xml"
        );
        $this->assertTrue($dataSet->assertEquals($xmlDataSet));
    }

    /**
     * @return Zend_Test_PHPUnit_Database_Connection
     */
    public function getConnection()
    {
        return new Zend_Test_PHPUnit_Database_Connection($this->dbAdapter, 'foo');
    }

    public function testSimpleTesterSetupAndRowsetEquals()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
            dirname(__FILE__)."/_files/sqliteIntegrationFixture.xml"
        );
        $fooDataTable = $dataSet->getTable("foo");

        $tester = new Zend_Test_PHPUnit_Database_SimpleTester($this->getConnection());
        $tester->setUpDatabase($dataSet);

        $fooTable = $this->createFooTable();
        $rows = $fooTable->fetchAll();

        $this->assertEquals(3, count($rows));

        $rowsetTable = new Zend_Test_PHPUnit_Database_DataSet_DbRowset($rows);
        $rowsetTable->assertEquals($fooDataTable);
    }

    /**
     * @return Zend_Test_PHPUnit_Database_TableFoo
     */
    public function createFooTable()
    {
        $table = new Zend_Test_PHPUnit_Database_TableFoo(array('db' => $this->dbAdapter));
        return $table;
    }

    /**
     * @return Zend_Test_PHPUnit_Database_TableBar
     */
    public function createBarTable()
    {
        $table = new Zend_Test_PHPUnit_Database_TableBar(array('db' => $this->dbAdapter));
        return $table;
    }
}

class Zend_Test_PHPUnit_Database_TableFoo extends Zend_Db_Table_Abstract
{
    protected $_name = "foo";

    protected $_primary = "id";
}

class Zend_Test_PHPUnit_Database_TableBar extends Zend_Db_Table_Abstract
{
    protected $_name = "bar";

    protected $_primary = "id";
}