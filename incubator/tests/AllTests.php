<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

/**
 * Read in user-defined test configuration if available; otherwise, read default test configuration
 */
if (is_readable('TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * Prepend library/ to the include_path (incubator first, then regular
 * framework).  This allows the tests to run out of the box and helps
 * prevent finding other copies of the framework that might be
 * present.
 */
set_include_path(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'library'
                 . PATH_SEPARATOR
                 . dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'library'
                 . PATH_SEPARATOR . get_include_path());

require_once 'Zend/AllTests.php';

class AllTests
{
    public static function main()
    {
        $parameters = array();

        if (TESTS_GENERATE_REPORT && extension_loaded('xdebug')) {
            $parameters['reportDirectory'] = TESTS_GENERATE_REPORT_TARGET;
        }

        if (defined('TESTS_ZEND_LOCALE_FORMAT_SETLOCALE') && TESTS_ZEND_LOCALE_FORMAT_SETLOCALE) {
            // run all tests in a special locale
            setlocale(LC_ALL, TESTS_ZEND_LOCALE_FORMAT_SETLOCALE);
        }

        PHPUnit_TextUI_TestRunner::run(self::suite(), $parameters);
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Incubator');

        $suite->addTest(Zend_AllTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}
