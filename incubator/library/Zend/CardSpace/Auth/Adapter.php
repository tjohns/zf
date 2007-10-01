<?php

require_once 'Zend/Auth/Adapter/Interface.php';
require_once 'Zend/Auth/Result.php';
require_once 'Zend/CardSpace.php';

class Zend_CardSpace_Auth_Adapter implements Zend_Auth_Adapter_Interface 
{
	protected $_xmlToken;
	protected $_cardSpace;
	
	public function __construct($strXmlDocument) {
		$this->_xmlToken = $strXmlDocument;
		$this->_cardSpace = new Zend_CardSpace();
	}
	
	public function setAdapter(Zend_CardSpace_Adapter_Interface $a) {
		$this->_cardSpace->setAdapter($a);
		return $this;
	}
	
	public function getAdapter() {
		return $this->_cardSpace->getAdapter();
	}
	
	public function getPKCipherObject() {
		return $this->_cardSpace->getPKCipherObject();
	}
	
	public function setPKICipherObject($cipherObj) {
		$this->_cardSpace->setPKICipherObject($cipherObj);
		return $this;
	}
	
	public function getSymCipherObject() {
		return $this->_cardSpace->getSymCipherObject();
	}
	
	public function setSymCipherObject($cipherObj) {
		$this->_cardSpace->setSymCipherObject($cipherObj);
		return $this;
	}	
	
	public function removeCertificatePair($key_id) {
		return $this->_cardSpace->removeCertificatePair($key_id);
	}
	
	public function addCertificatePair($private_key_file, $public_key_file, $type = Zend_CardSpace_Cipher::ENC_RSA_OAEP_MGF1P, $password = null) {
		return $this->_cardSpace->addCertificatePair($private_key_file, $public_key_file, $type, $password);
	}
	
	public function getCertificatePair($key_id) {
		return $this->_cardSpace->getCertificatePair($key_id);
	}
	
	public function setXmlToken($strXmlToken) {
		$this->_xmlToken = $strXmlToken;
		return $this;
	}
	
	public function getXmlToken() {
		return $this->_xmlToken;
	}
	
	public function authenticate() {

		try {
			$claims = $this->_cardSpace->process($this->getXmlToken());
		} catch(Exception $e) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE , null, array('Exception Thrown', 
			                                                                    $e->getMessage(),
			                                                                    $e->getTraceAsString(),
			                                                                    serialize($e)));
		}
		
		if(!$claims->isValid()) {
			switch($claims->getCode()) {
				case Zend_CardSpace_Claims::RESULT_PROCESSING_FAILURE:
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE,
					                            $claims, array('Processing Failure',
					                                         $claims->getErrorMsg()));
					break;
				case Zend_CardSpace_Claims::RESULT_VALIDATION_FAILURE:
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID,
					                            $claims, array('Validation Failure',
					                                            $claims->getErrorMsg()));
					break;
				default:
					return new Zend_Auth_Result(Zend_Auth_Result::FAILURE,
					                            $claims, array('Unknown Failure',
					                                           $claims->getErrorMsg()));
					break;
			}
		} 
		
		return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS,
		                            $claims);
	}
}