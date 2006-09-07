<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Db_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

if(TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_ENABLED == true) {
    require_once 'Zend/Db/Adapter/Pdo/MssqlTest.php';
}
if(TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED == true) {
    require_once 'Zend/Db/Adapter/Pdo/MysqlTest.php';
}

if(TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_ENABLED == true) {
    require_once 'Zend/Db/Adapter/Pdo/SqliteTest.php';
}

class Zend_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Db');

        if(TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_ENABLED == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_MssqlTest');
        }
        if(TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_MysqlTest');
        }

        if(TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_ENABLED == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_SqliteTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Db_AllTests::main') {
    Zend_Db_AllTests::main();
}
