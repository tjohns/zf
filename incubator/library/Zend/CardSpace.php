<?php

require_once 'Zend/CardSpace/Xml/EncryptedData.php';
require_once 'Zend/CardSpace/Exception.php';
require_once 'Zend/CardSpace/Cipher.php';

class Zend_CardSpace {
	
	const DIGEST_SHA1        = 'http://www.w3.org/2000/09/xmldsig#sha1';
	
	protected $_keyPairs;
	
	protected $_pkCipherObj;
	protected $_symCipherObj;
	
	public function getPKCipherObject() {
		return $this->_pkCipherObj;
	}
	
	public function setPKICipherObject($cipherObj) {
		$this->_pkCipherObj = $cipherObj;	
	}
	
	public function getSymCipherObject() {
		return $this->_symCipherObj;
	}
	
	public function setSymCipherObject($cipherObj) {
		$this->_symCipherObj = $cipherObj;
	}
	
	public function __construct() {
		$this->_keyPairs = array();
		
		if(!extension_loaded('mcrypt')) {
			throw new Zend_CardSpace_Exception("Use of the Zend_CardSpace component requires the mcrypt extension to be enabled in PHP");
		}
		
		if(!extension_loaded('openssl')) {
			throw new Zend_CardSpace_Exception("Use of the Zend_CardSpace component requires the openssl extension to be enabled in PHP");
		}
	}
	
	public function removeCertificatePair($key_id) {
		
		if(!key_exists($key_id, $this->_keyPairs)) {
			throw new Zend_CardSpace_Exception("Attempted to remove unknown key id: $key_id");
		}
		
		unset($this->_keyPairs[$key_id]);
	}
	
	public function addCertificatePair($private_key_file, $public_key_file, $type = Zend_CardSpace_Cipher::ENC_RSA_OAEP_MGF1P, $password = null) {
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
  	    	case Zend_CardSpace_Cipher::ENC_RSA:
  	    	case Zend_CardSpace_Cipher::ENC_RSA_OAEP_MGF1P:
		  	    $this->_keyPairs[$key_id] = array('private' => $private_key_file,
                                  'public'      => $public_key_file,
                                  'type_uri'    => $type);
                                  
                if(!is_null($password)) {
                	$this->_keyPairs[$key_id]['password'] = $password;
                } else {
                	$this->_keyPairs[$key_id]['password'] = null;
                }
                
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
		
		$temp = file($certificatePair['public']);
		unset($temp[count($temp)-1]);
		unset($temp[0]);
		$certificateData = base64_decode(implode("\n", $temp));
		
		switch($digestMethod) {
			case self::DIGEST_SHA1:
				$digest_retval = sha1($certificateData, true);
				break;
			default:
				throw new Zend_CardSpace_Exception("Invalid Digest Type Provided: $digestMethod");
		}
		
		return $digest_retval;
	}
	
	protected function findCertifiatePairByDigest($digest, $digestMethod = self::DIGEST_SHA1) {
		
		foreach($this->_keyPairs as $key_id => $certificate_data) {

			$cert_digest = $this->getPublicKeyDigest($key_id, $digestMethod);
		
			if($cert_digest == $digest) {
				return $key_id;
			}
		}
		
		return $key_id;
	}
	
	protected function extractSignedToken($strXmlToken) {
		$encryptedData = Zend_CardSpace_Xml_EncryptedData::getInstance($strXmlToken);
		
		// Determine the Encryption Method used to encrypt the token
		
		switch($encryptedData->getEncryptionMethod()) {
			case Zend_CardSpace_Cipher::ENC_AES128CBC:
			case Zend_CardSpace_Cipher::ENC_AES256CBC:
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
			case Zend_CardSpace_Cipher::ENC_RSA:
			case Zend_CardSpace_Cipher::ENC_RSA_OAEP_MGF1P:
				break;
			default:
				throw new Zend_CardSpace_Exception("Unknown Key Encryption Method used in secure token");
		}
		
		$securityTokenRef = $encryptedKey->getKeyInfo()->getSecurityTokenReference();
		
		$key_id = $this->findCertifiatePairByDigest($securityTokenRef->getKeyReference());
		
		if(!$key_id) {
			throw new Zend_CardSpace_Exception("Unable to find key pair used to encrypt symmetric CardSpace Key");
		}
		
		$certificate_pair = $this->getCertificatePair($key_id);
		
		// Santity Check
		
		if($certificate_pair['type_uri'] != $encryptedKey->getEncryptionMethod()) {
			throw new Zend_CardSpace_Exception("Certificate Pair which matches digest is not of same algorithm type as document, check addCertificate()");
		}
		
		$PKcipher = Zend_CardSpace_Cipher::getInstanceByURI($encryptedKey->getEncryptionMethod());
		
		$symmetricKey = $PKcipher->decrypt(base64_decode($encryptedKey->getCipherValue(), true), file_get_contents($certificate_pair['private']), $certificate_pair['password']);
		
		$symCipher = Zend_CardSpace_Cipher::getInstanceByURI($encryptedData->getEncryptionMethod());
		
		$signedToken = $symCipher->decrypt(base64_decode($encryptedData->getCipherValue(), true), $symmetricKey);

		return $signedToken;		
	}
	
	public function process($strXmlToken) {
		$signedToken = $this->extractSignedToken($strXmlToken);
		
		print $signedToken;
	}
}

function print_binary ($title, $binary)
{
   print "DUMP OF ".$title." (length: ".strlen($binary)." octents) <br>";

   $ascii = strtoupper(bin2hex($binary));
   $ascii_length = strlen($ascii);

   $offset = 0;
   $linelen = 0;
   $binary_offset = 0;
   $printbuf = sprintf("<b>%08d</b> ", $binary_offset);

   print "<font face=\"Courier New\">";

   while ($offset < $ascii_length){
      $printbuf = $printbuf.substr($ascii, $offset, 8);  
      $offset += 8;
      $linelen += 8;

      if ($linelen < 64){
         if ($offset < $ascii_length)
             $printbuf = $printbuf.'-';
      }
      else {
         $printbuf = $printbuf."<br>";
         print $printbuf;
         $binary_offset += 32;
         $printbuf = sprintf("<b>%08d</b> ", $binary_offset);
         $linelen = 0;
      }
   }

   if ($linelen > 0)
      print $printbuf;
   print "</font><br><br>";
}