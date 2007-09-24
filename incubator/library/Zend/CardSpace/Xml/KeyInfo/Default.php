<?php

require_once 'Zend/CardSpace/Xml/KeyInfo/Abstract.php';
require_once 'Zend/CardSpace/Xml/SecurityTokenReference.php';

class Zend_CardSpace_Xml_KeyInfo_Default extends Zend_CardSpace_Xml_KeyInfo_Abstract {
	
	public function getSecurityTokenReference() {
		
		$this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
		
		list($sectokenref) = $this->xpath('//o:SecurityTokenReference');
		
		if(!($sectokenref instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception('Could not locate the Security Token Reference');
		}
				
		return Zend_CardSpace_Xml_SecurityTokenReference::getInstance($sectokenref);
	}
}