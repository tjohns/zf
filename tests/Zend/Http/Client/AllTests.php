<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Http_Client_AllTests::main');
}

// Read local configuration
if (! defined('TESTS_ZEND_HTTP_CLIENT_BASEURI') &&
    is_readable('TestConfiguration.php')) {

    require_once 'TestConfiguration.php';
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Http/Client/StaticTest.php';
require_once 'Zend/Http/Client/SocketTest.php';
require_once 'Zend/Http/Client/SocketKeepaliveTest.php';
require_once 'Zend/Http/Client/TestAdapterTest.php';
//require_once 'Zend/Http/Client/CurlTest.php';

class Zend_Http_Client_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTestSuite('Zend_Http_Client_StaticTest');
        $suite->addTestSuite('Zend_Http_Client_SocketTest');
        $suite->addTestSuite('Zend_Http_Client_SocketKeepaliveTest');
        $suite->addTestSuite('Zend_Http_Client_TestAdapterTest');
        //$suite->addTestSuite('Zend_Http_Client_CurlTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Http_Client_AllTests::main') {
    Zend_AllTests::main();
}
