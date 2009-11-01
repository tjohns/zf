<?php

require_once "ResultSetMappingTest.php";

class Zend_Entity_Query_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Zend_Entity_Query");
        $suite->addTestSuite('Zend_Entity_Query_ResultSetMappingTest');

        return $suite;
    }
}