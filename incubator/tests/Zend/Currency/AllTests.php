<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Currency_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

//require_once 'Zend/Currency/CurrencyTest.php';

class Zend_Currency_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Currency');

//        $suite->addTestSuite('Zend_Currency_CurrencyTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Currency_AllTests::main') {
    Zend_Currency_AllTests::main();
}
