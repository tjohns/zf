<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Currency/AllTests.php';
require_once 'Zend/Date/AllTests.php';
require_once 'Zend/Locale/AllTests.php';
require_once 'Zend/Mail/AllTests.php';
require_once 'Zend/Measure/AllTests.php';
require_once 'Zend/XmlRpc/AllTests.php';
require_once 'Zend/Registry/AllTests.php';

require_once 'Zend/MeasureTest.php';

class Zend_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend');

        // place other tests here for incubator suite

        $suite->addTest(Zend_Currency_AllTests::suite());
        $suite->addTest(Zend_Date_AllTests::suite());
        $suite->addTest(Zend_Locale_AllTests::suite());
        $suite->addTest(Zend_Mail_AllTests::suite());
        $suite->addTest(Zend_Measure_AllTests::suite());
        $suite->addTest(Zend_XmlRpc_AllTests::suite());
        $suite->addTest(Zend_Registry_AllTests::suite());

        $suite->addTestSuite('Zend_MeasureTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_AllTests::main') {
    Zend_AllTests::main();
}
