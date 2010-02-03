<?php
require_once "PHPUnit/Framework.php";
require_once "PHPUnit/Extensions/Database/TestCase.php";

ini_set('display_errors',1);
error_reporting(E_ALL|E_STRICT);

define('ZEND_RBAC_LIBRARY_PATH', realpath(dirname(__FILE__).'/../../../../../branches/user/freak/Zend_Rbac/Library').'/');
define('ZEND_LIBRARY_PATH', realpath(dirname(__FILE__).'/../../../../../trunk/library').'/');

set_include_path(
    ZEND_RBAC_LIBRARY_PATH . PATH_SEPARATOR  .
    ZEND_LIBRARY_PATH . PATH_SEPARATOR  .    
    dirname(__FILE__) . PATH_SEPARATOR .
    get_include_path() . PATH_SEPARATOR
);

require_once 'Zend/Loader/Autoloader.php';
$autoloader =Zend_Loader_Autoloader::getInstance();
$autoloader->suppressNotFoundWarnings(false);

//PHPUnit_Util_Filter::addDirectoryToWhitelist(ZEND_RBAC_LIBRARY_PATH, ".php");
