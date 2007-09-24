<?php

require_once 'Zend/CardSpace/Xml/Exception.php';

final class Zend_CardSpace_Xml_EncryptedData {
	
	private function __construct() { }
	
	/**
	 * Returns an instance of the class
	 *
	 * @param string $xmlData The XML EncryptedData String
	 * @return Zend_CardSpace_Xml_EncryptedData_Abstract
	 */
	static public function getInstance($xmlData) {
		
		if($xmlData instanceof Zend_CardSpace_Xml_Element) {
			$strXmlData = $xmlData->asXML();
		} else if (is_string($xmlData)) {
			$strXmlData = $xmlData;
		} else {
			throw new Zend_CardSpace_Xml_Exception("Invalid Data provided to create instance");
		}
		
		$sxe = simplexml_load_string($strXmlData);
		
		switch($sxe['Type']) {
			case 'http://www.w3.org/2001/04/xmlenc#Element':
				include_once 'Zend/CardSpace/Xml/EncryptedData/XmlEnc.php';
				return simplexml_load_string($strXmlData, 'Zend_CardSpace_Xml_EncryptedData_XmlEnc');
			default:
				throw new Zend_CardSpace_Xml_Exception("Unknown EncryptedData type found");
		}
	}
}