<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Framework/Response.php';

class Zend_Tool_Framework_Client_ResponseTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Client_Response
     */
    protected $_response = null;
    
    protected $_responseBuffer = array();
    
    public function setup()
    {
        $this->_response = new Zend_Tool_Framework_Client_Response();
    }
    
    public function testContentGetterAndSetter()
    {
        $this->_response->setContent('foo');
        $this->assertEquals('foo', $this->_response->getContent());
        
        $this->_response->setContent('bar');
        $this->assertEquals('bar', $this->_response->getContent());
    }
    
    public function testContentCanBeAppended()
    {
        $this->_response->setContent('foo');
        $this->assertEquals('foo', $this->_response->getContent());
        
        $this->_response->setContent('bar');
        $this->assertEquals('bar', $this->_response->getContent());
        
        $this->_response->appendContent('foo');
        $this->assertEquals('barfoo', $this->_response->getContent());
    }
    
    public function testContentCallback()
    {
        $this->_response->setContentCallback(array($this, '_responseCallback'));
        $this->_response->appendContent('foo');
        $this->assertEquals('foo', implode('', $this->_responseBuffer));
        $this->_response->appendContent('bar');
        $this->_response->appendContent('baz');
        $this->assertEquals('foo-bar-baz', implode('-', $this->_responseBuffer));
    }
    
    public function testExceptionHandling()
    {
        $this->assertFalse($this->_response->isException());
        $this->_response->setException(new Exception('my response exception'));
        $this->assertTrue($this->_response->isException());
        $this->assertEquals('my response exception', $this->_response->getException()->getMessage());
    }
    
    public function testContentSeparatorAndCastingToString()
    {
        $this->assertNull($this->_response->getContentSeparator());
        $this->_response->setContentSeparator(' - ');
        $this->assertEquals(' - ', $this->_response->getContentSeparator());
        
        
        $this->_response->appendContent('foo');
        $this->_response->appendContent('boo');
        $this->assertEquals('foo - boo', $this->_response->__toString());
    }
    
    
    public function _responseCallback($content)
    {
        $this->_responseBuffer[] = $content;
    }
}
