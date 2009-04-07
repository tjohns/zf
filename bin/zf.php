<?php

/**
 * DEV ONLY START - this will be removed when this hits trunk
 */
$zendFrameworkPath    = realpath(dirname(__FILE__) . '/../../library/'); // trunk
$zendFrameworkIncPath = realpath(dirname(__FILE__) . '/../library/');    // incubator

if (($zendFrameworkPathOverride = getenv('ZF_PATH')) != '') {
    $zendFrameworkPath = $zendFrameworkPathOverride;
}

if (($zendFrameworkIncPathOverride = getenv('ZFI_PATH')) != '') {
    $zendFrameworkIncPath = $zendFrameworkIncPathOverride;
}

unset($zendFrameworkPathOverride, $zendFrameworkIncPathOverride);

$includePaths = array();
if ($zendFrameworkIncPath) {
    $includePaths[] = $zendFrameworkIncPath;
}
if ($zendFrameworkPath) {
    $includePaths[] = $zendFrameworkPath;
}

$includePaths[] = get_include_path();
set_include_path(implode(PATH_SEPARATOR, $includePaths));

unset($zendFrameworkPath, $zendFrameworkIncPath, $includePaths);
/**
 * DEV ONLY STOP
 */

require_once 'Zend/Tool/Framework/Client/Console.php';

// run the cli client
Zend_Tool_Framework_Client_Console::main();
