<?php

interface Zend_CardSapce_Cipher_Symmetric_AES256CBC_Interface {
	public function decrypt($encryptedData, $decryptionKey, $iv_length = null);
}