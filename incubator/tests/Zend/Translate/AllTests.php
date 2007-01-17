<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Test_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Translate/ArrayTest.php';
require_once 'Zend/Translate/GettextTest.php';

class Zend_Translate_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Test');

        $suite->addTestSuite('Zend_Translate_ArrayTest');
        $suite->addTestSuite('Zend_Translate_GettextTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Test_AllTests::main') {
    Zend_Translate_AllTests::main();
}
