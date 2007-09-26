<?php

require_once 'Zend/CardSpace/Cipher/Exception.php';

class Zend_CardSpace_Cipher {
	
	const ENC_AES256CBC      = 'http://www.w3.org/2001/04/xmlenc#aes256-cbc';
	const ENC_AES128CBC      = 'http://www.w3.org/2001/04/xmlenc#aes128-cbc';
	const ENC_RSA_OAEP_MGF1P = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';
	const ENC_RSA            = 'http://www.w3.org/2001/04/xmlenc#rsa-1_5';

	private function __construct() { }
	
	static public function getInstanceByURI($uri) {
		
		switch($uri) {
			case self::ENC_AES256CBC:
				include_once 'Zend/CardSpace/Cipher/Symmetric/Adapter/AES256CBC.php';
				return new Zend_CardSpace_Cipher_Symmetric_Adapter_AES256CBC();

			case self::ENC_AES128CBC:
				include_once 'Zend/CardSpace/Cipher/Symmetric/Adapter/AES128CBC.php';
				return new Zend_CardSpace_Cipher_Symmetric_Adapter_AES128CBC();
			
			case self::ENC_RSA_OAEP_MGF1P:
				include_once 'Zend/CardSpace/Cipher/PKI/Adapter/RSA.php';
				return new Zend_CardSpace_Cipher_PKI_Adapter_RSA(Zend_CardSpace_Cipher_PKI_Adapter_RSA::OAEP_PADDING);
				break;

			case self::ENC_RSA:
				include_once 'Zend/CardSpace/Cipher/PKI/Adapter/RSA.php';
				return new Zend_CardSpace_Cipher_PKI_Adapter_RSA(Zend_CardSpace_Cipher_PKI_Adapter_RSA::NO_PADDING);
				break;
			
			default:
				throw new Zend_CardSpace_Cipher_Exception("Unknown Cipher URI");
		}
	}
	
	
}