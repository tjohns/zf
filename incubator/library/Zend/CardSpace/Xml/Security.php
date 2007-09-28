<?php

require_once 'Zend/CardSpace/Xml/Security/Exception.php';
require_once 'Zend/CardSpace/Xml/Security/Transform.php';

class Zend_CardSpace_Xml_Security {
	
	private function __construct() { }
	
	const CANONICAL_METHOD_C14N = 'http://www.w3.org/2001/10/xml-exc-c14n#';
	
	const SIGNATURE_METHOD_SHA1 = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
	
	const DIGEST_METHOD_SHA1 = 'http://www.w3.org/2000/09/xmldsig#sha1';
	
	static public function validateXMLSignature($strXMLInput) {
		
		if(!extension_loaded('openssl')) {
			throw new Zend_CardSpace_Xml_Security_Exception("You must have the openssl extension installed to use this class");
		}
		
		$sxe = simplexml_load_string($strXMLInput);
		
		if(!isset($sxe->Signature)) {
			throw new Zend_CardSpace_Xml_Security_Exception("Could not identify XML Signature element");
		}
		
		if(!isset($sxe->Signature->SignedInfo)) {
			throw new Zend_CardSpace_Xml_Security_Exception("Signature is missing a SignedInfo block");
		}
		
		if(!isset($sxe->Signature->SignatureValue)) {
			throw new Zend_CardSpace_Xml_Security_Exception("Signature is missing a SignatureValue block");
		}
		
		if(!isset($sxe->Signature->KeyInfo)) {
			throw new Zend_CardSpace_Xml_Security_Exception("Signature is missing a KeyInfo block");
		}

		if(!isset($sxe->Signature->KeyInfo->KeyValue)) {
			throw new Zend_CardSpace_Xml_Security_Exception("Signature is missing a KeyValue block");
		}
				
		switch((string)$sxe->Signature->SignedInfo->CanonicalizationMethod['Algorithm']) {
			case self::CANONICAL_METHOD_C14N:
				$cMethod = (string)$sxe->Signature->SignedInfo->CanonicalizationMethod['Algorithm']; 
				break;
			default:
				throw new Zend_CardSpace_Xml_Security_Exception("Unknown or unsupported CanonicalizationMethod Requested");
		}
		
		switch((string)$sxe->Signature->SignedInfo->SignatureMethod['Algorithm']) {
			case self::SIGNATURE_METHOD_SHA1:
				$sMethod = (string)$sxe->Signature->SignedInfo->SignatureMethod['Algorithm'];
				break;
			default:
				throw new Zend_CardSpace_Xml_Security_Exception("Unknown or unsupported SignatureMethod Requested");
		}
		
		switch((string)$sxe->Signature->SignedInfo->Reference->DigestMethod['Algorithm']) {
			case self::DIGEST_METHOD_SHA1:
				$dMethod = (string)$sxe->Signature->SignedInfo->Reference->DigestMethod['Algorithm'];
				break;
			default:
				throw new Zend_CardSpace_Xml_Security_Exception("Unknown or unsupported DigestMethod Requested");
		}
		
		$dValue = base64_decode((string)$sxe->Signature->SignedInfo->Reference->DigestValue, true);
		
		$signatureValue = base64_decode((string)$sxe->Signature->SignatureValue, true);

		$transformer = new Zend_CardSpace_Xml_Security_Transform();
		
		foreach($sxe->Signature->SignedInfo->Reference->Transforms->children() as $transform) {
			$transformer->addTransform((string)$transform['Algorithm']);
		}

		$transformed_xml = $transformer->applyTransforms($strXMLInput);
		
		$transformed_xml_binhash = pack("H*", sha1($transformed_xml));

		if($transformed_xml_binhash != $dValue) {
			throw new Zend_CardSpace_Xml_Security_Exception("Locally Transformed XML does not match XML Document. Cannot Verify Signature");
		}
		
		$public_key = null;
		
		switch(true) {
			case isset($sxe->Signature->KeyInfo->KeyValue->X509Certificate):
				
				$certificate = (string)$sxe->Signature->KeyInfo->KeyValue->X509Certificate;
				
				
				$pem = "-----BEGIN CERTIFICATE-----\n" . 
				       wordwrap($certificate, 64, "\n", true) .
				       "\n-----END CERTIFICATE-----";

				$public_key = openssl_pkey_get_public($pem);
				
				if(!$public_key) {
					throw new Zend_CardSpace_Xml_Security_Exception("Unable to extract and prcoess X509 Certificate from KeyValue");
				}
				
				break;
			case isset($sxe->Signature->KeyInfo->KeyValue->RSAKeyValue):
				
				if(!isset($sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Modulus) ||
				   !isset($sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Exponent)) {
					throw new Zend_CardSpace_Xml_Security_Exception("RSA Key Value not in Modulus/Exponent form");   	
				}
				 
				$modulus = (string)$sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Modulus;
				$exponent = (string)$sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Exponent;
				
				$public_key = kimssl_pkey_get_public($modulus, $exponent);
					
				break;
			default:
				throw new Zend_CardSpace_Xml_Security_Exception("Unable to determine or unsupported representation of the KeyValue block");
		}

		$transformer = new Zend_CardSpace_Xml_Security_Transform();
		$transformer->addTransform((string)$sxe->Signature->SignedInfo->CanonicalizationMethod['Algorithm']);
		
		// The way we are doing our XML processing requires that we specifically add this
		// (even though it's in the <Signature> parent-block).. otherwise, our canonical form
		// fails signature verification
		$sxe->Signature->SignedInfo->addAttribute('xmlns', 'http://www.w3.org/2000/09/xmldsig#');
		
		$canonical_signedinfo = $transformer->applyTransforms($sxe->Signature->SignedInfo->asXML());
		
		if(openssl_verify($canonical_signedinfo, $signatureValue, $public_key)) {
			return (string)$sxe->Signature->SignedInfo->Reference['URI'];
		}
		
		return false;
	}
}

