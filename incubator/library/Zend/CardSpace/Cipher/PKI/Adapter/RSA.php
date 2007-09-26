<?php

require_once 'Zend/CardSpace/Cipher/PKI/Adapter/Abstract.php';
require_once 'Zend/CardSpace/Cipher/PKI/RSA/Interface.php';

class Zend_CardSpace_Cipher_PKI_Adapter_RSA extends Zend_CardSpace_Cipher_PKI_Adapter_Abstract
                                            implements Zend_CardSpace_Cipher_PKI_RSA_Interface {
	
    protected $_padding;
    
    public function __construct($padding = Zend_CardSpace_Cipher_PKI_Adapter_Abstract::NO_PADDING) {
    	if(!extension_loaded('openssl')) {
    		throw new Zend_CardSpace_Cipher_Exception("Use of this PKI RSA Adapter requires the openssl extension loaded");
    	}
    	
    	$this->setPadding($padding);
    }
    
	public function decrypt($encryptedData, $privateKey, $password, $padding = null) {
		
		$private_key = openssl_pkey_get_private(array($privateKey, $password));
		
		if(!$private_key) {
			throw new Zend_CardSpace_Cipher_Exception("Failed to load private key");
		}

		if(!is_null($padding)) {
			try {
				$this->setPadding($padding);
			} catch(Exception $e) {
				openssl_free_key($private_key);
				throw $e;
			}
		} 
		
		switch($this->getPadding()) {
			case self::NO_PADDING:
				$openssl_padding = OPENSSL_NO_PADDING;
				break;
			case self::OAEP_PADDING:
				$openssl_padding = OPENSSL_PKCS1_OAEP_PADDING;
				break;
		}
		
		$result = openssl_private_decrypt($encryptedData, $decryptedData, $private_key, $openssl_padding);
		
		openssl_free_key($private_key);
		
		if(!$result) {
			throw new Zend_CardSpace_Cipher_Exception("Unable to Decrypt Value using provided private key");
		}

		if($this->getPadding() == self::NO_PADDING) {
			$decryptedData = substr($decryptedData, 2);
			$start = strpos($decryptedData, 0) + 1;
			$decryptedData = substr($decryptedData, $start);
		}

		return $decryptedData;
	}
}
