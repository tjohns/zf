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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';


require_once 'Zend/InfoCard/Xml/EncryptedData.php';

/**
 * This is a demo unit test.. see the PHPUnit documentation http://www.phpunit.de/
 * for more details
 *
 */
class Zend_InfoCard_XmlParsing extends PHPUnit_Framework_TestCase
{
	const TOKEN_DOCUMENT = './encryptedtoken.xml';
	
	private $_xmlDocument;
	
	protected function setUp() {
		$this->loadXmlDocument();
	}
	
	private function loadXmlDocument() {
		$this->_xmlDocument = file_get_contents(self::TOKEN_DOCUMENT);
	}
	
	public function testEncryptedData() {
		$encryptedData = Zend_InfoCard_Xml_EncryptedData::getInstance($this->_xmlDocument);	
		
		$this->assertTrue($encryptedData instanceof Zend_InfoCard_Xml_EncryptedData_XmlEnc);
		$this->assertSame($encryptedData->getCipherValue(), 'AIgtBEv9lGMikyHjV/b5mQ5LbLyupNtRH8hl5I6tJsZI5CYP32BLo9FgxAY5ZReEv+XZbqcs5KORBvTbMkP6l7MY32WJGPBDDMSB7k6DshryoZqlmGMbjt2g1nM7xOuwwfru1jC7t+qCBXL4PPBpHDhHzAW7u8tB8LQCU6GklIFa1+GoZbQ00BY/OoPbE3rxhxgGPAHXfYPLjGALIkYo9czeTO/zfcydHl5Xcyp/PsskSOUhNFcftxG+fQELb/oqc50ldBWlxBM/qU7fLI4KRfUag3J5sanCUsgiYdF0iQNfiYnUKLa9ThDHjHUQnB5EEt77cM2/DKQkyExMBBgYcRo9GzqyLXiDCYWVatCQU6rAD8NkBBpFs8W/0QXIV1J/S3DuZS3Eo4x27gRlT5YfUeO7jAZvwqy51WHNXwq13QTV2AOGfvpK3054sZm+10jdfAq6tgYdShQgO2kHRGP1q9vAC3SfD49mP9q+AemJrAkiR2HZTxkEQ+AttdfPhc2dzdLXp+ukQdqpL/xlywIp+KIim+YVjhO+Bi92rRn5Kl0h7q6MkpoTGI1F+akmNhD6VmB1Nd0G6e4AGTisuyd+vygEH7fsZhZuiMSknajfgPgazKiLUihwRvfk4FJm18Ju97tXcl6LhIJpkOcq7sI25GhWz0mHX1ErOf/949pcozo=');
		$this->assertSame($encryptedData->getEncryptionMethod(), 'http://www.w3.org/2001/04/xmlenc#aes256-cbc');
		$this->assertTrue($encryptedData->getKeyInfo() instanceof Zend_InfoCard_Xml_KeyInfo_XmlDSig);
	}
	
	public function testEncryptedDataKeyInfo() {
		$keyinfo = Zend_InfoCard_Xml_EncryptedData::getInstance($this->_xmlDocument)->getKeyInfo();
		
		$this->assertTrue($keyinfo instanceof Zend_InfoCard_Xml_KeyInfo_XmlDSig);
		$this->assertTrue($keyinfo->getEncryptedKey() instanceof Zend_InfoCard_Xml_EncryptedKey);		
	}
	
	public function testEncryptedKey() {
		$enckey = Zend_InfoCard_Xml_EncryptedData::getInstance($this->_xmlDocument)->getKeyInfo()->getEncryptedKey();
		
		$this->assertTrue($enckey instanceof Zend_InfoCard_Xml_EncryptedKey);		

		$this->assertSame($enckey->getEncryptionMethod(), 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p');
		$this->assertSame($enckey->getDigestMethod(), 'http://www.w3.org/2000/09/xmldsig#sha1');
		$this->assertSame($enckey->getCipherValue(), 'AIgtBEv9lGMikyHjV/b5mQ5LbLyupNtRH8hl5I6tJsZI5CYP32BLo9FgxAY5ZReEv+XZbqcs5KORBvTbMkP6l7MY32WJGPBDDMSB7k6DshryoZqlmGMbjt2g1nM7xOuwwfru1jC7t+qCBXL4PPBpHDhHzAW7u8tB8LQCU6GklIFa1+GoZbQ00BY/OoPbE3rxhxgGPAHXfYPLjGALIkYo9czeTO/zfcydHl5Xcyp/PsskSOUhNFcftxG+fQELb/oqc50ldBWlxBM/qU7fLI4KRfUag3J5sanCUsgiYdF0iQNfiYnUKLa9ThDHjHUQnB5EEt77cM2/DKQkyExMBBgYcRo9GzqyLXiDCYWVatCQU6rAD8NkBBpFs8W/0QXIV1J/S3DuZS3Eo4x27gRlT5YfUeO7jAZvwqy51WHNXwq13QTV2AOGfvpK3054sZm+10jdfAq6tgYdShQgO2kHRGP1q9vAC3SfD49mP9q+AemJrAkiR2HZTxkEQ+AttdfPhc2dzdLXp+ukQdqpL/xlywIp+KIim+YVjhO+Bi92rRn5Kl0h7q6MkpoTGI1F+akmNhD6VmB1Nd0G6e4AGTisuyd+vygEH7fsZhZuiMSknajfgPgazKiLUihwRvfk4FJm18Ju97tXcl6LhIJpkOcq7sI25GhWz0mHX1ErOf/949pcozo=');
		
		$this->assertTrue($enckey->getKeyInfo() instanceof Zend_InfoCard_Xml_KeyInfo_Default);
	}

	public function testEncryptedKeyKeyInfo() {
		$keyinfo = Zend_InfoCard_Xml_EncryptedData::getInstance($this->_xmlDocument)->getKeyInfo()->getEncryptedKey()->getKeyInfo();
		
		$this->assertTrue($keyinfo instanceof Zend_InfoCard_Xml_KeyInfo_Default);
		$this->assertTrue($keyinfo->getSecurityTokenReference() instanceof Zend_InfoCard_Xml_SecurityTokenReference);
	}
	
	public function testSecurityTokenReference() {
		$sectoken = Zend_InfoCard_Xml_EncryptedData::getInstance($this->_xmlDocument)->getKeyInfo()
																					  ->getEncryptedKey()
																					  ->getKeyInfo()
																					  ->getSecurityTokenReference();
		$this->assertTrue($sectoken instanceof Zend_InfoCard_Xml_SecurityTokenReference);
		
		$this->assertSame($sectoken->getKeyThumbprintType(), 'http://docs.oasis-open.org/wss/oasis-wss-soap-message-security-1.1#ThumbprintSHA1');
		$this->assertSame($sectoken->getKeyThumbprintEncodingType(), 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
		$this->assertSame($sectoken->getKeyReference(false), '/OCqQ7Np25sOiA+4OsFh1R6qIeY=');
		
		
	}
}
