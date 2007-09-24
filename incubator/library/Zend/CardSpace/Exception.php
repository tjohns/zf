<?php

if(class_exists("Zend_Exception")) {
	
	abstract class Zend_CardSpace_Exception_Abstract extends Zend_Exception {
		
	}
	
} else {
	abstract class Zend_CardSpace_Exception_Abstract extends Exception {
		
	}
}

class Zend_CardSpace_Exception extends Zend_CardSpace_Exception_Abstract {
	
}