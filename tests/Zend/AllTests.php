<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/AclTest.php';
require_once 'Zend/Cache/AllTests.php';
require_once 'Zend/Db/AllTests.php';
require_once 'Zend/ConfigTest.php';
require_once 'Zend/Config/AllTests.php';
require_once 'Zend/Controller/AllTests.php';
require_once 'Zend/DateTest.php';
require_once 'Zend/Date/AllTests.php';
require_once 'Zend/Feed/AllTests.php';
require_once 'Zend/FilterTest.php';
require_once 'Zend/Gdata/AllTests.php';
require_once 'Zend/Http/AllTests.php';
require_once 'Zend/JsonTest.php';
require_once 'Zend/LocaleTest.php';
require_once 'Zend/Locale/AllTests.php';
require_once 'Zend/MailTest.php';
require_once 'Zend/MeasureTest.php';
require_once 'Zend/Measure/AllTests.php';
require_once 'Zend/MimeTest.php';
require_once 'Zend/Mime/AllTests.php';
require_once 'Zend/Pdf/AllTests.php';
require_once 'Zend/Registry/AllTests.php';
require_once 'Zend/Server/AllTests.php';
require_once 'Zend/UriTest.php';
require_once 'Zend/Uri/AllTests.php';
require_once 'Zend/ViewTest.php';
require_once 'Zend/XmlRpc/AllTests.php';


class Zend_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTestSuite('Zend_AclTest');
        $suite->addTest(Zend_Cache_AllTests::suite());
        $suite->addTest(Zend_Db_AllTests::suite());
        $suite->addTestSuite('Zend_ConfigTest');
        $suite->addTest(Zend_Config_AllTests::suite());
        $suite->addTest(Zend_Controller_AllTests::suite());
        $suite->addTestSuite('Zend_DateTest');
        $suite->addTest(Zend_Date_AllTests::suite());
        $suite->addTest(Zend_Feed_AllTests::suite());
        $suite->addTestSuite('Zend_LocaleTest');
        $suite->addTest(Zend_Locale_AllTests::suite());
        $suite->addTestSuite('Zend_FilterTest');
        $suite->addTest(Zend_Gdata_AllTests::suite());
        $suite->addTest(Zend_Http_AllTests::suite());
        $suite->addTestSuite('Zend_JsonTest');
        $suite->addTestSuite('Zend_MeasureTest');
        $suite->addTest(Zend_Measure_AllTests::suite());
        $suite->addTestSuite('Zend_MimeTest');
        $suite->addTest(Zend_Mime_AllTests::suite());
        $suite->addTest(Zend_Pdf_AllTests::suite());
        $suite->addTest(Zend_Registry_AllTests::suite());
        $suite->addTest(Zend_Server_AllTests::suite());
        $suite->addTestSuite('Zend_UriTest');
        $suite->addTest(Zend_Uri_AllTests::suite());
        $suite->addTestSuite('Zend_ViewTest');
        $suite->addTest(Zend_XmlRpc_AllTests::suite());
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_AllTests::main') {
    Zend_AllTests::main();
}
