<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "CodeTest.php";
require_once "TestingTest.php";

class Zend_Entity_MetadataFactory_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Resource - AllTests');
        $suite->addTestSuite('Zend_Entity_MetadataFactory_CodeTest');
        $suite->addTestSuite('Zend_Entity_MetadataFactory_TestingTest');

        return $suite;
    }
}
