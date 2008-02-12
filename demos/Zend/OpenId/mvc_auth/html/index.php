<?php
require_once 'Zend/Controller/Front.php';
Zend_Controller_Front::run(dirname(dirname(__FILE__)) . '/application/controllers');
