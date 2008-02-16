#!/usr/bin/php
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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once('Zend/Console/Factory.php');

function println($str = '')
{
    print("$str\n");
}

try {
    // Initiate all arguments and validate them
    $zc = Zend_Console_Factory::makeConsole($_SERVER['argv']);

    if($zc) {
        // Looks like everything is in order, execute the command
        $zc->execute();
    }
}  catch (Zend_Console_Exception $e) {
    println($e->getConsoleMessage());
    println();
    println('Usage:');
    println($e->getConsoleUsage());
    exit($e->getConsoleCode());
}

// As far as we know, everything came off just fine
exit(0);
?>