<?php

require_once "DbRowsetTest.php";

class Zend_Test_PHPUnit_Database_DataSet_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Test PHPUnit Database DataSets');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_DataSet_DbRowsetTest');

        return $suite;
    }
}