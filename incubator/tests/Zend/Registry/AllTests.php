<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Registry_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Registry/RegistryTest.php';

class Zend_Registry_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Registry');

        $suite->addTestSuite('Zend_Registry_RegistryTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Registry_AllTests::main') {
    Zend_Registry_AllTests::main();
}
