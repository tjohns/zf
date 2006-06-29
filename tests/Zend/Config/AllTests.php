<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Config_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Config/IniTest.php';
require_once 'Zend/Config/ArrayTest.php';
require_once 'Zend/Config/XmlTest.php';

class Zend_Config_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Config');

        $suite->addTestSuite('Zend_Config_IniTest');
        $suite->addTestSuite('Zend_Config_ArrayTest');
        $suite->addTestSuite('Zend_Config_XmlTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Config_AllTests::main') {
    Zend_Config_AllTests::main();
}
