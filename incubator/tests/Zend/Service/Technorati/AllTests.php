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
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TechnoratiTestHelper.php';


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Technorati_AllTests::main');
}


require 'TechnoratiTest.php';
require 'AuthorTest.php';
require 'WeblogTest.php';
require 'BlogInfoResultTest.php';
require 'GetInfoResultTest.php';
require 'KeyInfoResultTest.php';
require 'TagsResultTest.php';
require 'TagsResultSetTest.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Technorati');

        $suite->addTestSuite('Zend_Service_Technorati_TechnoratiTest');
        $suite->addTestSuite('Zend_Service_Technorati_AuthorTest');
        $suite->addTestSuite('Zend_Service_Technorati_WeblogTest');
        $suite->addTestSuite('Zend_Service_Technorati_BlogInfoResultTest');
        $suite->addTestSuite('Zend_Service_Technorati_GetInfoResultTest');
        $suite->addTestSuite('Zend_Service_Technorati_KeyInfoResultTest');
        $suite->addTestSuite('Zend_Service_Technorati_TagsResultTest');
        $suite->addTestSuite('Zend_Service_Technorati_TagsResultSetTest');
        
        return $suite;
    }
}

