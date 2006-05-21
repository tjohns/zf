<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Mime_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Mime/PartTest.php';
require_once 'Zend/Mime/MessageTest.php';

class Zend_Mime_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Mime');

        $suite->addTestSuite('Zend_Mime_PartTest');
        $suite->addTestSuite('Zend_Mime_MessageTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Mime_AllTests::main') {
    Zend_Mime_AllTests::main();
}
