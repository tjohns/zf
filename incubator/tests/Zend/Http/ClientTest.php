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
		
		// Test single value assignment
		foreach ($params as $key => $val) {
			$client->setParameterGet($key, $val);
		}
		
		$params = array_merge(array('name' => 'Arthur'), $params);
		
		$res = $client->request('GET');
		$this->assertEquals(serialize($params), $res->getBody());
		
		// Test array of values assignment
		$client->resetParameters();
		$client->setParameterGet($params);
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
			
			// Test signle parameter assignment
			foreach ($params as $key => $val) {
				$client->setParameterPost($key, $val);
			}
			
			$client->setEncType($type);
			$res = $client->request('POST');
			$this->assertEquals(serialize($params), $res->getBody(), "POST data integrity test failed when using '{$type}' as content-type");
			
			// Test para,meters array assignment
			$client->resetParameters();
			$client->setParameterPost($params);
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
		
		$this->assertEquals(3, $client->getRedirectionsCount(), 'Redirection counter is not as expected');
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
		$client->setConfig(array('strictredirects' => true));
		
		// Request
		$res = $client->request('POST');
		
		$this->assertEquals(3, $client->getRedirectionsCount(), 'Redirection counter is not as expected');
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
		// Try with strict redirections first
		$client->setConfig(array('strictredirects' => true, 'maxredirects' => 2));
		
		$res = $client->request('POST');
		$this->assertTrue($res->isRedirect(), 
			"Last response was not a redirection as expected. Response code: {$res->getStatus()}. Redirections counter: {$client->getRedirectionsCount()} (when strict redirects are on)");
		
		// Then try with normal redirections
		$client->setParameterGet('redirection', '0');
		$client->setConfig(array('strictredirects' => false));
		$res = $client->request('POST');
		$this->assertTrue($res->isRedirect(), 
			"Last response was not a redirection as expected. Response code: {$res->getStatus()}. Redirections counter: {$client->getRedirectionsCount()} (when strict redirects are off)");
	}
	
	public function testAbsolutePathRedirect() {
		$client = $this->_prepareClient('testRelativeRedirections');
		$client->setParameterGet('redirect', 'abpath');
		$client->setConfig(array('maxredirects' => 1));
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
		$client->setConfig(array('maxredirects' => 1));
		$res = $client->request('GET');
		
		// Get the new expected path
		$uri = Zend_Uri_Http::factory($this->baseuri);
		$uri->setPort(80);
		$uri->setPath($uri->getPath() . 'path/to/fake/file.ext');
		$uri = $uri->__toString();
		
		$this->assertEquals("{$uri}?redirect=relpath", $client->getUri(true),
			"The new location is not as expected: {$client->getUri(true)}");
	}
	
	public function testInvalidUriException() {
		try {
			$client = new Zend_Http_Client('http://__invalid__.com');
			$this->fail('Excepted invalid URI string exception was not thrown');
		} catch (Exception $e) {
			// We're good
		}
		
		try {
			$uri = Zend_Uri::factory('mailto:nobody@example.com');
			$client = new Zend_Http_Client($uri);
			$this->fail('Excepted invalid URI object exception was not thrown');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good
		}
	}
	
	public function testGetUri() {
		$this->client = new Zend_Http_Client('http://www.example.com');
		
		$uri = $this->client->getUri();
		$this->assertTrue($uri instanceof Zend_Uri_Http, 'Returned value is not a Uri object as expected');
		$this->assertEquals($uri->getHost(), 'www.example.com', 'Returned Uri object does not hold the expected host');
			
		$uri = $this->client->getUri(true);
		$this->assertTrue(is_string($uri), 'Returned value expected to be a string, ' . gettype($uri) . ' returned');
		$this->assertContains('www.example.com', $uri, 'Returned string is not the expected URI');
	}
	
	public function testInvalidHeaderName() {
		$this->client = new Zend_Http_Client('http://www.example.com');
		try {
			$this->client->setHeaders(array('in valid name' => 'foo'));
			$this->fail('Expected invalid header name exception was not thrown');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good
		}
	}
	
	public function testGetHeader() {
		$client = new Zend_Http_Client('http://www.example.com');
		$client->setHeaders(array(
			'Accept-encoding' => 'gzip,deflate',
			'Accept-language' => 'en,de,*',
		));
		
		$this->assertEquals($client->getHeader('Accept-encoding'), 'gzip,deflate', 'Returned value of header is not as expected');
		$this->assertEquals($client->getHeader('X-Fake-Header'), null, 'Non-existing header should not return a value');
	}
	
	public function testInvalidHeaderExcept() {
		$client = new Zend_Http_Client('http://www.example.com');
		try {
			$client->setHeaders('Ina_lid* Hea%der', 'is not good');
			$this->fail('Expected invalid header name exception was not thrown');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good
		}
	}
	
	public function testParameterUnset() {
		$client = $this->_prepareClient('testResetParameters');
		
		$gparams = array (
			'cheese' => 'camambert',
			'beer'   => 'jever pilnsen',
		);
		
		$pparams = array (
			'from' => 'bob',
			'to'   => 'alice'
		);
		
		$client->setParameterGet($gparams)->setParameterPost($pparams);
		
		// Remove some parameters
		$client->setParameterGet('cheese', null)->setParameterPost('to', null);
		$res = $client->request('POST');
		
		$this->assertNotContains('cheese', $res->getBody(), 'The "cheese" GET parameter was expected to be unset');
		$this->assertNotContains('alice', $res->getBody(), 'The "to" POST parameter was expected to be unset');
	}
	
	public function testHttpAuthBasic() {
		$client = $this->_prepareClient('testHttpAuth');
		$client->setParameterGet(array(
			'user'   => 'alice',
			'pass'   => 'secret',
			'method' => 'Basic'
		));
		
		// First - fail password
		$client->setAuth('alice', 'wrong');
		$res = $client->request();
		$this->assertEquals(401, $res->getStatus(), 'Expected HTTP 403 response was not recieved');
		
		// Now use good password
		$client->setAuth('alice', 'secret');
		$res = $client->request();
		$this->assertEquals(200, $res->getStatus(), 'Expected HTTP 200 response was not recieved');
	}
	
	public function testExceptUnsupportedAuth() {
		$client = new Zend_Http_Client();
		
		try {
			$client->setAuth('shahar', '1234', 'SuperStrongAlgo');
		} catch (Zend_Http_Client_Exception $e) {
			// We're good!
		}
	}
	
	public function testCancelAuth() {
		$client = $this->_prepareClient('testHttpAuth');
		
		// Set auth and cancel it
		$client->setAuth('alice', 'secret');
		$client->setAuth(false);
		$res = $client->request();
		$this->assertEquals(401, $res->getStatus(), 'Expected HTTP 401 response was not recieved');
		$this->assertNotContains('alice', $res->getBody(), "Body contains the user name, but it shouldn't");
		$this->assertNotContains('secret', $res->getBody(), "Body contains the password, but it shouldn't");
	}		
	
	public function testRawPostData() {
		$client = $this->_prepareClient(__FUNCTION__);
		$data = "Chuck Norris never wet his bed as a child. The bed wet itself out of fear.";
		
		$res = $client->setRawData($data, 'text/html')->request('POST');
		$this->assertEquals($data, $res->getBody(), 'Response body does not contain the expected data');
	}
	
	public function testCookiesStringNoJar() {
		$client = $this->_prepareClient('testCookies');
		$cookies = array(
			'name'   => 'value',
			'cookie' => 'crumble'
		);
		
		foreach ($cookies as $k => $v) {
			$client->setCookie($k, $v);
		}
		
		$res = $client->request();
		
		$this->assertEquals($res->getBody(), serialize($cookies), 'Response body does not contain the expected cookies');
	}
	
	public function testSetCookieJar() {
		$client = new Zend_Http_Client('http://www.example.com');
		$client->setCookieJar();
		$client->setCookie('cookie', 'value');
		$client->setCookie('chocolate', 'chips');
		$jar = $client->getCookieJar();
		
		// Check we got the right cookiejar
		$this->assertTrue($jar instanceof Zend_Http_CookieJar, '$jar is not an instance of Zend_Http_CookieJar as expected');
		$this->assertEquals(count($jar->getAllCookies()), 2, '$jar does not contain 2 cookies as expected');
		
		// Try unsetting the cookiejar
		$client->setCookieJar(null);
		$this->assertNull($client->getCookieJar(), 'Cookie jar is expected to be null but it is not');
	}
	
	public function testSetReadyCookieJar() {
		$client = new Zend_Http_Client();
		$jar = new Zend_Http_CookieJar();
		$jar->addCookie('cookie=value', 'http://www.example.com');
		$jar->addCookie('chocolate=chips; path=/foo', 'http://www.example.com');
		
		$client->setCookieJar($jar);
		
		$this->assertEquals($jar, $client->getCookieJar(), '$jar is not the client\'s cookie jar as expected');
	}
	
	public function testSetInvalidCookieJar() {
		$client = new Zend_Http_Client();
		
		try {
			$client->setCookieJar('cookiejar');
			$this->fail('Invalid cookiejar exception was not thrown');
		} catch (Exception $e) {
			// We're good
		}
	}
	
	public function testSetCookieObjectNoJar() {
		$client = $this->_prepareClient('testCookies');
		$refuri = $client->getUri();
		
		$cookies = array(
			Zend_Http_Cookie::factory('chocolate=chips', $refuri),
			Zend_Http_Cookie::factory('crumble=apple', $refuri)
		);
		
		$strcookies = array();
		foreach ($cookies as $c) {
			$client->setCookie($c);
			$strcookies[$c->getName()] = $c->getValue();
		}
		
		$res = $client->request();
		$this->assertEquals($res->getBody(), serialize($strcookies), 'Response body does not contain the expected cookies');
	}
	
	public function testSetCookieObjectJar() {
		$client = $this->_prepareClient('testCookies');
		$client->setCookieJar();
		$refuri = $client->getUri();
		
		$cookies = array(
			Zend_Http_Cookie::factory('chocolate=chips', $refuri),
			Zend_Http_Cookie::factory('crumble=apple', $refuri)
		);
		
		$strcookies = array();
		foreach ($cookies as $c) {
			$client->setCookie($c);
			$strcookies[$c->getName()] = $c->getValue();
		}
		
		$res = $client->request();
		$this->assertEquals($res->getBody(), serialize($strcookies), 'Response body does not contain the expected cookies');
	}
	
	public function testSocketErrorException() {
		// Reduce timeout to 3 seconds to avoid waiting
		$client = new Zend_Http_Client('http://255.255.255.255', array('timeout' => 3));
		
		try {
			$client->request();
			$this->fail('Expected connection error exception was not thrown');
		} catch (Zend_Http_Client_Adapter_Exception $e) { 
			// We're good!
		}
	}

	/**
	 * Prepare an HTTP client to request a file on the testing server
	 *
	 * @param string $target
	 * @return Zend_Http_Client
	 */
	public function _prepareClient($target)
	{
		$uri = $this->baseuri . $target . '.php';
		return new Zend_Http_Client($uri);
	}
}
