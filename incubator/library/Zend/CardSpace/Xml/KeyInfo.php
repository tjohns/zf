<?php

require_once 'Zend/CardSpace/Xml/Element.php';

class Zend_CardSpace_Xml_KeyInfo {
	
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
		
		if(!empty($namespaces)) {
			foreach($sxe->getDocNameSpaces() as $namespace) {
				switch($namespace) {
					case 'http://www.w3.org/2000/09/xmldsig#':
						include_once 'Zend/CardSpace/Xml/KeyInfo/XmlDSig.php';
						return simplexml_load_string($strXmlData, 'Zend_CardSpace_Xml_KeyInfo_XmlDSig');
					default:
						throw new Zend_CardSpace_Xml_Exception("Unknown KeyInfo Namespace provided");
				}
			}
		} 

		include_once 'Zend/CardSpace/Xml/KeyInfo/Default.php';
		return simplexml_load_string($strXmlData, 'Zend_CardSpace_Xml_KeyInfo_Default');
	}
	
}