<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Mail_AllTests::main');
}

if (!defined('TESTS_ZEND_MAIL_POP3_ENABLED')) {
    if (is_readable('TestConfiguration.php')) {
        require_once 'TestConfiguration.php';
    } else {
        require_once 'TestConfiguration.php.dist';
    }
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Mail/SmtpTest.php';

class Zend_Mail_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Mail');

        if (TESTS_ZEND_MAIL_SMTP_ENABLED) {
            $suite->addTestSuite('Zend_Mail_SmtpTest');
        }

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Mail_AllTests::main') {
    Zend_Mail_AllTests::main();
}
