<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_AllTests::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/View/Helper/FieldsetTest.php';
require_once 'Zend/View/Helper/FormTest.php';
require_once 'Zend/View/Helper/FormErrorsTest.php';
require_once 'Zend/View/Helper/FormMultiCheckboxTest.php';
require_once 'Zend/View/Helper/JsonTest.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Helper_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_View_Helper');

        $suite->addTestSuite('Zend_View_Helper_FieldsetTest');
        $suite->addTestSuite('Zend_View_Helper_FormTest');
        $suite->addTestSuite('Zend_View_Helper_FormErrorsTest');
        $suite->addTestSuite('Zend_View_Helper_FormMultiCheckboxTest');
        $suite->addTestSuite('Zend_View_Helper_JsonTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_AllTests::main') {
    Zend_View_Helper_AllTests::main();
}
