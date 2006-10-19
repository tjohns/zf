<?php
require_once 'Zend/XmlRpc/Response.php';
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'PHPUnit/Framework/IncompleteTestError.php';

/**
 * Test case for Zend_XmlRpc_Response
 *
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_XmlRpc_ResponseTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Zend_XmlRpc_Response object
     * @var Zend_XmlRpc_Response
     */
    protected $_response;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_response = new Zend_XmlRpc_Response();
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        unset($this->_response);
    }

    /**
     * __construct() test
     */
    public function test__construct()
    {
        $this->assertTrue($this->_response instanceof Zend_XmlRpc_Response);
    }

    /**
     * get/setReturnValue() test
     */
    public function testReturnValue()
    {
        $this->_response->setReturnValue('string');
        $this->assertEquals('string', $this->_response->getReturnValue());

        $this->_response->setReturnValue(array('one', 'two'));
        $this->assertSame(array('one', 'two'), $this->_response->getReturnValue());
    }

    /**
     * isFault() test
     *
     * Call as method call 
     *
     * Returns: boolean 
     */
    public function testIsFault()
    {
        $this->assertFalse($this->_response->isFault());
        $this->_response->loadXml('foo');
        $this->assertTrue($this->_response->isFault());
    }

    /**
     * loadXml() test
     *
     * Call as method call 
     *
     * Expects:
     * - response: 
     * 
     * Returns: boolean 
     */
    public function testLoadXml()
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $response = $dom->appendChild($dom->createElement('methodResponse'));
        $params   = $response->appendChild($dom->createElement('params'));
        $param    = $params->appendChild($dom->createElement('param'));
        $value    = $param->appendChild($dom->createElement('value'));
        $value->appendChild($dom->createElement('string', 'Return value'));

        $xml = $dom->saveXML();

        $parsed = $this->_response->loadXml($xml);
        $this->assertTrue($parsed, $xml);
        $this->assertEquals('Return value', $this->_response->getReturnValue());
    }

    /**
     * __toString() test
     *
     * Call as method call 
     *
     * Returns: string 
     */
    public function test__toString()
    {
        $this->_response->setReturnValue('return value');
        $xml = $this->_response->__toString();

        try {
            $sx = new SimpleXMLElement($xml);
        } catch (Exception $e) {
            $this->fail('Invalid XML returned');
        }

        $this->assertTrue($sx->params ? true : false);
        $this->assertTrue($sx->params->param ? true : false);
        $this->assertTrue($sx->params->param->value ? true : false);
        $this->assertTrue($sx->params->param->value->string ? true : false);
        $this->assertEquals('return value', (string) $sx->params->param->value->string);
    }


}
