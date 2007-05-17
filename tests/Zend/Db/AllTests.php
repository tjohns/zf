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
 * @version    $Id$
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Db_AllTests::main');
}


/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * PHPUnit_TextUI_TestRunner
 */
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Db_SkipTests
 */
require_once 'Zend/Db/SkipTests.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Db');

        /**
         * Static tests should always be enabled,
         * but if they're not, don't throw an error.
         */
        if (!defined('TESTS_ZEND_DB_ADAPTER_STATIC_ENABLED')) {
            define('TESTS_ZEND_DB_ADAPTER_STATIC_ENABLED', true);
        }

        self::_addDbTestSuites($suite, 'Static');

        self::_addDbTestSuites($suite, 'Db2');
        self::_addDbTestSuites($suite, 'Mysqli');
        self::_addDbTestSuites($suite, 'Oracle');

        /**
         * @todo: self::_addDbTestSuites($suite, 'Odbc');
         */

        self::_addDbTestSuites($suite, 'Pdo_Mssql');
        self::_addDbTestSuites($suite, 'Pdo_Mysql');
        self::_addDbTestSuites($suite, 'Pdo_Oci');
        self::_addDbTestSuites($suite, 'Pdo_Pgsql');
        self::_addDbTestSuites($suite, 'Pdo_Sqlite');

        return $suite;
    }

    protected static function _addDbTestSuites($suite, $driver)
    {
        $skipTestClass = "Zend_Db_Skip_{$driver}Test";
        $DRIVER = strtoupper($driver);
        $enabledConst = "TESTS_ZEND_DB_ADAPTER_{$DRIVER}_ENABLED";
        if (!defined($enabledConst) || constant($enabledConst) != true) {
            $skipTest = new $skipTestClass();
            $skipTest->message = "this Adapter is not enabled in TestConfiguration.php";
            $suite->addTest($skipTest);
            return;
        }

        $ext = array(
            'Oracle' => 'oci8',
            'Db2'    => 'ibm_db2',
            'Mysqli' => 'mysqli',
            /**
             * @todo: 'Odbc'
             */
        );

        if (isset($ext[$driver]) && !extension_loaded($ext[$driver])) {
            $skipTest = new $skipTestClass();
            $skipTest->message = "extension '{$ext[$driver]}' is not loaded";
            $suite->addTest($skipTest);
            return;
        }

        if (preg_match('/^pdo_(.*)/i', $driver, $matches)) {
            // check for PDO extension
            if (!extension_loaded('pdo')) {
                $skipTest = new $skipTestClass();
                $skipTest->message = "extension 'PDO' is not loaded";
                $suite->addTest($skipTest);
                return;
            }

            // check the PDO driver is available
            $pdo_driver = strtolower($matches[1]);
            if (!in_array($pdo_driver, PDO::getAvailableDrivers())) {
                $skipTest = new $skipTestClass();
                $skipTest->message = "PDO driver '{$pdo_driver}' is not available";
                $suite->addTest($skipTest);
                return;
            }
        }

        try {

            if ($driver == 'Static') {
                Zend_Loader::loadClass("Zend_Db_Profiler_{$driver}Test");
                $suite->addTestSuite("Zend_Db_Profiler_{$driver}Test");
            }

            Zend_Loader::loadClass("Zend_Db_Adapter_{$driver}Test");
            Zend_Loader::loadClass("Zend_Db_Statement_{$driver}Test");
            Zend_Loader::loadClass("Zend_Db_Select_{$driver}Test");
            Zend_Loader::loadClass("Zend_Db_Table_{$driver}Test");
            Zend_Loader::loadClass("Zend_Db_Table_Rowset_{$driver}Test");
            Zend_Loader::loadClass("Zend_Db_Table_Row_{$driver}Test");
            Zend_Loader::loadClass("Zend_Db_Table_Relationships_{$driver}Test");

            // if we get this far, there have been no exceptions loading classes
            // so we can add them as test suites

            $suite->addTestSuite("Zend_Db_Adapter_{$driver}Test");
            $suite->addTestSuite("Zend_Db_Statement_{$driver}Test");
            $suite->addTestSuite("Zend_Db_Select_{$driver}Test");
            $suite->addTestSuite("Zend_Db_Table_{$driver}Test");
            $suite->addTestSuite("Zend_Db_Table_Rowset_{$driver}Test");
            $suite->addTestSuite("Zend_Db_Table_Row_{$driver}Test");
            $suite->addTestSuite("Zend_Db_Table_Relationships_{$driver}Test");

        } catch (Zend_Exception $e) {
            $skipTest = new $skipTestClass();
            $skipTest->message = "cannot load test classes: " . $e->getMessage();
            $suite->addTest($skipTest);
        }
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_Db_AllTests::main') {
    Zend_Db_AllTests::main();
}
