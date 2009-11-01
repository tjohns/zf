<?php

require_once dirname(__FILE__)."/../../../TestHelper.php";

require_once "Id/AllTests.php";
require_once "Loader/AllTests.php";
require_once "Persister/AllTests.php";
require_once "QueryObjectTest.php";
require_once "MappingTest.php";
require_once "MapperTest.php";
require_once "TransactionTest.php";
require_once "SqlQueryTest.php";
require_once "SqlQueryBuilderTest.php";

require_once "IntegrationTest/AllTests.php";

class Zend_Entity_DbMapper_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity Database Mapper Tests');
        $suite->addTest(Zend_Db_Mapper_Id_AllTests::suite());
        $suite->addTest(Zend_Entity_DbMapper_Loader_AllTests::suite());
        $suite->addTest(Zend_Entity_DbMapper_Persister_AllTests::suite());
        $suite->addTestSuite('Zend_Entity_DbMapper_MappingTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_QueryObjectTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_MapperTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_SqlQueryBuilderTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_SqlQueryTest');
        $suite->addTestSuite('Zend_Entity_DbMapper_TransactionTest');

        $suite->addTest(Zend_Entity_DbMapper_IntegrationTest_AllTests::suite());

        return $suite;
    }
}