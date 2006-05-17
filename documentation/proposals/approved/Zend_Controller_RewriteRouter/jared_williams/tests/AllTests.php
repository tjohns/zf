<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_YARouter_AllTests::main');
}


require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once './YARouteTests.php';
require_once './YARouterTests.php';

class Zend_YARouter_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_YARouter');

        $suite->addTestSuite('Zend_YARoute_Test');
        $suite->addTestSuite('Zend_YARouter_Test');

        return $suite;
    }
}

    Zend_YARouter_AllTests::main();

