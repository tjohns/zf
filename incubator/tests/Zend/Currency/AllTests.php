<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Currency_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

//require_once 'Zend/Currency/CurrencyTest.php';

class Zend_Currency_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Currency');

//        $suite->addTestSuite('Zend_Currency_CurrencyTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Currency_AllTests::main') {
    Zend_Currency_AllTests::main();
}
