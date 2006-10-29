<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/AclTest.php';
require_once 'Zend/Controller/AllTests.php';
// require_once 'Zend/Currency/AllTests.php';
// require_once 'Zend/Date/AllTests.php';
require_once 'Zend/Http/AllTests.php';
// require_once 'Zend/LocaleTest.php';
// require_once 'Zend/Locale/AllTests.php';
// require_once 'Zend/Mail/AllTests.php';
// require_once 'Zend/MeasureTest.php';
// require_once 'Zend/Measure/AllTests.php';
require_once 'Zend/XmlRpc/AllTests.php';
require_once 'Zend/Registry/AllTests.php';

class Zend_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        // place other tests here for incubator suite

        $suite->addTestSuite('Zend_AclTest');
        $suite->addTest(Zend_Controller_AllTests::suite());
        // $suite->addTest(Zend_Currency_AllTests::suite());
        // $suite->addTest(Zend_Date_AllTests::suite());
        $suite->addTest(Zend_Http_AllTests::suite());
        // $suite->addTestSuite('Zend_LocaleTest');
        // $suite->addTest(Zend_Locale_AllTests::suite());
        // $suite->addTest(Zend_Mail_AllTests::suite());
        // $suite->addTestSuite('Zend_MeasureTest');
        // $suite->addTest(Zend_Measure_AllTests::suite());
        $suite->addTest(Zend_XmlRpc_AllTests::suite());
        $suite->addTest(Zend_Registry_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_AllTests::main') {
    Zend_AllTests::main();
}
