<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Tool_Project_AllTests::main');
}

require_once 'Zend/Tool/Project/ProfileTest.php';
require_once 'Zend/Tool/Project/Context/RegistryTest.php';

class Zend_Tool_Project_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Tool_Project');

        $suite->addTestSuite('Zend_Tool_Project_ProfileTest');
        $suite->addTestSuite('Zend_Tool_Project_Context_RegistryTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tool_Project_AllTests::main') {
    Zend_Tool_Project_AllTests::main();
}
