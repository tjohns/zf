<?php
require_once 'Zend/Controller/Front.php';
require_once 'PHPUnit/Framework/TestCase.php';

require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/Controller/Response/Cli.php';
require_once 'Zend/Controller/Dispatcher.php';
require_once 'Zend/Controller/Router.php';

class Zend_Controller_FrontTest extends PHPUnit_Framework_TestCase
{
    protected $_controller = null;

    public function setUp()
    {
        $this->_controller = Zend_Controller_Front::getInstance();
        $this->_controller->resetInstance();
        $this->_controller->setControllerDirectory(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
        $this->_controller->returnResponse(true);
        $this->_controller->throwExceptions(false);
    }

    public function tearDown()
    {
        unset($this->_controller);
    }

    public function testResetInstance()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_controller->getParam('foo'));

        $this->_controller->resetInstance();
        $this->assertNull($this->_controller->getParam('bar'));
        $this->assertSame(array(), $this->_controller->getParams());
        $this->assertSame(array(), $this->_controller->getDispatcher()->getControllerDirectory());
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
        $test = $this->_controller->getControllerDirectory();
        $expected = array(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
        $this->assertSame($expected, $test);
    }

    public function testGetSetParam()
    {
        $this->_controller->setParam('foo', 'bar');
        $this->assertEquals('bar', $this->_controller->getParam('foo'));

        $this->_controller->setParam('bar', 'baz');
        $this->assertEquals('baz', $this->_controller->getParam('bar'));
    }

    public function testGetSetParams()
    {
        $this->_controller->setParams(array('foo' => 'bar'));
        $this->assertSame(array('foo' => 'bar'), $this->_controller->getParams());

        $this->_controller->setParam('baz', 'bat');
        $this->assertSame(array('foo' => 'bar', 'baz' => 'bat'), $this->_controller->getParams());

        $this->_controller->setParams(array('foo' => 'bug'));
        $this->assertSame(array('foo' => 'bug', 'baz' => 'bat'), $this->_controller->getParams());
    }

    public function testClearParams()
    {
        $this->_controller->setParams(array('foo' => 'bar', 'baz' => 'bat'));
        $this->assertSame(array('foo' => 'bar', 'baz' => 'bat'), $this->_controller->getParams());

        $this->_controller->clearParams('foo');
        $this->assertSame(array('baz' => 'bat'), $this->_controller->getParams());

        $this->_controller->clearParams();
        $this->assertSame(array(), $this->_controller->getParams());

        $this->_controller->setParams(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'));
        $this->assertSame(array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat'), $this->_controller->getParams());
        $this->_controller->clearParams(array('foo', 'baz'));
        $this->assertSame(array('bar' => 'baz'), $this->_controller->getParams());
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
        $request = new Zend_Controller_Request_Http('http://example.com/foo/bar');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('Bar action called', $body, $body);
        $this->assertContains('preDispatch called', $body, $body);
        $this->assertContains('postDispatch called', $body, $body);
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
        $this->_controller->setParam('foo', 'bar');
        $this->_controller->setParam('baz', 'bat');
        $response = $this->_controller->dispatch($request);

        $body = $response->getBody();
        $this->assertContains('foo: bar', $body);
        $this->assertContains('baz: bat', $body);
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

    /**
     * Test that run() throws exception when called from object instance
     */
    public function _testRunThrowsException()
    {
        try {
            $this->_controller->run(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files');
            $this->fail('Should not be able to call run() from object instance');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test that set/getBaseUrl() functionality works
     */
    public function testSetGetBaseUrl()
    {
        $this->assertNull($this->_controller->getBaseUrl());
        $this->_controller->setBaseUrl('/index.php');
        $this->assertEquals('/index.php', $this->_controller->getBaseUrl());
    }

    /**
     * Test that a set base URL is pushed to the request during the dispatch 
     * process
     */
    public function testBaseUrlPushedToRequest()
    {
        $this->_controller->setBaseUrl('/index.php');
        $request  = new Zend_Controller_Request_Http('http://example.com/index');
        $response = new Zend_Controller_Response_Cli();
        $response = $this->_controller->dispatch($request, $response);

        $this->assertContains('index.php', $request->getBaseUrl());
    }

    /**
     * Test that throwExceptions() sets and returns value properly
     */
    public function testThrowExceptions()
    {
        $this->_controller->throwExceptions(true);
        $this->assertTrue($this->_controller->throwExceptions());
        $this->_controller->throwExceptions(false);
        $this->assertFalse($this->_controller->throwExceptions());
    }

    /**
     * Test that with throwExceptions() set, an exception is thrown
     */
    public function testThrowExceptionsThrows()
    {
        $this->_controller->throwExceptions(true);
        $this->_controller->setControllerDirectory(dirname(__FILE__));
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/bogus/baz');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setRouter(new Zend_Controller_Router());

        try {
            $response = $this->_controller->dispatch($request);
            $this->fail('Invalid controller should throw exception');
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Test that returnResponse() sets and returns value properly
     */
    public function testReturnResponse()
    {
        $this->_controller->returnResponse(true);
        $this->assertTrue($this->_controller->returnResponse());
        $this->_controller->returnResponse(false);
        $this->assertFalse($this->_controller->returnResponse());
    }

    /**
     * Test that with returnResponse set to false, output is echoed and equals that in the response
     */
    public function testReturnResponseReturnsResponse()
    {
        $request = new Zend_Controller_Request_Http('http://framework.zend.com/foo/bar/var1/baz');
        $this->_controller->setResponse(new Zend_Controller_Response_Cli());
        $this->_controller->setRouter(new Zend_Controller_Router());
        $this->_controller->returnResponse(false);

        ob_start();
        $this->_controller->dispatch($request);
        $body = ob_get_clean();

        $actual = $this->_controller->getResponse()->getBody();
        $this->assertEquals($actual, $body);
    }
}
