<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_HttpClient_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Http/ResponseTest.php';
require_once 'Zend/Http/ClientTest.php';

class Zend_Http_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_HttpClient');

        $suite->addTestSuite('Zend_Http_ClientTest');
		$suite->addTestSuite('Zend_Http_ResponseTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Http_AllTests::main') {
    Zend_HttpClient_AllTests::main();
}
