<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_XmlRpc_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Soap/AutoDiscoverTest.php';

class Zend_Soap_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Soap');

        $suite->addTestSuite('Zend_Soap_AutoDiscoverTest');
       
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Soap_AllTests::main') {
    Zend_Soap_AllTests::main();
}
