<?php

require_once "DataSet/AllTests.php";
require_once "TestCaseTest.php";

class Zend_Test_PHPUnit_Database_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Test PHPUnit Database Extension');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_TestCaseTest');
        $suite->addTest(Zend_Test_PHPUnit_Database_DataSet_AllTests::suite());

        return $suite;
    }
}