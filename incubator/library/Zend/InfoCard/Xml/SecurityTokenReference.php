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
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 * @author     John Coggeshall <john@zend.com>
 */

/**
 * Zend_InfoCard_Xml_Element
 */
require_once 'Zend/InfoCard/Xml/Element.php';

/**
 * Represents a SecurityTokenReference XML block
 * 
 * @category   Zend
 * @package    Zend_InfoCard
 * @subpackage Zend_InfoCard_Xml
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     John Coggeshall <john@zend.com>
 */
class Zend_InfoCard_Xml_SecurityTokenReference extends Zend_InfoCard_Xml_Element {

	/**
	 * Base64 Binary Encoding URI
	 */
	const ENCODING_BASE64BIN = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary';

	/**
	 * Return an instance of the object based on the input XML
	 *
	 * @throws Zend_InfoCard_Xml_Exception
	 * @param string $xmlData The SecurityTokenReference XML Block
	 * @return Zend_InfoCard_Xml_SecurityTokenReference
	 */
	static public function getInstance($xmlData) {
		if($xmlData instanceof Zend_InfoCard_Xml_Element) {
			$strXmlData = $xmlData->asXML();
		} else if (is_string($xmlData)) {
			$strXmlData = $xmlData;
		} else {
			throw new Zend_InfoCard_Xml_Exception("Invalid Data provided to create instance");
		}
		
		$sxe = simplexml_load_string($strXmlData);
		
		if($sxe->getName() != "SecurityTokenReference") {
			throw new Zend_InfoCard_Xml_Exception("Invalid XML Block provided for SecurityTokenReference");
		}
		
		return simplexml_load_string($strXmlData, "Zend_InfoCard_Xml_SecurityTokenReference");		
	}
	
	/**
	 * Return the Key Identifier XML Object
	 *
	 * @throws Zend_InfoCard_Xml_Exception
	 * @return Zend_InfoCard_Xml_Element
	 */
	protected function getKeyIdentifier() {
		$this->registerXPathNamespace('o', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
		list($keyident) = $this->xpath('//o:KeyIdentifier');
		
		if(!($keyident instanceof Zend_InfoCard_Xml_Element)) {
			throw new Zend_InfoCard_Xml_Exception("Failed to retrieve Key Identifier");
		}
		
		return $keyident;
	}
	
	/**
	 * Return the Key URI identifying the thumbprint type used
	 * 
	 * @throws  Zend_InfoCard_Xml_Exception
	 * @return string The thumbprint type URI
	 */
	public function getKeyThumbprintType() {

		$keyident = $this->getKeyIdentifier();
		
		$dom = self::convertToDOM($keyident);

		if(!$dom->hasAttribute('ValueType')) {
			throw new Zend_InfoCard_Xml_Exception("Key Identifier did not provide a type for the value");
		}
		
		return $dom->getAttribute('ValueType');
	}
	
	
	/**
	 * Return the thumbprint encoding type used as a URI
	 *
	 * @throws Zend_InfoCard_Xml_Exception
	 * @return string the URI of the thumbprint encoding used
	 */
	public function getKeyThumbprintEncodingType() {
		
		$keyident = $this->getKeyIdentifier();
		
		$dom = self::convertToDOM($keyident);
		
		if(!$dom->hasAttribute('EncodingType')) {
			throw new Zend_InfoCard_Xml_Exception("Unable to determine the encoding type for the key identifier");
		}
		
		return $dom->getAttribute('EncodingType');
	}
	
	/**
	 * Get the key reference data used to identify the public key
	 *
	 * @throws Zend_InfoCard_Xml_Exception
	 * @param bool $decode if true, will return a decoded version of the key
	 * @return string the key reference thumbprint, either in binary or encoded form
	 */
	public function getKeyReference($decode = true) {
		$keyIdentifier = $this->getKeyIdentifier();
		
		$dom = self::convertToDOM($keyIdentifier);
		$encoded = $dom->nodeValue;
		
		if(empty($encoded)) {
			throw new Zend_InfoCard_Xml_Exception("Could not find the Key Reference Encoded Value");
		}
		
		if($decode) {
			
			$decoded = "";
			switch($this->getKeyThumbprintEncodingType()) {
				case self::ENCODING_BASE64BIN:
					$decoded = base64_decode($encoded, true);
					break;
				default:
					throw new Zend_InfoCard_Xml_Exception("Unknown Key Reference Encoding Type: {$this->getKeyThumbprintEncodingType()}");
			}
			
			if(!$decoded || empty($decoded)) {
				throw new Zend_InfoCard_Xml_Exception("Failed to decode key reference");
			}
			
			return $decoded;
		}
		
		return $encoded;
	}
}