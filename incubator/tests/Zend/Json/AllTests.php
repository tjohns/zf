<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_XmlRpc_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Json/ServerTest.php';
require_once 'Zend/Json/JsonXMLTest.php';

class Zend_Json_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Json');

        $suite->addTestSuite('Zend_Json_ServerTest');
        //$suite->addTestSuite('Zend_Json_ClientTest');
        $suite->addTestSuite('Zend_Json_JsonXMLTest');
       
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Json_AllTests::main') {
    Zend_Json_AllTests::main();
}
