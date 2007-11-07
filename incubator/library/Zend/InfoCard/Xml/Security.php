<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 * @author     John Coggeshall <john@zend.com>
 */

/**
 * Zend_InfoCard_Xml_Security_Exception
 */
require_once 'Zend/InfoCard/Xml/Security/Exception.php';

/**
 * Zend_InfoCard_Xml_Security_Transform
 */
require_once 'Zend/InfoCard/Xml/Security/Transform.php';

/**
 * 
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml_Security
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     John Coggeshall <john@zend.com>
 */
class Zend_InfoCard_Xml_Security {

	/**
	 * ASN.1 type INTEGER class
	 */
	const ASN_TYPE_INTEGER = 0x02;
	
	/**
	 * ASN.1 type BIT STRING class
	 */
	const ASN_TYPE_BITSTRING = 0x03;
	
	/**
	 * ASN.1 type SEQUENCE class
	 */
	const ASN_TYPE_SEQUENCE = 0x30;
	
	private function __construct() { }
	
	/**
	 * The URI for Canonical Method C14N Exclusive
	 */
	const CANONICAL_METHOD_C14N_EXC = 'http://www.w3.org/2001/10/xml-exc-c14n#';

	/**
	 * The URI for Signature Method SHA1
	 */
	const SIGNATURE_METHOD_SHA1 = 'http://www.w3.org/2000/09/xmldsig#rsa-sha1';
	
	/**
	 * The URI for Digest Method SHA1
	 */
	const DIGEST_METHOD_SHA1 = 'http://www.w3.org/2000/09/xmldsig#sha1';

	/**
	 * The Identifier for RSA Keys
	 */
	const RSA_KEY_IDENTIFIER = '300D06092A864886F70D0101010500';
	
	/**
	 * Validates the signature of a provided XML block
	 *
	 * @throws Zend_InfoCard_Xml_Security_Exception
	 * @param string $strXMLInput An XML block containing a Signature
	 * @return bool True if the signature validated, false otherwise
	 */
	static public function validateXMLSignature($strXMLInput) {
		
		if(!extension_loaded('openssl')) {
			throw new Zend_InfoCard_Xml_Security_Exception("You must have the openssl extension installed to use this class");
		}
		
		$sxe = simplexml_load_string($strXMLInput);
		
		if(!isset($sxe->Signature)) {
			throw new Zend_InfoCard_Xml_Security_Exception("Could not identify XML Signature element");
		}
		
		if(!isset($sxe->Signature->SignedInfo)) {
			throw new Zend_InfoCard_Xml_Security_Exception("Signature is missing a SignedInfo block");
		}
		
		if(!isset($sxe->Signature->SignatureValue)) {
			throw new Zend_InfoCard_Xml_Security_Exception("Signature is missing a SignatureValue block");
		}
		
		if(!isset($sxe->Signature->KeyInfo)) {
			throw new Zend_InfoCard_Xml_Security_Exception("Signature is missing a KeyInfo block");
		}

		if(!isset($sxe->Signature->KeyInfo->KeyValue)) {
			throw new Zend_InfoCard_Xml_Security_Exception("Signature is missing a KeyValue block");
		}
				
		switch((string)$sxe->Signature->SignedInfo->CanonicalizationMethod['Algorithm']) {
			case self::CANONICAL_METHOD_C14N_EXC:
				$cMethod = (string)$sxe->Signature->SignedInfo->CanonicalizationMethod['Algorithm']; 
				break;
			default:
				throw new Zend_InfoCard_Xml_Security_Exception("Unknown or unsupported CanonicalizationMethod Requested");
		}
		
		switch((string)$sxe->Signature->SignedInfo->SignatureMethod['Algorithm']) {
			case self::SIGNATURE_METHOD_SHA1:
				$sMethod = (string)$sxe->Signature->SignedInfo->SignatureMethod['Algorithm'];
				break;
			default:
				throw new Zend_InfoCard_Xml_Security_Exception("Unknown or unsupported SignatureMethod Requested");
		}
		
		switch((string)$sxe->Signature->SignedInfo->Reference->DigestMethod['Algorithm']) {
			case self::DIGEST_METHOD_SHA1:
				$dMethod = (string)$sxe->Signature->SignedInfo->Reference->DigestMethod['Algorithm'];
				break;
			default:
				throw new Zend_InfoCard_Xml_Security_Exception("Unknown or unsupported DigestMethod Requested");
		}
		
		$dValue = base64_decode((string)$sxe->Signature->SignedInfo->Reference->DigestValue, true);
		
		$signatureValue = base64_decode((string)$sxe->Signature->SignatureValue, true);

		$transformer = new Zend_InfoCard_Xml_Security_Transform();
		
		foreach($sxe->Signature->SignedInfo->Reference->Transforms->children() as $transform) {
			$transformer->addTransform((string)$transform['Algorithm']);
		}

		$transformed_xml = $transformer->applyTransforms($strXMLInput);
		
		$transformed_xml_binhash = pack("H*", sha1($transformed_xml));

		if($transformed_xml_binhash != $dValue) {
			throw new Zend_InfoCard_Xml_Security_Exception("Locally Transformed XML does not match XML Document. Cannot Verify Signature");
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
					throw new Zend_InfoCard_Xml_Security_Exception("Unable to extract and prcoess X509 Certificate from KeyValue");
				}
				
				break;
			case isset($sxe->Signature->KeyInfo->KeyValue->RSAKeyValue):
				
				if(!isset($sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Modulus) ||
				   !isset($sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Exponent)) {
					throw new Zend_InfoCard_Xml_Security_Exception("RSA Key Value not in Modulus/Exponent form");   	
				}
				 
				$modulus = base64_decode((string)$sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Modulus);
				$exponent = base64_decode((string)$sxe->Signature->KeyInfo->KeyValue->RSAKeyValue->Exponent);
				
				$pem_public_key = self::getPublicKeyFromModExp($modulus, $exponent);
			    
				print "<PRE>$pem_public_key</PRE>";
				
				$public_key = openssl_pkey_get_public ($pem_public_key);

				break;
			default:
				throw new Zend_InfoCard_Xml_Security_Exception("Unable to determine or unsupported representation of the KeyValue block");
		}

