<?php

require_once dirname(__FILE__)."/../../TestHelper.php";

require_once "CoreTest.php";
require_once "Import/SchemaTest.php";
require_once "Application/Resource/DoctrineTest.php";

class Zend_Doctrine_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite();
        $suite->addTestSuite('Zend_Doctrine_Application_Resource_DoctrineTest');
        $suite->addTestSuite('Zend_Doctrine_CoreTest');
        $suite->addTestSuite('Zend_Doctrine_Import_SchemaTest');

        return $suite;
    }
}