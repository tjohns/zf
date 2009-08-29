<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "TestCase.php";
require_once "ArrayTest.php";
require_once "Entity/SimpleFixtureTest.php";
require_once "Entity/ManyToOneFixtureTest.php";
require_once "Entity/OneToManyFixtureTest.php";
require_once "Entity/ManyToManyFixtureTest.php";
require_once "Entity/CollectionElementsFixtureTest.php";

class Zend_Entity_Mapper_Loader_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper_Loader Tests');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_ArrayTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Entity_SimpleFixtureTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Entity_ManyToOneFixtureTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Entity_OneToManyFixtureTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Entity_ManyToManyFixtureTest');
        $suite->addTestSuite('Zend_Entity_Mapper_Loader_Entity_CollectionElementsFixtureTest');
        
        return $suite;
    }
}
