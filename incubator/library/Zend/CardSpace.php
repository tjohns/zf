<?php

require_once 'Zend/CardSpace/Xml/EncryptedData.php';
require_once 'Zend/CardSpace/Exception.php';

class Zend_CardSpace {
	
	const ENC_NONE = 'none';
	const ENC_AES256CBC = 'http://www.w3.org/2001/04/xmlenc#aes256-cbc';
	const ENC_RSA_OAEP_MGF1P = 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p';
	
	const DIGEST_SHA1 = 'http://www.w3.org/2000/09/xmldsig#sha1';
	
	protected $_keyPairs;
	
	public function __construct() {
		$this->_keyPairs = array();
	}
	
	public function removeCertificatePair($key_id) {
		
		if(!key_exists($key_id, $this->_keyPairs)) {
			throw new Zend_CardSpace_Exception("Attempted to remove unknown key id: $key_id");
		}
		
		unset($this->_keyPairs[$key_id]);
	}
	
	public function addCertificatePair($private_key_file, $public_key_file, $type = self::ENC_RSA_OAEP_MGF1P) {
		if(!file_exists($private_key_file) ||
		   !file_exists($public_key_file)) {
		   	throw new Zend_CardSpace_Exception("Could not locate the public and private certificate pair files: $private_key_file, $public_key_file");
		} 
		
		if(!is_readable($private_key_file) || 
		   !is_readable($public_key_file)) {
		   	throw new Zend_CardSpace_Exception("Could not read the public and private certificate pair files (check permissions): $private_key_file, $public_key_file");
  	    }
  	    
  	    $key_id = md5($private_key_file.$public_key_file);
  	    
  	    if(key_exists($key_id, $this->_keyPairs)) {
  	    	throw new Zend_CardSpace_Exception("Attempted to add previously existing certificate pair: $private_key_file, $public_key_file");
  	    }
  	    
  	    switch($type) {
  	    	case self::ENC_RSA_OAEP_MGF1P:
		  	    $this->_keyPairs[$key_id] = array('private' => $private_key_file,
                                  'public'  => $public_key_file,
                                  'type'    => $type);
                return $key_id;
  	    		break;
  	    	default:
  	    		throw new Zend_CardSpace_Exception("Invalid Certificate Pair Type specified: $type");
  	    }
	}
	
	public function getCertificatePair($key_id) {
		if(key_exists($key_id, $this->_keyPairs)) {
			return $this->_keyPairs[$key_id];
		} 
		
		throw new Zend_CardSpace_Exception("Invalid Certificate Pair ID provided: $key_id");
	}
	
	protected function getPublicKeyDigest($key_id, $digestMethod = self::DIGEST_SHA1) {
		$certificatePair = $this->getCertificatePair($key_id);		
		
		switch($digestMethod) {
			case self::DIGEST_SHA1:
				preg_match("/-{5}BEGIN\sCERTIFICATE(\sREQUEST)?-{5}(.*)-{5}END\sCERTIFICATE(\sREQUEST)?-{5}/s", file_get_contents($certificatePair['public']), $results);
				
				if(isset($results[2])) {
					$digest_retval = sha1(trim($results[2]), true);
				}
				
				//$digest_retval = sha1_file($certificatePair['public'], true);
				break;
			default:
				throw new Zend_CardSpace_Exception("Invalid Digest Type Provided: $digestMethod");
		}
		
		return $digest_retval;
	}
	
	protected function findCertifiatePairByDigest($digest, $digestMethod = self::DIGEST_SHA1) {

		print "LOoking for $digest<br>";
		
		foreach($this->_keyPairs as $key_id => $certificate_data) {
			
			$cert_digest = $this->getPublicKeyDigest($key_id, $digestMethod);
		
			print "Looking at KeyID #$key_id: $cert_digest<br>";
				
			if($cert_digest == $digest) {
				return $key_id;
			}
		}
		
		return false;
	}
	
	public function process($strXmlToken) {

		$encryptedData = Zend_CardSpace_Xml_EncryptedData::getInstance($strXmlToken);
		
		// Determine the Encryption Method used to encrypt the token
		
		switch($encryptedData->getEncryptionMethod()) {
			case self::ENC_AES256CBC:
				
				break;
			default:
				throw new Zend_CardSpace_Exception("Unknown Encryption Method used in the secure token");
		}
		
		// Figure out the Key we are using to decrypt the token
		
		$keyinfo = $encryptedData->getKeyInfo();
		
		
		if(!($keyinfo instanceof Zend_CardSpace_Xml_KeyInfo_XmlDSig)) {
			throw new Zend_CardSpace_Exception("Expected a XML digital signature KeyInfo, but was not found");
		}
		
		
		$encryptedKey = $keyinfo->getEncryptedKey();
		
		switch($encryptedKey->getEncryptionMethod()) {
			case self::ENC_RSA_OAEP_MGF1P:
				break;
			default:
				throw new Zend_CardSpace_Exception("Unknown Key Encryption Method used in secure token");
		}
		
		$securityTokenRef = $encryptedKey->getKeyInfo()->getSecurityTokenReference();
		
		var_dump($this->findCertifiatePairByDigest($securityTokenRef->getKeyReference()));
	}
	
	
}