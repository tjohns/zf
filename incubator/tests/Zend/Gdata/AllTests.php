<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Gdata_AllTests::main');

    /**
     * Prepend library/ to the include_path.  This allows the tests to run out of the box and
     * helps prevent finding other copies of the framework that might be present.
     */
    $zf_top = dirname(dirname(dirname(dirname(__FILE__))));
    set_include_path($zf_top . DIRECTORY_SEPARATOR . 'library'
         . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Gdata/BaseTest.php';
require_once 'Zend/Gdata/BloggerTest.php';
require_once 'Zend/Gdata/CalendarTest.php';
require_once 'Zend/Gdata/CodeSearchTest.php';
require_once 'Zend/Gdata/SpreadsheetsTest.php';

// require_once 'Zend/Gdata/ClientLoginTest.php';
// require_once 'Zend/Gdata/AuthSubTest.php';

class Zend_Gdata_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Gdata');

        // $suite->addTestSuite('Zend_Gdata_BaseTest');
        // $suite->addTestSuite('Zend_Gdata_BloggerTest');
        // $suite->addTestSuite('Zend_Gdata_CalendarTest');
        // $suite->addTestSuite('Zend_Gdata_CodeSearchTest');
        // $suite->addTestSuite('Zend_Gdata_SpreadsheetsTest');

        // $suite->addTestSuite('Zend_Gdata_ClientLoginTest');
        // $suite->addTestSuite('Zend_Gdata_AuthSubTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Gdata_AllTests::main') {
    Zend_Gdata_AllTests::main();
}
