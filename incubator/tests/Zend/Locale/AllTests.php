<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Locale_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting( E_ALL | E_STRICT ); // now required for each test suite
// define('TESTS_ZEND_LOCALE_BCMATH_ENABLED', false); // uncomment to disable use of bcmath extension by Zend_Date

require_once 'Zend/Locale/DataTest.php';
require_once 'Zend/Locale/FormatTest.php';

// echo "BCMATH is ", Zend_Locale_Math::isBcmathDisabled() ? 'disabled':'not disabled', "\n";

class Zend_Locale_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Locale');

        $suite->addTestSuite('Zend_Locale_DataTest');
        $suite->addTestSuite('Zend_Locale_FormatTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Locale_AllTests::main') {
    Zend_Locale_AllTests::main();
}
