<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// require_once 'Zend/Http/ClientTest.php';
require_once 'Zend/Http/RequestTest.php';
require_once 'Zend/Http/ResponseTest.php';

class Zend_Http_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        // $suite->addTestSuite('Zend_Http_ClientTest');
        $suite->addTestSuite('Zend_Http_RequestTest');
        $suite->addTestSuite('Zend_Http_ResponseTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_AllTests::main') {
    Zend_AllTests::main();
}
