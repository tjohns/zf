<?php

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Date_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Date/DateObjectTest.php';

class Zend_Date_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Date_DateObject');

        $suite->addTestSuite('Zend_Date_DateObjectTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Date_AllTests::main') {
    Zend_Date_AllTests::main();
}
