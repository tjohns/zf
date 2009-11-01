<?php

require_once "CategoryTestCase.php";
require_once "ClinicTestCase.php";
require_once "UniversityTestCase.php";

class Zend_Entity_DbMapper_IntegrationTest_AllTests
{
    static public function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend_Entity Integration Test Suite');

        if(isset($GLOBALS['ZEND_ENTITY_DBMAPPER_SKIPINTEGRATION']) && $GLOBALS['ZEND_ENTITY_DBMAPPER_SKIPINTEGRATION'] == "1") {
            return $suite;
        }

        if(defined('TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED') && TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED == true) {
            require_once "Clinic/PDOMysqlTest.php";
            require_once "University/PDOMysqlTest.php";
            require_once "Category/PdoMysqlTest.php";

            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_Clinic_PDOMysqlTest');
            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_University_PDOMysqlTest');
            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_Category_PdoMysqlTest');
        }

        if(defined('TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED') && TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED == true) {
            require_once "Clinic/MysqliTest.php";
            require_once "University/MysqliTest.php";

            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_Clinic_MysqliTest');
            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_University_MysqliTest');
        }

        if(defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED') && TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED == true) {
            require_once "Clinic/PDOPgsqlTest.php";

            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_Clinic_PDOPgsqlTest');
            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_University_PdoPgsqlTest');
        }

        if(defined('TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED') && TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED == true) {
            require_once "University/PdoOciTest.php";

            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_University_PdoOciTest');

            
        }
        if(defined('TESTS_ZEND_DB_ADAPTER_ORACLE_ENABLED') && TESTS_ZEND_DB_ADAPTER_ORACLE_ENABLED == true) {
            require_once "University/OracleTest.php";

            $suite->addTestSuite('Zend_Entity_DbMapper_IntegrationTest_University_OracleTest');
        }
        

        return $suite;
    }
}