<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Measure_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Measure/CapacityTest.php';

class Zend_Measure_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Measure');

        $suite->addTestSuite('Zend_Measure_CapacityTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Measure_AllTests::main') {
    Zend_Measure_AllTests::main();
}
