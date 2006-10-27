<?php
require_once 'Zend/Controller/Front.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Controller/Dispatcher.php';
require_once 'Zend/Controller/Router.php';

class Zend_Controller_FrontTest extends PHPUnit_Framework_TestCase
{
    protected $_controller;

    public function setUp()
    {
        $this->_controller = new Zend_Controller_Front();
        $this->_controller->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
    }

    public function tearDown()
    {
        unset($this->_controller);
    }

    public function testSetGetRequest()
    {
        $request = new Zend_Controller_Request_Http();
        $this->_controller->setRequest($request);

        $this->assertTrue($request === $this->_controller->getRequest());
    }

    public function testSetGetResponse()
    {
        $response = new Zend_Controller_Response_Cli();
        $this->_controller->setResponse($response);

        $this->assertTrue($response === $this->_controller->getResponse());
    }

    public function testSetGetRouter()
    {
        $router = new Zend_Controller_Router();
        $this->_controller->setRouter($router);

        $this->assertTrue($router === $this->_controller->getRouter());
    }

    public function testSetGetDispatcher()
    {
        $dispatcher = new Zend_Controller_Dispatcher();
        $this->_controller->setDispatcher($dispatcher);

        $this->assertTrue($dispatcher === $this->_controller->getDispatcher());
    }

    public function testSetGetControllerDirectory()
    {
        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files', $this->_controller->getControllerDirectory());
    }

    public function testAddParam()
    {
        $this->_controller->addParam('foo');
        $this->assertSame(array('foo'), $this->_controller->getParams());

        $this->_controller->addParam('bar');
        $this->assertSame(array('foo', 'bar'), $this->_controller->getParams());
    }

    public function testSetParams()
    {
        $this->_controller->setParams(array('foo', 'bar'));
        $this->assertSame(array('foo', 'bar'), $this->_controller->getParams());

        $this->_controller->addParam('foo');
        $this->assertSame(array('foo', 'bar', 'foo'), $this->_controller->getParams());

        $this->_controller->setParams(array('foo', 'bar'));
        $this->assertSame(array('foo', 'bar'), $this->_controller->getParams());
    }

    public function testSetGetDefaultController()
    {
        $this->assertEquals('index', $this->_controller->getDefaultController());

        $this->_controller->setDefaultController('foo');
        $this->assertEquals('foo', $this->_controller->getDefaultController());
    }

    public function testSetGetDefaultAction()
    {
        $this->assertEquals('index', $this->_controller->getDefaultAction());

        $this->_controller->setDefaultAction('bar');
        $this->assertEquals('bar', $this->_controller->getDefaultAction());
    }

    /**
     * Test default action on valid controller
     */
    public function testDispatch()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertContains('Index action called', $response->getBody());
    }

    /**
     * Test valid action on valid controller
     */
    public function testDispatch1()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('index');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $this->assertContains('Index action called', $response->getBody());
    }

    /**
     * Test invalid action on valid controller
     */
    public function testDispatch2()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('foo');

        try {
            $this->_controller->dispatch($request);
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

        try {
            $this->_controller->dispatch($request);
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
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body);
        $this->assertContains('preDispatch called', $body);
        $this->assertContains('postDispatch called', $body);
    }

    /**
     * Test that extra arguments get passed
     */
    public function testDispatch5()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setControllerName('index');
        $request->setActionName('args');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->addParam('foo');
        $this->_controller->addParam('bar');
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('foo; bar', $body);
    }

    /**
     * Test using router
     */
    public function testDispatch6()
    {
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/foo/bar/var1/baz');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setRouter(new Zend_Controller_Router());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body);
        $params = $request->getParams();
        $this->assertTrue(isset($params['var1']));
        $this->assertEquals('baz', $params['var1']);
    }

    /**
     * Test without router, using GET params
     */
    public function testDispatch7()
    {
        if ('cli' == strtolower(php_sapi_name())) {
            $this->markTestSkipped('Issues with $_GET in CLI interface prevents test from passing');
        }
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/index.php?controller=foo&action=bar');

        $response = new Zend_Controller_Response_Cli();
        $response = $this->_controller->dispatch($request, $response);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body);
    }
}
