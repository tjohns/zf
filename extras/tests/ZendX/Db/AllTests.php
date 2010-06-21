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
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendX_Db_AllTests::main');
}


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

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
require_once 'ZendX/Db/SkipTests.php';

/**
 * @see ZendX_Db_Profiler_AllTests
 */
require_once 'Zend/Db/Profiler/AllTests.php';

/**
 * @category   ZendX
 * @package    ZendX_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendX_Db_AllTests
{

    protected static $_skipTestSuite = null;

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - ZendX_Db');

        /**
         * Static tests should always be enabled,
         * but if they're not, don't throw an error.
         */
        if (!defined('TESTS_ZENDX_DB_ADAPTER_STATIC_ENABLED')) {
            define('TESTS_ZENDX_DB_ADAPTER_STATIC_ENABLED', false);
        }

		self::_addDbTestSuites($suite, 'Firebird');

        /**
         * @todo  self::_addDbTestSuites($suite, 'Odbc');
         */		

        if (self::$_skipTestSuite !== null) {
            $suite->addTest(self::$_skipTestSuite);
        }

        $suite->addTest(Zend_Db_Profiler_AllTests::suite());

        return $suite;
    }

    protected static function _addDbTestSuites($suite, $driver)
    {
        $DRIVER = strtoupper($driver);
        $enabledConst = "TESTS_ZENDX_DB_ADAPTER_{$DRIVER}_ENABLED";
        if (!defined($enabledConst) || constant($enabledConst) != true) {
            self::_skipTestSuite($driver, "this Adapter is not enabled in TestConfiguration.php");
            return;
        }

        $ext = array(
			'Firebird' => 'interbase',
            /**
             * @todo  'Odbc'
             */
        );

        if (isset($ext[$driver]) && !extension_loaded($ext[$driver])) {
            self::_skipTestSuite($driver, "extension '{$ext[$driver]}' is not loaded");
            return;
        }

        if (preg_match('/^pdo_(.*)/i', $driver, $matches)) {
            // check for PDO extension
            if (!extension_loaded('pdo')) {
                self::_skipTestSuite($driver, "extension 'PDO' is not loaded");
                return;
            }

            // check the PDO driver is available
            $pdo_driver = strtolower($matches[1]);
            if (!in_array($pdo_driver, PDO::getAvailableDrivers())) {
                self::_skipTestSuite($driver, "PDO driver '{$pdo_driver}' is not available");
                return;
            }
        }

        try {

            Zend_Loader::loadClass("ZendX_Db_Adapter_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Profiler_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Statement_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Select_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Table_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Table_Select_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Table_Rowset_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Table_Row_{$driver}Test");
            Zend_Loader::loadClass("ZendX_Db_Table_Relationships_{$driver}Test");

            // if we get this far, there have been no exceptions loading classes
            // so we can add them as test suites

            $suite->addTestSuite("ZendX_Db_Adapter_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Profiler_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Statement_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Select_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Table_{$driver}Test");
            $suite->addTestSuite("Zendx_Db_Table_Select_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Table_Rowset_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Table_Row_{$driver}Test");
            $suite->addTestSuite("ZendX_Db_Table_Relationships_{$driver}Test");

        } catch (Zend_Exception $e) {
            self::_skipTestSuite($driver, "cannot load test classes: " . $e->getMessage());
        }
    }

    protected static function _skipTestSuite($driver, $message = '')
    {
        $skipTestClass = "ZendX_Db_Skip_{$driver}Test";
        $skipTest = new $skipTestClass();
        $skipTest->message = $message;

        if (self::$_skipTestSuite === null) {
            self::$_skipTestSuite = new PHPUnit_Framework_TestSuite('ZendX_Db skipped test suites');
        }

        self::$_skipTestSuite->addTest($skipTest);
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_DbX_AllTests::main') {
    Zend_Db_AllTests::main();
}
