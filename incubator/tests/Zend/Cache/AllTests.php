<?php

if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Cache_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'FactoryTest.php';
require_once 'FactoryException.php';
require_once 'CoreTest.php';

class Zend_Cache_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Cache');
		$suite->addTestSuite('Zend_Cache_FactoryTest');
		$suite->addTestSuite('Zend_Cache_FactoryException');
		$suite->addTestSuite('Zend_Cache_CoreTest');
        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Cache_AllTests::main') {
    Zend_Cache_AllTests::main();
}
