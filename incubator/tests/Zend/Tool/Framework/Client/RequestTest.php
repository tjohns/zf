<?php

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Framework/Request.php';

class Zend_Tool_Framework_Client_RequestTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Tool_Framework_Client_Request
     */
    protected $_request = null;
    
    public function setup()
    {
        $this->_request = new Zend_Tool_Framework_Client_Request();
    }
    
    public function testProviderNameGetterAndSetter()
    {
        $this->_request->setProviderName('foo');
        $this->assertEquals('foo', $this->_request->getProviderName());
    }
    
    public function testSpecialtyNameGetterAndSetter()
    {
        $this->_request->setSpecialtyName('foo');
        $this->assertEquals('foo', $this->_request->getSpecialtyName());
    }
    
    public function testActionNameGetterAndSetter()
    {
        $this->_request->setActionName('foo');
        $this->assertEquals('foo', $this->_request->getActionName());
    }
    
    public function testActionParametersGetterAndSetter()
    {
        $this->_request->setActionParameter('foo', 'bar');
        $this->_request->setActionParameter('bar', 'baz');
        $this->assertEquals('bar', $this->_request->getActionParameter('foo'));
        $this->assertArrayHasKey('foo', $this->_request->getActionParameters());
        $this->assertArrayHasKey('bar', $this->_request->getActionParameters());
        $this->assertEquals(2, count($this->_request->getActionParameters()));
    }
    
    public function testProviderParameterGetterAndSetter()
    {
        $this->_request->setProviderParameter('foo', 'bar');
        $this->_request->setProviderParameter('bar', 'baz');
        $this->assertEquals('bar', $this->_request->getProviderParameter('foo'));
        $this->assertArrayHasKey('foo', $this->_request->getProviderParameters());
        $this->assertArrayHasKey('bar', $this->_request->getProviderParameters());
        $this->assertEquals(2, count($this->_request->getProviderParameters()));
    }
    
    public function testPretendGetterAndSetter()
    {
        $this->assertFalse($this->_request->isPretend());
        $this->_request->setPretend(true);
        $this->assertTrue($this->_request->isPretend());
    }
    
    public function testDispatchableGetterAndSetter()
    {
        $this->assertTrue($this->_request->isDispatchable());
        $this->_request->setDispatchable(false);
        $this->assertFalse($this->_request->isDispatchable());
    }
    
    /*
    protected $_providerName = null;
    protected $_specialtyName = null;
    protected $_actionName = null;
    protected $_actionParameters = array();
    protected $_providerParameters = array();
    protected $_isPretend = false;
    protected $_isDispatchable = true;
    */        
    
}
