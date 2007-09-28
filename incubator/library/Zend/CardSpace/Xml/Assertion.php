<?php

require_once 'Zend/CardSpace/Xml/Exception.php';
require_once 'Zend/CardSpace/Xml/Assertion/Interface.php';

final class Zend_CardSpace_Xml_Assertion {

	const TYPE_SAML = 'urn:oasis:names:tc:SAML:1.0:assertion';

	private function __construct() { }
	
	static public function getInstance($xmlData) {
		
		if($xmlData instanceof Zend_CardSpace_Xml_Element) {
			$strXmlData = $xmlData->asXML();
		} else if (is_string($xmlData)) {
			$strXmlData = $xmlData;
		} else {
			throw new Zend_CardSpace_Xml_Exception("Invalid Data provided to create instance");
		}
		
		$sxe = simplexml_load_string($strXmlData);
		
		$namespaces = $sxe->getDocNameSpaces();

		foreach($namespaces as $namespace) {
			switch($namespace) {
				case self::TYPE_SAML:
					include_once 'Zend/CardSpace/Xml/Assertion/SAML.php';
					return simplexml_load_string($strXmlData, 'Zend_CardSpace_Xml_Assertion_SAML', null);
			}
		}
		
		throw new Zend_CardSpace_Xml_Exception("Unable to determine Assertion type by Namespace");
	}
}