<?php

require_once dirname(__FILE__)."/../../TestHelper.php";


require_once "Loader/AllTests.php";
require_once "Persister/AllTests.php";
require_once "SelectTest.php";
require_once "DbSelectQueryTest.php";
require_once "MapperTest.php";

class Zend_Entity_Mapper_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity_Mapper Tests');
        $suite->addTest(Zend_Entity_Mapper_Loader_AllTests::suite());
        $suite->addTest(Zend_Entity_Mapper_Persister_AllTests::suite());
        $suite->addTestSuite('Zend_Entity_Mapper_SelectTest');
        $suite->addTestSuite('Zend_Entity_Mapper_MapperTest');
        $suite->addTestSuite('Zend_Entity_Mapper_DbSelectQueryTest');

        return $suite;
    }
}