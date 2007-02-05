<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Delicious_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'PublicDataTest.php';
require_once 'PrivateDataTest.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Delicious');

        $suite->addTestSuite('Zend_Service_Delicious_PublicDataTest');
        $suite->addTestSuite('Zend_Service_Delicious_PrivateDataTest');

        return $suite;
    }
}

