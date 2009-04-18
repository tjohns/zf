<?php

define('ZEND_ENTITY_MAPPER_LIBRARY_PATH', realpath(dirname(__FILE__)."/../library"));

set_include_path(
    get_include_path() . PATH_SEPARATOR .
    ZEND_ENTITY_MAPPER_LIBRARY_PATH
);

define('ZEND_ENTITY_CLINIC_SQLITE_DATA', dirname(__FILE__)."/Zend/Entity/ScenarioData/Clinic/clinic.sqlite");

require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();

PHPUnit_Util_Filter::addDirectoryToWhitelist(ZEND_ENTITY_MAPPER_LIBRARY_PATH, ".php");

require_once "PHPUnit/Framework.php";

require_once "Zend/Entity/Fixture/Entities.php";
