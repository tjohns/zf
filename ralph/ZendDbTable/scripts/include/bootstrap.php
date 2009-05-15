<?php

$libraryPath = realpath(dirname(__FILE__) . '/../../library/');

if (isset($_ENV['ZF_STANDARD_TRUNK'])) {
    $libraryPath .= PATH_SEPARATOR . $_ENV['ZF_STANDARD_TRUNK'];
}

set_include_path($libraryPath . PATH_SEPARATOR . get_include_path());

$ip = get_include_path();

$loaded = @include 'Zend/Loader/Autoloader.php';

if (!$loaded) {
    echo 'ZF Standard Trunk was not found in your include path, please set env var ZF_STANDARD_TRUNK to point to the library within it.';
    exit(1);
}

Zend_Loader_Autoloader::getInstance();

$dbAdapter = Zend_Db::factory('Pdo_Sqlite', array('dbname' => dirname(__FILE__) . '/db.sqlite'));
$dbAdapter->getConnection();

Zend_Db_Table::setDefaultAdapter($dbAdapter);
