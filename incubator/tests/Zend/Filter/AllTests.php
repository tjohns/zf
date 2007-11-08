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
 * @package    Zend
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    require_once dirname(__FILE__) . '/../../TestHelper.php';
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Filter/AlphaTest.php';
require_once 'Zend/Filter/CamelCaseToDashTest.php';
require_once 'Zend/Filter/CamelCaseToSeparatorTest.php';
require_once 'Zend/Filter/CamelCaseToUnderscoreTest.php';
require_once 'Zend/Filter/InflectorTest.php';
require_once 'Zend/Filter/PregReplaceTest.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Filter');

        $suite->addTestSuite('Zend_Filter_AlphaTest');
        $suite->addTestSuite('Zend_Filter_CamelCaseToDashTest');
        $suite->addTestSuite('Zend_Filter_CamelCaseToSeparatorTest');
        $suite->addTestSuite('Zend_Filter_CamelCaseToUnderscoreTest');
        $suite->addTestSuite('Zend_Filter_InflectorTest');
        $suite->addTestSuite('Zend_Filter_PregReplaceTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Filter_AllTests::main') {
    Zend_Filter_AllTests::main();
}
