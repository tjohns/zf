#! /usr/bin/php -d safe_mode=Off
<?php

/**
 * DEV ONLY START
 */
define('ZF_BIN_PATH', dirname(__FILE__));
set_include_path('.:/usr/lib/php');
set_include_path(get_include_path() . PATH_SEPARATOR . ZF_BIN_PATH . '/../library/' . PATH_SEPARATOR . ZF_BIN_PATH . '/../../../repo-trunk/library');
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();
/**
 * DEV ONLY STOP
 */

require_once 'Zend/Tool/Cli.php';

try {
    $cliTool = new Zend_Tool_Cli(); // replace this with a factory if we decide to support more than cli
    $cliTool
        ->setArguments(array_splice($_SERVER['argv'], 1))
        ->run();

}  catch (Zend_Console_Exception $e) {
    // get usage?
}

        
echo PHP_EOL . PHP_EOL;
