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


require_once 'Zend/InfoCard.php';

class Zend_InfoCard_Assertion_Test extends PHPUnit_Framework_TestCase
{
	const TOKEN_DOCUMENT = './_files/signedToken.xml';
	
	const SSL_PUB_KEY = "./_files/ssl_pub.cert";
	const SSL_PRV_KEY = "./_files/ssl_private.cert";
	
	private $_xmlDocument;
	
	protected function setUp() {
		$this->loadXmlDocument();
	}
	
	private function loadXmlDocument() {
		$this->_xmlDocument = file_get_contents(self::TOKEN_DOCUMENT);
	}

	public function testAssertionProcess() {
		
		$assertions = Zend_InfoCard_Xml_Assertion::getInstance($this->_xmlDocument);
		
		$this->assertTrue($assertions instanceof Zend_InfoCard_Xml_Assertion_SAML);
		
		$this->assertSame($assertions->getMajorVersion(), 1);
		$this->assertSame($assertions->getMinorversion(), 1);
		$this->assertSame($assertions->getAssertionID(), "uuid:5cf2cd76-acf6-45ef-9059-a811801b80cc");
		$this->assertSame($assertions->getIssuer(), "http://schemas.xmlsoap.org/ws/2005/05/identity/issuer/self");
		$this->assertSame($assertions->getConfirmationMethod(), Zend_InfoCard_Xml_Assertion_SAML::CONFIRMATION_BEARER);
		$this->assertSame($assertions->getIssuedTimestamp(), 1190153823);
		
	}
}

