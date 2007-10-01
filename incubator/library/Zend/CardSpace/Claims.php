<?php

require_once 'Zend/CardSpace/Exception.php';

class Zend_CardSpace_Claims {
	
	const RESULT_SUCCESS = 1;
	const RESULT_PROCESSING_FAILURE = 2;
	const RESULT_VALIDATION_FAILURE = 3;
	
	protected $_default_namespace;
	protected $_isValid = true;
	protected $_error;
	protected $_claims;
	protected $_code;
	
	public function forceValid() {
		trigger_error("Forcing Claims to be valid although it is a security risk", E_USER_WARNING);
		$this->_isValid = true;
	}
	
	public function getCardID() {
		return $this->getClaim('http://schemas.xmlsoap.org/ws/2005/05/identity/claims/privatepersonalidentifier');
	}
	
	public function getDefaultNamespace() {
		
		if(is_null($this->_default_namespace)) {
			
			$namespaces = array();
			$leader = '';
			foreach($this->_claims as $claim) {
				
				if(!isset($namespaces[$claim['namespace']])) {
					$namespaces[$claim['namespace']] = 1;
				} else {
					$namespaces[$claim['namespace']]++;
				}
				
				if(empty($leader) || ($namespaces[$claim['namespace']] > $leader)) {
					$leader = $claim['namespace'];
				}
			}
			
			if(empty($leader)) {
				throw new Zend_CardSpace_Exception("Failed to determine default namespace");
			}
			
			$this->setDefaultNamespace($leader);
		}
		
		return $this->_default_namespace;
	}
	
	public function setDefaultNamespace($namespace) {
		
		foreach($this->_claims as $claim) {
			if($namespace == $claim['namespace']) {
				$this->_default_namespace = $namespace;
				return $this;		
			}
		}
		
		throw new Zend_CardSpace_Exception("At least one claim must exist in specified namespace to make it the default namespace");
	}
	
	public function isValid() {
		return $this->_isValid;
	}
	
	public function setError($error) {
		$this->_error = $error;
		$this->_isValid = false;
		return $this;
	}
	
	public function getErrorMsg() {
		return $this->_error;
	}
	
	public function setClaims($claims) {
		if(!is_null($this->_claims)) {
			throw new Zend_CardSpace_Exception("Claim objects are read-only");
		}
		
		$this->_claims = $claims;
	}
	
	public function setCode($code) {
		switch($code) {
			case self::RESULT_PROCESSING_FAILURE:
			case self::RESULT_SUCCESS:
			case self::RESULT_VALIDATION_FAILURE:
				$this->_code = $code;
				return $this;
		}
		
		throw new Zend_CardSpace_Exception("Attempted to set unknown error code");
	}
	
	public function getCode() {
		return $this->_code;
	}
	
	public function getClaim($claimURI) {
		if($this->claimExists($claimURI)) {
			return $this->_claims[$claimURI]['value'];
		}
		
		return null;
	}
	
	public function claimExists($claimURI) {
		return isset($this->_claims[$claimURI]);
	}
	
	public function __unset($k) {
		throw new Zend_CardSpace_Exception("Claim objects are read-only");
	}
	
	public function __isset($k) {
		return $this->claimExists("{$this->getDefaultNamespace()}/$k");
	}
	
	public function __get($k) {
		return $this->getClaim("{$this->getDefaultNamespace()}/$k");
	}
	
	public function __set($k, $v) {
		throw new Zend_CardSpace_Exception("Claim objects are read-only");
	}
}