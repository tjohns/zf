<?php

require_once 'Zend/CardSpace/Cipher/PKI/Adapter/Abstract.php';

interface Zend_CardSpace_Cipher_PKI_RSA_Interface {
	public function decrypt($encryptedData, $privateKey, $password, $padding = Zend_CardSpace_Cipher_PKI_Adapter_Abstract::NO_PADDING);
}