function kimssl_pkey_get_public ($modulus, $exponent)
{
    // decode to binary
    $modulus = base64_decode($modulus);
    $exponent = base64_decode($exponent);

    // make an ASN publicKeyInfo
    $exponentEncoding = makeAsnSegment(0x02, $exponent);    
    $modulusEncoding = makeAsnSegment(0x02, $modulus);    
    $sequenceEncoding = makeAsnSegment(0x30, 
        $modulusEncoding.$exponentEncoding);
    $bitstringEncoding = makeAsnSegment(0x03, $sequenceEncoding);
    $rsaAlgorithmIdentifier = pack("H*", "300D06092A864886F70D0101010500"); 
    $publicKeyInfo = makeAsnSegment (0x30, 
        $rsaAlgorithmIdentifier.$bitstringEncoding);

    // encode the publicKeyInfo in base64 and add PEM brackets
    $publicKeyInfoBase64 = base64_encode($publicKeyInfo);    
    $encoding = "-----BEGIN PUBLIC KEY-----\n";
    $offset = 0;
    while ($segment=substr($publicKeyInfoBase64, $offset, 64)){
       $encoding = $encoding.$segment."\n";
       $offset += 64;
    }
    $encoding = $encoding."-----END PUBLIC KEY-----\n";

    // use the PEM version of the key to get a key handle
    $publicKey = openssl_pkey_get_public ($encoding);

    return ($publicKey);
}

// this helper function is necessary because PHP's openssl
// currently requires that the public key be in PEM format
// This does the ASN.1 type and length encoding

function makeAsnSegment($type, $string)
{
    // fix up integers and bitstrings
    switch ($type){
        case 0x02:
            if (ord($string) > 0x7f)
                $string = chr(0).$string;
            break;
        case 0x03:
            $string = chr(0).$string;
            break;
    }

    $length = strlen($string);

    if ($length < 128){
       $output = sprintf("%c%c%s", $type, $length, $string);
    }
    else if ($length < 0x0100){
       $output = sprintf("%c%c%c%s", $type, 0x81, $length, $string);
    }
    else if ($length < 0x010000) {
       $output = sprintf("%c%c%c%c%s", $type, 0x82, $length/0x0100, $length%0x0100, $string);
    }
    else {
        $output = NULL;
    }

    return($output);
}