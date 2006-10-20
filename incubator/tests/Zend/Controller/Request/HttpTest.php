<?php
require_once 'Zend/Controller/Request/Http.php';
require_once 'PHPUnit/Framework/TestCase.php';

class Zend_Controller_Request_HttpTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_Http_Request
     */
    protected $_request;

    public function setUp()
    {
        $this->_request = new Zend_Controller_Request_Http();
    }

    public function tearDown()
    {
        unset($this->_request);
    }

    public function testSetGetControllerKey()
    {
        $this->_request->setControllerKey('controller');
        $this->assertEquals('controller', $this->_request->getControllerKey());

        $this->_request->setControllerKey('foo');
        $this->assertEquals('foo', $this->_request->getControllerKey());
    } 

    public function testSetGetActionKey()
    {
        $this->_request->setActionKey('action');
        $this->assertEquals('action', $this->_request->getActionKey());

        $this->_request->setActionKey('foo');
        $this->assertEquals('foo', $this->_request->getActionKey());
    } 

    public function testSetGetControllerName()
    {
        $this->_request->setControllerName('foo');
        $this->assertEquals('foo', $this->_request->getControllerName());

        $this->_request->setControllerName('bar');
        $this->assertEquals('bar', $this->_request->getControllerName());
    }
 
    public function testSetGetActionName()
    {
        $this->_request->setActionName('foo');
        $this->assertEquals('foo', $this->_request->getActionName());

        $this->_request->setActionName('bar');
        $this->assertEquals('bar', $this->_request->getActionName());
    }
 
    public function testSetGetParam()
    {
        $this->_request->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_request->getParam('foo'));
    }
 
    public function testSetGetParams()
    {
        $params = array(
            'foo' => 'bar',
            'boo' => 'bah',
            'fee' => 'fi'
        );
        $this->_request->setParams($params);
        $this->assertSame($params, $this->_request->getParams());
    }
}
