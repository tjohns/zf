<?php

if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Cache_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

error_reporting(E_STRICT);
date_default_timezone_set('Europe/Paris'); // to avoid an E_STRICT notice
require_once 'FactoryTest.php';
require_once 'CoreTest.php';
require_once 'FileBackendTest.php';
require_once 'SqliteBackendTest.php';
require_once 'OutputFrontendTest.php';
require_once 'FunctionFrontendTest.php';
require_once 'ClassFrontendTest.php';
require_once 'FileFrontendTest.php';

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
		$suite->addTestSuite('Zend_Cache_CoreTest');
        $suite->addTestSuite('Zend_Cache_FileBackendTest');
        $suite->addTestSuite('Zend_Cache_OutputFrontendTest');
        $suite->addTestSuite('Zend_Cache_FunctionFrontendTest');
        $suite->addTestSuite('Zend_Cache_ClassFrontendTest');
        $suite->addTestSuite('Zend_Cache_SqliteBackendTest');
        $suite->addTestSuite('Zend_Cache_FileFrontendTest');
        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Cache_AllTests::main') {
    Zend_Cache_AllTests::main();
}
