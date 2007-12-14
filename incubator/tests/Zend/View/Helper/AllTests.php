<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_AllTests::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/View/Helper/DoctypeTest.php';
require_once 'Zend/View/Helper/HeadLinkTest.php';
require_once 'Zend/View/Helper/HeadMetaTest.php';
require_once 'Zend/View/Helper/HeadScriptTest.php';
require_once 'Zend/View/Helper/HeadStyleTest.php';
require_once 'Zend/View/Helper/HeadTitleTest.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
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

        $suite->addTestSuite('Zend_View_Helper_DoctypeTest');
        $suite->addTestSuite('Zend_View_Helper_HeadLinkTest');
        $suite->addTestSuite('Zend_View_Helper_HeadMetaTest');
        $suite->addTestSuite('Zend_View_Helper_HeadScriptTest');
        $suite->addTestSuite('Zend_View_Helper_HeadStyleTest');
        $suite->addTestSuite('Zend_View_Helper_HeadTitleTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_AllTests::main') {
    Zend_View_Helper_AllTests::main();
}
