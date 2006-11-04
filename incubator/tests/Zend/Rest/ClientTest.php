<?php
/**
 * @package Zend_Rest
 * @subpackage UnitTests
 */

/**
 * Zend_Rest_Server
 */
require_once 'Zend/Rest/Client.php';

/**
 * PHPUnit Test Case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * Test cases for Zend_Rest_Server
 *
 * @package Zend_Rest
 * @subpackage UnitTests
 */
class Zend_Rest_ClientTest extends PHPUnit_Framework_TestCase 
{
	static $path;
	function __construct()
	{
		self::$path = dirname(__FILE__).'/responses/';
	}
	
	function testResponseSuccess()
	{
		$xml = file_get_contents(self::$path ."returnString.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertTrue($client->isSuccess());
	}
	
	function testResponseIsError()
	{
		$xml = file_get_contents(self::$path ."returnError.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertTrue($client->isError());
	}
	
	function testResponseString()
	{
		$xml = file_get_contents(self::$path ."returnString.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertEquals("string", $client->__toString());
	}
	
	function testResponseInt()
	{
		$xml = file_get_contents(self::$path ."returnInt.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertEquals("123", $client->__toString());
	}
	
	function testResponseArray()
	{
		$xml = file_get_contents(self::$path ."returnArray.xml");
		// <foo>bar</foo><baz>1</baz><key_1>0</key_1><bat>123</bat>
		$client = new Zend_Rest_Client_Result($xml);
		foreach ($client as $key => $value) {
			$result_array[$key] = (string) $value;
		}
		$this->assertEquals(array("foo" => "bar", "baz" => "1", "key_1" => "0", "bat" => "123", "status" => "success"), $result_array);
	}
	
	function testResponseObject()
	{
		$xml = file_get_contents(self::$path ."returnObject.xml");
		// <foo>bar</foo><baz>1</baz><bat>123</bat><qux>0</qux><status>success</status>
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertEquals("bar", $client->foo);
		$this->assertEquals("1", $client->baz);
		$this->assertEquals("123", $client->bat);
		$this->assertEquals("0", $client->qux);
		$this->assertEquals("success", $client->status);
	}
	
	function testResponseTrue()
	{
		$xml = file_get_contents(self::$path ."returnTrue.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertTrue((bool)$client->response);
	}
	
	function testResponseFalse()
	{
		$xml = file_get_contents(self::$path ."returnFalse.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertFalse((bool)$client->response);
	}
	
	function testResponseVoid()
	{
		$xml = file_get_contents(self::$path . "returnVoid.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertEquals(null, $client->response);
	}
	
	function testResponseException()
	{
		$xml = file_get_contents(self::$path . "returnError.xml");
		$client = new Zend_Rest_Client_Result($xml);
		$this->assertTrue($client->isError());
	}
	
	function testFlickrEcho()
	{
		if (!TESTS_ZEND_REST_CLIENT_FLICKR_APIKEY) {
			$this->markTestSkipped("Flickr API Key not found");
		}
		$client = new Zend_Rest_Client('http://api.flickr.com/services/rest/');
 		$result = $client->method('flickr.test.echo')->name('Davey Shafik')->api_key(TESTS_ZEND_REST_CLIENT_FLICKR_APIKEY)->get()->name;
 		$this->assertEquals("Davey Shafik", $result);
	}
}