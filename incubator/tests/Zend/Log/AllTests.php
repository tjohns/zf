<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_XmlRpc_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Log/BuiltinFilterTest.php';
require_once 'Zend/Log/LevelTest.php';
require_once 'Zend/Log/LogTest.php';

class Zend_Log_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Log');

        $suite->addTestSuite('Zend_Log_BuiltinFilterTest');
        $suite->addTestSuite('Zend_Log_LevelTest');
        $suite->addTestSuite('Zend_Log_LogTest');
       
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Log_AllTests::main') {
    Zend_Log_AllTests::main();
}
