<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Uri_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Uri/HttpTest.php';

class Zend_Uri_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Uri');

        $suite->addTestSuite('Zend_Uri_HttpTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Uri_AllTests::main') {
    Zend_Uri_AllTests::main();
}
