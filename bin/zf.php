#! /usr/bin/php -d safe_mode=Off
<?php

/**
 * DEV ONLY START
 */
$zendFrameworkPath = getenv('ZF_PATH');
if ($zendFrameworkPath == '' || !file_exists($zendFrameworkPath)) {
    die('While in development: please set env var ZF_PATH to your copy of zend framework.');
}
define('ZF_LIBRARY_PATH', $zendFrameworkPath);
define('CLI_LIBRARY_PATH', str_replace('\\', '/', dirname(__FILE__)) . '/../library/');
unset($zendFrameworkPath);
set_include_path(CLI_LIBRARY_PATH . PATH_SEPARATOR . ZF_LIBRARY_PATH);
/**
 * DEV ONLY STOP
 */

require_once 'Zend/Loader.php';

Zend_Loader::registerAutoload();




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
