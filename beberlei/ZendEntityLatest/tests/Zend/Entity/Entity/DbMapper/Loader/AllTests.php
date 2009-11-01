<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";

require_once "TestCase.php";
require_once "SingleScalarTest.php";
require_once "ScalarTest.php";
require_once "ArrayTest.php";
require_once "Entity/SimpleFixtureTest.php";
require_once "Entity/ManyToOneFixtureTest.php";
require_once "Entity/OneToManyFixtureTest.php";
require_once "Entity/ManyToManyFixtureTest.php";
require_once "Entity/CollectionElementsFixtureTest.php";
require_once "Entity/VersionFixtureTest.php";
require_once "Entity/NullableFixtureTest.php";
require_once "Entity/SelfReferenceTest.php";
require_once "RefreshTest.php";
require_once "Validate/ArrayTest.php";
require_once "Validate/EntityTest.php";
require_once "Validate/ScalarTest.php";

class Zend_Entity_DbMapper_Loader_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity Database Mapper Loader Tests');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_SingleScalarTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_ScalarTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_ArrayTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_SimpleFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_ManyToOneFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_OneToManyFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_ManyToManyFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_CollectionElementsFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_VersionFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_NullableFixtureTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Entity_SelfReferenceTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_RefreshTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Validate_ArrayTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Validate_EntityTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_Loader_Validate_ScalarTest');
        
        return $suite;
    }
}
