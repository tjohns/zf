<?php

require_once "InsertTest.php";
require_once "TruncateTest.php";
require_once "DeleteAllTest.php";

class Zend_Test_PHPUnit_Database_Operation_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Test PHPUnit Database Operation');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_Operation_InsertTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_Operation_TruncateTest');
        $suite->addTestSuite('Zend_Test_PHPUnit_Database_Operation_DeleteAllTest');

        return $suite;
    }
}