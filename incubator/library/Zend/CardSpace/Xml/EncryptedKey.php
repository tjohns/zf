<?php

require_once 'Zend/CardSpace/Xml/Element.php';
require_once 'Zend/CardSpace/Xml/EncryptedKey.php';
require_once 'Zend/CardSpace/Xml/KeyInfo/Interface.php';

class Zend_CardSpace_Xml_EncryptedKey extends Zend_CardSpace_Xml_Element 
                                      implements Zend_CardSpace_Xml_KeyInfo_Interface {

	static public function getInstance($xmlData) {
		if($xmlData instanceof Zend_CardSpace_Xml_Element) {
			$strXmlData = $xmlData->asXML();
		} else if (is_string($xmlData)) {
			$strXmlData = $xmlData;
		} else {
			throw new Zend_CardSpace_Xml_Exception("Invalid Data provided to create instance");
		}
		
		$sxe = simplexml_load_string($strXmlData);

		if($sxe->getName() != "EncryptedKey") {
			throw new Zend_CardSpace_Xml_Exception("Invalid XML Block provided for EncryptedKey");
		}
		
		return simplexml_load_string($strXmlData, "Zend_CardSpace_Xml_EncryptedKey");
	}

	public function getEncryptionMethod() {
		
		$this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
		list($encryption_method) = $this->xpath("//e:EncryptionMethod");	
		
		if(!($encryption_method instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the e:EncryptionMethod KeyInfo encryption block");
		}
		
		$dom = self::convertToDOM($encryption_method);
		
		if(!$dom->hasAttribute('Algorithm')) {
			throw new Zend_CardSpace_Xml_Exception("Unable to determine the encryption algorithm in the Symmetric enc:EncryptionMethod XML block");
		}
		
		return $dom->getAttribute('Algorithm');
		
	}
	
	public function getDigestMethod() {
		$this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
		list($encryption_method) = $this->xpath("//e:EncryptionMethod");	
		
		if(!($encryption_method instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the e:EncryptionMethod KeyInfo encryption block");
		}

		if(!($encryption_method->DigestMethod instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the DigestMethod block");
		}
				
		$dom = self::convertToDOM($encryption_method->DigestMethod);
		
		if(!$dom->hasAttribute('Algorithm')) {
			throw new Zend_CardSpace_Xml_Exception("Unable to determine the digest algorithm for the symmetric Keyinfo");
		}
		
		return $dom->getAttribute('Algorithm');
		
	}
	
	public function getKeyInfo() {
		return Zend_CardSpace_Xml_KeyInfo::getInstance($this->KeyInfo);
	}
	
	public function getCipherValue() {
		
		$this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
			
		list($cipherdata) = $this->xpath("//e:CipherData");
		
		if(!($cipherdata instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the e:CipherData block");
		}
		
		$cipherdata->registerXPathNameSpace('enc', 'http://www.w3.org/2001/04/xmlenc#');
		list($ciphervalue) = $cipherdata->xpath("//enc:CipherValue");
		
		if(!($ciphervalue instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to fidn the enc:CipherValue block");
		}
		
		return (string)$ciphervalue;
	}
}