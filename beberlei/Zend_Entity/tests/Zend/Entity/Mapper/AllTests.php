<?php

require_once dirname(__FILE__)."/../../TestHelper.php";


require_once "Loader/AllTests.php";
require_once "Persister/AllTests.php";
require_once "QueryObjectTest.php";
require_once "MappingTest.php";
require_once "NativeQueryBuilderTest.php";
require_once "MapperTest.php";
require_once "TransactionTest.php";
require_once "ResultSetMappingTest.php";

class Zend_Entity_Mapper_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper Tests');
        $suite->addTest(Zend_Entity_Mapper_Loader_AllTests::suite());
        $suite->addTest(Zend_Entity_Mapper_Persister_AllTests::suite());
        $suite->addTestSuite('Zend_Entity_Mapper_MappingTest');
        $suite->addTestSuite('Zend_Entity_Mapper_ResultSetMappingTest');
        $suite->addTestSuite('Zend_Entity_Mapper_QueryObjectTest');
        $suite->addTestSuite('Zend_Entity_Mapper_MapperTest');
        $suite->addTestSuite('Zend_Entity_Mapper_NativeQueryBuilderTest');
        $suite->addTestSuite('Zend_Entity_Mapper_TransactionTest');

        return $suite;
    }
}