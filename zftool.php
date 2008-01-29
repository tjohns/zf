<?php

error_reporting(E_STRICT);

/**
 * @todo DEV ONLY
 */ 
set_include_path(
    './library/'
    . PATH_SEPARATOR . '../../repo-trunk/library'
    . PATH_SEPARATOR . get_include_path()
    );

/* make this smarter */
define('ZFTOOL_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'zftool');
set_include_path(
    ZFTOOL_PATH . DIRECTORY_SEPARATOR . 'library'
    . PATH_SEPARATOR . get_include_path()
    );
    
require_once 'Zend/Loader.php';
require_once 'ZfTool.php';

/**
 * @todo DEV ONLY
 */
Zend_Loader::registerAutoload();
/*
  ["argv"] => array(7) {
    [0] => string(10) "zftool.php"
    [1] => string(2) "-p"
    [2] => string(15) "./test-project/"
    [3] => string(6) "create"
    [4] => string(4) "file"
    [5] => string(2) "-f"
    [6] => string(11) "my-file.php"
  }
  ["argc"] => int(7)
*/
$_SERVER['argv'] = array(
    'zftool.phpx',
    '-p',
    './test-project/',
    'create',
    'file',
    '-f',
    'my-file.php'
    );
$_SERVER['argc'] = 7;
  
$zfTool = ZfTool::getInstance();
$zfTool
    ->setZfToolPath(ZFTOOL_PATH)
    ->run();

echo 'Goodbye.';

    