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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

error_reporting( E_ALL | E_STRICT );

if (is_readable('TestConfiguration.php')) {
    require_once('TestConfiguration.php');
} else {
    require_once('TestConfiguration.php.dist');
}

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Gdata_AllTests::main');

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

/**
 * Tests that do not require online access to servers
 */
require_once 'Zend/Gdata/GdataTest.php';
require_once 'Zend/Gdata/BaseTest.php';
require_once 'Zend/Gdata/BloggerTest.php';
require_once 'Zend/Gdata/CalendarTest.php';
require_once 'Zend/Gdata/CodeSearchTest.php';
require_once 'Zend/Gdata/DataTest.php';
// require_once 'Zend/Gdata/SpreadsheetsTest.php';

/**
 * Tests that do require online access to servers
 */

require_once 'Zend/Gdata/BaseOnlineTest.php';
require_once 'Zend/Gdata/BloggerOnlineTest.php';
require_once 'Zend/Gdata/CalendarOnlineTest.php';
require_once 'Zend/Gdata/CodeSearchOnlineTest.php';
// require_once 'Zend/Gdata/SpreadsheetsOnlineTest.php';

/**
 * Tests that require authentication
 */
// require_once 'Zend/Gdata/BaseClientLoginTest.php';
// require_once 'Zend/Gdata/BloggerClientLoginTest.php';
// require_once 'Zend/Gdata/CalendarClientLoginTest.php';
// require_once 'Zend/Gdata/SpreadsheetsClientLoginTest.php';

require_once 'Zend/Gdata/SkipTests.php';

class Zend_Gdata_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Gdata');

        /**
         * Tests that do not require online access to servers
         */
        $suite->addTestSuite('Zend_Gdata_GdataTest');
        $suite->addTestSuite('Zend_Gdata_BaseTest');
        $suite->addTestSuite('Zend_Gdata_BloggerTest');
        $suite->addTestSuite('Zend_Gdata_CalendarTest');
        $suite->addTestSuite('Zend_Gdata_CodeSearchTest');
        $suite->addTestSuite('Zend_Gdata_DataTest');
        // $suite->addTestSuite('Zend_Gdata_SpreadsheetsTest');

        if (defined('TESTS_ZEND_GDATA_ONLINE_ENABLED') &&
        constant('TESTS_ZEND_GDATA_ONLINE_ENABLED') == true) {
            /**
             * Tests that do require online access to servers
             */
            $suite->addTestSuite('Zend_Gdata_BaseOnlineTest');
            $suite->addTestSuite('Zend_Gdata_BloggerOnlineTest');
            $suite->addTestSuite('Zend_Gdata_CalendarOnlineTest');
            $suite->addTestSuite('Zend_Gdata_CodeSearchOnlineTest');
            // $suite->addTestSuite('Zend_Gdata_SpreadsheetsOnlineTest');
        } else {
            $suite->addTestSuite('Zend_Gdata_SkipOnlineTest');
        }

        if (defined('TESTS_ZEND_GDATA_CLIENTLOGIN_ENABLED') &&
        constant('TESTS_ZEND_GDATA_CLIENTLOGIN_ENABLED') == true) {
            /**
             * Tests that require authentication
             */
            // $suite->addTestSuite('Zend_Gdata_BaseClientLoginTest');
            // $suite->addTestSuite('Zend_Gdata_BloggerClientLoginTest');
            // $suite->addTestSuite('Zend_Gdata_CalendarClientLoginTest');
            // $suite->addTestSuite('Zend_Gdata_SpreadsheetsClientLoginTest');
        } else {
            $suite->addTestSuite('Zend_Gdata_SkipClientLoginTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Gdata_AllTests::main') {
    Zend_Gdata_AllTests::main();
}
