<?php

require_once dirname(__FILE__)."/../TestHelper.php";

require_once "DbAdapterMock.php";
require_once "DbStatementMock.php";

require_once "CollectionTest.php";
require_once "IdentityMapTest.php";
require_once "ManagerTest.php";
require_once "MetadataFactory/AllTests.php";
require_once "Mapper/AllTests.php";
require_once "LazyLoad/AllTests.php";
require_once "MapperTest.php";
require_once "DebugTest.php";

class Zend_Entity_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Zend_Entity");
        $suite->addTestSuite('Zend_Entity_CollectionTest');
        $suite->addTestSuite('Zend_Entity_DebugTest');
        $suite->addTest(Zend_Entity_LazyLoad_AllTests::suite());
        $suite->addTestSuite('Zend_Entity_IdentityMapTest');
        $suite->addTestSuite('Zend_Entity_ManagerTest');
        $suite->addTestSuite('Zend_Entity_MapperTest');
        $suite->addTestSuite('Zend_Entity_MetadataFactory_AllTests');
        $suite->addTest(Zend_Entity_Mapper_AllTests::suite());
        $suite->addTest(Zend_Entity_IntegrationTest_AllTests::suite());
        return $suite;
    }
}