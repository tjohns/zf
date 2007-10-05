<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    $base = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
    $paths = array(
        $base . '/incubator/tests',
        $base . '/incubator/library',
        $base . '/library'
    );
    set_include_path(implode(PATH_SEPARATOR, $paths) . PATH_SEPARATOR . get_include_path());
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/View/Helper/ActionTest.php';
require_once 'Zend/View/Helper/PartialTest.php';
require_once 'Zend/View/Helper/PartialLoopTest.php';
require_once 'Zend/View/Helper/PlaceholderTest.php';
require_once 'Zend/View/Helper/Placeholder/ContainerTest.php';
require_once 'Zend/View/Helper/Placeholder/RegistryTest.php';


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

        $suite->addTestSuite('Zend_View_Helper_ActionTest');
        $suite->addTestSuite('Zend_View_Helper_PartialTest');
        $suite->addTestSuite('Zend_View_Helper_PartialLoopTest');
        $suite->addTestSuite('Zend_View_Helper_PlaceholderTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_ContainerTest');
        $suite->addTestSuite('Zend_View_Helper_Placeholder_RegistryTest');
        
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_AllTests::main') {
    Zend_View_Helper_AllTests::main();
}
