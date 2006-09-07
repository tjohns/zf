<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Cache/AllTests.php';
require_once 'Zend/Db/AllTests.php';
require_once 'Zend/ConfigTest.php';
require_once 'Zend/Config/AllTests.php';
require_once 'Zend/Controller/AllTests.php';
require_once 'Zend/Feed/AllTests.php';
require_once 'Zend/FilterTest.php';
require_once 'Zend/Http/AllTests.php';
require_once 'Zend/JsonTest.php';
require_once 'Zend/MailTest.php';
require_once 'Zend/MimeTest.php';
require_once 'Zend/Mime/AllTests.php';
require_once 'Zend/Pdf/AllTests.php';
require_once 'Zend/UriTest.php';
require_once 'Zend/Uri/AllTests.php';
require_once 'Zend/ViewTest.php';


class Zend_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend');

        $suite->addTest(Zend_Cache_AllTests::suite());
        $suite->addTest(Zend_Db_AllTests::suite());
        $suite->addTestSuite('Zend_ConfigTest');
        $suite->addTest(Zend_Config_AllTests::suite());
        $suite->addTest(Zend_Controller_AllTests::suite());
        $suite->addTest(Zend_Feed_AllTests::suite());
        $suite->addTestSuite('Zend_FilterTest');
        $suite->addTest(Zend_Http_AllTests::suite());
        $suite->addTestSuite('Zend_JsonTest');
        $suite->addTestSuite('Zend_MimeTest');
        $suite->addTest(Zend_Mime_AllTests::suite());
        $suite->addTest(Zend_Pdf_AllTests::suite());
        $suite->addTestSuite('Zend_UriTest');
        $suite->addTest(Zend_Uri_AllTests::suite());
        $suite->addTestSuite('Zend_ViewTest');
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_AllTests::main') {
    Zend_AllTests::main();
}
