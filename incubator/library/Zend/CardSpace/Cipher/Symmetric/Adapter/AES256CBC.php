<?php

require_once 'Zend/CardSpace/Cipher/Symmetric/Adapter/Abstract.php';
require_once 'Zend/CardSpace/Cipher/Symmetric/AES256CBC/Interface.php';
require_once 'Zend/CardSpace/Cipher/Exception.php';

class Zend_CardSpace_Cipher_Symmetric_Adapter_AES256CBC extends Zend_CardSpace_Cipher_Symmetric_Adapter_Abstract
                                                         implements Zend_CardSapce_Cipher_Symmetric_AES256CBC_Interface {
	
	const MCRYPT_CIPHER = MCRYPT_RIJNDAEL_128;
	const MCRYPT_MODE   = MCRYPT_MODE_CBC;
	const IV_LENGTH     = 16;
	
	public function __construct() {
		if(!extension_loaded('mcrypt')) {
			throw new Zend_CardSpace_Cipher_Exception("Use of the AES256CBC Cipher requires the mcrypt extension");
		}
	}
	
	public function decrypt($encryptedData, $decryptionKey, $iv_length = null) {
		
		$iv_length = is_null($iv_length) ? self::IV_LENGTH : $iv_length;
		
		$mcrypt_iv = null;
		
		if($iv_length > 0) {
		 	$mcrypt_iv = substr($encryptedData, 0, $iv_length);
        	$encryptedData = substr($encryptedData, $iv_length);
		}
		
		$decrypted = mcrypt_decrypt(self::MCRYPT_CIPHER, $decryptionKey, $encryptedData, self::MCRYPT_MODE, $mcrypt_iv);
		
		if(!$decrypted) {
			throw new Zend_CardSpace_Cipher_Exception("Failed to decrypt data using AES256CBC Algorithm");
		}

		$decryptedLength = strlen($decrypted);
		$paddingLength = substr($decrypted, $decryptedLength -1, 1);
		$decrypted = substr($decrypted, 0, $decryptedLength - ord($paddingLength));
		
		return rtrim($decrypted, "\0");
	}
	
}

