<?php

/**
 * Zend_Http_Client unit tests
 * 
 * Currently, in order to work, the $baseuri property or 
 * TESTS_ZEND_HTTP_CLIENT_REMOTE_BASEURI constant must point to the base URI of
 * the _files directory. To set up, you will need to link the _files directory
 * to your web server's document root, or set an "Alias" to it. Then, set the
 * TESTS_ZEND_HTTP_CLIENT_REMOTE_BASEURI to the base URI of this directory.
 * 
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 */

/**
 * Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @todo Find a way to test efficiently without a web server (Zend_Http_Server ?)
 * @todo File Uploads: Send one or more files and check the $_FILES array
 * @todo Raw POST data: Send RAW post data and check how it is recieved by PHP
 * @todo Cookies: Test the $_COOKIE array when sending cookies
 * @todo CookieJar: Test the stickyness of cookies on different paths
 * @todo HTTP Auth: Send user name, password and check the values of $_SERVER['AUTH...']
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 */
class Zend_Http_ClientTest extends PHPUnit_Framework_TestCase
{
	protected $baseuri = 'http://localhost/Framework/tests/';

	/**
	 * HTTP Client
	 *
	 * @var Zend_Http_Client
	 */
	protected $client;

	public function setUp()
	{
		if (defined('TESTS_ZEND_HTTP_CLIENT_REMOTE_BASEURI'))
		$this->baseuri = TESTS_ZEND_HTTP_CLIENT_REMOTE_BASEURI;

	}

	public function testSimpleRequests()
	{
		$client = $this->_prepareClient(__FUNCTION__);
		$methods = array('GET', 'POST', 'OPTIONS', 'PUT', 'DELETE');

		foreach ($methods as $method) {
			$res = $client->request($method);
			$this->assertEquals('Success', $res->getBody(), "HTTP {$method} request failed.");
		}
	}
	
	public function testGetData()
	{
		$params = array(
			'quest' => 'To seek the holy grail',
			'YourMother' => 'Was a hamster',
			'specialChars' => '<>$+ &?=[]^%',
			'array' => array('firstItem', 'secondItem', '3rdItem')
		);
		
		$client = $this->_prepareClient(__FUNCTION__);
		$client->setUri($client->getUri(true) . '?name=Arthur');
		
		foreach ($params as $key => $val) {
			$client->setParameterGet($key, $val);
		}
		
		$params = array_merge(array('name' => 'Arthur'), $params);
		
		$res = $client->request('GET');
		$this->assertEquals(serialize($params), $res->getBody());
	}

	public function testPostData()
	{
		$enctypes = array(Zend_Http_Client::ENC_URLENCODED, Zend_Http_Client::ENC_FORMDATA);
		$params = array(
			'quest' => 'To seek the holy grail',
			'YourMother' => 'Was a hamster',
			'specialChars' => '<>$+ &?=[]^%',
			'array' => array('firstItem', 'secondItem', '3rdItem')
		);
		
		foreach ($enctypes as $type) {
			$client = $this->_prepareClient(__FUNCTION__);
			
			foreach ($params as $key => $val) {
				$client->setParameterPost($key, $val);
			}
			
			$client->setEncType($type);
			$res = $client->request('POST');
			$this->assertEquals(serialize($params), $res->getBody(), "POST data integrity test failed when using '{$type}' as content-type");
		}
	}
	
	public function testResetParameters() 
	{
		$client = $this->_prepareClient(__FUNCTION__);
		
		$params = array(
			'quest' => 'To seek the holy grail',
			'YourMother' => 'Was a hamster',
			'specialChars' => '<>$+ &?=[]^%',
			'array' => array('firstItem', 'secondItem', '3rdItem')
		);
		
		foreach ($params as $key => $val) {
				$client->setParameterPost($key, $val);
				$client->setParameterGet($key, $val);
		}
		
		$res = $client->request('POST');

		$this->assertContains(serialize($params) . "\n" . serialize($params), 
			$res->getBody(), 
			"returned body does not contain all GET and POST parameters (it should!)");
		
		$client->resetParameters();
		$res = $client->request('POST');
		
		$this->assertNotContains(serialize($params), $res->getBody(), 
			"returned body contains GET or POST parameters (it shouldn't!)");
	}
	
	public function testHeadersSingle()
	{
		$client = $this->_prepareClient('testHeaders');
		$headers = array(
			'Accept-encoding' => 'gzip,deflate',
			'X-baz' => 'Foo',
			'X-powered-by' => 'A large wooden badger'
		);
		
		foreach ($headers as $key => $val) {
			$client->setHeaders($key, $val);
		}
		
		$acceptHeader = "Accept: text/xml,text/html,*/*";
		$client->setHeaders($acceptHeader);
		
		$res = $client->request('TRACE');
		
		foreach ($headers as $key => $val) {
			$this->assertContains(strtolower("$key: $val"), strtolower($res->getBody()));
		}
		
		$this->assertContains(strtolower($acceptHeader), strtolower($res->getBody()));
	}
	
