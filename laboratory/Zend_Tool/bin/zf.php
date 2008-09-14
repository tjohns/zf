<?php

/**
 * DEV ONLY START - this will be removed when this hits incubator
 */
$zendFrameworkPath    = null;
$zendFrameworkLabPath = null;

if ($zendFrameworkPath === null) {
    $zendFrameworkPath = @include_once 'Zend/Loader.php';
    if ($zendFrameworkPath === false) {
        // get the env var?
        $zendFrameworkPath = getenv('ZF_PATH');
        if ($zendFrameworkPath == '' || !file_exists($zendFrameworkPath)) {
            die('zf.php cannot find the Zend Framework Standard Library.  Please either set the $zendFrameworkPath in zf.php OR set the ZF_PATH environment variable.' . PHP_EOL);
        }
    }
}

if ($zendFrameworkLabPath === null) {
    $zendFrameworkLabPath = @include_once 'ZendL/Tool/Rpc/Endpoint/Cli.php';
    if ($zendFrameworkLabPath === false) {
        // get the env var?
        $zendFrameworkLabPath = getenv('ZFL_PATH');
        if ($zendFrameworkLabPath == '' || !file_exists($zendFrameworkLabPath)) {
            die('zf.php cannot find the Zend Framework Labratory Library.  Please either set the $zendFrameworkPath in zf.php OR set the ZFL_PATH environment variable.' . PHP_EOL);
        }
    }
}

if ($zendFrameworkLabPath !== 1) {
    set_include_path($zendFrameworkLabPath . PATH_SEPARATOR . get_include_path());
}

if ($zendFrameworkPath !== 1) {
    set_include_path($zendFrameworkPath . PATH_SEPARATOR . get_include_path());
}

unset($zendFrameworkPath, $zendFrameworkLabPath);
/**
 * DEV ONLY STOP
 */

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

// run the cli endpoint
ZendL_Tool_Rpc_Endpoint_Cli::main();
