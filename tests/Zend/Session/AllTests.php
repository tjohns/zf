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
 * @package    Zend_Session_AllTests
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

define('TESTS_GENERATE_REPORT_TARGET', '/var/www/html/tests');

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Session_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting ( E_ALL | E_STRICT );

#class Zend_Session_PathHelper {}
require_once 'PathHelper.php';

class Zend_Session_AllTests extends Zend_Session_PathHelper
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        // whitebox testing will be added at a later date
        // self::buildSessionTestFile('Core.php');

        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Session');

        require_once 'SessionTest.php';
        //require_once 'CoreTest.php';

        $suite->addTestSuite('Zend_SessionTest');
        #$suite->addTestSuite('Zend_Session_CoreTest');

        return $suite;
    }

    /*
     * Enable whitebox testing by making class extendable,
     * and converting private members to protected.
     *
     * @param string $filename - enable whitebox testing on code in $filename
     * @return bool            - successfully created whitebox test file?
     */
    protected static function buildSessionTestFile($filename)
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Core.php';

        if (false === ($out = fopen($filename, 'w'))) {
            return false;
        }

        $src = self::$pathIncubatorLibrary
            . DIRECTORY_SEPARATOR . 'Zend'
            . DIRECTORY_SEPARATOR . 'Session'
            . DIRECTORY_SEPARATOR . 'Core.php';
        
        echo "$src\n";
        $lines = file($src);
        
        foreach ($lines as $line) {
        
            $line = rtrim($line);
        
            if ($line === 'final class Zend_Session_Core') {
                fputs($out, "class Zend_Session_Core\n");
            } else {
                if (false === fputs($out, (preg_replace('/^(\s*)private static\s/',
                    '\1protected static ', $line)."\n"))) {
                    return false;
                }
            }
        }

        return true;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Session_AllTests::main') {
    Zend_Session_AllTests::main();
}
