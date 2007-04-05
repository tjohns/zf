<?php
if (!defined('PHPUnit_MAIN_METHOD')) {

    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_AllTests::main');

    set_include_path(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
                 . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Action/HelperBrokerTest.php';
require_once 'Action/Helper/FlashMessengerTest.php';

class Zend_Controller_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Controller');

        $suite->addTestSuite('Zend_Controller_Action_HelperBrokerTest');
        $suite->addTestSuite('Zend_Controller_Action_Helper_FlashMessengerTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Controller_AllTests::main') {
    Zend_Controller_AllTests::main();
}
