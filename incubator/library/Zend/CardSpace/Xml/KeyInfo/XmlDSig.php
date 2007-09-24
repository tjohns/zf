<?php

require_once 'Zend/CardSpace/Xml/KeyInfo/Abstract.php';
require_once 'Zend/CardSpace/Xml/EncryptedKey.php';
require_once 'Zend/CardSpace/Xml/KeyInfo/Interface.php';

class Zend_CardSpace_Xml_KeyInfo_XmlDSig extends Zend_CardSpace_Xml_KeyInfo_Abstract
                                         implements Zend_CardSpace_Xml_KeyInfo_Interface  {
	
	/**
	 * Returns an instance of the EncryptedKey Data Block
	 *
	 * @return Zend_CardSpace_Xml_EncryptedKey
	 */
	public function getEncryptedKey() {
		$this->registerXPathNamespace('e', 'http://www.w3.org/2001/04/xmlenc#');
		list($encryptedkey) = $this->xpath('//e:EncryptedKey');
		
		if(!($encryptedkey instanceof Zend_CardSpace_Xml_Element)) {
			throw new Zend_CardSpace_Xml_Exception("Failed to retrieve encrypted key");
		}

		return Zend_CardSpace_Xml_EncryptedKey::getInstance($encryptedkey);
	}

	/**
	 * Returns the KeyInfo Block within the encrypted key
	 *
	 * @return Zend_CardSpace_Xml_KeyInfo_Default
	 */
	public function getKeyInfo() {
		return $this->getEncryptedKey()->getKeyInfo();
	}
}