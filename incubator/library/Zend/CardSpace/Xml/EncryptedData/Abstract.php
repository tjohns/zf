<?php

require_once 'Zend/CardSpace/Xml/Element.php';
require_once 'Zend/CardSpace/Xml/KeyInfo.php';

abstract class Zend_CardSpace_Xml_EncryptedData_Abstract extends Zend_CardSpace_Xml_Element {
	
	/**
	 * Returns the KeyInfo Block
	 *
	 * @return Zend_CardSpace_Xml_KeyInfo_Abstract
	 */
	public function getKeyInfo() {
		return Zend_CardSpace_Xml_KeyInfo::getInstance($this->KeyInfo[0]);
	}
	
	public function getEncryptionMethod() {
		
		/**
		 * @todo This is pretty hacky unless we can always be confident that the first
		 * EncryptionMethod block is the correct one (the AES or compariable symetric algorithm)..
		 * the second is the PK method if provided. 
		 */
		list($encryption_method) = $this->xpath("//enc:EncryptionMethod");

		if(!($encryption_method instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Unable to find the enc:EncryptionMethod symmetric encryption block");
		}
		
		$dom = self::convertToDOM($encryption_method);
		
		if(!$dom->hasAttribute('Algorithm')) {
			throw new Zend_CardSpace_Xml_Exception("Unable to determine the encryption algorithm in the Symmetric enc:EncryptionMethod XML block");
		}
		
		return $dom->getAttribute('Algorithm');
	}
	
	abstract function getCipherValue();
}