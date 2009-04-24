<?php

require_once dirname(__FILE__)."/../TestHelper.php";

require_once "DbAdapterMock.php";
require_once "DbStatementMock.php";

require_once "CollectionTest.php";
require_once "IdentityMapTest.php";
require_once "UnitOfWorkTest.php";
require_once "ManagerTest.php";
require_once "Resource/AllTests.php";
require_once "Adapter/TestCase.php";
require_once "Adapter/PDO/MySQL/ClinicScenario.php";
require_once "Adapter/PDO/SqLite/ClinicScenario.php";
require_once "Mapper/AllTests.php";

class Zend_Entity_AllTests
{
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Zend_Entity");
        $suite->addTestSuite('Zend_Entity_CollectionTest');
        $suite->addTestSuite('Zend_Entity_IdentityMapTest');
        $suite->addTestSuite('Zend_Entity_UnitOfWorkTest');
        $suite->addTestSuite('Zend_Entity_ManagerTest');
        $suite->addTestSuite('Zend_Entity_Resource_AllTests');
        $suite->addTest(Zend_Entity_Mapper_AllTests::suite());
        //$suite->addTestSuite('Zend_Entity_Adapter_PDO_MySQL_ClinicScenario');
        //$suite->addTestSuite('Zend_Entity_Adapter_PDO_SqLite_ClinicScenario');
        return $suite;
    }
}