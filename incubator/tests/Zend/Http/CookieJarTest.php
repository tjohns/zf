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
require_once 'Zend/Http/CookieJar.php';

/**
 * Zend_Http_Cookie unit tests
 * 
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com/)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Http_CookieJarTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Test we can add cookies to the jar
     * 
     */
    public function testAddCookie() 
    {
        $jar = new Zend_Http_CookieJar();
        $this->assertEquals(0, count($jar->getAllCookies()), 'Cookie jar is expected to contain 0 cookies');
        
        $jar->addCookie('foo=bar; domain=example.com');
        $cookie = $jar->getCookie('http://example.com/', 'foo');
        $this->assertTrue($cookie instanceof Zend_Http_Cookie, '$cookie is expected to be a Cookie object');
        $this->assertEquals('bar', $cookie->getValue(), 'Cookie value is expected to be "bar"');
        
        $jar->addCookie('cookie=brownie; domain=geekz.co.uk;');
        $this->assertEquals(2, count($jar->getAllCookies()), 'Cookie jar is expected to contain 2 cookies');
    }
    
    /**
     * Check we get an expection if a non-valid cookie is passed to addCookie
     *
     */
    public function testExceptAddInvalidCookie()
    {
    	$jar = new Zend_Http_CookieJar();
    	
    	try {
    		$jar->addCookie('garbage');
    		$this->fail('Expected exception was not thrown');
    	} catch (Zend_Http_Exception $e) {
    		// We are ok
    	}

    	try {
    		$jar->addCookie(new Zend_Http_Cookiejar());
    		$this->fail('Expected exception was not thrown');
    	} catch (Zend_Http_Exception $e) {
    		// We are ok
    	}
    }

    /**
     * Test we can read cookies from a Response object
     *
     */
    public function testAddCookiesFromResponse() 
    {
    	$jar = new Zend_Http_Cookiejar();
    	$res_str = file_get_contents(dirname(realpath(__FILE__)) . 
    	    DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
    	$response = Zend_Http_Response::fromString($res_str);
        
    	$jar->addCookiesFromResponse($response, 'http://www.example.com');
    	
    	$this->assertEquals(3, count($jar->getAllCookies()));
    	
    	$cookie_str = 'foo=bar;BOFH=Feature+was+not+beta+tested;time=1164234700;';
    	$this->assertEquals($cookie_str, $jar->getAllCookies(Zend_Http_CookieJar::COOKIE_STRING_CONCAT));
    }
    
    public function testExceptAddCookiesInvalidResponse()
    {
    	$jar = new Zend_Http_Cookiejar();
    	
    	try {
    		$jar->addCookiesFromResponse('somestring', 'http://www.example.com');
    		$this->fail('Excepted exception was not thrown');
    	} catch (Zend_Http_Exception $e) {
    		// We are ok
    	}
    	
    	try {
    		$jar->addCookiesFromResponse(new stdClass(), 'http://www.example.com');
    		$this->fail('Excepted exception was not thrown');
    	} catch (Zend_Http_Exception $e) {
    		// We are ok
    	}
    }

    /**
     * Test we can get all cookies as an array of Cookie objects
     * 
     */
    public function testGetAllCookies() 
    {
        $jar = new Zend_Http_CookieJar();
        
        $cookies = array(
            'name=Arthur; domain=camelot.gov.uk',
            'quest=holy+grail; domain=forest.euwing.com',
            'swallow=african; domain=bridge-of-death.net'
        );
        
        foreach ($cookies as $cookie) {
        	$jar->addCookie($cookie);
        }
        
        $cobjects = $jar->getAllCookies();
        
        foreach ($cobjects as $id => $cookie) {
        	$this->assertContains($cookie->__toString(), $cookies[$id]);
        }
    }
    
    /**
     * Test we can get all cookies as a concatenated string
     * 
     */
    public function testGetAllCookiesAsConcat() 
    {
        $jar = new Zend_Http_CookieJar();
        
        $cookies = array(
            'name=Arthur; domain=camelot.gov.uk',
            'quest=holy+grail; domain=forest.euwing.com',
            'swallow=african; domain=bridge-of-death.net'
        );
        
        foreach ($cookies as $cookie) {
        	$jar->addCookie($cookie);
        }
        
        $expected = 'name=Arthur;quest=holy+grail;swallow=african;';
        $real = $jar->getAllCookies(Zend_Http_CookieJar::COOKIE_STRING_CONCAT );
        
        $this->assertEquals($expected, $real, 'Concatenated string is not as expected');
    }

    /**
     * Test we can get a single cookie as an object
     * 
     */
    public function testGetCookieAsObject() 
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie->__toString(), 'http://www.example.com/tests/');
        
        $cobj = $jar->getCookie('http://www.example.com/tests/', 'foo');
        
        $this->assertTrue($cobj instanceof Zend_Http_Cookie, '$cobj is not a Cookie object');
        $this->assertEquals($cookie->getName(), $cobj->getName(), 'Cookie name is not as expected');
        $this->assertEquals($cookie->getValue(), $cobj->getValue(), 'Cookie value is not as expected');
        $this->assertEquals($cookie->getDomain(), $cobj->getDomain(), 'Cookie domain is not as expected');
        $this->assertEquals($cookie->getPath(), $cobj->getPath(), 'Cookie path is not as expected');
    }

    /**
     * @todo Implement testGetCookie().
     */
    public function testGetCookieAsString() 
    {
        $cookie = Zend_Http_Cookie::fromString('foo=bar; domain=www.example.com; path=/tests');
        $jar = new Zend_Http_CookieJar();
        $jar->addCookie($cookie);
        
        $cstr = $jar->getCookie('http://www.example.com/tests/', 'foo', Zend_Http_CookieJar::COOKIE_STRING_ARRAY);
        $this->assertEquals($cookie->__toString(), $cstr, 'Cookie string is not the expected string');

        $cstr = $jar->getCookie('http://www.example.com/tests/', 'foo', Zend_Http_CookieJar::COOKIE_STRING_CONCAT);
        $this->assertEquals($cookie->__toString(), $cstr, 'Cookie string is not the expected string');
    }
    
    /**
     * @todo Implement testGetCookie().
     */
    public function testExceptGetCookieInvalidUri() 
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetCookie().
     */
    public function testExceptGetCookieInvalidReturnType() 
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
    
    /**
     * @todo Implement testDeleteAllCookies().
     */
    public function testDeleteAllCookies() 
    {
        $jar = new Zend_Http_Cookiejar();
    	$res_str = file_get_contents(dirname(realpath(__FILE__)) . 
    	    DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
    	$response = Zend_Http_Response::fromString($res_str);
        
    	$jar->addCookiesFromResponse($response, 'http://www.example.com');
    	
    	$this->assertEquals(3, count($jar->getAllCookies()), 'CookieJar expected to contain 3 cookies');
    	$jar->deleteAllCookies();
    	$this->assertEquals(0, count($jar->getAllCookies()), 'CookieJar is expected to contain 0 cookies');
    }

    /**
     * @todo Implement testDeleteExpiredCookies().
     */
    public function testDeleteExpiredCookies() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDeleteSessionCookies().
     */
    public function testDeleteSessionCookies() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testDeleteCookies().
     */
    public function testDeleteCookies() {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * @todo Implement testGetMatchingCookies().
     */
    public function testGetMatchingCookies()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * Test we can build a new object from a response object
     */
    public function testFromResponse() 
    {
    	$res_str = file_get_contents(dirname(realpath(__FILE__)) . 
    	    DIRECTORY_SEPARATOR . '_files'  . DIRECTORY_SEPARATOR . 'response_with_cookies');
    	$response = Zend_Http_Response::fromString($res_str);
        
    	$jar = Zend_Http_CookieJar::fromResponse($response, 'http://www.example.com');
    	
    	$this->assertTrue($jar instanceof Zend_Http_CookieJar, '$jar is not an instance of CookieJar as expected');
    	$this->assertEquals(3, count($jar->getAllCookies()), 'CookieJar expected to contain 3 cookies');
    }
}
