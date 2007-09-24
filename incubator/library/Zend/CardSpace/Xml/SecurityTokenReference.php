<?php

require_once 'Zend/CardSpace/Xml/Element.php';

class Zend_CardSpace_Xml_SecurityTokenReference extends Zend_CardSpace_Xml_Element {

	const ENCODING_BASE64BIN = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';
	
	static public function getInstance($xmlData) {
		if($xmlData instanceof Zend_CardSpace_Xml_Element) {
			$strXmlData = $xmlData->asXML();
		} else if (is_string($xmlData)) {
			$strXmlData = $xmlData;
		} else {
			throw new Zend_CardSpace_Xml_Exception("Invalid Data provided to create instance");
		}
		
		$sxe = simplexml_load_string($strXmlData);
		
		if($sxe->getName() != "SecurityTokenReference") {
			throw new Zend_CardSpace_Xml_Exception("Invalid XML Block provided for SecurityTokenReference");
		}
		
		return simplexml_load_string($strXmlData, "Zend_CardSpace_Xml_SecurityTokenReference");		
	}
	
	protected function getKeyIdentifier() {
		$this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
		list($keyident) = $this->xpath('//o:KeyIdentifier');
		
		if(!($keyident instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Failed to retrieve Key Identifier");
		}
		
		return $keyident;
	}
	
	public function getKeyThumbprintType() {

		$keyident = $this->getKeyIdentifier();
		
		$dom = self::convertToDOM($keyident);

		if(!$dom->hasAttribute('ValueType')) {
			throw new Zend_CardSpace_Xml_Exception("Key Identifier did not provide a type for the value");
		}
		
		return $dom->getAttribute('ValueType');
	}
	
	public function getKeyThumbprintEncodingType() {
		
		$keyident = $this->getKeyIdentifier();
		
		$dom = self::convertToDOM($keyident);
		
		if(!$dom->hasAttribute('EncodingType')) {
			throw new Zend_CardSpace_Xml_Exception("Unable to determine the encoding type for the key identifier");
		}
		
		return $dom->getAttribute('EncodingType');
	}
	
	public function getKeyReference($decode = true) {
		$keyIdentifier = $this->getKeyIdentifier();
		
		$dom = self::convertToDOM($keyIdentifier);
		$encoded = $dom->nodeValue;
		
		if(empty($encoded)) {
			throw new Zend_CardSpace_Xml_Exception("Could not find the Key Reference Encoded Value");
		}
		
		if($decode) {
			
			$decoded = "";
			switch($this->getKeyThumbprintEncodingType()) {
				case self::ENCODING_BASE64BIN:
					$decoded = base64_decode($encoded, true);
					break;
				default:
					throw new Zend_CardSpace_Xml_Exception("Unknown Key Reference Encoding Type: {$this->getKeyThumbprintEncodingType()}");
			}
			
			if(!$decoded || empty($decoded)) {
				throw new Zend_CardSpace_Xml_Exception("Failed to decode key reference");
			}
			
			return $decoded;
		}
		
		return $encoded;
	}
}