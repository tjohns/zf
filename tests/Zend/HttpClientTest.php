<?php
/**
 * @package    Zend_HttpClient
 * @subpackage UnitTests
 */


/**
 * Zend_HttpClient
 */
require_once 'Zend/HttpClient.php';

/**
 * PHPUnit2 test case
 */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_HttpClient
 * @subpackage UnitTests
 */
class Zend_HttpClientTest extends PHPUnit2_Framework_TestCase
{
    protected $_remoteEnabled = false;

    public function setUp()
    {
        /**
         * @todo reimplement with markAsSkipped for PHPUnit 3.0
         */
        if (defined('TESTS_ZEND_HTTPCLIENT_REMOTE_ENABLED')) {
            $this->_remoteEnabled = TESTS_ZEND_HTTPCLIENT_REMOTE_ENABLED;
        }
    }

    public function testConstructValid()
    {
        // throws exception on failure
        new Zend_HttpClient('http://zend.com', array('name: value'));
    }

    public function testConstructBadHeadersNotArray()
    {
		try {
            new Zend_HttpClient('http://zend.com', '');
		} catch (Zend_HttpClient_Exception $e) {
			$this->assertRegexp('/headers must be supplied as an array/i', $e->getMessage());
			return;
		}
		$this->fail('No exception was returned; expected Zend_HttpClient_Exception');
    }

    public function testConstructBadHeadersHeaderNotString()
    {
		try {
            new Zend_HttpClient('http://zend.com', array(1));
		} catch (Zend_HttpClient_Exception $e) {
			$this->assertRegexp('/header must be a string/i', $e->getMessage());
			return;
		}
		$this->fail('No exception was returned; expected Zend_HttpClient_Exception');
    }

    public function testConstructBadHeadersBadHeaderFormat()
    {
		try {
            new Zend_HttpClient('http://zend.com', array(''));
		} catch (Zend_HttpClient_Exception $e) {
			$this->assertRegexp('/bad header/i', $e->getMessage());
			return;
		}
		$this->fail('No exception was returned; expected Zend_HttpClient_Exception');
    }

    public function testValidGetRequest()
    {
        if (!$this->_remoteEnabled) {
            return;
        }

        $http = new Zend_HttpClient(TESTS_ZEND_HTTPCLIENT_REMOTE_URI);
        $response = $http->get();
        $this->assertEquals(200, $response->getStatus(), 'GET request returned unexpected response code (' .$response->getStatus(). ')');
        $this->assertType('array', $response->getHeaders(), 'GET request failed to return headers');
        $this->assertNotNull($response->getBody(), 'GET request failed to return a document body');
    }

    public function testBadUri()
    {
        if (!$this->_remoteEnabled) {
            return;
        }

        try {
            $http = new Zend_HttpClient('http://baduri.org');
            $http->setTimeout(1);
            $response = $http->get();
        } catch (Exception $e) {
            $this->assertRegExp('/^Unable to Connect to (.*)/', $e->getMessage());
        }
    }
}
