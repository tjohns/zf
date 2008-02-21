<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Service/SlideShareTest.php';

class Zend_Service_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service');

		$suite->addTestSuite('Zend_Service_SlideShareTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_AllTests::main') {
    Zend_Service_AllTests::main();
}
