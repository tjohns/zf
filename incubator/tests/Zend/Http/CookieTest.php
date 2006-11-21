<?php

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @version    $Id$
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once "PHPUnit/Framework/TestCase.php";
require_once 'Zend/Http/Cookie.php';

/**
 * Zend_Http_Cookie unit tests
 * 
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_CookieTest extends PHPUnit_Framework_TestCase 
{
	/**
	 * Make sure we can't set invalid names
	 */
    public function testSetInvalidName()
    {
    	$invalidcharacters = "=,; \t\r\n\013\014";
    	$l = strlen($invalidcharacters) - 1;
    	for ($i = 0; $i < $l; $i++) {
    		$name = 'cookie_' . $invalidcharacters[$i];
    		try {
    			$cookie = new Zend_Http_Cookie($name, 'foo', 'example.com');
    			$this->fail('Expected invalid cookie name exception was not thrown for "' . $name . '"');
    		} catch (Zend_Http_Exception $e) {
    			// We're good!
    		}
    	}
    }
    
	/**
     * Test we get the cookie name properly
     */
    public function testGetName() 
    {
    	// Array of cookies and their names. We need to test each 'keyword' in
    	// a cookie string
    	$cookies = array(
    		'justacookie' => 'justacookie=foo; domain=example.com',
    		'expires'     => 'expires=tomorrow; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=example.com',
    		'domain'      => 'domain=unittests; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=example.com', 
    		'path'        => 'path=indexAction; path=/; domain=.foo.com',
    		'secure'      => 'secure=sha1; secure; domain=.foo.com',
    		'PHPSESSID'   => 'PHPSESSID=1234567890abcdef; secure; domain=.foo.com; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT;'
    	);
    	
    	foreach ($cookies as $name => $cstr) {
    		$cookie = Zend_Http_Cookie::fromString($cstr);
    		if (! $cookie instanceof Zend_Http_Cookie) $this->fail('Cookie ' . $name . ' is not a proper Cookie object');
    		$this->assertEquals($name, $cookie->getName(), 'Cookie name is not as expected');
    	}
    }

    /**
     * Make sure we get the correct value if it was set through the constructor
     * 
     */
    public function testGetValueConstructor() 
    {
    	$values = array(
    		'simpleCookie', 'space cookie', '!@#$%^*&()* ][{}?;', "line\n\rbreaks"
    	);
    	
    	foreach ($values as $val) {
    		$cookie = new Zend_Http_Cookie('cookie', $val, 'example.com', time(), '/', true);
    		$this->assertEquals($val, $cookie->getValue());
    	}
    }
    
    /**
     * Make sure we get the correct value if it was set through fromString()
     *
     */
    public function testGetValueFromString()
    {
    	$values = array(
    		'simpleCookie', 'space cookie', '!@#$%^*&()* ][{}?;', "line\n\rbreaks"
    	);
    	
    	foreach ($values as $val) {
    		$cookie = Zend_Http_Cookie::fromString('cookie=' . urlencode($val) . '; domain=example.com');
    		$this->assertEquals($val, $cookie->getValue());
    	}
    }

    /**
     * Make sure we get the correct domain when it's set in the cookie string
     * 
     */
    public function testGetDomainInStr() 
    {
        $domains = array(
            'cookie=foo; domain=example.com' => 'example.com',
            'cookie=foo; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => '.example.com',
            'cookie=foo; domain=some.really.deep.domain.com; path=/; expires=Tue, 21-Nov-2006 08:33:44 GMT;' => 'some.really.deep.domain.com'
        );
    	
        foreach ($domains as $cstr => $domain) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie instanceof Zend_Http_Cookie) $this->fail('We didn\'t get a valid Cookie object');
        	$this->assertEquals($domain, $cookie->getDomain());
        }
    }

    /**
     * Make sure we get the correct domain when it's set in a reference URL
     * 
     */
    public function testGetDomainInRefUrl() 
    {
        $domains = array(
            'example.com', 'www.example.com', 'some.really.deep.domain.com'
        );
    	
        foreach ($domains as $domain) {
        	$cookie = Zend_Http_Cookie::fromString('foo=baz; path=/', 'http://' . $domain);
        	if (! $cookie instanceof Zend_Http_Cookie) $this->fail('We didn\'t get a valid Cookie object');
        	$this->assertEquals($domain, $cookie->getDomain());
        }
    }

    /**
     * Make sure we get the correct path when it's set in the cookie string
     */
    public function testGetPathInStr() 
    {
    	$cookies = array(
    	    'cookie=foo; domain=example.com' => '/',
            'cookie=foo; path=/foo/baz; expires=Tue, 21-Nov-2006 08:33:44 GMT; domain=.example.com;' => '/foo/baz',
            'cookie=foo; domain=some.really.deep.domain.com; path=/Space Out/; expires=Tue, 21-Nov-2006 08:33:44 GMT;' => '/Space Out/'
        );
        
        foreach ($cookies as $cstr => $path) {
        	$cookie = Zend_Http_Cookie::fromString($cstr);
        	if (! $cookie instanceof Zend_Http_Cookie) $this->fail('Failed generatic a valid cookie object');
        	$this->assertEquals($path, $cookie->getPath(), 'Cookie path is not as expected');
        }
    }

    /**
     * Make sure we get the correct path when it's set a reference URL
     */
    public function testGetPathInRefUrl() 
    {
    	$refUrls = array(
    	    'http://www.example.com/foo/bar/' => '/foo/bar',
    	    'http://foo.com'                 => '/',
    	    'http://qua.qua.co.uk/path/to/very/deep/file.php'           => '/path/to/very/deep/'
    	);
    	
    	foreach ($refUrls as $url => $path) {
    		$cookie = Zend_Http_Cookie::fromString('foo=bar', $url);
    		if (! $cookie instanceof Zend_Http_Cookie) $this->fail('Failed generating a valid cookie object');
    		$this->assertEquals($path, $cookie->getPath(), 'Cookie path is not as expected');
    	}
    }

    /**
     * @todo Implement testGetExpiryTime().
     */
    public function testGetExpiryTime() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testIsSecure().
     */
    public function testIsSecure() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testIsExpired().
     */
    public function testIsExpired() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testIsSessionCookie().
     */
    public function testIsSessionCookie() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testAsString().
     */
    public function testAsString() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testMatch().
     */
    public function testMatch() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testFromString().
     */
    public function testFromString() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}
