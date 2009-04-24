<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "TestCase.php";

require_once "FormulaTest.php";
require_once "PropertyTest.php";
require_once "EntityTest.php";
require_once "JoinTest.php";
require_once "PrimaryKeyTest.php";
require_once "CollectionTest.php";

class Zend_Entity_Mapper_Definition_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper_Definition Tests');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_EntityTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_FormulaTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_PropertyTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_JoinTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_PrimaryKeyTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Definition_CollectionTest');

        return $suite;
    }
}