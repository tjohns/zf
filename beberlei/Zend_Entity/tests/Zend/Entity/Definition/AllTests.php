<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "TestCase.php";

require_once "FormulaTest.php";
require_once "PropertyTest.php";
require_once "EntityTest.php";
require_once "JoinTest.php";
require_once "PrimaryKeyTest.php";
require_once "CollectionTest.php";
require_once "UtilityTest.php";
require_once "OneToOneRelationTest.php";
require_once "ManyToOneRelationTest.php";
require_once "ManyToManyRelationTest.php";
require_once "Id/AllTests.php";

class Zend_Entity_Definition_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper_Definition Tests');
        $suite->addTestSuite('Zend_Entity_Definition_EntityTest');
        $suite->addTestSuite('Zend_Entity_Definition_FormulaTest');
        $suite->addTestSuite('Zend_Entity_Definition_PropertyTest');
        $suite->addTestSuite('Zend_Entity_Definition_JoinTest');
        $suite->addTestSuite('Zend_Entity_Definition_PrimaryKeyTest');
        $suite->addTestSuite('Zend_Entity_Definition_CollectionTest');
        $suite->addTestSuite('Zend_Entity_Definition_UtilityTest');
        $suite->addTestSuite('Zend_Entity_Definition_OneToOneRelationTest');
        $suite->addTestSuite('Zend_Entity_Definition_ManyToOneRelationTest');
        $suite->addTestSuite('Zend_Entity_Definition_ManyToManyRelationTest');

        $suite->addTest(Zend_Entity_Definition_Id_AllTests::suite());

        return $suite;
    }
}