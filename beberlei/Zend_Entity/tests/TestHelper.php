<?php

require_once "PHPUnit/Framework.php";
require_once "PHPUnit/Extensions/Database/TestCase.php";

define('ZEND_ENTITY_LIBRARY_PATH', realpath(dirname(__FILE__)."/../library"));

set_include_path(
    get_include_path() . PATH_SEPARATOR .
    ZEND_ENTITY_LIBRARY_PATH . PATH_SEPARATOR .
    dirname(__FILE__)
);

define('ZEND_ENTITY_CLINIC_SQLITE_DATA', dirname(__FILE__)."/Zend/Entity/ScenarioData/Clinic/clinic.sqlite");

require_once 'Zend/Loader/Autoloader.php';
$autoloader =Zend_Loader_Autoloader::getInstance();
$autoloader->suppressNotFoundWarnings(false);

PHPUnit_Util_Filter::addDirectoryToWhitelist(ZEND_ENTITY_LIBRARY_PATH, ".php");

require_once "Zend/Entity/Fixture/Entities.php";

if(file_exists("TestConfiguration.php")) {
    require_once "TestConfiguration.php";
}