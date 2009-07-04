<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";

require_once "DataSet/AllTests.php";
require_once "Operation/AllTests.php";
require_once "Metadata/GenericTest.php";
require_once "TestCaseTest.php";
require_once "ConnectionTest.php";
require_once "SimpleTesterTest.php";

class Zend_Test_PHPUnit_Database_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Test PHPUnit Database Extension');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_TestCaseTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_ConnectionTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_SimpleTesterTest');
        $suite->addTest(Zend_Test_PHPUnit_Database_DataSet_AllTests::suite());
        $suite->addTest(Zend_Test_PHPUnit_Database_Operation_AllTests::suite());
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_Metadata_GenericTest');

        return $suite;
    }
}