<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_YARouter_AllTests::main');
}


require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once './YARouteTests.php';
require_once './YARouterTests.php';

class Zend_YARouter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_YARouter');

        $suite->addTestSuite('Zend_YARoute_Test');
        $suite->addTestSuite('Zend_YARouter_Test');

        return $suite;
    }
}

    Zend_YARouter_AllTests::main();

