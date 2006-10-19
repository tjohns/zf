<?php

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 */

/**
 * Zend_Http_Cookie
 */
require_once 'Zend/Http/Cookie.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 */
class Zend_Http_CookieTest extends PHPUnit_Framework_TestCase
{
	protected $invalidNameChars = "=,; \t\r\n\013\014";
	
	protected $invalidCookieStrings = array (
		'=value; expires=Mon, 12-Jun-2006 17:06:51 GMT; secure',
		'expires=Mon, 12-Jun-2006 17:06:51 GMT; secure',
		'invalidcookie'
	);
	
	protected $validCookies = array (
		'baz=foo; expires=Mon, 12-Jun-2006 17:06:51 GMT; secure',
		'cookie=12345',
		'expires=tomorrow; expires=Mon, 12-Jun-2006 17:06:51 GMT; domain=www.example.com; path=/path',
	);
	
	protected $secureTests = array (
		true,
		false,
		false,
	);
	
	protected $nameTests = array (
		'baz'
	);
	
	protected $valueTests = array (
		'foo'
	);
	
	protected $domainTests = array (
		''
	);
	
	protected $pathTests = array (
	
	);
	
	protected $expireTests = array (
	
	);
	
	protected $sessionTests = array(
	
	);

	protected $matchTests = array (
	
	);
	
	protected $nonMatchTests = array (
	
	);
	
	public function setUp()
	{ }
	
	public function testInvalidName()
	{
		for ($i = 0; $i < strlen($this->invalidNameChars); $i++)
		{
			try {
				$cookie = new Zend_Http_Cookie("cookie" . $this->invalidNameChars[$i], 'value', 'www.example.com');
				$this->fail('An expected Zend_Http_Exception has not been raised');
			} catch (Zend_Http_Exception $expected) {
				$this->assertContains('Cookie name cannot contain', $expected->getMessage());
			}
			
		}
		
		foreach ($this->invalidCookieStrings as $cookieStr) {
			try {
				$cookie = Zend_Http_Cookie::factory($cookieStr);
				$this->fail('An expected Zend_Http_Exception has not been raised');
			} catch (Zend_Http_Exception $expected) {
				
			}
		}
	}
	
	public function testInvalidDomain()
	{
		try {
			$cookie = new Zend_Http_Cookie("cookie", "value", null, null, null, false);
			$this->fail('An expected Zend_Http_Exception has not been raised');
		} catch (Zend_Http_Exception $expected) {
			$this->assertEquals('Cookies must have a domain', $expected->getMessage());
		}
	}
	
	public function testIsSecure()
	{
		foreach ($this->secureTests as $cookieStr) {
			$cookie = Zend_Http_Cookie::factory($cookieStr);
			$this->assertTrue($cookie->isSecure());
		}
		
		foreach ($this->nonSecureTests as $cookieStr) {
			$cookie = Zend_Http_Cookie::factory($cookieStr);
			$this->assertFalse($cookie->isSecure());
		}
	}
	
	public function testGetDomain()
	{
		
	}
	
	public function testGetPath()
	{
		
	}
	
	public function testGetName()
	{
		
	}
	
	public function testGetValue()
	{
		
	}
	
	public function testIsExpired()
	{
		
	}
	
	public function testIsSessionCookie()
	{
		
	}
	
	public function testMatch()
	{
		
	}
}