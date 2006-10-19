<?php

/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 */

/**
 * Zend_Http_Response
 */
require_once 'Zend/Http/Response.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Http
 * @subpackage UnitTests
 */
class Zend_Http_ResponseTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{ }
	
	public function testGzipResponse ()
	{
		$response_text = file_get_contents(dirname(__FILE__) . '/_files/response_gzip');
		
		$res = Zend_Http_Response::factory($response_text);
		
		$this->assertEquals('gzip', $res->getHeader('Content-encoding'));
		$this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
		$this->assertEquals('f24dd075ba2ebfb3bf21270e3fdc5303', md5($res->getRawBody()));
	}
		
	public function testDeflateResponse ()
	{
		$response_text = file_get_contents(dirname(__FILE__) . '/_files/response_deflate');
		
		$res = Zend_Http_Response::factory($response_text);
		
		$this->assertEquals('deflate', $res->getHeader('Content-encoding'));
		$this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
		$this->assertEquals('ad62c21c3aa77b6a6f39600f6dd553b8', md5($res->getRawBody()));
	}
			
	public function testChunkedResponse ()
	{
		$response_text = file_get_contents(dirname(__FILE__) . '/_files/response_chunked');
		
		$res = Zend_Http_Response::factory($response_text);
		
		$this->assertEquals('chunked', $res->getHeader('Transfer-encoding'));
		$this->assertEquals('0b13cb193de9450aa70a6403e2c9902f', md5($res->getBody()));
		$this->assertEquals('c0cc9d44790fa2a58078059bab1902a9', md5($res->getRawBody()));
	}
	
	public function testLineBreaksCompatibility()
	{
		$response_text_lf = file_get_contents(dirname(__FILE__) . '/_files/response_lfonly');
		$res_lf = Zend_Http_Response::factory($response_text_lf);
		
		$response_text_crlf = file_get_contents(dirname(__FILE__) . '/_files/response_crlf');
		$res_crlf = Zend_Http_Response::factory($response_text_crlf);
		
		$this->assertEquals($res_lf->getHeadersAsString(true), $res_crlf->getHeadersAsString(true), 'Responses headers do not match');
		$this->assertEquals($res_lf->getBody(), $res_crlf->getBody(), 'Response bodies do not match');
	}
}