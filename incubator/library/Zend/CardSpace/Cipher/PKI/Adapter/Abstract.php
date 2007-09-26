<?php

require_once 'Zend/CardSpace/Cipher/PKI/Interface.php';

abstract class Zend_CardSpace_Cipher_PKI_Adapter_Abstract implements Zend_CardSpace_Cipher_PKI_Interface {
	
	const OAEP_PADDING = 1;
    const NO_PADDING = 2;
    
    protected $_padding;
    
    public function setPadding($padding) {
    	switch($padding) {
    		case self::OAEP_PADDING:
    		case self::NO_PADDING:
    			$this->_padding = $padding;
    			break;
    		default:
    			throw new Zend_CardSpace_Cipher_Exception("Invalid Padding Type Provided");
    	}
    }
    
    public function getPadding() {
    	return $this->_padding;
    }
}