	public function testHeadersArray()
	{
		$client = $this->_prepareClient('testHeaders');
		$headers = array(
			'Accept-encoding' => 'gzip,deflate',
			'X-baz' => 'Foo',
			'X-powered-by' => 'A large wooden badger',
			'Accept: text/xml,text/html,*/*'
		);
		
		$client->setHeaders($headers);
		$res = $client->request('TRACE');
		
		foreach ($headers as $key => $val) {
			if (is_string($key)) {
				$this->assertContains(strtolower("$key: $val"), strtolower($res->getBody()));
			} else {
				$this->assertContains(strtolower($val), strtolower($res->getBody()));
			}
		}
 	}
		
	public function testRedirectDefault()
	{
		$client = $this->_prepareClient('testRedirections');
		
		// Set some parameters
		$client->setParameterGet('swallow', 'african');
		$client->setParameterPost('Camelot', 'A silly place');
		
		// Request
		$res = $client->request('POST');
		
		$this->assertEquals(3, $client->getLastRedirectionsCount(), 'Redirection counter is not as expected');
		$this->assertNotContains('swallow', $res->getBody());
		$this->assertNotContains('Camelot', $res->getBody());
	}
	
	public function testRedirectStrict()
	{
		$client = $this->_prepareClient('testRedirections');
		
		// Set some parameters
		$client->setParameterGet('swallow', 'african');
		$client->setParameterPost('Camelot', 'A silly place');
		
		// Set strict redirections
		$client->setStrictRedirects();
		
		// Request
		$res = $client->request('POST');
		
		$this->assertEquals(3, $client->getLastRedirectionsCount(), 'Redirection counter is not as expected');
		$this->assertContains('swallow', $res->getBody());
		$this->assertContains('Camelot', $res->getBody());
	}
	
	public function testMaxRedirectsExceeded()
	{
		$client = $this->_prepareClient('testRedirections');
		
		// Set some parameters
		$client->setParameterGet('swallow', 'african');
		$client->setParameterPost('Camelot', 'A silly place');
		
		// Set lower max redirections
		$client->setMaxRedirects(2);
		
		// Try with strict redirections first
		$client->setStrictRedirects();
		$res = $client->request('POST');
		$this->assertTrue($res->isRedirect(), 
			"Last response was not a redirection as expected. Response code: {$res->getStatus()}. Redirections counter: {$client->getLastRedirectionsCount()} (when strict redirects are on)");
		
		// Then try with normal redirections
		$client->setParameterGet('redirection', '0');
		$client->setStrictRedirects(false);
		$res = $client->request('POST');
		$this->assertTrue($res->isRedirect(), 
			"Last response was not a redirection as expected. Response code: {$res->getStatus()}. Redirections counter: {$client->getLastRedirectionsCount()} (when strict redirects are off)");
	}
	
	public function testAbsolutePathRedirect() {
		$client = $this->_prepareClient('testRelativeRedirections');
		$client->setParameterGet('redirect', 'abpath');
		$client->setMaxRedirects(1);
		$res = $client->request('GET');
		
		// Get the host part of our baseuri
		preg_match("|^(http://[^/:]+)(:\d+)*|", $this->baseuri, $host);
		$port = ':80';
		if (isset($host[2])) $port = $host[2];
		$host = $host[1] . $port;
		
		$this->assertEquals("{$host}/path/to/fake/file.ext?redirect=abpath", $client->getUri(true),
			"The new location is not as expected: {$client->getUri(true)}");
	}
	
	public function testRelativePathRedirect() {
		$client = $this->_prepareClient('testRelativeRedirections');
		$client->setParameterGet('redirect', 'relpath');
		$client->setMaxRedirects(1);
		$res = $client->request('GET');
		
		// Get the new expected path
		$uri = Zend_Uri_Http::factory($this->baseuri);
		$uri->setPort(80);
		$uri->setPath($uri->getPath() . 'path/to/fake/file.ext');
		$uri = $uri->__toString();
		
		$this->assertEquals("{$uri}?redirect=relpath", $client->getUri(true),
			"The new location is not as expected: {$client->getUri(true)}");
	}
	
	public function _prepareClient($target)
	{
		$uri = $this->baseuri . $target . '.php';
		return new Zend_Http_Client($uri);
	}
}
