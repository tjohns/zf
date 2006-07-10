<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Mail_AllTests::main');
}

if(!defined('TESTS_ZEND_MAIL_POP3_ENABLED')) {
    if (is_readable('TestConfiguration.php')) {
        require_once 'TestConfiguration.php';
    } else {
        require_once 'TestConfiguration.php.dist';
    }
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Mail/MboxTest.php';
require_once 'Zend/Mail/Pop3Test.php';
require_once 'Zend/Mail/ListTest.php';

class Zend_Mail_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Mail');

        $suite->addTestSuite('Zend_Mail_ListTest');
        $suite->addTestSuite('Zend_Mail_MboxTest');
        if(TESTS_ZEND_MAIL_POP3_ENABLED) {
            $suite->addTestSuite('Zend_Mail_Pop3Test');
        }
        
        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Mail_AllTests::main') {
    Zend_Mail_AllTests::main();
}
