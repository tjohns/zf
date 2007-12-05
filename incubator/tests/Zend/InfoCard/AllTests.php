<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_InfoCard_AllTests::main');
}

// This is a hack so we can run the InfoCard AllTests.php manually
ini_set('include_path', ini_get('include_path') . ":..".DIRECTORY_SEPARATOR."..:..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."library:..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."library");

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/InfoCard/XmlParsing.php';
require_once 'Zend/InfoCard/Process.php';
require_once 'Zend/InfoCard/Assertion_Test.php';

class AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite("Zend Framework - Zend_InfoCard");

        $suite->addTestSuite('Zend_InfoCard_XmlParsing');
	$suite->addTestSuite('Zend_InfoCard_Process');
	$suite->addTestSuite('Zend_InfoCard_Assertion_Test');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_InfoCard_AllTests::main') {
    Zend_InfoCard_AllTests::main();
}
