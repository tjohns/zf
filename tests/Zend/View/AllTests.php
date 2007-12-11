<?php
if (!defined('PHPUnit_MAIN_METHOD')) {

    define('PHPUnit_MAIN_METHOD', 'Zend_View_AllTests::main');

    set_include_path(
        dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
        . dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
        . get_include_path()
    );
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Helper/AllTests.php';

class Zend_View_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_View');

        $suite->addTest(Zend_View_Helper_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_AllTests::main') {
    Zend_View_AllTests::main();
}
