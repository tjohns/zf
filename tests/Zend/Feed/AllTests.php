<?php
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'Zend_Feed_AllTests::main');

    /**
     * Prepend library/ to the include_path.  This allows the tests to run out of the box and
     * helps prevent finding other copies of the framework that might be present.
     */
    set_include_path(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'library'
                     . PATH_SEPARATOR . get_include_path());
}

require_once 'PHPUnit2/Framework/TestSuite.php';
require_once 'PHPUnit2/TextUI/TestRunner.php';

require_once 'Zend/Feed/ArrayAccessTest.php';
require_once 'Zend/Feed/AtomEntryOnlyTest.php';
require_once 'Zend/Feed/AtomPublishingTest.php';
require_once 'Zend/Feed/CountTest.php';
require_once 'Zend/Feed/ElementTest.php';
require_once 'Zend/Feed/ImportTest.php';
require_once 'Zend/Feed/IteratorTest.php';

class Zend_Feed_AllTests
{
    public static function main()
    {
        PHPUnit2_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit2_Framework_TestSuite('Zend Framework - Zend_Feed');

        $suite->addTestSuite('Zend_Feed_ArrayAccessTest');
        $suite->addTestSuite('Zend_Feed_AtomEntryOnlyTest');
        $suite->addTestSuite('Zend_Feed_AtomPublishingTest');
        $suite->addTestSuite('Zend_Feed_CountTest');
        $suite->addTestSuite('Zend_Feed_ElementTest');
        $suite->addTestSuite('Zend_Feed_ImportTest');
        $suite->addTestSuite('Zend_Feed_IteratorTest');

        return $suite;
    }
}

if (PHPUnit2_MAIN_METHOD == 'Zend_Feed_AllTests::main') {
    Zend_Feed_AllTests::main();
}
