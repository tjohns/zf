<?php
if (!defined('PHPUnit_MAIN_METHOD')) {

    define('PHPUnit_MAIN_METHOD', 'Zend_Controller_AllTests::main');

    set_include_path(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
                 . dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'library' . PATH_SEPARATOR
                 . get_include_path());
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'ActionTest.php';
require_once 'DispatcherTest.php';
require_once 'FrontTest.php';
require_once 'Plugin/BrokerTest.php';
require_once 'Request/HttpTest.php';
require_once 'Response/HttpTest.php';
require_once 'RouterTest.php';
require_once 'RouteTest.php';
require_once 'RewriteRouterTest.php';


class Zend_Controller_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Controller');

        $suite->addTestSuite('Zend_Controller_ActionTest');
        $suite->addTestSuite('Zend_Controller_DispatcherTest');
        $suite->addTestSuite('Zend_Controller_FrontTest');
        $suite->addTestSuite('Zend_Controller_Plugin_BrokerTest');
        $suite->addTestSuite('Zend_Controller_Request_HttpTest');
        $suite->addTestSuite('Zend_Controller_Response_HttpTest');
        $suite->addTestSuite('Zend_Controller_RouterTest');
        $suite->addTestSuite('Zend_Controller_RouteTest');
        $suite->addTestSuite('Zend_Controller_RewriteRouterTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Config_AllTests::main') {
    Zend_Config_AllTests::main();
}
