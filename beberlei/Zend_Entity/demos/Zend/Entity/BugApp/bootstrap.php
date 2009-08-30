<?php

set_include_path(
    realpath(dirname(__FILE__)."/../../../../library") . PATH_SEPARATOR .
    get_include_path()
);

require_once "entities/Bug.php";
require_once "entities/User.php";
require_once "entities/Product.php";
require_once "Zend/Loader/Autoloader.php";

$loader = Zend_Loader_Autoloader::getInstance();

require_once dirname(__FILE__)."/config.php";

$pathToMetadataDirectory = dirname(__FILE__)."/definitions";
$metadataFactory = new Zend_Entity_MetadataFactory_Code($pathToMetadataDirectory);

$db = Zend_Db::factory($dbAdapter, $dbConfig);
$mapper = Zend_Db_Mapper_Mapper::create(array('db' => $db, 'metadataFactory' => $metadataFactory));

$entityManager = new Zend_Entity_Manager(array('mapper' => $mapper, 'metadataFactory' => $metadataFactory));
