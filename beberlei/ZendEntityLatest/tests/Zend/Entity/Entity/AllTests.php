<?php

require_once dirname(__FILE__)."/../../TestHelper.php";

require_once "Zend/Loader/Autoloader.php";
$autoloader = Zend_Loader_Autoloader::getInstance();

require_once "CollectionTest.php";
require_once "Collection/ArrayTest.php";
require_once "DebugTest.php";
require_once "Definition/AllTests.php";
require_once "Event/AllTests.php";
require_once "IdentityMapTest.php";
require_once "LazyLoad/AllTests.php";
require_once "ManagerFactoryTest.php";
require_once "ManagerTest.php";
require_once "MetadataFactory/AllTests.php";
require_once "Query/AllTests.php";
require_once "StateTransformer/AllTests.php";
require_once "Tool/AllTests.php";
require_once "UnitOfWorkTest.php";

require_once "DbMapper/AllTests.php";

require_once "Zend/Entity/Fixture/Entities.php";

class Zend_Entity_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Zend_Entity");
        $suite->addTest(Zend_Entity_Definition_AllTests::suite());
        $suite->addTest(Zend_Entity_Event_AllTests::suite());
        $suite->addTestSuite('Zend_Entity_CollectionTest');
        $suite->addTestSuite('Zend_Entity_Collection_ArrayTest');
        $suite->addTestSuite('Zend_Entity_DebugTest');
        $suite->addTest(Zend_Entity_LazyLoad_AllTests::suite());
        $suite->addTestSuite('Zend_Entity_IdentityMapTest');
        $suite->addTestSuite('Zend_Entity_ManagerFactoryTest');
        $suite->addTestSuite('Zend_Entity_ManagerTest');
        $suite->addTestSuite('Zend_Entity_UnitOfWorkTest');
        $suite->addTestSuite('Zend_Entity_MetadataFactory_AllTests');
        $suite->addTest(Zend_Entity_StateTransformer_AllTests::suite());
        $suite->addTest(Zend_Entity_Tool_AllTests::suite());
        $suite->addTest(Zend_Entity_Query_AllTests::suite());
        $suite->addTest(Zend_Entity_DbMapper_AllTests::suite());
        return $suite;
    }
}