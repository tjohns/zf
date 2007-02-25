<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

error_reporting( E_ALL | E_STRICT );

if (is_readable('TestConfiguration.php')) {
    require_once('TestConfiguration.php');
} else {
    require_once('TestConfiguration.php.dist');
}

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Db_AllTests::main');

    /**
     * Prepend library/ to the include_path.  This allows the tests to run out
     * of the box and helps prevent finding other copies of the framework that
     * might be present.
     */
    $zf_top = dirname(dirname(dirname(dirname(__FILE__))));
    set_include_path($zf_top . DIRECTORY_SEPARATOR . 'library'
         . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Db/DbTest.php';
require_once 'Zend/Db/ProfilerTest.php';
require_once 'Zend/Db/Adapter/Db2Test.php';
require_once 'Zend/Db/Adapter/MysqliTest.php';
require_once 'Zend/Db/Adapter/OracleTest.php';
require_once 'Zend/Db/Adapter/Pdo/MssqlTest.php';
require_once 'Zend/Db/Adapter/Pdo/MysqlTest.php';
require_once 'Zend/Db/Adapter/Pdo/OciTest.php';
require_once 'Zend/Db/Adapter/Pdo/PgsqlTest.php';
require_once 'Zend/Db/Adapter/Pdo/SqliteTest.php';

require_once 'Zend/Db/Adapter/SkipTests.php';

class Zend_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Db');

        $suite->addTestSuite('Zend_Db_DbTest');
        $suite->addTestSuite('Zend_Db_ProfilerTest');

        if (defined('TESTS_ZEND_DB_ADAPTER_DB2_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_DB2_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Db2Test');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_Db2Test');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_MYSQLI_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_MysqliTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_MysqliTest');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_ORACLE_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_ORACLE_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_OracleTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_OracleTest');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_PDO_MSSQL_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_MssqlTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_Pdo_MssqlTest');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_PDO_MYSQL_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_MysqlTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_Pdo_MysqlTest');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_PDO_OCI_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_OciTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_Pdo_OciTest');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_PDO_PGSQL_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_PgsqlTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_Pdo_PgsqlTest');
        }

        if (defined('TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_ENABLED') &&
        constant('TESTS_ZEND_DB_ADAPTER_PDO_SQLITE_ENABLED') == true) {
            $suite->addTestSuite('Zend_Db_Adapter_Pdo_SqliteTest');
        } else {
            $suite->addTestSuite('Zend_Db_Adapter_Skip_Pdo_SqliteTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Db_AllTests::main') {
    Zend_Db_AllTests::main();
}
