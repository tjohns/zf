<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Controller/AllTests.php';

class Zend_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTest(Zend_Controller_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_AllTests::main') {
    Zend_AllTests::main();
}
