<?php

require_once 'Zend/CardSpace/Xml/EncryptedData/Abstract.php';

class Zend_CardSpace_Xml_EncryptedData_XmlEnc extends Zend_CardSpace_Xml_EncryptedData_Abstract {

	public function getCipherData() {
		$this->registerXPathNamespace('enc', 'http://www.w3.org/2001/04/xmlenc#');
			
		list(,$cipherdata) = $this->xpath("//enc:CipherData");
		
		if(!($cipherdata instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the enc:CipherData block");
		}
		
		list($ciphervalue) = $cipherdata->xpath("//enc:CipherValue");
		
		if(!($ciphervalue instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to fidn the enc:CipherValue block");
		}
		
		return (string)$ciphervalue;		
	}
}