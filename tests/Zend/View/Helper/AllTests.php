<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_AllTests::main');
}
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/View/Helper/DeclareVarsTest.php';
require_once 'Zend/View/Helper/FormCheckboxTest.php';
require_once 'Zend/View/Helper/FormLabelTest.php';
require_once 'Zend/View/Helper/FormSelectTest.php';
require_once 'Zend/View/Helper/FormTextTest.php';
require_once 'Zend/View/Helper/HtmlListTest.php';
require_once 'Zend/View/Helper/LayoutTest.php';
require_once 'Zend/View/Helper/PlaceholderTest.php';
require_once 'Zend/View/Helper/Placeholder/ContainerTest.php';
require_once 'Zend/View/Helper/Placeholder/RegistryTest.php';
require_once 'Zend/View/Helper/UrlTest.php';


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

        $suite->addTestSuite('Zend_View_Helper_DeclareVarsTest');
        $suite->addTestSuite('Zend_View_Helper_FormCheckboxTest');
        $suite->addTestSuite('Zend_View_Helper_FormLabelTest');
        $suite->addTestSuite('Zend_View_Helper_FormSelectTest');
        $suite->addTestSuite('Zend_View_Helper_FormTextTest');
        $suite->addTestSuite('Zend_View_Helper_HtmlListTest');
        $suite->addTestSuite('Zend_View_Helper_LayoutTest');
        $suite->addTestSuite('Zend_View_Helper_PlaceholderTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_ContainerTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_RegistryTest');
        $suite->addTestSuite('Zend_View_Helper_UrlTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_AllTests::main') {
    Zend_View_Helper_AllTests::main();
}
