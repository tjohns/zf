<?php

require_once "AutoIncrementTest.php";

class Zend_Entity_Definition_Id_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Definition_Id Tests');
        $suite->addTestSuite('Zend_Entity_Definition_Id_AutoIncrementTest');

        return $suite;
    }
}