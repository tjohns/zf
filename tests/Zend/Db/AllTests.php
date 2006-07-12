<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Db_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

if(TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_ENABLED == true) {
    require_once 'Zend/Db/Adapter/Pdo/MssqlTest.php';
}
if(TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED == true) {
    require_once 'Zend/Db/Adapter/Pdo/MysqlTest.php';
}

class Zend_Db_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Db');

        if(TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_ENABLED == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_MssqlTest');
        }
        if(TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_MysqlTest');
        }

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Db_AllTests::main') {
    Zend_Db_AllTests::main();
}
