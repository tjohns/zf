<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_HttpClient_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Http/ResponseTest.php';
require_once 'Zend/Http/ClientTest.php';

class Zend_Http_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_HttpClient');

        $suite->addTestSuite('Zend_Http_ClientTest');
		$suite->addTestSuite('Zend_Http_Client_ResponseTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_AllTests::main') {
    Zend_HttpClient_AllTests::main();
}
