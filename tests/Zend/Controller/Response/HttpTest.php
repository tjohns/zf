<?php
require_once 'Zend/Controller/Response/Http.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_Controller_Response_HttpTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_Http_Response
     */
    protected $_response;

    public function setUp()
    {
        $this->_response = new Zend_Controller_Response_Http();
    }

    public function tearDown()
    {
        unset($this->_response);
    }

    public function testSetHeader()
    {
        $expected = array(array('name' => 'Content-Type', 'value' => 'text/xml'));
        $this->_response->setHeader('Content-Type', 'text/xml');
        $this->assertSame($expected, $this->_response->getHeaders());

        $expected[] =array('name' => 'Content-Type', 'value' => 'text/html');
        $this->_response->setHeader('Content-Type', 'text/html');
        $this->assertSame($expected, $this->_response->getHeaders());

        $expected = array(array('name' => 'Content-Type', 'value' => 'text/plain'));
        $this->_response->setHeader('Content-Type', 'text/plain', true);
        $count = 0;
        foreach ($this->_response->getHeaders() as $header) {
            if ('Content-Type' == $header['name']) {
                if ('text/plain' == $header['value']) {
                    ++$count;
                } else {
                    $this->fail('Found header, but incorrect value');
                }
            }
        }
        $this->assertEquals(1, $count);
    }

    public function testClearHeaders()
    {
        $this->_response->setHeader('Content-Type', 'text/xml');
        $headers = $this->_response->getHeaders();
        $this->assertEquals(1, count($headers));

        $this->_response->clearHeaders();
        $headers = $this->_response->getHeaders();
        $this->assertEquals(0, count($headers));
    }

    public function testSetBody()
    {
        $expected = 'content for the response body';
        $this->_response->setBody($expected);
        $this->assertEquals($expected, $this->_response->getBody());

        $expected = 'new content';
        $this->_response->setBody($expected);
        $this->assertEquals($expected, $this->_response->getBody());
    }

    public function testAppendBody()
    {
        $expected = 'content for the response body';
        $this->_response->setBody($expected);

        $additional = '; and then there was more';
        $this->_response->appendBody($additional);
        $this->assertEquals($expected . $additional, $this->_response->getBody());
    }

    public function test__toString()
    {
        $skipHeadersTest = headers_sent();

        $this->_response->setHeader('Content-Type', 'text/plain');
        $this->_response->setBody('Content');
        $this->_response->appendBody('; and more content.');

        $expected = 'Content; and more content.';
        $result = $this->_response->__toString();

        if (!$skipHeadersTest) {
            $this->assertTrue(headers_sent());
            $headers = headers_list();
            $found = false;
            foreach ($headers as $header) {
                if ('Content-Type: text/plain' == $header) {
                    $found = true;
                }
            }
            $this->assertTrue($found, var_export($headers, 1));
        }
    }

    public function testGetBodyAsArray()
    {
        $string1 = 'content for the response body';
        $string2 = 'more content for the response body';
        $string3 = 'even more content for the response body';
        $this->_response->appendBody($string1);
        $this->_response->appendBody($string2);
        $this->_response->appendBody($string3);

        $expected = array($string1, $string2, $string3);
        $this->assertEquals($expected, $this->_response->getBody(true));
    }
}
