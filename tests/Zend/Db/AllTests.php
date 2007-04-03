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

require_once 'Zend/Loader.php';
require_once 'Zend/Db/SkipTests.php';

define('TESTS_ZEND_DB_ADAPTER_STATIC_ENABLED', true);

class Zend_Db_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Db');

        self::_addDbTestSuites($suite, 'Static');

        self::_addDbTestSuites($suite, 'Db2');
        self::_addDbTestSuites($suite, 'Mysqli');
        self::_addDbTestSuites($suite, 'Oracle');
        // @todo: self::_addDbTestSuites($suite, 'Odbc');

        self::_addDbTestSuites($suite, 'Pdo_Mssql');
        self::_addDbTestSuites($suite, 'Pdo_Mysql');
        self::_addDbTestSuites($suite, 'Pdo_Oci');
        self::_addDbTestSuites($suite, 'Pdo_Pgsql');
        self::_addDbTestSuites($suite, 'Pdo_Sqlite');

        return $suite;
    }

    protected static function _addDbTestSuites($suite, $driver)
    {
        $DRIVER = strtoupper($driver);
        $enabledConst = "TESTS_ZEND_DB_ADAPTER_${DRIVER}_ENABLED";
        if (!defined($enabledConst) || constant($enabledConst) != true) {
            $suite->addTestSuite("Zend_Db_Skip_{$driver}Test");
            return;
        }

        $ext = array(
            'Oracle' => 'oci8',
            'Db2'    => 'ibm_db2',
            'Mysqli' => 'mysqli',
            // @todo: 'Odbc'
        );

        if (isset($ext[$driver]) && !extension_loaded($ext[$driver])) {
            $suite->addTestSuite("Zend_Db_Skip_{$driver}Test");
            return;
        }

        if (preg_match('/^pdo_(.*)/i', $driver, $matches)) {
            // check for PDO extension
            if (!extension_loaded('pdo')) {
                $suite->addTestSuite("Zend_Db_Skip_PdoTest");
                return;
            }

            // check the PDO driver is available
            if (!in_array(strtolower($matches[1]), PDO::getAvailableDrivers())) {
                $suite->addTestSuite("Zend_Db_Skip_{$driver}Test");
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
            $suite->addTestSuite("Zend_Db_Skip_{$driver}Test");
        }
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_Db_AllTests::main') {
    Zend_Db_AllTests::main();
}
