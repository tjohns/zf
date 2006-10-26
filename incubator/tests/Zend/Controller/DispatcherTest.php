<?php
require_once 'Zend/Controller/Dispatcher.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';

class Zend_Controller_DispatcherTest extends PHPUnit_Framework_TestCase 
{
    protected $_dispatcher;

    public function setUp()
    {
        $this->_dispatcher = new Zend_Controller_Dispatcher();
        $this->_dispatcher->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
    }

    public function tearDown()
    {
        unset($this->_dispatcher);
    }

    public function testFormatControllerName()
    {
        $this->assertEquals('IndexController', $this->_dispatcher->formatControllerName('index'));
        $this->assertEquals('Site_CustomController', $this->_dispatcher->formatControllerName('site_custom'));
    }

    public function testFormatActionName()
    {
        $this->assertEquals('indexAction', $this->_dispatcher->formatActionName('index'));
        $this->assertEquals('myindexAction', $this->_dispatcher->formatActionName('myIndex'));
        $this->assertEquals('my_IndexAction', $this->_dispatcher->formatActionName('my_index'));
    }

    public function testGetSetControllerDirectory()
    {
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files', $this->_dispatcher->getControllerDirectory());
    }

    public function testIsDispatchable()
    {
        $request = new Zend_Controller_Request_Http();

        $request->setControllerName('index');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('foo');
        $this->assertTrue($this->_dispatcher->isDispatchable($request));

        $request->setControllerName('baz');
        $this->assertFalse($this->_dispatcher->isDispatchable($request));
    }

    public function testSetGetResponse()
    {
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->setResponse($response);
        $this->assertTrue($response === $this->_dispatcher->getResponse());
    }

    public function testSetGetDefaultController()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultController());

        $this->_dispatcher->setDefaultController('foo');
        $this->assertEquals('foo', $this->_dispatcher->getDefaultController());
    }

    public function testSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_dispatcher->getDefaultAction());

        $this->_dispatcher->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_dispatcher->getDefaultAction());
    }

    /**
     * Test default action on valid controller
     */
    public function testDispatch()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    /**
     * Test valid action on valid controller
     */
    public function testDispatch1()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('index');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $this->assertContains('Index action called', $this->_dispatcher->getResponse()->getBody());
    }

    /**
     * Test invalid action on valid controller
     */
    public function testDispatch2()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('foo');
        $response = new Zend_Controller_Response_Cli();

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should be raised by __call');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test invalid controller
     */
    public function testDispatch3()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('baz');
        $response = new Zend_Controller_Response_Cli();

        try {
            $this->_dispatcher->dispatch($request, $response);
            $this->fail('Exception should be raised; no such controller');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test valid action on valid controller; test pre/postDispatch
     */
    public function testDispatch4()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('foo');
        $request->setActionName('bar');
        $response = new Zend_Controller_Response_Cli();
        $this->_dispatcher->dispatch($request, $response);

        $body = $this->_dispatcher->getResponse()->getBody();
        $this->assertContains('Bar action called', $body);
        $this->assertContains('preDispatch called', $body);
        $this->assertContains('postDispatch called', $body);
    }

}
