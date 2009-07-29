<?php
error_reporting(E_ALL|E_STRICT); 
ini_set('display_errors',1);

set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).'/../');
require_once 'Zend/Image.php';