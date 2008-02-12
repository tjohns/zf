<?php
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Config/Ini.php';
$config = new Zend_Config_Ini(dirname(__FILE__)."/config.ini");
$front = Zend_Controller_Front::getInstance();
$front->setControllerDirectory(dirname(dirname(__FILE__)) . '/application/controllers')
      ->setBaseUrl($config->baseUrl);
$front->dispatch();
