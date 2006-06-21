<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'AllTests::main');
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

/**
 * Read in user-defined test configuration if available; otherwise, read default test configuration
 */
if (is_readable('TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

/**
 * Prepend library/ to the include_path.  This allows the tests to run out of the box and
 * helps prevent finding other copies of the framework that might be present.
 */
set_include_path(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'library'
                 . PATH_SEPARATOR . '.' . PATH_SEPARATOR . get_include_path());

require_once 'ZendTest.php';
require_once 'Zend/AllTests.php';

class AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework');

        $suite->addTestSuite('ZendTest');

        $suite->addTest(Zend_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
