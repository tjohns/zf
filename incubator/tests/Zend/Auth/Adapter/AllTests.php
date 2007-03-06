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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 3412 2007-02-14 22:22:35Z darby $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Auth_Adapter_AllTests::main');
}


/**
 * PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';


/**
 * PHPUnit_TextUI_TestRunner
 */
require_once 'PHPUnit/TextUI/TestRunner.php';


/**
 * Zend_Auth_FileResolverTest
 */
require_once 'Zend/Auth/Adapter/FileResolverTest.php';

/**
 * Zend_Auth_HttpObjectTest
 */
require_once 'Zend/Auth/Adapter/HttpObjectTest.php';

/**
 * Zend_Auth_HttpAuthTest
 */
require_once 'Zend/Auth/Adapter/HttpAuthTest.php';

/**
 * Zend_Auth_HttpProxyTest
 */
require_once 'Zend/Auth/Adapter/HttpProxyTest.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Auth Adapters');

        $suite->addTestSuite('Zend_Auth_Adapter_FileResolverTest');
        $suite->addTestSuite('Zend_Auth_Adapter_HttpObjectTest');
        $suite->addTestSuite('Zend_Auth_Adapter_HttpAuthTest');
        $suite->addTestSuite('Zend_Auth_Adapter_HttpProxyTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Auth_Adapter_AllTests::main') {
    Zend_Auth_Adapter_AllTests::main();
}
