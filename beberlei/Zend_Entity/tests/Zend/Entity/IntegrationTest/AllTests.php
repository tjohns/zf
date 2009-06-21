<?php

require_once "ClinicIntegrationTest.php";

class Zend_Entity_IntegrationTest_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity Integration Test Suite');

        $suite->addTestSuite('Zend_Entity_IntegrationTest_ClinicIntegrationTest');

        return $suite;
    }
}