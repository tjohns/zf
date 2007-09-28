<?php

interface Zend_CardSpace_Adapter_Interface {
	public function storeAssertion($assertionURI, $assertionID, $conditions);
	public function retrieveAssertion($assertionURI, $assertionID);
	public function removeAssertion($asserionURI, $assertionID);
}