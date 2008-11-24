<?php

/**
 * DEV ONLY START - this will be removed when this hits incubator
 */
$zendFrameworkPath    = dirname(__FILE__) . '/../../library/';
$zendFrameworkIncPath = dirname(__FILE__) . '/../library/';

if ($zendFrameworkPathOverride = getenv('ZF_PATH') != '') {
    $zendFrameworkPath = $zendFrameworkPathOverride;
}

if ($zendFrameworkIncPathOverride = getenv('ZFI_PATH') != '') {
    $zendFrameworkIncPath = $zendFrameworkIncPathOverride;
}

set_include_path($zendFrameworkIncPath . PATH_SEPARATOR . $zendFrameworkPath . PATH_SEPARATOR . get_include_path());

unset($zendFrameworkPath, $zendFrameworkIncPath, $zendFrameworkPathOverride, $zendFrameworkIncPathOverride);
/**
 * DEV ONLY STOP
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

// run the cli endpoint
Zend_Tool_Framework_Endpoint_Cli::main();
