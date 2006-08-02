<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Locale_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Locale/DataTest.php';
require_once 'Zend/Locale/FormatTest.php';

class Zend_Locale_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Locale');

        $suite->addTestSuite('Zend_Locale_DataTest');
        $suite->addTestSuite('Zend_Locale_FormatTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Locale_AllTests::main') {
    Zend_Locale_AllTests::main();
}
