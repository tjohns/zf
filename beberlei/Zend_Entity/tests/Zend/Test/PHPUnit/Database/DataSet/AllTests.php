<?php

require_once dirname(__FILE__)."/../../../../../TestHelper.php";

require_once "DbRowsetTest.php";
require_once "QueryDataSetTest.php";
require_once "QueryTableTest.php";
require_once "DbTableTest.php";
require_once "DbTableDataSetTest.php";

class Zend_Test_PHPUnit_Database_DataSet_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Test PHPUnit Database DataSets');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_DataSet_DbRowsetTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_DataSet_QueryDataSetTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_DataSet_QueryTableTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_DataSet_DbTableTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_DataSet_DbTableDataSetTest');

        return $suite;
    }
}