<?php

require_once 'Zend/CardSpace/Adapter/Interface.php';
require_once 'Zend/CardSpace/Adapter/Exception.php';

class Zend_CardSpace_Adapter_Default implements Zend_CardSpace_Adapter_Interface {
	
	public function storeAssertion($assertionURI, $assertionID, $conditions) {
		return true;
	}
	
	public function retrieveAssertion($assertionURI, $assertionID) {
		return false;
	}
	
	public function removeAssertion($assertionURI, $assertionID) {
		return null;
	}
}