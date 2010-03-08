<?php

require_once 'PHPUnit/Framework.php';

define('ZEND_DOCTRINE_DC12_DIRECTORY', '/home/benny/code/php/wsnetbeans/Doctrine/branches/1.2/lib');
define('ZEND_DOCTRINE_DC12_SFYAML', '/home/benny/code/php/wsnetbeans/Doctrine/branches/1.2/lib/vendor/sfYaml/sfYaml.php');

set_include_path(
    dirname(__FILE__)."/../library" . PATH_SEPARATOR .
    ZEND_DOCTRINE_DC12_DIRECTORY . PATH_SEPARATOR . get_include_path()
);

require_once 'Zend/Loader/Autoloader.php';
$loader = Zend_Loader_Autoloader::getInstance();
$loader->registerNamespace('Doctrine');

require_once(ZEND_DOCTRINE_DC12_SFYAML);