		$transformer = new Zend_InfoCard_Xml_Security_Transform();
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
	
	/**
	 * Transform an RSA Key in Modulus/Exponent format into a PEM encoding and
	 * return an openssl resource for it
	 *
	 * @param string $modulus The RSA Modulus in binary format
	 * @param string $exponent The RSA exponent in binary format
	 * @return string The PEM encoded version of the key
	 */
	static protected function getPublicKeyFromModExp( $modulus, $exponent ) {
	        //Encode modulus into ASN.1 INTEGER type
	        $modulusInteger  = self::encodeInteger( $modulus );
	
	        //Encode exponent into ASN.1 INTEGER type
	        $exponentInteger = self::encodeInteger( $exponent );
	
	        //Encode modulus/exponent into ASN.1 SEQUENCE type
	        $modExpSequence  = self::encodeSequence( $modulusInteger . $exponentInteger );
	
	        //Encode the modulus/exponent sequence into ASN.1 BIT STRING type
	        $modExpBitString = self::encodeBitString( $modExpSequence );
	
	        //Pack the RSA_KEY_IDENTIFIER value into a binary string
	        //format: Hex string, high nibble first
	        $binRsaKeyIdentifier = pack( "H*", self::RSA_KEY_IDENTIFIER );
	
	        //Encode RSAPublicKey SEQUENCE with the
	        //key identifier and encoded bitstring
	        $publicKeySequence = self::encodeSequence( $binRsaKeyIdentifier . $modExpBitString );
	
	        //Base64 encode the string;
	        $publicKeyInfoBase64 = base64_encode( $publicKeySequence );
	
	        //Split into 64 char chunks
	        $lenPublicKeyInfo = strlen( $publicKeyInfoBase64 );
	        $lines = $lenPublicKeyInfo/64;
	        $tmpPublicKeyString = '';
	        for ($i=0;$i<$lines;$i++) {
	                $tmpPublicKeyString .= substr( $publicKeyInfoBase64, $i * 64, 64 ) . "\n";
	        }
	        //Enclose encoded public key string x.509 within public key tags
	        $publicKeyString = "-----BEGIN PUBLIC KEY-----\n";
	        $publicKeyString .= $tmpPublicKeyString;
	        $publicKeyString .= "-----END PUBLIC KEY-----\n";
	
	        return $publicKeyString;
	}
	
	/**
	* Encodes an ASN.1 value of the type INTEGER
	*/
	static protected function encodeInteger( $inContent ) {
	        $encodedIntegerVal = '';
	        //Content class/type
	        $type = self::ASN_TYPE_INTEGER;
	        //$type = 0x02;
	        //INTEGER is represented as a primitive
	        //If value of first octet > 127, then we require another octet
	        //so we add a leading 0 ..
	        if ( ord($inContent > 127 )) {
	                $inContent = chr(0) . $inContent;
	        }
	
	        //Length of content
	        $length = strlen( $inContent );
	
	        //Encode Bit String
	        $encodedIntegerVal = sprintf( "%c%c%s", $type, $length, $inContent );
	        return $encodedIntegerVal;
	}
	
	/**
	* Encodes an ASN.1 value of the type BIT STRING
	*/
	static protected function encodeBitString( $inContent ) {
	        $encodedBitString = '';
	        //Content class/type
	        $type = self::ASN_TYPE_BITSTRING;
	        //Bit string is represented as a primitive
	        //Add leading 0
	        $inContent = chr(0) . $inContent;
	
	        //Length of content
	        $length = strlen( $inContent );
	
	        //Encode Bit String
	        $encodedBitString = sprintf( "%c%c%s", $type, $length, $inContent );
	
	        return $encodedBitString;
	}
	
	/**
	* Encodes an ASN.1 value of the type SEQUENCE
	*/
	static protected function encodeSequence( $inContent ) {
	//Note: SEQUENCE value has to be constructed / not primitive.
	// We are doing this in the calling function
	//so the content octet already has been encoded for
	//the individual types constituting the values in the sequence
	
	        $encodedSequence = '';
	        //Content class/type
	        $type = self::ASN_TYPE_SEQUENCE;
	
	        //Length of content
	        $length = strlen( $inContent );
	
	        //Encode Bit String
	        $encodedSequence = sprintf( "%c%c%s", $type, $length, $inContent );
	
	        return $encodedSequence;
	}	
}
