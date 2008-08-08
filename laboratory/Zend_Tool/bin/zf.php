<?php

/**
 * DEV ONLY START - this will be removed when this hits incubator
 */
$zendFrameworkPath = getenv('ZF_PATH');
if ($zendFrameworkPath == '' || !file_exists($zendFrameworkPath)) {
    die('While in development: please set env var ZF_PATH to your copy of zend framework.' . PHP_EOL);
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

ZendL_Tool_Rpc_Endpoint_Cli::main();
