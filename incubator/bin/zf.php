<?php

/**
 * DEV ONLY START - this will be removed when this hits trunk
 */
$zendFrameworkPath    = realpath(dirname(__FILE__) . '/../../library/'); // trunk
$zendFrameworkIncPath = realpath(dirname(__FILE__) . '/../library/');    // incubator

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

if (isset($_ENV['PWD'])) {
    chdir($_ENV['PWD']);
}

require_once 'Zend/Loader.php';
require_once 'Zend/Tool/Framework/Client/Cli.php';

// run the cli client
Zend_Tool_Framework_Client_Cli::main();
